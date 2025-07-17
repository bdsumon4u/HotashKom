<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PurchaseCreate extends Component
{
    public $search = '';

    public $products = [];

    public $selectedProduct = null;

    public $selectedVariant = null;

    public $items = [];

    public $purchase_date;

    public $supplier_name;

    public $supplier_phone;

    public $notes;

    public $invoice_number;

    public $searchKey = 0;

    public $inputKey = 0;

    protected $rules = [
        'purchase_date' => 'required|date',
        'supplier_name' => 'nullable|string|max:255',
        'supplier_phone' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
        'invoice_number' => 'nullable|string|max:255',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.price' => 'required|numeric|min:0.01',
        'items.*.quantity' => 'required|integer|min:1',
    ];

    public function mount()
    {
        $this->purchase_date = now()->toDateString();
    }

    public function updatedSearch($value)
    {
        $this->products = [];
        $this->selectedProduct = null;
        $this->selectedVariant = null;
        if (strlen($value) > 2) {
            $this->products = Product::with(['variations.options', 'options', 'brand'])
                ->whereNull('parent_id')
                ->whereIsActive(1)
                ->where(function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%")
                        ->orWhere('sku', 'like', "%{$value}%")
                        ->orWhereHas('variations', function ($q2) use ($value) {
                            $q2->where('name', 'like', "%{$value}%")
                                ->orWhere('sku', 'like', "%{$value}%");
                        });
                })
                ->take(8)
                ->get();
        }
    }

    public function selectProduct($productId)
    {
        $product = Product::with(['variations.options', 'options', 'brand'])->find($productId);
        $this->selectedProduct = $product;
        $this->selectedVariant = null;
        $this->addItem($product);
        $this->search = '';
        $this->inputKey++;
    }

    public function selectVariant($variantId)
    {
        $variant = Product::with(['options', 'brand', 'parent'])->find($variantId);
        $this->selectedVariant = $variant;
        $this->selectedProduct = $variant->parent;
        $this->addItem($variant);
        $this->search = '';
        $this->inputKey++;
    }

    public function addItem($product)
    {
        // Prevent duplicate
        foreach ($this->items as $item) {
            if ($item['product_id'] == $product->id) {
                return;
            }
        }
        // Get last purchase price
        $lastPurchase = ProductPurchase::where('product_id', $product->id)
            ->orderByDesc('id')
            ->first();
        $defaultPrice = $lastPurchase ? $lastPurchase->price : null;
        $this->items[] = [
            'product_id' => $product->id,
            'name' => $product->parent ? ($product->parent->name.' ['.$product->name.']') : $product->name,
            'sku' => $product->sku,
            'options' => $product->options->pluck('name')->toArray(),
            'price' => $defaultPrice,
            'quantity' => 1,
            'selling_price' => $product->selling_price,
            'stock_count' => $product->stock_count,
        ];
    }

    public function updateItem($index, $field, $value)
    {
        if (isset($this->items[$index])) {
            $this->items[$index][$field] = $value;
        }
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function getTotalProperty()
    {
        return collect($this->items)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function save()
    {
        $this->validate();
        $adminId = Auth::guard('admin')->id();
        $totalAmount = collect($this->items)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
        $purchase = Purchase::create([
            'admin_id' => $adminId,
            'purchase_date' => $this->purchase_date,
            'supplier_name' => $this->supplier_name,
            'supplier_phone' => $this->supplier_phone,
            'notes' => $this->notes,
            'invoice_number' => $this->invoice_number,
            'total_amount' => $totalAmount,
        ]);
        $attachData = [];
        foreach ($this->items as $item) {
            $attachData[$item['product_id']] = [
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity'],
            ];
        }
        $purchase->products()->attach($attachData);
        // Optimized: Weighted average and stock update using attached products
        $parentProductIds = [];
        foreach ($purchase->products as $product) {
            $currentStock = $product->stock_count;
            $currentAvg = $product->average_purchase_price ?? 0;
            $purchaseQty = $product->pivot->quantity;
            $purchasePrice = $product->pivot->price;
            $newStock = $currentStock + $purchaseQty;
            $newTotalCost = ($currentStock * $currentAvg) + ($purchaseQty * $purchasePrice);
            $newAvg = $newStock > 0 ? $newTotalCost / $newStock : $purchasePrice;
            $product->stock_count = $newStock;
            $product->average_purchase_price = $newAvg;
            $product->save();
            if ($product->parent_id) {
                $parentProductIds[] = $product->parent_id;
            }
        }
        // Recalculate average_purchase_price for parent products based on their variants
        $parentProductIds = array_unique($parentProductIds);
        if (! empty($parentProductIds)) {
            // Load all variants for all affected parents in one query
            $variants = Product::whereIn('parent_id', $parentProductIds)
                ->get(['id', 'parent_id', 'stock_count', 'average_purchase_price']);

            // Group variants by parent_id
            $variantsByParent = $variants->groupBy('parent_id');

            // Load all parent products in one query
            $parentProducts = Product::whereIn('id', $parentProductIds)->get();

            foreach ($parentProducts as $parent) {
                $totalStock = 0;
                $totalCost = 0;
                foreach ($variantsByParent[$parent->id] ?? [] as $variant) {
                    $variantStock = $variant->stock_count;
                    $variantAvg = $variant->average_purchase_price ?? 0;
                    $totalStock += $variantStock;
                    $totalCost += $variantStock * $variantAvg;
                }
                if ($totalStock > 0) {
                    $parent->average_purchase_price = $totalCost / $totalStock;
                } else {
                    $parent->average_purchase_price = null;
                }
                $parent->save();
            }
        }
        session()->flash('success', 'Purchase record created successfully!');

        return redirect()->route('admin.purchases.index');
    }

    public function render()
    {
        return view('livewire.admin.purchase-create', [
            'products' => $this->products,
            'items' => $this->items,
            'total' => $this->total,
        ]);
    }
}
