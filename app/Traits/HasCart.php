<?php

namespace App\Traits;

use App\Http\Resources\ProductResource;
use App\Models\Product;

trait HasCart
{
    public function addToKart(Product $product, int $quantity = 1, string $instance = 'default', $retailPrice = null)
    {
        session(['kart' => $instance]);
        if ($instance == 'landing') {
            cart()->destroy();
        }

        $fraudQuantity = setting('fraud')->max_qty_per_product ?? 3;
        $maxQuantity = $product->should_track ? min($product->stock_count, $fraudQuantity) : $fraudQuantity;
        $quantity = min($quantity, $maxQuantity);

        $productData = (new ProductResource($product))->toCartItem($quantity);
        $productData['max'] = $maxQuantity;
        $productData['retail_price'] = $retailPrice;
        $productData['shipping_inside'] = $product->shipping_inside;
        $productData['shipping_outside'] = $product->shipping_outside;

        cart()->instance($instance)->add(
            $product->id,
            $product->var_name,
            $quantity,
            $productData['price'],
            $productData
        );

        storeOrUpdateCart();

        $this->dispatch('dataLayer', [
            'event' => 'add_to_cart',
            'ecommerce' => [
                'currency' => 'BDT',
                'value' => $retailPrice,
                'items' => [
                    [
                        'item_id' => $product->id,
                        'item_name' => $product->var_name,
                        'item_category' => $product->category,
                        'price' => $retailPrice,
                        'quantity' => $quantity,
                    ],
                ],
            ],
        ]);

        $this->dispatch('cartUpdated');
        $this->dispatch('notify', ['message' => 'Product added to cart']);

        if ($instance != 'default' && $instance != 'landing') {
            return redirect()->route('checkout');
        }
    }
}
