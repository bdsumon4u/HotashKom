<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Supplier;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function __construct(public AccountingService $accountingService) {}

    public function index()
    {
        abort_unless(request()->user()->is('admin'), 403);

        $suppliers = Supplier::withCount('purchases')
            ->withSum('purchases', 'total_amount')
            ->withSum('purchases', 'paid_amount')
            ->latest('id')
            ->get();

        $accounts = Account::where('type', Account::TYPE_ASSET)->where('is_active', true)->orderBy('name')->get();

        return view('admin.suppliers.index', compact('suppliers', 'accounts'));
    }

    public function create()
    {
        abort_unless(request()->user()->is('admin'), 403);

        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        abort_unless(request()->user()->is('admin'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'opening_balance' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['opening_balance'] = (float) ($validated['opening_balance'] ?? 0);
        $validated['current_due'] = $validated['opening_balance'];
        $validated['is_active'] = $request->boolean('is_active', true);

        Supplier::create($validated);

        return to_route('admin.suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        abort_unless(request()->user()->is('admin'), 403);

        $supplier->load(['purchases.products', 'purchasePayments.account']);
        $supplier->recalculateDue();

        $accounts = Account::where('type', Account::TYPE_ASSET)->where('is_active', true)->orderBy('name')->get();

        return view('admin.suppliers.show', compact('supplier', 'accounts'));
    }

    public function edit(Supplier $supplier)
    {
        abort_unless(request()->user()->is('admin'), 403);

        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        abort_unless(request()->user()->is('admin'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'opening_balance' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['opening_balance'] = (float) ($validated['opening_balance'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active');

        $supplier->update($validated);
        $supplier->recalculateDue();

        return to_route('admin.suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function storePayment(Request $request, Supplier $supplier)
    {
        abort_unless(request()->user()->is('admin'), 403);

        $validated = $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'purchase_id' => ['nullable', 'exists:purchases,id'],
            'reference' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $supplier): void {
            $payment = $supplier->purchasePayments()->create([
                'account_id' => $validated['account_id'],
                'purchase_id' => $validated['purchase_id'] ?? null,
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'admin_id' => auth('admin')->id(),
            ]);

            // If paying against a specific purchase, adjust purchase's paid/due amount
            if (! empty($validated['purchase_id'])) {
                $purchase = $supplier->purchases()->find($validated['purchase_id']);
                if ($purchase) {
                    $purchase->paid_amount += (float) $validated['amount'];
                    $purchase->due_amount = max(0, (float) $purchase->total_amount - $purchase->paid_amount);
                    $purchase->payment_status = $purchase->due_amount <= 0 ? 'paid' : 'partial';
                    $purchase->save();
                }
            }

            $supplier->recalculateDue();

            // Sync with accounting if enabled: Find Accounts Payable account and Payment Account
            if (config('accounting.enabled', true) && ! empty($validated['account_id'])) {
                $payableAccount = Account::where('type', Account::TYPE_LIABILITY)->first();
                $paymentAccount = Account::find($validated['account_id']);

                if ($payableAccount && $paymentAccount) {
                    $this->accountingService->createJournalEntry(
                        [
                            'entry_date' => $validated['payment_date'],
                            'reference' => $validated['reference'] ?? ('Payment to '.$supplier->name),
                            'description' => 'Supplier Payment: '.$supplier->name.($validated['notes'] ? ' - '.$validated['notes'] : ''),
                            'source_type' => Supplier::class,
                            'source_id' => $supplier->id,
                        ],
                        [
                            [
                                'account_id' => $payableAccount->id,
                                'debit' => (float) $validated['amount'],
                                'credit' => 0,
                                'notes' => 'Supplier Due Settlement',
                            ],
                            [
                                'account_id' => $paymentAccount->id,
                                'debit' => 0,
                                'credit' => (float) $validated['amount'],
                                'notes' => 'Payment Disbursed',
                            ],
                        ]
                    );
                }
            }
        });

        return back()->with('success', 'Supplier payment recorded successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        abort_unless(request()->user()->is('admin'), 403);

        if ($supplier->purchases()->exists() || $supplier->purchasePayments()->exists()) {
            return back()->with('danger', 'Cannot delete supplier with existing purchases or payments. You can deactivate the supplier instead.');
        }

        $supplier->delete();

        return back()->with('success', 'Supplier deleted successfully.');
    }
}
