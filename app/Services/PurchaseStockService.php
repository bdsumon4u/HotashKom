<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\Log;

class PurchaseStockService
{
    /**
     * Apply stock changes for a new purchase
     */
    public function applyStockChanges(Purchase $purchase): void
    {
        $parentProductIds = [];

        foreach ($purchase->products as $product) {
            $currentStock = $product->stock_count;
            $currentAvg = $product->average_purchase_price ?? 0;
            $purchaseQty = $product->pivot->quantity;
            $purchasePrice = $product->pivot->price;

            $newStock = $currentStock + $purchaseQty;
            $newTotalCost = ($currentStock * $currentAvg) + ($purchaseQty * $purchasePrice);
            $newAvg = $newStock > 0 ? $newTotalCost / $newStock : $purchasePrice;

            Log::info("Applying stock changes for product {$product->id}:", [
                'product' => $product->name,
                'current_stock' => $currentStock,
                'current_avg' => $currentAvg,
                'purchase_qty' => $purchaseQty,
                'purchase_price' => $purchasePrice,
                'new_stock' => $newStock,
                'new_avg' => $newAvg,
            ]);

            $product->stock_count = $newStock;
            $product->average_purchase_price = $newAvg;
            $product->should_track = 1;
            $product->save();

            if ($product->parent_id) {
                $parentProductIds[] = $product->parent_id;
            }
        }

        $this->recalculateParentProducts($parentProductIds);
    }

    /**
     * Revert stock changes when deleting a purchase
     */
    public function revertStockChanges(Purchase $purchase): void
    {
        // Get all product purchases with their original data
        $productPurchases = $purchase->productPurchases()->with('product')->get();

        foreach ($productPurchases as $productPurchase) {
            $product = $productPurchase->product;
            $currentStock = $product->stock_count;
            $currentAvg = $product->average_purchase_price ?? 0;
            $purchaseQty = $productPurchase->quantity;
            $purchasePrice = $productPurchase->price;

            // Revert stock
            $newStock = $currentStock - $purchaseQty;
            $totalCostBeforePurchase = ($currentStock * $currentAvg) - ($purchaseQty * $purchasePrice);
            $newAvg = $newStock > 0 ? $totalCostBeforePurchase / $newStock : null;

            Log::info("Reverting stock changes for product {$product->id}:", [
                'product' => $product->name,
                'current_stock' => $currentStock,
                'current_avg' => $currentAvg,
                'purchase_qty' => $purchaseQty,
                'purchase_price' => $purchasePrice,
                'new_stock' => $newStock,
                'new_avg' => $newAvg,
            ]);

            $product->stock_count = max(0, $newStock);
            $product->average_purchase_price = $newAvg;
            $product->save();
        }

        // Recalculate parent products
        $parentProductIds = $productPurchases->pluck('product.parent_id')->filter()->unique()->toArray();
        $this->recalculateParentProducts($parentProductIds);
    }

