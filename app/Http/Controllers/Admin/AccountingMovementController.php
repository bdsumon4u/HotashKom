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
        abort_unless(in_array($journalEntry->source_type, ['Shipment', 'CourierReturn', 'CourierPayout'], true), 404);

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

    private function history(Request $request, string $sourceType, string $title): View
    {
        abort_unless(request()->user()->is('admin'), 403);

        $startDate = $this->dateValue($request->input('start_d'), now()->startOfMonth());
        $endDate = $this->dateValue($request->input('end_d'), now()->endOfMonth());

        $entries = JournalEntry::query()
            ->where('source_type', $sourceType)
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->with('items.account')
            ->latest('entry_date')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.accounting.movements.index', compact('entries', 'startDate', 'endDate', 'title', 'sourceType'));
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
