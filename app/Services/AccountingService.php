<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\TransactionCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AccountingService
{
    /**
     * Create a double-entry Journal Entry with balancing debits and credits.
     *
     * @param  array{
     *     entry_date: string,
     *     reference?: ?string,
     *     description?: ?string,
     *     source_type?: ?string,
     *     source_id?: ?int,
     *     admin_id?: ?int
     * }  $header
     * @param  array<int, array{
     *     account_id: int,
     *     category_id?: ?int,
     *     debit?: float|int|string,
     *     credit?: float|int|string,
     *     notes?: ?string
     * }>  $items
     */
    public function createJournalEntry(array $header, array $items): JournalEntry
    {
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        foreach ($items as $item) {
            $totalDebit += (float) ($item['debit'] ?? 0);
            $totalCredit += (float) ($item['credit'] ?? 0);
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            throw ValidationException::withMessages([
                'journal_entry' => sprintf('Total debit (%.2f) must equal total credit (%.2f).', $totalDebit, $totalCredit),
            ]);
        }

        if ($totalDebit <= 0) {
            throw ValidationException::withMessages([
                'journal_entry' => 'Journal entry amount must be greater than zero.',
            ]);
        }

        return DB::transaction(function () use ($header, $items): JournalEntry {
            $entryDate = Carbon::parse($header['entry_date'] ?? now());

            $entryNumber = $header['entry_number'] ?? ('JE-'.$entryDate->format('Ymd').'-'.str_pad((string) (JournalEntry::whereDate('entry_date', $entryDate)->count() + 1), 4, '0', STR_PAD_LEFT));

            $entry = JournalEntry::create([
                'entry_number' => $entryNumber,
                'entry_date' => $entryDate->format('Y-m-d'),
                'reference' => $header['reference'] ?? null,
                'description' => $header['description'] ?? null,
                'source_type' => $header['source_type'] ?? null,
                'source_id' => $header['source_id'] ?? null,
                'admin_id' => $header['admin_id'] ?? auth('admin')->id(),
            ]);

            $affectedAccountIds = [];

            foreach ($items as $item) {
                $debit = (float) ($item['debit'] ?? 0);
                $credit = (float) ($item['credit'] ?? 0);

                if ($debit <= 0 && $credit <= 0) {
                    continue;
                }

                $entry->items()->create([
                    'account_id' => $item['account_id'],
                    'category_id' => $item['category_id'] ?? null,
                    'debit' => $debit,
                    'credit' => $credit,
                    'notes' => $item['notes'] ?? null,
                ]);

                $affectedAccountIds[] = (int) $item['account_id'];
            }

            foreach (array_unique($affectedAccountIds) as $accountId) {
                if ($account = Account::find($accountId)) {
                    $account->recalculateBalance();
                }
            }

            return $entry;
        });
    }

    /**
     * Delete a journal entry and recalculate balances for all affected accounts.
     */
    public function deleteJournalEntry(JournalEntry|int $entry): bool
    {
        $journalEntry = $entry instanceof JournalEntry ? $entry : JournalEntry::findOrFail($entry);

        return (bool) DB::transaction(function () use ($journalEntry): bool {
            $affectedAccountIds = $journalEntry->items()->pluck('account_id')->toArray();

            $journalEntry->delete();

            foreach (array_unique($affectedAccountIds) as $accountId) {
                if ($account = Account::find($accountId)) {
                    $account->recalculateBalance();
                }
            }

            return true;
        });
    }

    /**
     * Update an existing journal entry and recalculate balances for all affected accounts.
     */
    public function updateJournalEntry(
        JournalEntry|int $entry,
        array $header,
        array $items
    ): JournalEntry {
        $journalEntry = $entry instanceof JournalEntry ? $entry : JournalEntry::findOrFail($entry);

        return DB::transaction(function () use ($journalEntry, $header, $items): JournalEntry {
            $affectedAccountIds = $journalEntry->items()->pluck('account_id')->toArray();

            $entryDate = isset($header['entry_date']) ? Carbon::parse($header['entry_date']) : Carbon::parse($journalEntry->entry_date);

            $journalEntry->update([
                'entry_date' => $entryDate->format('Y-m-d'),
                'reference' => $header['reference'] ?? $journalEntry->reference,
                'description' => $header['description'] ?? $journalEntry->description,
            ]);

            $journalEntry->items()->delete();

            foreach ($items as $item) {
                $debit = (float) ($item['debit'] ?? 0);
                $credit = (float) ($item['credit'] ?? 0);

                if ($debit <= 0 && $credit <= 0) {
                    continue;
                }

                $journalEntry->items()->create([
                    'account_id' => $item['account_id'],
                    'category_id' => $item['category_id'] ?? null,
                    'debit' => $debit,
                    'credit' => $credit,
                    'notes' => $item['notes'] ?? null,
                ]);

                $affectedAccountIds[] = (int) $item['account_id'];
            }

            foreach (array_unique($affectedAccountIds) as $accountId) {
                if ($account = Account::find($accountId)) {
                    $account->recalculateBalance();
                }
            }

            return $journalEntry;
        });
    }

    /**
     * Sync or create a journal entry linked to a source document.
     */
    public function syncSourceJournalEntry(
        string $sourceType,
        int $sourceId,
        array $header,
        array $items
    ): JournalEntry {
        $existingEntry = JournalEntry::where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->first();

        if ($existingEntry) {
            return $this->updateJournalEntry($existingEntry, $header, $items);
        }

        $header['source_type'] = $sourceType;
        $header['source_id'] = $sourceId;

        return $this->createJournalEntry($header, $items);
    }

    /**
     * Record a quick expense.
     */
    public function recordExpense(
        int $fromAccountId,
        int $expenseAccountId,
        float $amount,
        ?int $categoryId = null,
        ?string $date = null,
        ?string $description = null,
        ?string $reference = null
    ): JournalEntry {
        return $this->createJournalEntry(
            [
                'entry_date' => $date ?? now()->toDateString(),
                'reference' => $reference,
                'description' => $description,
            ],
            [
                [
                    'account_id' => $expenseAccountId,
                    'category_id' => $categoryId,
                    'debit' => $amount,
                    'credit' => 0,
                    'notes' => $description,
                ],
                [
                    'account_id' => $fromAccountId,
                    'category_id' => null,
                    'debit' => 0,
                    'credit' => $amount,
                    'notes' => $description,
                ],
            ]
        );
    }

    /**
     * Record a quick income (e.g. Courier payout or extra revenue).
     */
    public function recordIncome(
        int $toAccountId,
        int $incomeAccountId,
        float $amount,
        ?int $categoryId = null,
        ?string $date = null,
        ?string $description = null,
        ?string $reference = null
    ): JournalEntry {
        return $this->createJournalEntry(
            [
                'entry_date' => $date ?? now()->toDateString(),
                'reference' => $reference,
                'description' => $description,
            ],
            [
                [
                    'account_id' => $toAccountId,
                    'category_id' => null,
                    'debit' => $amount,
                    'credit' => 0,
                    'notes' => $description,
                ],
                [
                    'account_id' => $incomeAccountId,
                    'category_id' => $categoryId,
                    'debit' => 0,
                    'credit' => $amount,
                    'notes' => $description,
                ],
            ]
        );
    }

    /**
     * Record a transfer between asset accounts.
     */
    public function recordTransfer(
        int $fromAccountId,
        int $toAccountId,
        float $amount,
        ?string $date = null,
        ?string $description = null,
        ?string $reference = null
    ): JournalEntry {
        return $this->createJournalEntry(
            [
                'entry_date' => $date ?? now()->toDateString(),
                'reference' => $reference,
                'description' => $description ?? 'Internal Account Transfer',
            ],
            [
                [
                    'account_id' => $toAccountId,
                    'debit' => $amount,
                    'credit' => 0,
                    'notes' => 'Transfer In',
                ],
                [
                    'account_id' => $fromAccountId,
                    'debit' => 0,
                    'credit' => $amount,
                    'notes' => 'Transfer Out',
                ],
            ]
        );
    }

    /**
     * Get monthly ledger and company balance sheet for given year and month.
     */
    public function getMonthlyLedger(int $year, int $month, ?int $filterAccountId = null): array
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $accountsQuery = Account::query()->when($filterAccountId, fn ($q) => $q->where('id', $filterAccountId));
        $accounts = $accountsQuery->orderBy('type')->orderBy('name')->get();

        $ledgerData = [];
        $totalAssets = 0.0;
        $totalLiabilities = 0.0;
        $totalEquity = 0.0;
        $totalIncomeMonth = 0.0;
        $totalExpenseMonth = 0.0;

        foreach ($accounts as $account) {
            // Calculate opening balance before this month
            $priorDebit = (float) DB::table('journal_entry_items')
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_items.journal_entry_id')
                ->where('journal_entry_items.account_id', $account->id)
                ->where('journal_entries.entry_date', '<', $startDate->toDateString())
                ->sum('journal_entry_items.debit');

            $priorCredit = (float) DB::table('journal_entry_items')
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_items.journal_entry_id')
                ->where('journal_entry_items.account_id', $account->id)
                ->where('journal_entries.entry_date', '<', $startDate->toDateString())
                ->sum('journal_entry_items.credit');

            if ($account->isDebitNature()) {
                $openingBalance = (float) $account->opening_balance + ($priorDebit - $priorCredit);
            } else {
                $openingBalance = (float) $account->opening_balance + ($priorCredit - $priorDebit);
            }

            // Get transactions within the month
            $items = DB::table('journal_entry_items')
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_items.journal_entry_id')
                ->leftJoin('transaction_categories', 'transaction_categories.id', '=', 'journal_entry_items.category_id')
                ->where('journal_entry_items.account_id', $account->id)
                ->whereBetween('journal_entries.entry_date', [$startDate->toDateString(), $endDate->toDateString()])
                ->select([
                    'journal_entries.entry_date',
                    'journal_entries.entry_number',
                    'journal_entries.reference',
                    'journal_entries.description as entry_description',
                    'journal_entry_items.notes',
                    'journal_entry_items.debit',
                    'journal_entry_items.credit',
                    'transaction_categories.name as category_name',
                ])
                ->orderBy('journal_entries.entry_date')
                ->orderBy('journal_entries.id')
                ->get();

            $monthDebit = (float) $items->sum('debit');
            $monthCredit = (float) $items->sum('credit');

            if ($account->isDebitNature()) {
                $endingBalance = $openingBalance + ($monthDebit - $monthCredit);
            } else {
                $endingBalance = $openingBalance + ($monthCredit - $monthDebit);
            }

            // Aggregate company totals
            if ($account->type === Account::TYPE_ASSET) {
                $totalAssets += $endingBalance;
            } elseif ($account->type === Account::TYPE_LIABILITY) {
                $totalLiabilities += $endingBalance;
            } elseif ($account->type === Account::TYPE_EQUITY) {
                $totalEquity += $endingBalance;
            } elseif ($account->type === Account::TYPE_INCOME) {
                $totalIncomeMonth += ($monthCredit - $monthDebit);
            } elseif ($account->type === Account::TYPE_EXPENSE) {
                $totalExpenseMonth += ($monthDebit - $monthCredit);
            }

            $ledgerData[] = [
                'account' => $account,
                'opening_balance' => $openingBalance,
                'total_debit' => $monthDebit,
                'total_credit' => $monthCredit,
                'ending_balance' => $endingBalance,
                'items' => $items,
            ];
        }

        $netProfitMonth = $totalIncomeMonth - $totalExpenseMonth;
        $companyNetBalance = $totalAssets - $totalLiabilities;

        return [
            'year' => $year,
            'month' => $month,
            'month_name' => $startDate->format('F Y'),
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'accounts' => $ledgerData,
            'summary' => [
                'total_assets' => $totalAssets,
                'total_liabilities' => $totalLiabilities,
                'total_equity' => $totalEquity,
                'total_income_month' => $totalIncomeMonth,
                'total_expense_month' => $totalExpenseMonth,
                'net_profit_month' => $netProfitMonth,
                'company_net_balance' => $companyNetBalance,
            ],
        ];
    }

    /**
     * Seed initial default chart of accounts and categories for the current tenant.
     */
    public function seedDefaultAccounts(): void
    {
        $defaultCategories = [
            ['name' => 'Utility Bills', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'AC & Electricity Bill', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'Water Bill', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'Office Snacks & Food', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'Ads & Marketing', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'Dollar Cost (Cards/FB Ads)', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'Cost of Goods Sold', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'Staff Salary', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'Paper & Stationery', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'Packaging Material', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'Courier Payout (Sales)', 'type' => TransactionCategory::TYPE_INCOME],
            ['name' => 'Delivery Charges Income', 'type' => TransactionCategory::TYPE_INCOME],
            ['name' => 'Extra / Misc Income', 'type' => TransactionCategory::TYPE_INCOME],
        ];

        foreach ($defaultCategories as $cat) {
            TransactionCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }

        $defaultAccounts = [
            // Assets
            ['name' => 'Cash in Hand', 'code' => '1001', 'type' => Account::TYPE_ASSET, 'is_system' => true],
            ['name' => 'Bank Account', 'code' => '1002', 'type' => Account::TYPE_ASSET, 'is_system' => true],
            ['name' => 'bKash / Mobile Wallet', 'code' => '1003', 'type' => Account::TYPE_ASSET, 'is_system' => true],
            ['name' => 'Inventory / Product Stock', 'code' => '1004', 'type' => Account::TYPE_ASSET, 'is_system' => true],

            // Liabilities
            ['name' => 'Accounts Payable (Supplier Dues)', 'code' => '2001', 'type' => Account::TYPE_LIABILITY, 'is_system' => true],

            // Equity
            ['name' => "Owner's Capital / Investment", 'code' => '3001', 'type' => Account::TYPE_EQUITY, 'is_system' => true],
            ['name' => "Owner's Drawings / Withdrawals", 'code' => '3002', 'type' => Account::TYPE_EQUITY, 'is_system' => true],

            // Income
            ['name' => 'Product Sales Revenue', 'code' => '4001', 'type' => Account::TYPE_INCOME, 'is_system' => true],
            ['name' => 'Delivery Charges Income', 'code' => '4002', 'type' => Account::TYPE_INCOME, 'is_system' => true],
            ['name' => 'Other / Extra Income', 'code' => '4003', 'type' => Account::TYPE_INCOME, 'is_system' => true],

            // Expenses
            ['name' => 'Product Purchase Cost', 'code' => '5001', 'type' => Account::TYPE_EXPENSE, 'is_system' => true],
            ['name' => 'Advertising & Marketing Cost', 'code' => '5002', 'type' => Account::TYPE_EXPENSE, 'is_system' => true],
            ['name' => 'Dollar Cost Expense', 'code' => '5003', 'type' => Account::TYPE_EXPENSE, 'is_system' => true],
            ['name' => 'Staff Salaries & Wages', 'code' => '5004', 'type' => Account::TYPE_EXPENSE, 'is_system' => true],
            ['name' => 'Office Utility Bills (Electricity, Water, AC)', 'code' => '5005', 'type' => Account::TYPE_EXPENSE, 'is_system' => true],
            ['name' => 'Office Snacks & Refreshments', 'code' => '5006', 'type' => Account::TYPE_EXPENSE, 'is_system' => true],
            ['name' => 'Stationery, Paper & Office Supplies', 'code' => '5007', 'type' => Account::TYPE_EXPENSE, 'is_system' => true],
            ['name' => 'Office Rent', 'code' => '5008', 'type' => Account::TYPE_EXPENSE, 'is_system' => true],
            ['name' => 'Miscellaneous Expenses', 'code' => '5009', 'type' => Account::TYPE_EXPENSE, 'is_system' => true],
        ];

        foreach ($defaultAccounts as $acc) {
            Account::firstOrCreate(['name' => $acc['name']], $acc);
        }
    }
}
