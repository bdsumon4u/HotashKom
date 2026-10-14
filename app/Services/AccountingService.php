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
     *
     * @param  array<int>|int|null  $filterAccountIds
     */
    public function getMonthlyLedger(int $year, int $month, array|int|null $filterAccountIds = null): array
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $accountIds = is_array($filterAccountIds)
            ? array_values(array_filter(array_map('intval', $filterAccountIds)))
            : ($filterAccountIds ? [(int) $filterAccountIds] : []);

        $accountsQuery = Account::query()->when(! empty($accountIds), fn ($q) => $q->whereIn('id', $accountIds));
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
            // Expense Sub-Tags / Classifications
            ['name' => 'AC & Electricity Bill', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'Water & Sanitation Bill', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'Internet & Wi-Fi Bill', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'Facebook & Meta Ads', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'TikTok & Video Ads', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'Google & YouTube Ads', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'Packaging Poly, Boxes & Tape', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'Courier Return / Parcel Damage Fee', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'Staff Festival Bonus & Incentives', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'Tea, Coffee & Daily Office Snacks', 'type' => TransactionCategory::TYPE_EXPENSE],
            ['name' => 'Office Cleaning & Maintenance', 'type' => TransactionCategory::TYPE_EXPENSE],

            // Income Sub-Tags / Classifications
            ['name' => 'Wholesale & Bulk Orders', 'type' => TransactionCategory::TYPE_INCOME],
            ['name' => 'Scrap & Box Packaging Sales', 'type' => TransactionCategory::TYPE_INCOME],
            ['name' => 'Affiliate & Referral Earnings', 'type' => TransactionCategory::TYPE_INCOME],
            ['name' => 'Bank Profit & Interest', 'type' => TransactionCategory::TYPE_INCOME],
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
            ['name' => 'Accounts Receivable (Customer / Courier Dues)', 'code' => '1005', 'type' => Account::TYPE_ASSET, 'is_system' => true],
            ['name' => 'In Courier (Products Shipped)', 'code' => '1006', 'type' => Account::TYPE_ASSET, 'is_system' => true],

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
            ['name' => 'Damaged & Lost Goods Expense', 'code' => '5011', 'type' => Account::TYPE_EXPENSE, 'is_system' => true],
        ];

        foreach ($defaultAccounts as $acc) {
            Account::firstOrCreate(['name' => $acc['name']], $acc);
        }
    }

    /**
     * Get or create the 'In Courier (Products Shipped)' asset account.
     */
    public function getInCourierAccount(): Account
    {
        return Account::where('code', '1006')
            ->orWhere('name', 'like', '%In Courier%')
            ->first() ?? Account::firstOrCreate(
                ['code' => '1006'],
                [
                    'name' => 'In Courier (Products Shipped)',
                    'type' => Account::TYPE_ASSET,
                    'is_system' => true,
                    'is_active' => true,
                ]
            );
    }

    /**
     * Get or create the 'Inventory / Product Stock' asset account.
     */
    public function getInventoryAccount(): Account
    {
        return Account::where('code', '1004')
            ->orWhere('name', 'like', '%Inventory%')
            ->orWhere('name', 'like', '%Product Stock%')
            ->first() ?? Account::firstOrCreate(
                ['code' => '1004'],
                [
                    'name' => 'Inventory / Product Stock',
                    'type' => Account::TYPE_ASSET,
                    'is_system' => true,
                    'is_active' => true,
                ]
            );
    }

    /**
     * Get or create the 'Product Purchase Cost' expense account (COGS).
     */
    public function getProductPurchaseCostAccount(): Account
    {
        return Account::where('code', '5001')
            ->orWhere('name', 'like', '%Product Purchase Cost%')
            ->orWhere('name', 'like', '%Purchase Cost%')
            ->first() ?? Account::firstOrCreate(
                ['code' => '5001'],
                [
                    'name' => 'Product Purchase Cost',
                    'type' => Account::TYPE_EXPENSE,
                    'is_system' => true,
                    'is_active' => true,
                ]
            );
    }

    /**
     * Get or create the 'Product Sales Revenue' income account.
     */
    public function getProductSalesRevenueAccount(): Account
    {
        return Account::where('code', '4001')
            ->orWhere('name', 'like', '%Product Sales Revenue%')
            ->orWhere('name', 'like', '%Sales Revenue%')
            ->first() ?? Account::firstOrCreate(
                ['code' => '4001'],
                [
                    'name' => 'Product Sales Revenue',
                    'type' => Account::TYPE_INCOME,
                    'is_system' => true,
                    'is_active' => true,
                ]
            );
    }

    /**
     * Get or create the courier delivery charges / expense account.
     */
    public function getCourierDeliveryExpenseAccount(): Account
    {
        return Account::where('name', 'like', '%Courier Return%')
            ->orWhere('name', 'like', '%Delivery%')
            ->orWhere('code', '5009')
            ->orWhere('type', Account::TYPE_EXPENSE)
            ->first() ?? Account::firstOrCreate(
                ['name' => 'Courier Delivery Charges & Fees'],
                [
                    'code' => '5010',
                    'type' => Account::TYPE_EXPENSE,
                    'is_system' => true,
                    'is_active' => true,
                ]
            );
    }

    /**
     * Record shipment of goods from warehouse Inventory to 'In Courier' asset account.
     *
     * @param  array{
     *     entry_date: string,
     *     courier_name: string,
     *     tracking_code?: ?string,
     *     notes?: ?string,
     *     total_purchase_cost: float,
     *     items_summary?: ?string
     * }  $data
     */
    public function recordShipmentToCourier(array $data): JournalEntry
    {
        $inCourierAccount = $this->getInCourierAccount();
        $inventoryAccount = $this->getInventoryAccount();
        $totalCost = (float) $data['total_purchase_cost'];

        $description = 'Shipment to Courier: '.$data['courier_name'];
        if (! empty($data['tracking_code'])) {
            $description .= ' (Tracking: '.$data['tracking_code'].')';
        }
        if (! empty($data['notes'])) {
            $description .= ' - '.$data['notes'];
        }

        return $this->createJournalEntry(
            [
                'entry_date' => $data['entry_date'] ?? now()->toDateString(),
                'reference' => $data['tracking_code'] ?? null,
                'description' => $description,
                'source_type' => 'Shipment',
            ],
            [
                [
                    'account_id' => $inCourierAccount->id,
                    'debit' => $totalCost,
                    'credit' => 0,
                    'notes' => 'Shipped to courier: '.$data['courier_name'].(! empty($data['items_summary']) ? ' ['.$data['items_summary'].']' : ''),
                ],
                [
                    'account_id' => $inventoryAccount->id,
                    'debit' => 0,
                    'credit' => $totalCost,
                    'notes' => 'Inventory deducted for courier dispatch',
                ],
            ]
        );
    }

    /**
     * Record delivery settlement / payout from courier.
     * Moves purchase cost from 'In Courier' asset to 'Product Purchase Cost' expense.
     * Records gross sales revenue into 'Product Sales Revenue' and courier fee expense.
     * Deposits net cash/bank into selected asset account.
     *
     * @param  array{
     *     entry_date: string,
     *     deposit_account_id: int,
     *     gross_sales: float,
     *     total_purchase_cost: float,
     *     courier_charge?: float,
     *     courier_name?: ?string,
     *     reference?: ?string,
     *     description?: ?string,
     *     items_summary?: ?string
     * }  $data
     */
    public function recordCourierPayoutSettlement(array $data): JournalEntry
    {
        $depositAccount = Account::findOrFail($data['deposit_account_id']);
        $salesAccount = $this->getProductSalesRevenueAccount();
        $inCourierAccount = $this->getInCourierAccount();
        $cogsAccount = $this->getProductPurchaseCostAccount();

        $grossSales = (float) $data['gross_sales'];
        $courierCharge = (float) ($data['courier_charge'] ?? 0);
        $totalPurchaseCost = (float) ($data['total_purchase_cost'] ?? 0);
        $netReceived = max(0, $grossSales - $courierCharge);

        $journalItems = [];

        // 1. Debit Deposit Account (Cash/Bank/bKash) with net payout received
        if ($netReceived > 0) {
            $journalItems[] = [
                'account_id' => $depositAccount->id,
                'debit' => $netReceived,
                'credit' => 0,
                'notes' => 'Net Payout Received'.(! empty($data['courier_name']) ? ' from '.$data['courier_name'] : ''),
            ];
        }

        // 2. Debit Courier Charge Expense if any
        if ($courierCharge > 0) {
            $deliveryExpenseAccount = $this->getCourierDeliveryExpenseAccount();
            $journalItems[] = [
                'account_id' => $deliveryExpenseAccount->id,
                'debit' => $courierCharge,
                'credit' => 0,
                'notes' => 'Courier Delivery Fee Deducted'.(! empty($data['courier_name']) ? ' by '.$data['courier_name'] : ''),
            ];
        }

        // 3. Credit Product Sales Revenue for gross sales
        if ($grossSales > 0) {
            $journalItems[] = [
                'account_id' => $salesAccount->id,
                'debit' => 0,
                'credit' => $grossSales,
                'notes' => 'Gross Delivered Sales Revenue',
            ];
        }

        // 4. Cost of Goods Sold (COGS): Debit Product Purchase Cost (Expense), Credit In Courier (Asset)
        if ($totalPurchaseCost > 0) {
            $journalItems[] = [
                'account_id' => $cogsAccount->id,
                'debit' => $totalPurchaseCost,
                'credit' => 0,
                'notes' => 'Product Cost of Goods Sold (Delivered)'.(! empty($data['items_summary']) ? ' ['.$data['items_summary'].']' : ''),
            ];

            $journalItems[] = [
                'account_id' => $inCourierAccount->id,
                'debit' => 0,
                'credit' => $totalPurchaseCost,
                'notes' => 'Relieved from In Courier asset upon delivery',
            ];
        }

        $description = $data['description'] ?? ('Courier Payout / Sales Settlement'.(! empty($data['courier_name']) ? ' ('.$data['courier_name'].')' : ''));

        return $this->createJournalEntry(
            [
                'entry_date' => $data['entry_date'] ?? now()->toDateString(),
                'reference' => $data['reference'] ?? null,
                'description' => $description,
                'source_type' => 'CourierPayout',
            ],
            $journalItems
        );
    }

    /**
     * Record returned products from courier back into warehouse Inventory.
     * Transfers purchase cost from 'In Courier' asset back to 'Inventory / Product Stock' asset.
     * If a courier return fee was charged, records it as an expense and credits the payment account.
     *
     * @param  array{
     *     entry_date: string,
     *     courier_name?: ?string,
     *     tracking_code?: ?string,
     *     notes?: ?string,
     *     total_purchase_cost: float,
     *     return_charge?: float,
     *     paid_from_account_id?: ?int,
     *     items_summary?: ?string
     * }  $data
     */
    public function recordCourierReturn(array $data): JournalEntry
    {
        $inCourierAccount = $this->getInCourierAccount();
        $inventoryAccount = $this->getInventoryAccount();
        $totalCost = (float) $data['total_purchase_cost'];
        $returnCharge = (float) ($data['return_charge'] ?? 0);
        $paidFromAccountId = ! empty($data['paid_from_account_id']) ? (int) $data['paid_from_account_id'] : null;

        $description = 'Courier Return Restocked'.(! empty($data['courier_name']) ? ': '.$data['courier_name'] : '');
        if (! empty($data['tracking_code'])) {
            $description .= ' (Tracking: '.$data['tracking_code'].')';
        }
        if (! empty($data['notes'])) {
            $description .= ' - '.$data['notes'];
        }

        $journalItems = [];

        // 1. Move value from In Courier back to Inventory
        if ($totalCost > 0) {
            $journalItems[] = [
                'account_id' => $inventoryAccount->id,
                'debit' => $totalCost,
                'credit' => 0,
                'notes' => 'Returned stock restocked to warehouse'.(! empty($data['items_summary']) ? ' ['.$data['items_summary'].']' : ''),
            ];

            $journalItems[] = [
                'account_id' => $inCourierAccount->id,
                'debit' => 0,
                'credit' => $totalCost,
                'notes' => 'Relieved from In Courier asset upon return',
            ];
        }

        // 2. Return fee / courier penalty charge if any
        if ($returnCharge > 0 && $paidFromAccountId) {
            $deliveryExpenseAccount = $this->getCourierDeliveryExpenseAccount();
            $journalItems[] = [
                'account_id' => $deliveryExpenseAccount->id,
                'debit' => $returnCharge,
                'credit' => 0,
                'notes' => 'Courier Return / Damage Fee'.(! empty($data['courier_name']) ? ' ('.$data['courier_name'].')' : ''),
            ];

            $journalItems[] = [
                'account_id' => $paidFromAccountId,
                'debit' => 0,
                'credit' => $returnCharge,
                'notes' => 'Paid courier return fee',
            ];
        }

        return $this->createJournalEntry(
            [
                'entry_date' => $data['entry_date'] ?? now()->toDateString(),
                'reference' => $data['tracking_code'] ?? null,
                'description' => $description,
                'source_type' => 'CourierReturn',
            ],
            $journalItems
        );
    }

    /**
     * Get or create the 'Damaged & Lost Goods Expense' account.
     */
    public function getDamagedGoodsExpenseAccount(): Account
    {
        return Account::where('code', '5011')
            ->orWhere('name', 'like', '%Damaged%')
            ->orWhere('name', 'like', '%Spoiled%')
            ->first() ?? Account::firstOrCreate(
                ['code' => '5011'],
                [
                    'name' => 'Damaged & Lost Goods Expense',
                    'type' => Account::TYPE_EXPENSE,
                    'is_system' => true,
                    'is_active' => true,
                ]
            );
    }

    /**
     * Get or create the 'Advertising & Marketing Cost' expense account.
     */
    public function getMarketingExpenseAccount(): Account
    {
        return Account::where('code', '5002')
            ->orWhere('name', 'like', '%Advertising%')
            ->orWhere('name', 'like', '%Marketing%')
            ->first() ?? Account::firstOrCreate(
                ['code' => '5002'],
                [
                    'name' => 'Advertising & Marketing Cost',
                    'type' => Account::TYPE_EXPENSE,
                    'is_system' => true,
                    'is_active' => true,
                ]
            );
    }

    /**
     * Get or create the 'Owner\'s Drawings / Withdrawals' equity account.
     */
    public function getOwnersDrawingsAccount(): Account
    {
        return Account::where('code', '3002')
            ->orWhere('name', 'like', '%Drawings%')
            ->orWhere('name', 'like', '%Withdrawals%')
            ->first() ?? Account::firstOrCreate(
                ['code' => '3002'],
                [
                    'name' => "Owner's Drawings / Withdrawals",
                    'type' => Account::TYPE_EQUITY,
                    'is_system' => true,
                    'is_active' => true,
                ]
            );
    }

    /**
     * Record a stock adjustment / inventory write-off (damaged goods, promotional gift, personal drawing, etc.).
     * Decreases 'Inventory / Product Stock (#1004)' asset account and debits the designated expense or drawing account.
     *
     * @param  array{
     *     entry_date: string,
     *     reason: string,
     *     debit_account_id: int,
     *     total_purchase_cost: float,
     *     notes?: ?string,
     *     reference?: ?string,
     *     items_summary?: ?string
     * }  $data
     */
    public function recordStockAdjustment(array $data): JournalEntry
    {
        $inventoryAccount = $this->getInventoryAccount();
        $debitAccount = Account::findOrFail($data['debit_account_id']);
        $totalCost = (float) $data['total_purchase_cost'];

        $reasonLabels = [
            'damage' => 'Damaged / Broken / Expired Goods (পণ্য নষ্ট/ড্যামেজ)',
            'gift' => 'Promotional Gift / Sample (বিজ্ঞাপন ও উপহার)',
            'drawing' => "Owner's Personal Use / Gift (ব্যক্তিগত উত্তোলন)",
            'other' => 'Other Stock Adjustment (অন্যান্য স্টক সমন্বয়)',
        ];
        $reasonText = $reasonLabels[$data['reason']] ?? ucfirst($data['reason']);

        $description = 'Stock Adjustment: '.$reasonText;
        if (! empty($data['notes'])) {
            $description .= ' - '.$data['notes'];
        }

        return $this->createJournalEntry(
            [
                'entry_date' => $data['entry_date'] ?? now()->toDateString(),
                'reference' => $data['reference'] ?? null,
                'description' => $description,
                'source_type' => 'StockAdjustment',
            ],
            [
                [
                    'account_id' => $debitAccount->id,
                    'debit' => $totalCost,
                    'credit' => 0,
                    'notes' => 'Stock write-off: '.$reasonText.(! empty($data['items_summary']) ? ' ['.$data['items_summary'].']' : ''),
                ],
                [
                    'account_id' => $inventoryAccount->id,
                    'debit' => 0,
                    'credit' => $totalCost,
                    'notes' => 'Inventory asset reduced for stock write-off',
                ],
            ]
        );
    }
}
