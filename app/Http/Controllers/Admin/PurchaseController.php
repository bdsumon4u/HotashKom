<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\AccountingService;
use App\Services\PurchaseStockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of purchases with stock reports.
     */
    public function index()
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');

        $stockStats = Product::stockStatistics();

        $monthlyPurchases = Purchase::selectRaw("
                DATE_FORMAT(purchase_date, '%Y-%m') as month,
                COUNT(*) as total_orders,
                COALESCE(SUM(total_amount), 0) as total_amount,
                COALESCE(SUM(paid_amount), 0) as total_paid,
                COALESCE(SUM(due_amount), 0) as total_due,
                SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_count,
                SUM(CASE WHEN payment_status = 'partial' THEN 1 ELSE 0 END) as partial_count,
                SUM(CASE WHEN payment_status = 'unpaid' THEN 1 ELSE 0 END) as unpaid_count
            ")
            ->whereNotNull('purchase_date')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($row) {
                $row->month_label = Carbon::parse($row->month.'-01')->format('F Y');

                return $row;
            });

        $purchaseTotals = [
            'orders' => (int) $monthlyPurchases->sum('total_orders'),
            'amount' => (float) $monthlyPurchases->sum('total_amount'),
            'paid' => (float) $monthlyPurchases->sum('total_paid'),
            'due' => (float) $monthlyPurchases->sum('total_due'),
            'paid_count' => (int) $monthlyPurchases->sum('paid_count'),
            'partial_count' => (int) $monthlyPurchases->sum('partial_count'),
            'unpaid_count' => (int) $monthlyPurchases->sum('unpaid_count'),
        ];

        return view('admin.purchases.index', array_merge($stockStats, [
            'monthlyPurchases' => $monthlyPurchases,
            'purchaseTotals' => $purchaseTotals,
        ]));
    }

    /**
     * Show the form for creating a new purchase.
     */
    public function create()
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');

        return view('admin.purchases.create');
    }

    /**
     * Store a newly created purchase.
     */
    public function store()
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');

        // This will be handled by the Livewire component
        return to_route('admin.purchases.index');
    }

    /**
     * Display the specified purchase.
     */
    public function show(Purchase $purchase)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');

        return view('admin.purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified purchase.
     */
    public function edit(Purchase $purchase)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');

        return view('admin.purchases.edit', compact('purchase'));
    }

    /**
     * Update the specified purchase.
     */
    public function update(Request $request, Purchase $purchase)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');

        // This will be handled by the Livewire component
        return to_route('admin.purchases.index');
    }

    /**
     * Remove the specified purchase from storage.
     */
    public function destroy(Purchase $purchase)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');

        DB::transaction(function () use ($purchase): void {
            // Delete linked accounting journal entry if any
            $linkedEntry = JournalEntry::where('source_type', Purchase::class)
                ->where('source_id', $purchase->id)
                ->first();
            if ($linkedEntry) {
                app(AccountingService::class)->deleteJournalEntry($linkedEntry);
            }

            // Use service to revert stock changes
            $stockService = new PurchaseStockService;
            $stockService->revertStockChanges($purchase);

            // Delete linked payments & recalculate supplier due
            $supplierId = $purchase->supplier_id;
            $purchase->payments()->delete();
            if ($supplierId && ($supplier = Supplier::find($supplierId))) {
                $supplier->recalculateDue();
            }

            // Delete the purchase
            $purchase->delete();
        });

        return to_route('admin.purchases.index')
            ->with('success', 'Purchase record deleted and accounting ledger updated successfully!');
    }
}
