<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Account;
use App\Models\Product;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CourierPayoutCreate extends Component
{
    public string $search = '';

    public array $products = [];

    public array $items = [];

    public string $entry_date = '';

    public string $courier_name = '';

    public string $reference = '';

    public string $notes = '';

    public ?int $deposit_account_id = null;

    public float|int|string|null $courier_charge = 0;

    public ?string $override_total_sales = null;

    public ?string $override_purchase_cost = null;

    public int $inputKey = 0;

    protected array $rules = [
        'entry_date' => 'required|date',
        'deposit_account_id' => 'required|exists:accounts,id',
        'courier_name' => 'nullable|string|max:255',
        'reference' => 'nullable|string|max:255',
        'notes' => 'nullable|string|max:1000',
        'courier_charge' => 'nullable|numeric|min:0',
        'override_total_sales' => 'nullable|numeric|min:0',
        'override_purchase_cost' => 'nullable|numeric|min:0',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.unit_purchase_cost' => 'required|numeric|min:0',
        'items.*.unit_retail_price' => 'required|numeric|min:0',
    ];

    public function mount(): void
    {
        $this->entry_date = now()->toDateString();

        $defaultAccount = Account::where('type', Account::TYPE_ASSET)
            ->where('is_active', true)
            ->where(function ($q): void {
                $q->where('code', '1002') // Bank
                    ->orWhere('code', '1001') // Cash
                    ->orWhere('code', '1003'); // bKash
            })
            ->first();

        if ($defaultAccount) {
            $this->deposit_account_id = $defaultAccount->id;
        } else {
            $firstAsset = Account::where('type', Account::TYPE_ASSET)->where('is_active', true)->first();
            if ($firstAsset) {
                $this->deposit_account_id = $firstAsset->id;
            }
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

        $cost = ($avgCost !== null && (float) $avgCost > 0) ? round((float) $avgCost, 2) : 0;
        $retail = (float) ($product->selling_price ?? ($product->parent?->selling_price ?? 0));

        $displayName = $product->parent
            ? ($product->parent->name.' ['.$product->name.']')
            : $product->name;

        $this->items[] = [
            'product_id' => $product->id,
            'name' => $displayName,
            'sku' => $product->sku ?? '',
            'options' => $product->options ? $product->options->pluck('name')->toArray() : [],
            'quantity' => 1,
            'unit_purchase_cost' => $cost,
            'unit_retail_price' => $retail,
        ];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function getCalculatedPurchaseCostProperty(): float
    {
        return (float) collect($this->items)->sum(
            fn ($item): float => (float) ($item['unit_purchase_cost'] ?? 0) * (int) ($item['quantity'] ?? 0)
        );
    }

    public function getCalculatedTotalSalesProperty(): float
    {
        return (float) collect($this->items)->sum(
            fn ($item): float => (float) ($item['unit_retail_price'] ?? 0) * (int) ($item['quantity'] ?? 0)
        );
    }

    public function getFinalPurchaseCostProperty(): float
    {
        if ($this->override_purchase_cost !== null && trim((string) $this->override_purchase_cost) !== '') {
            return (float) $this->override_purchase_cost;
        }

        return $this->calculated_purchase_cost;
    }

    public function getFinalGrossSalesProperty(): float
    {
        if ($this->override_total_sales !== null && trim((string) $this->override_total_sales) !== '') {
            return (float) $this->override_total_sales;
        }

        return $this->calculated_total_sales;
    }

    public function getCourierChargeAmountProperty(): float
    {
        return max(0, (float) ($this->courier_charge ?? 0));
    }

    public function getNetReceivedAmountProperty(): float
    {
        return max(0, $this->final_gross_sales - $this->courier_charge_amount);
    }

    public function save(AccountingService $accountingService)
    {
        $this->validate();

        $grossSales = $this->final_gross_sales;
        $purchaseCost = $this->final_purchase_cost;

        if ($grossSales <= 0 && $purchaseCost <= 0) {
            $this->addError('override_total_sales', 'Total sales or delivered products must be greater than zero.');

            return null;
        }

        return DB::transaction(function () use ($accountingService, $grossSales, $purchaseCost) {
            $itemsSummaryList = [];
            foreach ($this->items as $item) {
                $itemsSummaryList[] = $item['name'].' (x'.$item['quantity'].')';
            }
            $itemsSummary = implode(', ', $itemsSummaryList);

            $accountingService->recordCourierPayoutSettlement([
                'entry_date' => $this->entry_date,
                'deposit_account_id' => (int) $this->deposit_account_id,
                'gross_sales' => $grossSales,
                'total_purchase_cost' => $purchaseCost,
                'courier_charge' => $this->courier_charge_amount,
                'courier_name' => $this->courier_name ?: null,
                'reference' => $this->reference ?: null,
                'description' => $this->notes ?: null,
                'items_summary' => $itemsSummary ?: null,
            ]);

            session()->flash(
                'success',
                sprintf(
                    'Courier Payout Settlement recorded! ৳%s deposited, ৳%s recognized as revenue, and ৳%s transferred from In Courier to COGS expense.',
                    number_format($this->net_received_amount, 2),
                    number_format($grossSales, 2),
                    number_format($purchaseCost, 2)
                )
            );

            return to_route('admin.accounting.transactions.index');
        });
    }

    public function render()
    {
        $accounts = Account::where('type', Account::TYPE_ASSET)
            ->where('is_active', true)
            ->where('code', '!=', '1004') // Not warehouse inventory
            ->where('code', '!=', '1006') // Not in courier
            ->orderBy('name')
            ->get();

        return view('livewire.admin.courier-payout-create', [
            'accounts' => $accounts,
            'calculatedPurchaseCost' => $this->calculated_purchase_cost,
            'calculatedTotalSales' => $this->calculated_total_sales,
            'finalPurchaseCost' => $this->final_purchase_cost,
            'finalGrossSales' => $this->final_gross_sales,
            'courierChargeAmount' => $this->courier_charge_amount,
            'netReceivedAmount' => $this->net_received_amount,
        ]);
    }
}
