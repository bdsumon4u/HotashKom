<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Jobs\SyncProductStockWithResellers;
use App\Models\Account;
use App\Models\Product;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CourierReturnCreate extends Component
{
    public string $search = '';

    public array $products = [];

    public array $items = [];

    public string $return_date = '';

    public string $courier_name = '';

    public string $tracking_code = '';

    public string $notes = '';

    public float|int|string|null $return_charge = 0;

    public ?int $paid_from_account_id = null;

    public ?string $override_total_cost = null;

    public int $inputKey = 0;

    protected array $rules = [
        'return_date' => 'required|date',
        'courier_name' => 'nullable|string|max:255',
        'tracking_code' => 'nullable|string|max:255',
        'notes' => 'nullable|string|max:1000',
        'return_charge' => 'nullable|numeric|min:0',
        'paid_from_account_id' => 'nullable|exists:accounts,id',
        'override_total_cost' => 'nullable|numeric|min:0',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.price' => 'required|numeric|min:0',
    ];

    protected array $messages = [
        'items.required' => 'Please add at least one returned product.',
        'items.min' => 'Please add at least one returned product.',
        'items.*.quantity.required' => 'Quantity is required.',
        'items.*.quantity.min' => 'Quantity must be at least 1.',
        'items.*.price.required' => 'Purchase cost is required.',
    ];

    public function mount(): void
    {
        $this->return_date = now()->toDateString();

        $defaultAccount = Account::where('type', Account::TYPE_ASSET)
            ->where('is_active', true)
            ->where(function ($q): void {
                $q->where('code', '1001') // Cash
                    ->orWhere('code', '1002') // Bank
                    ->orWhere('code', '1003'); // bKash
            })
            ->first();

        if ($defaultAccount) {
            $this->paid_from_account_id = $defaultAccount->id;
        }
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

        $avgCost = $product->average_purchase_price ?? null;
        if (($avgCost === null || (float) $avgCost <= 0) && $product->parent) {
            $avgCost = $product->parent->average_purchase_price ?? null;
        }

        $price = ($avgCost !== null && (float) $avgCost > 0) ? round((float) $avgCost, 2) : 0.0;

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

    public function getCalculatedPurchaseCostProperty(): float
    {
        return (float) collect($this->items)->sum(
            fn ($item): float => (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0)
        );
    }

    public function getFinalPurchaseCostProperty(): float
    {
        if ($this->override_total_cost !== null && trim((string) $this->override_total_cost) !== '') {
            return (float) $this->override_total_cost;
        }

        return $this->calculated_purchase_cost;
    }

    public function getReturnChargeAmountProperty(): float
    {
        return max(0, (float) ($this->return_charge ?? 0));
    }

    public function save(AccountingService $accountingService)
    {
        $this->validate();

        if ($this->return_charge_amount > 0 && ! $this->paid_from_account_id) {
            $this->addError('paid_from_account_id', 'Please select the payment account for the return fee.');

            return null;
        }

        return DB::transaction(function () use ($accountingService) {
            $totalCost = $this->final_purchase_cost;
            $itemsSummaryList = [];

            // 1. Restock physical warehouse count for returned items
            foreach ($this->items as $item) {
                $product = Product::find($item['product_id']);
                if ($product && $product->should_track) {
                    $product->increment('stock_count', (int) $item['quantity']);

                    if (class_exists(SyncProductStockWithResellers::class)) {
                        dispatch(new SyncProductStockWithResellers($product));
                    }
                }

                $itemsSummaryList[] = $item['name'].' (x'.$item['quantity'].')';
            }

            $itemsSummary = implode(', ', $itemsSummaryList);

            // 2. Transfer value from In Courier (Asset) back to Inventory (Asset)
            $accountingService->recordCourierReturn([
                'entry_date' => $this->return_date,
                'courier_name' => $this->courier_name ?: null,
                'tracking_code' => $this->tracking_code ?: null,
                'notes' => $this->notes ?: null,
                'total_purchase_cost' => $totalCost,
                'return_charge' => $this->return_charge_amount,
                'paid_from_account_id' => $this->paid_from_account_id,
                'items_summary' => $itemsSummary,
            ]);

            session()->flash(
                'success',
                sprintf(
                    'Courier return recorded! Warehouse stock has been restored (+%d pcs) and ৳%s transferred from In Courier back to Inventory.',
                    $this->total_quantity,
                    number_format($totalCost, 2)
                )
            );

            return to_route('admin.reports.return');
        });
    }

    public function render()
    {
        $accounts = Account::where('type', Account::TYPE_ASSET)
            ->where('is_active', true)
            ->where('code', '!=', '1004') // Not inventory
            ->where('code', '!=', '1006') // Not in courier
            ->orderBy('name')
            ->get();

        return view('livewire.admin.courier-return-create', [
            'accounts' => $accounts,
            'totalQuantity' => $this->total_quantity,
            'calculatedPurchaseCost' => $this->calculated_purchase_cost,
            'finalPurchaseCost' => $this->final_purchase_cost,
            'returnChargeAmount' => $this->return_charge_amount,
        ]);
    }
}
