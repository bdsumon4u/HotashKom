<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalEntryItem;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    public function __construct(public AccountingService $accountingService) {}

    public function index()
    {
        abort_unless(request()->user()->is('admin'), 403);

        // Ensure default accounts exist
        if (Account::count() === 0) {
            $this->accountingService->seedDefaultAccounts();
        }

        $accounts = Account::orderBy('type')->orderBy('name')->get();

        $groupedAccounts = $accounts->groupBy('type');

        return view('admin.accounting.accounts.index', compact('accounts', 'groupedAccounts'));
    }

    public function create()
    {
        abort_unless(request()->user()->is('admin'), 403);

        return view('admin.accounting.accounts.create');
    }

    public function store(Request $request)
    {
        abort_unless(request()->user()->is('admin'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'type' => ['required', 'in:asset,liability,equity,income,expense'],
            'opening_balance' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['opening_balance'] = (float) ($validated['opening_balance'] ?? 0);
        $validated['current_balance'] = $validated['opening_balance'];
        $validated['is_active'] = $request->boolean('is_active', true);

        Account::create($validated);

        return to_route('admin.accounting.accounts.index')->with('success', 'Account created successfully.');
    }

    public function edit(Account $account)
    {
        abort_unless(request()->user()->is('admin'), 403);

        return view('admin.accounting.accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        abort_unless(request()->user()->is('admin'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'type' => ['required', 'in:asset,liability,equity,income,expense'],
            'opening_balance' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['opening_balance'] = (float) ($validated['opening_balance'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active');

        $account->update($validated);
        $account->recalculateBalance();

        return to_route('admin.accounting.accounts.index')->with('success', 'Account updated successfully.');
    }

    public function show(Account $account, Request $request)
    {
        abort_unless($request->user()->is('admin'), 403);

        $startDate = $request->filled('start_d')
            ? Carbon::parse($request->input('start_d'))->startOfDay()
            : Carbon::now()->startOfMonth()->startOfDay();

        $endDate = $request->filled('end_d')
            ? Carbon::parse($request->input('end_d'))->endOfDay()
            : Carbon::now()->endOfDay();

        $isDebitNature = $account->isDebitNature();

        // 1. Calculate opening balance prior to $startDate
        $priorItems = JournalEntryItem::where('account_id', $account->id)
            ->whereHas('journalEntry', function ($q) use ($startDate) {
                $q->where('entry_date', '<', $startDate->toDateString());
            })
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->first();

        $priorDebit = (float) ($priorItems->total_debit ?? 0);
        $priorCredit = (float) ($priorItems->total_credit ?? 0);

        if ($isDebitNature) {
            $openingBalance = (float) $account->opening_balance + ($priorDebit - $priorCredit);
        } else {
            $openingBalance = (float) $account->opening_balance + ($priorCredit - $priorDebit);
        }

        // 2. Fetch journal entry items in range [$startDate, $endDate]
        $items = JournalEntryItem::with(['journalEntry', 'category'])
            ->where('account_id', $account->id)
            ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('entry_date', [$startDate->toDateString(), $endDate->toDateString()]);
            })
            ->join('journal_entries', 'journal_entry_items.journal_entry_id', '=', 'journal_entries.id')
            ->orderBy('journal_entries.entry_date', 'asc')
            ->orderBy('journal_entries.id', 'asc')
            ->orderBy('journal_entry_items.id', 'asc')
            ->select('journal_entry_items.*')
            ->get();

        // Build transactions with running balance
        $runningBalance = $openingBalance;
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        $transactions = [];
        foreach ($items as $item) {
            $debit = (float) $item->debit;
            $credit = (float) $item->credit;
            $totalDebit += $debit;
            $totalCredit += $credit;

            if ($isDebitNature) {
                $runningBalance += ($debit - $credit);
            } else {
                $runningBalance += ($credit - $debit);
            }

            $transactions[] = [
                'id' => $item->id,
                'journal_entry_id' => $item->journal_entry_id,
                'entry_number' => $item->journalEntry?->entry_number,
                'date' => $item->journalEntry?->entry_date?->format('Y-m-d'),
                'reference' => $item->journalEntry?->reference,
                'description' => $item->journalEntry?->description,
                'notes' => $item->notes,
                'category' => $item->category?->name,
                'source_type' => $item->journalEntry?->source_type,
                'source_id' => $item->journalEntry?->source_id,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $runningBalance,
            ];
        }

        $closingBalance = $runningBalance;
        $netMovement = $isDebitNature ? ($totalDebit - $totalCredit) : ($totalCredit - $totalDebit);

        // 3. Daily Summary
        $dailySummary = [];
        $groupedByDate = collect($transactions)->groupBy('date');
        $dayRunningBalance = $openingBalance;

        foreach ($groupedByDate as $date => $dayItems) {
            $dayDebit = (float) $dayItems->sum('debit');
            $dayCredit = (float) $dayItems->sum('credit');
            $dayNet = $isDebitNature ? ($dayDebit - $dayCredit) : ($dayCredit - $dayDebit);
            $dayRunningBalance += $dayNet;

            $dailySummary[] = [
                'date' => $date,
                'count' => $dayItems->count(),
                'debit' => $dayDebit,
                'credit' => $dayCredit,
                'net_movement' => $dayNet,
                'closing_balance' => $dayRunningBalance,
            ];
        }

        // 4. Monthly Summary
        $monthlySummary = [];
        $groupedByMonth = collect($transactions)->groupBy(function ($item) {
            return substr((string) $item['date'], 0, 7);
        });
        $monthRunningBalance = $openingBalance;

        foreach ($groupedByMonth as $month => $monthItems) {
            $mDebit = (float) $monthItems->sum('debit');
            $mCredit = (float) $monthItems->sum('credit');
            $mNet = $isDebitNature ? ($mDebit - $mCredit) : ($mCredit - $mDebit);
            $monthRunningBalance += $mNet;

            $monthlySummary[] = [
                'month' => $month,
                'month_label' => Carbon::parse($month.'-01')->format('F Y'),
                'count' => $monthItems->count(),
                'debit' => $mDebit,
                'credit' => $mCredit,
                'net_movement' => $mNet,
                'closing_balance' => $monthRunningBalance,
            ];
        }

        return view('admin.accounting.accounts.show', compact(
            'account',
            'startDate',
            'endDate',
            'openingBalance',
            'closingBalance',
            'totalDebit',
            'totalCredit',
            'netMovement',
            'transactions',
            'dailySummary',
            'monthlySummary',
            'isDebitNature'
        ));
    }

    public function destroy(Account $account)
    {
        abort_unless(request()->user()->is('admin'), 403);

        try {
            $account->delete();

            return back()->with('success', 'Account deleted successfully.');
        } catch (ValidationException $e) {
            return back()->with('danger', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('danger', 'Cannot delete account because it is referenced in transactions.');
        }
    }
}
