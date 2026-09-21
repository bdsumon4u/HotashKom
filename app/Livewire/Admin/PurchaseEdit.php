<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Account;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\AccountingService;
use App\Services\PurchaseStockService;
use Illuminate\Support\Facades\DB;
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

    public $supplier_id = null;

    public $supplier_name;

    public $supplier_phone;

    public $paid_amount = 0;

    public $payment_account_id = null;

    public $notes;

    public $invoice_number;

    public $searchKey = 0;

    public $inputKey = 0;

    protected $rules = [
        'purchase_date' => 'required|date',
        'supplier_id' => 'nullable|exists:suppliers,id',
        'supplier_name' => 'nullable|string|max:255',
        'supplier_phone' => 'nullable|string|max:255',
        'paid_amount' => 'nullable|numeric|min:0',
        'payment_account_id' => 'nullable|exists:accounts,id',
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
        $this->supplier_id = $purchase->supplier_id;
        $this->supplier_name = $purchase->supplier_name;
        $this->supplier_phone = $purchase->supplier_phone;
        $this->paid_amount = (float) $purchase->paid_amount;
        $this->notes = $purchase->notes;
        $this->invoice_number = $purchase->invoice_number;

        if (config('accounting.enabled', false)) {
            $existingPayment = $purchase->payments()->first();
            if ($existingPayment && $existingPayment->account_id) {
                $this->payment_account_id = $existingPayment->account_id;
            } else {
                $defaultAccount = Account::where('type', Account::TYPE_ASSET)->where('is_active', true)->first();
                if ($defaultAccount) {
                    $this->payment_account_id = $defaultAccount->id;
                }
            }
        }

        // Load existing items
        foreach ($purchase->productPurchases as $productPurchase) {
            $product = $productPurchase->product;
            if (! $product) {
                continue;
            }
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

    public function updatedSupplierId($value): void
    {
        if ($value && ($supplier = Supplier::find($value))) {
            $this->supplier_name = $supplier->name;
            $this->supplier_phone = $supplier->phone ?? '';
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

    public function getTotalProperty(): float
    {
        return (float) collect($this->items)->sum(fn ($item): float => (float) ($item['price'] ?? 0) * (float) ($item['quantity'] ?? 0));
    }

    public function getDueProperty(): float
    {
        $paid = (float) ($this->paid_amount ?? 0);

        return max(0, $this->total - $paid);
    }

    public function payFull(): void
    {
        $this->paid_amount = $this->total;
    }

    public function save()
    {
        $this->validate();

        return DB::transaction(function () {
            // Store old items for stock comparison
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

            $totalAmount = $this->total;
            $paidAmount = min($totalAmount, (float) ($this->paid_amount ?? 0));
            $dueAmount = max(0, $totalAmount - $paidAmount);
            $paymentStatus = $dueAmount <= 0 ? Purchase::STATUS_PAID : ($paidAmount > 0 ? Purchase::STATUS_PARTIAL : Purchase::STATUS_UNPAID);

            // Create or resolve supplier
            $supplierId = $this->supplier_id;
            if (! $supplierId && ! empty($this->supplier_name)) {
                $supplier = Supplier::firstOrCreate(
                    ['name' => $this->supplier_name],
                    [
                        'phone' => $this->supplier_phone,
                    ]
                );
                $supplierId = $supplier->id;
            }

            // Update purchase record
            $this->purchase->update([
                'supplier_id' => $supplierId,
                'purchase_date' => $this->purchase_date,
                'supplier_name' => $this->supplier_name,
                'supplier_phone' => $this->supplier_phone,
                'notes' => $this->notes,
                'invoice_number' => $this->invoice_number,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_status' => $paymentStatus,
            ]);

            // Remove old product associations
            $this->purchase->products()->detach();

            // Add new product associations
            $attachData = [];
            $tenantId = function_exists('tenant') && tenant() ? tenant()->getTenantKey() : null;
            foreach ($this->items as $item) {
                $attachData[$item['product_id']] = [
                    'tenant_id' => $tenantId,
                    'price' => $item['price'] ?? 0,
                    'quantity' => $item['quantity'] ?? 0,
                    'subtotal' => (float) ($item['price'] ?? 0) * (float) ($item['quantity'] ?? 0),
                ];
            }
            $this->purchase->products()->attach($attachData);

            if ($supplierId && ($supplierModel = Supplier::find($supplierId))) {
                $supplierModel->recalculateDue();
            }

            // Sync direct payment record
            $adminId = auth('admin')->id();
            $existingPayment = $this->purchase->payments()->first();
            if ($paidAmount > 0 && $supplierId && $this->payment_account_id) {
                if ($existingPayment) {
                    $existingPayment->update([
                        'supplier_id' => $supplierId,
                        'account_id' => $this->payment_account_id,
                        'amount' => $paidAmount,
                        'payment_date' => $this->purchase_date,
                        'reference' => $this->invoice_number ?? ('Purchase #'.$this->purchase->id),
                        'notes' => 'Updated payment for Purchase #'.$this->purchase->id,
                    ]);
                } else {
                    $this->purchase->payments()->create([
                        'supplier_id' => $supplierId,
                        'account_id' => $this->payment_account_id,
                        'amount' => $paidAmount,
                        'payment_date' => $this->purchase_date,
                        'reference' => $this->invoice_number ?? ('Purchase #'.$this->purchase->id),
                        'notes' => 'Initial payment for Purchase #'.$this->purchase->id,
                        'admin_id' => $adminId,
                    ]);
                }
            } elseif ($existingPayment && $paidAmount <= 0) {
                $existingPayment->delete();
            }

            if ($supplierId && ($supplierModel = Supplier::find($supplierId))) {
                $supplierModel->recalculateDue();
            }

            // Sync with accounting double-entry general ledger if module is enabled
            if (config('accounting.enabled', true)) {
                $accountingService = app(AccountingService::class);
                $inventoryAccount = Account::where('code', '1004')->orWhere('type', Account::TYPE_EXPENSE)->first();
                $payableAccount = Account::where('type', Account::TYPE_LIABILITY)->first();
                $paymentAccount = $this->payment_account_id ? Account::find($this->payment_account_id) : Account::where('type', Account::TYPE_ASSET)->first();

                if ($inventoryAccount) {
                    $journalItems = [
                        [
                            'account_id' => $inventoryAccount->id,
                            'debit' => $totalAmount,
                            'credit' => 0,
                            'notes' => 'Inventory Purchase (Purchase #'.$this->purchase->id.')',
                        ],
                    ];

                    if ($paidAmount > 0 && $paymentAccount) {
                        $journalItems[] = [
                            'account_id' => $paymentAccount->id,
                            'debit' => 0,
                            'credit' => $paidAmount,
                            'notes' => 'Paid from '.$paymentAccount->name,
                        ];
                    }

                    if ($dueAmount > 0 && $payableAccount) {
                        $journalItems[] = [
                            'account_id' => $payableAccount->id,
                            'debit' => 0,
                            'credit' => $dueAmount,
                            'notes' => 'Supplier Payable (Due for Purchase #'.$this->purchase->id.')',
                        ];
                    }

                    $accountingService->syncSourceJournalEntry(
                        Purchase::class,
                        $this->purchase->id,
                        [
                            'entry_date' => $this->purchase_date,
                            'reference' => $this->invoice_number ?? ('Purchase #'.$this->purchase->id),
                            'description' => 'Purchase #'.$this->purchase->id.($this->supplier_name ? ' from '.$this->supplier_name : ''),
                        ],
                        $journalItems
                    );
                }
            }

            // Use service to update stock changes with proper old and new data
            $stockService = new PurchaseStockService;
            $stockService->updateStockChanges($this->purchase, $oldItems, $newItems);

            session()->flash('success', 'Purchase record updated and ledger synchronized successfully!');

            return to_route('admin.purchases.index');
        });
    }

    public function render()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $accounts = config('accounting.enabled', false)
            ? Account::where('type', Account::TYPE_ASSET)->where('is_active', true)->orderBy('name')->get()
            : collect();

        return view('livewire.admin.purchase-edit', [
            'products' => $this->products,
            'items' => $this->items,
            'total' => $this->total,
            'due' => $this->due,
            'suppliers' => $suppliers,
            'accounts' => $accounts,
        ]);
    }
}
