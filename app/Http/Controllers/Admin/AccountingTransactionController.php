<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use App\Models\TransactionCategory;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AccountingTransactionController extends Controller
{
    public function __construct(public AccountingService $accountingService) {}

    public function index(Request $request)
    {
        abort_unless(request()->user()->is('admin'), 403);

        $rawStart = $request->input('start_d');
        $rawEnd = $request->input('end_d');

        try {
            $startDate = ! empty($rawStart) && is_string($rawStart) ? Carbon::parse($rawStart)->toDateString() : now()->startOfMonth()->toDateString();
        } catch (\Throwable) {
            $startDate = now()->startOfMonth()->toDateString();
        }

        try {
            $endDate = ! empty($rawEnd) && is_string($rawEnd) ? Carbon::parse($rawEnd)->toDateString() : now()->endOfMonth()->toDateString();
        } catch (\Throwable) {
            $endDate = now()->endOfMonth()->toDateString();
        }

        $rawAccountId = $request->input('account_id');
        $accountId = filter_var($rawAccountId, FILTER_VALIDATE_INT) !== false && (int) $rawAccountId > 0 ? (int) $rawAccountId : null;

        $rawCategoryId = $request->input('category_id');
        $categoryId = filter_var($rawCategoryId, FILTER_VALIDATE_INT) !== false && (int) $rawCategoryId > 0 ? (int) $rawCategoryId : null;

        $query = JournalEntry::with(['items.account', 'items.category', 'admin'])
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->when($accountId, function ($q) use ($accountId): void {
                $q->whereHas('items', fn ($sub) => $sub->where('account_id', $accountId));
            })
            ->when($categoryId, function ($q) use ($categoryId): void {
                $q->whereHas('items', fn ($sub) => $sub->where('category_id', $categoryId));
            })
            ->latest('entry_date')
            ->latest('id');

        $entries = $query->paginate(30)->withQueryString();

        $accounts = Account::where('is_active', true)->orderBy('name')->get();
        $categories = TransactionCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.accounting.transactions.index', compact(
            'entries',
            'accounts',
            'categories',
            'startDate',
            'endDate'
        ));
    }

    public function create()
    {
        abort_unless(request()->user()->is('admin'), 403);

        $accounts = Account::where('is_active', true)->orderBy('name')->get();
        $categories = TransactionCategory::where('is_active', true)->orderBy('name')->get();

        $assetAccounts = $accounts->where('type', Account::TYPE_ASSET);
        $expenseAccounts = $accounts->where('type', Account::TYPE_EXPENSE);
        $incomeAccounts = $accounts->where('type', Account::TYPE_INCOME);
        $equityAccounts = $accounts->where('type', Account::TYPE_EQUITY);
        $liabilityAccounts = $accounts->where('type', Account::TYPE_LIABILITY);

        return view('admin.accounting.transactions.create', compact(
            'accounts',
            'assetAccounts',
            'expenseAccounts',
            'incomeAccounts',
            'equityAccounts',
            'liabilityAccounts',
            'categories'
        ));
    }

    public function store(Request $request)
    {
        abort_unless(request()->user()->is('admin'), 403);

        $type = $request->input('transaction_type');

        if ($type === 'expense') {
            $categoryId = $request->category_id ?? $request->expense_category_id;

            $request->merge(['category_id' => $categoryId]);
            $request->validate([
                'from_account_id' => ['required', 'exists:accounts,id'],
                'expense_account_id' => ['required', 'exists:accounts,id'],
                'amount' => ['required', 'numeric', 'min:0.01'],
                'category_id' => ['nullable', 'exists:transaction_categories,id'],
                'entry_date' => ['required', 'date'],
                'description' => ['nullable', 'string'],
                'reference' => ['nullable', 'string'],
            ]);

            $this->accountingService->recordExpense(
                (int) $request->from_account_id,
                (int) $request->expense_account_id,
                (float) $request->amount,
                $categoryId ? (int) $categoryId : null,
                $request->entry_date,
                $request->description,
                $request->reference
            );

            return to_route('admin.accounting.transactions.index')->with('success', 'Expense recorded successfully.');
        }

        if ($type === 'income') {
            $categoryId = $request->category_id ?? $request->income_category_id;

            $request->merge(['category_id' => $categoryId]);
            $request->validate([
                'to_account_id' => ['required', 'exists:accounts,id'],
                'income_account_id' => ['required', 'exists:accounts,id'],
                'amount' => ['required', 'numeric', 'min:0.01'],
                'category_id' => ['nullable', 'exists:transaction_categories,id'],
                'entry_date' => ['required', 'date'],
                'description' => ['nullable', 'string'],
                'reference' => ['nullable', 'string'],
            ]);

            $this->accountingService->recordIncome(
                (int) $request->to_account_id,
                (int) $request->income_account_id,
                (float) $request->amount,
                $categoryId ? (int) $categoryId : null,
                $request->entry_date,
                $request->description,
                $request->reference
            );

            return to_route('admin.accounting.transactions.index')->with('success', 'Income recorded successfully.');
        }

        if ($type === 'transfer') {
            $request->validate([
                'from_account_id' => ['required', 'exists:accounts,id', 'different:to_account_id'],
                'to_account_id' => ['required', 'exists:accounts,id'],
                'amount' => ['required', 'numeric', 'min:0.01'],
                'entry_date' => ['required', 'date'],
                'description' => ['nullable', 'string'],
                'reference' => ['nullable', 'string'],
            ]);

            $this->accountingService->recordTransfer(
                (int) $request->from_account_id,
                (int) $request->to_account_id,
                (float) $request->amount,
                $request->entry_date,
                $request->description,
                $request->reference
            );

            return to_route('admin.accounting.transactions.index')->with('success', 'Transfer recorded successfully.');
        }

        if ($type === 'investment') {
            $request->validate([
                'to_account_id' => ['required', 'exists:accounts,id'],
                'equity_account_id' => ['required', 'exists:accounts,id'],
                'amount' => ['required', 'numeric', 'min:0.01'],
                'entry_date' => ['required', 'date'],
                'description' => ['nullable', 'string'],
            ]);

            $this->accountingService->createJournalEntry(
                [
                    'entry_date' => $request->entry_date,
                    'reference' => $request->reference,
                    'description' => $request->description ?? 'Owner Investment / Capital Addition',
                ],
                [
                    [
                        'account_id' => (int) $request->to_account_id,
                        'debit' => (float) $request->amount,
                        'credit' => 0,
                        'notes' => 'Capital Received',
                    ],
                    [
                        'account_id' => (int) $request->equity_account_id,
                        'debit' => 0,
                        'credit' => (float) $request->amount,
                        'notes' => 'Owner Investment',
                    ],
                ]
            );

            return to_route('admin.accounting.transactions.index')->with('success', 'Investment recorded successfully.');
        }

        if ($type === 'withdrawal') {
            $request->validate([
                'from_account_id' => ['required', 'exists:accounts,id'],
                'equity_account_id' => ['required', 'exists:accounts,id'],
                'amount' => ['required', 'numeric', 'min:0.01'],
                'entry_date' => ['required', 'date'],
                'description' => ['nullable', 'string'],
            ]);

            $this->accountingService->createJournalEntry(
                [
                    'entry_date' => $request->entry_date,
                    'reference' => $request->reference,
                    'description' => $request->description ?? 'Owner Drawing / Withdrawal',
                ],
                [
                    [
                        'account_id' => (int) $request->equity_account_id,
                        'debit' => (float) $request->amount,
                        'credit' => 0,
                        'notes' => 'Owner Drawing',
                    ],
                    [
                        'account_id' => (int) $request->from_account_id,
                        'debit' => 0,
                        'credit' => (float) $request->amount,
                        'notes' => 'Funds Withdrawn',
                    ],
                ]
            );

            return to_route('admin.accounting.transactions.index')->with('success', 'Withdrawal recorded successfully.');
        }

        return back()->with('danger', 'Invalid transaction type.');
    }

    public function edit(JournalEntry|int|string $transaction)
    {
        abort_unless(request()->user()->is('admin'), 403);

        $entry = $transaction instanceof JournalEntry ? $transaction : JournalEntry::findOrFail($transaction);

        if ($entry->source_type === Purchase::class && $entry->source_id) {
            return redirect()->route('admin.purchases.edit', $entry->source_id)
                ->with('info', 'This transaction was automatically created by Purchase #'.$entry->source_id.'. Please edit the purchase to synchronize the ledger.');
        }

        if ($entry->source_type === Supplier::class || $entry->source_type === PurchasePayment::class) {
            return redirect()->route('admin.suppliers.index')
                ->with('info', 'This transaction is linked to supplier payments. Please manage payments from the Suppliers section.');
        }

        $accounts = Account::where('is_active', true)->orderBy('name')->get();
        $categories = TransactionCategory::where('is_active', true)->orderBy('name')->get();

        $debitItem = $entry->items->where('debit', '>', 0)->first();
        $creditItem = $entry->items->where('credit', '>', 0)->first();

        $amount = (float) ($debitItem ? $debitItem->debit : ($creditItem ? $creditItem->credit : 0));
        $debitAccountId = $debitItem ? $debitItem->account_id : null;
        $creditAccountId = $creditItem ? $creditItem->account_id : null;
        $categoryId = $debitItem?->category_id ?? $creditItem?->category_id;

        return view('admin.accounting.transactions.edit', compact(
            'entry',
            'accounts',
            'categories',
            'amount',
            'debitAccountId',
            'creditAccountId',
            'categoryId'
        ));
    }

    public function update(Request $request, JournalEntry|int|string $transaction)
    {
        abort_unless(request()->user()->is('admin'), 403);

        $entry = $transaction instanceof JournalEntry ? $transaction : JournalEntry::findOrFail($transaction);

        $validated = $request->validate([
            'entry_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'debit_account_id' => ['required', 'exists:accounts,id'],
            'credit_account_id' => ['required', 'exists:accounts,id', 'different:debit_account_id'],
            'category_id' => ['nullable', 'exists:transaction_categories,id'],
            'reference' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $amount = (float) $validated['amount'];
        $debitAccountId = (int) $validated['debit_account_id'];
        $creditAccountId = (int) $validated['credit_account_id'];
        $categoryId = ! empty($validated['category_id']) ? (int) $validated['category_id'] : null;

        $items = [
            [
                'account_id' => $debitAccountId,
                'category_id' => $categoryId,
                'debit' => $amount,
                'credit' => 0,
                'notes' => $validated['description'] ?? null,
            ],
            [
                'account_id' => $creditAccountId,
                'category_id' => null,
                'debit' => 0,
                'credit' => $amount,
                'notes' => $validated['description'] ?? null,
            ],
        ];

        $this->accountingService->updateJournalEntry(
            $entry,
            [
                'entry_date' => $validated['entry_date'],
                'reference' => $validated['reference'] ?? null,
                'description' => $validated['description'] ?? null,
            ],
            $items
        );

        return to_route('admin.accounting.transactions.index')
            ->with('success', 'Transaction updated and account balances recalculated successfully.');
    }

    public function destroy(JournalEntry|int|string $transaction)
    {
        abort_unless(request()->user()->is('admin'), 403);

        $entry = $transaction instanceof JournalEntry ? $transaction : JournalEntry::findOrFail($transaction);

        $this->accountingService->deleteJournalEntry($entry);

        return back()->with('success', 'Transaction deleted and account balances recalculated.');
    }
}
