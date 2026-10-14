<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Jobs\SyncProductStockWithResellers;
use App\Models\Account;
use App\Models\Product;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StockAdjustmentCreate extends Component
{
    public string $search = '';

    public array $products = [];

    public array $items = [];

    public string $entry_date = '';

    public string $reason = 'damage';

    public ?int $debit_account_id = null;

    public string $notes = '';

    public int $inputKey = 0;

    protected array $rules = [
        'entry_date' => 'required|date',
        'reason' => 'required|string|in:damage,gift,drawing,other',
        'debit_account_id' => 'required|exists:accounts,id',
        'notes' => 'nullable|string|max:1000',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.price' => 'required|numeric|min:0.01',
        'items.*.quantity' => 'required|integer|min:1',
    ];

    protected array $messages = [
        'items.required' => 'Please add at least one product to adjust/write off.',
        'items.min' => 'Please add at least one product to adjust/write off.',
        'items.*.price.required' => 'Unit purchase cost is required.',
        'items.*.price.min' => 'Unit purchase cost must be greater than zero.',
        'items.*.quantity.required' => 'Quantity is required.',
        'items.*.quantity.min' => 'Quantity must be at least 1.',
        'debit_account_id.required' => 'Please select the target expense or drawings account.',
    ];

    public function mount(AccountingService $accountingService): void
    {
        $this->entry_date = now()->toDateString();
        $this->reason = 'damage';
        $this->debit_account_id = $accountingService->getDamagedGoodsExpenseAccount()->id;
    }

    public function updatedReason(string $value, AccountingService $accountingService): void
    {
        if ($value === 'damage') {
            $this->debit_account_id = $accountingService->getDamagedGoodsExpenseAccount()->id;
        } elseif ($value === 'gift') {
            $this->debit_account_id = $accountingService->getMarketingExpenseAccount()->id;
        } elseif ($value === 'drawing') {
            $this->debit_account_id = $accountingService->getOwnersDrawingsAccount()->id;
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

            // 1. Decrement warehouse stock for each item written off
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

            // 2. Record double-entry Journal Entry: Debit Expense/Equity, Credit Inventory (#1004)
            $accountingService->recordStockAdjustment([
                'entry_date' => $this->entry_date,
                'reason' => $this->reason,
                'debit_account_id' => (int) $this->debit_account_id,
                'total_purchase_cost' => $totalCost,
                'notes' => $this->notes ?: null,
                'items_summary' => $itemsSummary,
            ]);

            $debitAccount = Account::find($this->debit_account_id);

            session()->flash(
                'success',
                sprintf(
                    'Stock adjustment recorded! %d items deducted from warehouse stock and ৳%s transferred from Inventory (#1004) to %s.',
                    $this->total_quantity,
                    number_format($totalCost, 2),
                    $debitAccount?->name ?? 'selected expense'
                )
            );

            return to_route('admin.reports.stock');
        });
    }

    public function render()
    {
        $accounts = Account::whereIn('type', [Account::TYPE_EXPENSE, Account::TYPE_EQUITY])
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('livewire.admin.stock-adjustment-create', [
            'accounts' => $accounts,
            'totalQuantity' => $this->total_quantity,
            'totalPurchaseCost' => $this->total_purchase_cost,
        ]);
    }
}
