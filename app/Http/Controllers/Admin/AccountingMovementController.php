<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AccountingMovementController extends Controller
{
    public function shipments(Request $request): View
    {
        return $this->history($request, 'Shipment', 'Shipment History');
    }

    public function returns(Request $request): View
    {
        return $this->history($request, 'CourierReturn', 'Courier Return History');
    }

    public function show(JournalEntry $journalEntry): View
    {
        abort_unless(request()->user()->is('admin'), 403);
        abort_unless(in_array($journalEntry->source_type, ['Shipment', 'CourierReturn', 'CourierPayout', 'StockAdjustment'], true), 404);

        $journalEntry->load('items.account');
        $details = $journalEntry->details;

        if ($details === null) {
            $legacyNotes = $journalEntry->items
                ->first(fn ($item) => preg_match('/\[(.*)\]\s*$/u', (string) $item->notes) === 1)
                ?->notes;

            if (preg_match('/\[(.*)\]\s*$/u', (string) $legacyNotes, $summaryMatch) === 1) {
                preg_match_all('/(.+?) \(x(\d+)\)(?:, |$)/u', $summaryMatch[1], $productMatches, PREG_SET_ORDER);
                $details = collect($productMatches)->map(fn (array $product): array => [
                    'product_name' => trim($product[1]),
                    'quantity' => (int) $product[2],
                ])->all();
            }
        }

        return view('admin.accounting.movements.show', compact('journalEntry', 'details'));
    }

    public function adjustments(Request $request): View
    {
        abort_unless(request()->user()->is('admin'), 403);

        $startDate = $this->dateValue($request->input('start_d'), now()->startOfMonth());
        $endDate = $this->dateValue($request->input('end_d'), now()->endOfMonth());

        $entries = JournalEntry::query()
            ->where('source_type', 'StockAdjustment')
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->with('items.account')
            ->latest('entry_date')
            ->latest('id')
            ->get();

        $mapped = $entries->map(function ($entry) {
            $debitItem = $entry->items->firstWhere('debit', '>', 0);
            $amount = (float) ($debitItem?->debit ?? $entry->totalDebit());

            $reason = 'other';
            $desc = strtolower((string) $entry->description);
            if (str_contains($desc, 'damaged') || str_contains($desc, 'damage') || str_contains($desc, 'expired')) {
                $reason = 'damage';
            } elseif (str_contains($desc, 'gift') || str_contains($desc, 'promotional') || str_contains($desc, 'sample')) {
                $reason = 'gift';
            } elseif (str_contains($desc, 'drawing') || str_contains($desc, 'personal')) {
                $reason = 'drawing';
            }

            return [
                'id' => $entry->id,
                'entry_number' => $entry->entry_number,
                'entry_date' => $entry->entry_date?->format('Y-m-d'),
                'date_formatted' => $entry->entry_date?->format('M d, Y'),
                'description' => $entry->description,
                'notes' => $debitItem?->notes,
                'target_account' => $debitItem?->account?->name ?? 'Expense',
                'amount' => $amount,
                'reason' => $reason,
            ];
        });

        $monthlySummary = $mapped->groupBy(function ($item) {
            return substr((string) $item['entry_date'], 0, 7);
        })->map(function ($group, $monthKey) {
            return [
                'month' => $monthKey,
                'month_label' => Carbon::parse($monthKey.'-01')->format('F Y'),
                'count' => $group->count(),
                'total_amount' => (float) $group->sum('amount'),
                'damage_count' => $group->where('reason', 'damage')->count(),
                'damage_amount' => (float) $group->where('reason', 'damage')->sum('amount'),
                'gift_count' => $group->where('reason', 'gift')->count(),
                'gift_amount' => (float) $group->where('reason', 'gift')->sum('amount'),
                'drawing_count' => $group->where('reason', 'drawing')->count(),
                'drawing_amount' => (float) $group->where('reason', 'drawing')->sum('amount'),
                'other_count' => $group->where('reason', 'other')->count(),
                'other_amount' => (float) $group->where('reason', 'other')->sum('amount'),
            ];
        })->sortKeysDesc();

        $dailySummary = $mapped->groupBy('entry_date')->map(function ($group, $date) {
            return [
                'date' => $date,
                'date_formatted' => Carbon::parse($date)->format('M d, Y'),
                'count' => $group->count(),
                'total_amount' => (float) $group->sum('amount'),
            ];
        })->sortKeysDesc();

        $grandTotals = [
            'count' => $mapped->count(),
            'total_amount' => (float) $mapped->sum('amount'),
            'damage_amount' => (float) $mapped->where('reason', 'damage')->sum('amount'),
            'gift_amount' => (float) $mapped->where('reason', 'gift')->sum('amount'),
            'drawing_amount' => (float) $mapped->where('reason', 'drawing')->sum('amount'),
            'other_amount' => (float) $mapped->where('reason', 'other')->sum('amount'),
        ];

        return view('admin.accounting.adjustments.index', compact(
            'startDate',
            'endDate',
            'mapped',
            'monthlySummary',
            'dailySummary',
            'grandTotals'
        ));
    }

    private function history(Request $request, string $sourceType, string $title): View
    {
        abort_unless(request()->user()->is('admin'), 403);

        if ($request->filled('month')) {
            $monthInput = (string) $request->input('month');
            $startDate = Carbon::parse($monthInput.'-01')->startOfMonth()->toDateString();
            $endDate = Carbon::parse($monthInput.'-01')->endOfMonth()->toDateString();
        } else {
            $startDate = $this->dateValue($request->input('start_d'), now()->startOfMonth());
            $endDate = $this->dateValue($request->input('end_d'), now()->endOfMonth());
        }

        // All-time entries of this sourceType for monthly summary
        $allEntries = JournalEntry::where('source_type', $sourceType)
            ->with('items.account')
            ->orderBy('entry_date', 'desc')
            ->get();

        $monthlyBreakdown = $allEntries->groupBy(function ($entry) {
            return $entry->entry_date ? $entry->entry_date->format('Y-m') : 'unknown';
        })->reject(fn ($group, $key) => $key === 'unknown')
            ->map(function ($group, $monthKey) {
                $totalCost = 0.0;
                $totalQty = 0;

                foreach ($group as $entry) {
                    $details = $entry->details ?? [];
                    $inventoryItem = $entry->items->first(fn ($item) => $item->account?->code === '1004');
                    $cost = $inventoryItem
                        ? (float) ($inventoryItem->debit ?: $inventoryItem->credit)
                        : (float) collect($details)->sum(fn ($d) => (float) ($d['line_purchase_total'] ?? ((float) ($d['purchase_price'] ?? 0) * (int) ($d['quantity'] ?? 0))));

                    $qty = (int) collect($details)->sum(fn ($d) => (int) ($d['quantity'] ?? 0));

                    $totalCost += $cost;
                    $totalQty += $qty;
                }

                return [
                    'month' => $monthKey,
                    'month_label' => Carbon::parse($monthKey.'-01')->format('F Y'),
                    'count' => $group->count(),
                    'total_qty' => $totalQty,
                    'total_cost' => $totalCost,
                ];
            });

        $monthTotals = [
            'count' => (int) $monthlyBreakdown->sum('count'),
            'total_qty' => (int) $monthlyBreakdown->sum('total_qty'),
            'total_cost' => (float) $monthlyBreakdown->sum('total_cost'),
        ];

        // Period statistics for selected month/range
        $periodEntries = $allEntries->filter(function ($entry) use ($startDate, $endDate) {
            $d = $entry->entry_date?->format('Y-m-d');

            return $d >= $startDate && $d <= $endDate;
        });

        $periodStats = [
            'count' => $periodEntries->count(),
            'total_qty' => 0,
            'total_cost' => 0.0,
        ];

        foreach ($periodEntries as $entry) {
            $details = $entry->details ?? [];
            $inventoryItem = $entry->items->first(fn ($item) => $item->account?->code === '1004');
            $cost = $inventoryItem
                ? (float) ($inventoryItem->debit ?: $inventoryItem->credit)
                : (float) collect($details)->sum(fn ($d) => (float) ($d['line_purchase_total'] ?? ((float) ($d['purchase_price'] ?? 0) * (int) ($d['quantity'] ?? 0))));

            $qty = (int) collect($details)->sum(fn ($d) => (int) ($d['quantity'] ?? 0));

            $periodStats['total_cost'] += $cost;
            $periodStats['total_qty'] += $qty;
        }

        $entries = JournalEntry::query()
            ->where('source_type', $sourceType)
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->with('items.account')
            ->latest('entry_date')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.accounting.movements.index', compact(
            'entries',
            'startDate',
            'endDate',
            'title',
            'sourceType',
            'monthlyBreakdown',
            'monthTotals',
            'periodStats'
        ));
    }

    private function dateValue(mixed $value, Carbon $fallback): string
    {
        try {
            return is_string($value) && $value !== ''
                ? Carbon::parse($value)->toDateString()
                : $fallback->toDateString();
        } catch (\Throwable) {
            return $fallback->toDateString();
        }
    }
}
