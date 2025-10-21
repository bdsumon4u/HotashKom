<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\Purchase;
use App\Services\PurchaseStockService;
use Livewire\Component;

class PurchaseEdit extends Component
{
    public Purchase $purchase;

    public $search = '';

    public $products = [];

    public $selectedProduct;

    public $selectedVariant;

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

    public function mount(Purchase $purchase): void
    {
        $this->purchase = $purchase;
        $this->purchase_date = $purchase->purchase_date->toDateString();
        $this->supplier_name = $purchase->supplier_name;
        $this->supplier_phone = $purchase->supplier_phone;
        $this->notes = $purchase->notes;
        $this->invoice_number = $purchase->invoice_number;

        // Load existing items
        foreach ($purchase->productPurchases as $productPurchase) {
            $product = $productPurchase->product;
            $this->items[] = [
                'product_id' => $product->id,
                'name' => $product->parent ? ($product->parent->name.' ['.$product->name.']') : $product->name,
                'sku' => $product->sku,
                'options' => $product->options->pluck('name')->toArray(),
                'price' => $productPurchase->price,
                'quantity' => $productPurchase->quantity,
                'selling_price' => $product->selling_price,
                'stock_count' => $product->stock_count,
            ];
        }
    }

    public function updatedSearch($value): void
    {
        $this->products = [];
        $this->selectedProduct = null;
        $this->selectedVariant = null;
        if (strlen((string) $value) > 2) {
            $this->products = Product::with(['variations.options', 'options', 'brand'])
                ->whereNull('parent_id')
                ->whereIsActive(1)
                ->where(function ($q) use ($value): void {
                    $q->where('name', 'like', "%{$value}%")
                        ->orWhere('sku', 'like', "%{$value}%")
                        ->orWhereHas('variations', function ($q2) use ($value): void {
                            $q2->where('name', 'like', "%{$value}%")
                                ->orWhere('sku', 'like', "%{$value}%");
                        });
                })
                ->take(8)
                ->get();
        }
    }

    public function selectProduct($productId): void
    {
        $product = Product::with(['variations.options', 'options', 'brand'])->find($productId);
        $this->selectedProduct = $product;
        $this->selectedVariant = null;
        $this->addItem($product);
        $this->search = '';
        $this->inputKey++;
    }

    public function selectVariant($variantId): void
    {
        $variant = Product::with(['options', 'brand', 'parent'])->find($variantId);
        $this->selectedVariant = $variant;
        $this->selectedProduct = $variant->parent;
        $this->addItem($variant);
        $this->search = '';
        $this->inputKey++;
    }

    public function addItem($product): void
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

    public function updateItem($index, $field, $value): void
    {
        if (isset($this->items[$index])) {
            $this->items[$index][$field] = $value;
        }
    }

    public function removeItem($index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function getTotalProperty()
    {
        return collect($this->items)->sum(fn ($item): float => (float) ($item['price'] ?? 0) * (float) ($item['quantity'] ?? 0));
    }

    public function save()
    {
        $this->validate();

        // Store old items for comparison (before any changes)
        $oldItems = $this->purchase->productPurchases->map(fn ($pp): array => [
            'product_id' => $pp->product_id,
            'price' => (float) $pp->price,
            'quantity' => (int) $pp->quantity,
        ])->toArray();

        // Format new items for comparison
        $newItems = collect($this->items)->map(fn ($item): array => [
            'product_id' => (int) $item['product_id'],
            'price' => (float) ($item['price'] ?? 0),
            'quantity' => (int) ($item['quantity'] ?? 0),
        ])->all();

        // Update purchase record
        $this->purchase->update([
            'purchase_date' => $this->purchase_date,
            'supplier_name' => $this->supplier_name,
            'supplier_phone' => $this->supplier_phone,
            'notes' => $this->notes,
            'invoice_number' => $this->invoice_number,
            'total_amount' => $this->total,
        ]);

        // Remove old product associations
        $this->purchase->products()->detach();

        // Add new product associations
        $attachData = [];
        foreach ($this->items as $item) {
            $attachData[$item['product_id']] = [
                'price' => $item['price'] ?? 0,
                'quantity' => $item['quantity'] ?? 0,
                'subtotal' => (float) ($item['price'] ?? 0) * (float) ($item['quantity'] ?? 0),
            ];
        }
        $this->purchase->products()->attach($attachData);

        // Use service to update stock changes with proper old and new data
        $stockService = new PurchaseStockService;
        $stockService->updateStockChanges($this->purchase, $oldItems, $newItems);

        session()->flash('success', 'Purchase record updated successfully!');

        return to_route('admin.purchases.index');
    }

    public function render()
    {
        return view('livewire.admin.purchase-edit', [
            'products' => $this->products,
            'items' => $this->items,
            'total' => $this->total,
        ]);
    }
}
