<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Jobs\SyncProductStockWithResellers;
use App\Models\Product;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ShipmentCreate extends Component
{
    public string $search = '';

    public array $products = [];

    public array $items = [];

    public string $shipment_date = '';

    public string $courier_name = '';

    public string $tracking_code = '';

    public string $notes = '';

    public int $inputKey = 0;

    protected array $rules = [
        'shipment_date' => 'required|date',
        'courier_name' => 'required|string|max:255',
        'tracking_code' => 'nullable|string|max:255',
        'notes' => 'nullable|string|max:1000',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.price' => 'required|numeric|min:0.01',
        'items.*.quantity' => 'required|integer|min:1',
    ];

    protected array $messages = [
        'items.required' => 'Please add at least one product to ship.',
        'items.min' => 'Please add at least one product to ship.',
        'items.*.price.required' => 'Purchase cost is required for each item.',
        'items.*.price.min' => 'Purchase cost must be greater than zero.',
        'items.*.quantity.required' => 'Quantity is required.',
        'items.*.quantity.min' => 'Quantity must be at least 1.',
    ];

    public function mount(): void
    {
        $this->shipment_date = now()->toDateString();
    }

    public function updatedSearch(string $value): void
    {
        $this->products = [];

        if (strlen(trim($value)) > 2) {
            $this->products = Product::with(['variations.options', 'options', 'brand', 'parent'])
                ->whereNull('parent_id')
                ->where('is_active', 1)
                ->where(function ($q) use ($value): void {
                    $q->where('name', 'like', "%{$value}%")
                        ->orWhere('sku', 'like', "%{$value}%")
                        ->orWhereHas('variations', function ($q2) use ($value): void {
                            $q2->where('name', 'like', "%{$value}%")
                                ->orWhere('sku', 'like', "%{$value}%");
                        });
                })
                ->take(8)
                ->get()
                ->all();
        }
    }

    public function selectProduct(int $productId): void
    {
        $product = Product::with(['variations.options', 'options', 'brand'])->find($productId);
        if ($product) {
            $this->addItem($product);
        }
        $this->search = '';
        $this->products = [];
        $this->inputKey++;
    }

    public function selectVariant(int $variantId): void
    {
        $variant = Product::with(['options', 'brand', 'parent'])->find($variantId);
        if ($variant) {
            $this->addItem($variant);
        }
        $this->search = '';
        $this->products = [];
        $this->inputKey++;
    }

    public function addItem(Product $product): void
    {
        foreach ($this->items as $item) {
            if ($item['product_id'] === $product->id) {
                return;
            }
        }

        // Determine default purchase cost
        $avgCost = $product->average_purchase_price ?? null;
        if (($avgCost === null || (float) $avgCost <= 0) && $product->parent) {
            $avgCost = $product->parent->average_purchase_price ?? null;
        }

        $price = ($avgCost !== null && (float) $avgCost > 0) ? round((float) $avgCost, 2) : null;

        $displayName = $product->parent
            ? ($product->parent->name.' ['.$product->name.']')
            : $product->name;

        $this->items[] = [
            'product_id' => $product->id,
            'name' => $displayName,
            'sku' => $product->sku ?? '',
            'options' => $product->options ? $product->options->pluck('name')->toArray() : [],
            'price' => $price,
            'quantity' => 1,
            'stock_count' => (int) ($product->stock_count ?? 0),
            'selling_price' => (float) ($product->selling_price ?? 0),
        ];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function getTotalQuantityProperty(): int
    {
        return (int) collect($this->items)->sum(fn ($item): int => (int) ($item['quantity'] ?? 0));
    }

    public function getTotalPurchaseCostProperty(): float
    {
        return (float) collect($this->items)->sum(
            fn ($item): float => (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0)
        );
    }

    public function save(AccountingService $accountingService)
    {
        $this->validate();

        return DB::transaction(function () use ($accountingService) {
            $totalCost = $this->total_purchase_cost;
            $itemsSummaryList = [];

            // 1. Decrement warehouse stock for each shipped product
            foreach ($this->items as $item) {
                $product = Product::find($item['product_id']);
                if ($product && $product->should_track) {
                    $product->decrement('stock_count', (int) $item['quantity']);

                    if (class_exists(SyncProductStockWithResellers::class)) {
                        dispatch(new SyncProductStockWithResellers($product));
                    }
                }

                $itemsSummaryList[] = $item['name'].' (x'.$item['quantity'].')';
            }

            $itemsSummary = implode(', ', $itemsSummaryList);

            // 2. Transfer value from Inventory (Asset) to In Courier (Asset) via double-entry Journal Entry
            $accountingService->recordShipmentToCourier([
                'entry_date' => $this->shipment_date,
                'courier_name' => $this->courier_name,
                'tracking_code' => $this->tracking_code ?: null,
                'notes' => $this->notes ?: null,
                'total_purchase_cost' => $totalCost,
                'items_summary' => $itemsSummary,
            ]);

            session()->flash(
                'success',
                sprintf(
                    'Shipment successfully recorded! Warehouse stock has been reduced and ৳%s transferred from Inventory to In Courier account.',
                    number_format($totalCost, 2)
                )
            );

            return to_route('admin.accounting.transactions.index');
        });
    }

    public function render()
    {
        return view('livewire.admin.shipment-create', [
            'totalQuantity' => $this->total_quantity,
            'totalPurchaseCost' => $this->total_purchase_cost,
        ]);
    }
}