    /**
     * Update stock changes when editing a purchase
     * This method properly handles the difference between old and new purchase data
     */
    public function updateStockChanges(Purchase $purchase, array $oldItems, array $newItems): void
    {
        Log::info("Updating stock changes for purchase {$purchase->id}:", [
            'old_items' => $oldItems,
            'new_items' => $newItems,
        ]);

        // Create maps for easy lookup
        $oldItemsMap = collect($oldItems)->keyBy('product_id');
        $newItemsMap = collect($newItems)->keyBy('product_id');

        // Get all affected product IDs
        $allProductIds = array_unique(array_merge(
            array_keys($oldItemsMap->toArray()),
            array_keys($newItemsMap->toArray())
        ));

        // Load all affected products
        $products = Product::whereIn('id', $allProductIds)->get()->keyBy('id');
        $parentProductIds = [];

        foreach ($allProductIds as $productId) {
            $product = $products->get($productId);
            if (! $product) {
                continue;
            }

            $oldItem = $oldItemsMap->get($productId);
            $newItem = $newItemsMap->get($productId);

            $currentStock = $product->stock_count;
            $currentAvg = $product->average_purchase_price ?? 0;

            Log::info("Processing product {$productId} ({$product->name}):", [
                'current_stock' => $currentStock,
                'current_avg' => $currentAvg,
                'old_item' => $oldItem,
                'new_item' => $newItem,
            ]);

            if ($oldItem && $newItem) {
                // Product exists in both old and new - calculate the difference
                $oldQty = $oldItem['quantity'];
                $oldPrice = $oldItem['price'];
                $newQty = $newItem['quantity'];
                $newPrice = $newItem['price'];

                // First, revert the old purchase
                $stockAfterRevert = $currentStock - $oldQty;
                $totalCostAfterRevert = ($currentStock * $currentAvg) - ($oldQty * $oldPrice);
                $avgAfterRevert = $stockAfterRevert > 0 ? $totalCostAfterRevert / $stockAfterRevert : 0;

                // Then apply the new purchase
                $finalStock = $stockAfterRevert + $newQty;
                $finalTotalCost = ($stockAfterRevert * $avgAfterRevert) + ($newQty * $newPrice);
                $finalAvg = $finalStock > 0 ? $finalTotalCost / $finalStock : $newPrice;

                Log::info("Product {$productId} - Updated quantities/prices:", [
                    'old_qty' => $oldQty, 'old_price' => $oldPrice,
                    'new_qty' => $newQty, 'new_price' => $newPrice,
                    'stock_after_revert' => $stockAfterRevert,
                    'avg_after_revert' => $avgAfterRevert,
                    'final_stock' => $finalStock,
                    'final_avg' => $finalAvg,
                ]);

                $product->stock_count = $finalStock;
                $product->average_purchase_price = $finalAvg;

            } elseif ($oldItem && ! $newItem) {
                // Product was removed - just revert the old purchase
                $oldQty = $oldItem['quantity'];
                $oldPrice = $oldItem['price'];

                $newStock = $currentStock - $oldQty;
                $totalCostBeforePurchase = ($currentStock * $currentAvg) - ($oldQty * $oldPrice);
                $newAvg = $newStock > 0 ? $totalCostBeforePurchase / $newStock : null;

                Log::info("Product {$productId} - Removed from purchase:", [
                    'old_qty' => $oldQty, 'old_price' => $oldPrice,
                    'new_stock' => $newStock, 'new_avg' => $newAvg,
                ]);

                $product->stock_count = max(0, $newStock);
                $product->average_purchase_price = $newAvg;

            } elseif (! $oldItem && $newItem) {
                // Product was added - just apply the new purchase
                $newQty = $newItem['quantity'];
                $newPrice = $newItem['price'];

                $newStock = $currentStock + $newQty;
                $newTotalCost = ($currentStock * $currentAvg) + ($newQty * $newPrice);
                $newAvg = $newStock > 0 ? $newTotalCost / $newStock : $newPrice;

                Log::info("Product {$productId} - Added to purchase:", [
                    'new_qty' => $newQty, 'new_price' => $newPrice,
                    'new_stock' => $newStock, 'new_avg' => $newAvg,
                ]);

                $product->stock_count = $newStock;
                $product->average_purchase_price = $newAvg;
            }

            $product->should_track = 1;
            $product->save();

            if ($product->parent_id) {
                $parentProductIds[] = $product->parent_id;
            }
        }

        $this->recalculateParentProducts($parentProductIds);
    }

    /**
     * Recalculate average purchase price for parent products based on their variants
     */
    private function recalculateParentProducts(array $parentIds): void
    {
        if (empty($parentIds)) {
            return;
        }

        $variants = Product::whereIn('parent_id', $parentIds)
            ->get(['id', 'parent_id', 'stock_count', 'average_purchase_price']);

        $variantsByParent = $variants->groupBy('parent_id');
        $parentProducts = Product::whereIn('id', $parentIds)->get();

        foreach ($parentProducts as $parent) {
            $totalStock = 0;
            $totalCost = 0;

            foreach ($variantsByParent[$parent->id] ?? [] as $variant) {
                $variantStock = $variant->stock_count;
                $variantAvg = $variant->average_purchase_price ?? 0;
                $totalStock += $variantStock;
                $totalCost += $variantStock * $variantAvg;
            }

            $newAvg = $totalStock > 0 ? $totalCost / $totalStock : null;

            Log::info("Recalculating parent product {$parent->id} ({$parent->name}):", [
                'total_stock' => $totalStock,
                'total_cost' => $totalCost,
                'new_avg' => $newAvg,
            ]);

            if ($totalStock > 0) {
                $parent->should_track = 1;
                $parent->average_purchase_price = $newAvg;
            } else {
                $parent->average_purchase_price = null;
            }

            $parent->save();
        }
    }
}
