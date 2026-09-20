<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\TransactionCategory;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LedgerReportController extends Controller
{
    public function __construct(public AccountingService $accountingService) {}

    public function monthly(Request $request)
    {
        abort_unless(request()->user()->is('admin'), 403);

        $rawMonth = $request->input('month');
        $date = null;

        if (! empty($rawMonth) && is_string($rawMonth)) {
            $rawMonth = trim($rawMonth);
            // Matches YYYY-MM or YYYY-M
            if (preg_match('/^(\d{4})-(\d{1,2})$/', $rawMonth, $matches)) {
                $y = (int) $matches[1];
                $m = (int) $matches[2];
                if ($y >= 1970 && $y <= 2100 && $m >= 1 && $m <= 12) {
                    $date = Carbon::createFromDate($y, $m, 1);
                }
            } elseif (preg_match('/^(\d{4})(\d{2})$/', $rawMonth, $matches)) {
                $y = (int) $matches[1];
                $m = (int) $matches[2];
                if ($y >= 1970 && $y <= 2100 && $m >= 1 && $m <= 12) {
                    $date = Carbon::createFromDate($y, $m, 1);
                }
            } else {
                try {
                    $parsed = Carbon::parse($rawMonth);
                    if ($parsed && $parsed->year >= 1970 && $parsed->year <= 2100) {
                        $date = $parsed->startOfMonth();
                    }
                } catch (\Throwable) {
                    // Fallback to current month on invalid parse
                }
            }
        }

        $date ??= now()->startOfMonth();
        $monthYear = $date->format('Y-m');
        $year = (int) $date->format('Y');
        $month = (int) $date->format('m');

        $rawAccountId = $request->input('account_id');
        $accountId = filter_var($rawAccountId, FILTER_VALIDATE_INT) !== false && (int) $rawAccountId > 0 ? (int) $rawAccountId : null;
        if ($accountId && ! Account::where('id', $accountId)->exists()) {
            $accountId = null;
        }

        // Ensure default chart of accounts is seeded
        if (Account::count() === 0) {
            $this->accountingService->seedDefaultAccounts();
        }

        $ledgerData = $this->accountingService->getMonthlyLedger($year, $month, $accountId);

        $accounts = Account::where('is_active', true)->orderBy('name')->get();
        $assetAccounts = $accounts->where('type', Account::TYPE_ASSET);
        $incomeAccounts = $accounts->where('type', Account::TYPE_INCOME);
        $categories = TransactionCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.accounting.ledger.monthly', compact(
            'ledgerData',
            'monthYear',
            'accounts',
            'assetAccounts',
            'incomeAccounts',
            'categories',
            'accountId'
        ));
    }

    public function recordCourierPayout(Request $request)
    {
        abort_unless(request()->user()->is('admin'), 403);

        $validated = $request->validate([
            'deposit_account_id' => ['nullable', 'exists:accounts,id'],
            'to_account_id' => ['nullable', 'exists:accounts,id'],
            'total_sales' => ['nullable', 'numeric', 'min:0.01'],
            'amount' => ['nullable', 'numeric', 'min:0.01'],
            'courier_charge' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['nullable', 'exists:transaction_categories,id'],
            'entry_date' => ['required', 'date'],
            'courier_name' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $depositAccountId = (int) ($validated['deposit_account_id'] ?? $validated['to_account_id'] ?? 0);
        if (! $depositAccountId || ! Account::where('id', $depositAccountId)->exists()) {
            $depositAccountId = (int) (Account::where('type', Account::TYPE_ASSET)->value('id') ?? 0);
        }

        $totalSales = (float) ($validated['total_sales'] ?? $validated['amount'] ?? 0);
        if ($totalSales <= 0) {
            return back()->with('danger', 'Please provide a valid total sales amount.');
        }

        $courierCharge = (float) ($validated['courier_charge'] ?? 0);
        $netReceived = max(0.01, $totalSales - $courierCharge);

        $salesAccount = Account::where('type', Account::TYPE_INCOME)->first();
        $deliveryExpenseAccount = Account::where('type', Account::TYPE_EXPENSE)->first();

        if (! $salesAccount) {
            return back()->with('danger', 'Please configure at least one Income/Sales account first.');
        }

        $journalItems = [
            [
                'account_id' => $depositAccountId,
                'debit' => $netReceived,
                'credit' => 0,
                'notes' => 'Net Payout Received'.(! empty($validated['courier_name']) ? ' from '.$validated['courier_name'] : ''),
            ],
            [
                'account_id' => $salesAccount->id,
                'debit' => 0,
                'credit' => $totalSales,
                'notes' => 'Delivered Sales Revenue',
            ],
        ];

        if ($courierCharge > 0 && $deliveryExpenseAccount) {
            $journalItems[] = [
                'account_id' => $deliveryExpenseAccount->id,
                'debit' => $courierCharge,
                'credit' => 0,
                'notes' => 'Courier Delivery Charges & Fees',
            ];
        }

        $description = $validated['description'] ?? ('Courier Payout / Sales Settlement'.(! empty($validated['courier_name']) ? ' ('.$validated['courier_name'].')' : ''));

        $this->accountingService->createJournalEntry(
            [
                'entry_date' => $validated['entry_date'],
                'reference' => $validated['reference'] ?? null,
                'description' => $description,
            ],
            $journalItems
        );

        return back()->with('success', 'Courier payout / sales revenue successfully recorded to ledger.');
    }
}
