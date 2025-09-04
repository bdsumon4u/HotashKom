<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Services\PurchaseStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of purchases with stock reports.
     */
    public function index()
    {
        return view('admin.purchases.index', Product::stockStatistics());
    }

    /**
     * Show the form for creating a new purchase.
     */
    public function create()
    {
        return view('admin.purchases.create');
    }

    /**
     * Store a newly created purchase.
     */
    public function store()
    {
        // This will be handled by the Livewire component
        return redirect()->route('admin.purchases.index');
    }

    /**
     * Display the specified purchase.
     */
    public function show(Purchase $purchase)
    {
        return view('admin.purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified purchase.
     */
    public function edit(Purchase $purchase)
    {
        return view('admin.purchases.edit', compact('purchase'));
    }

    /**
     * Update the specified purchase.
     */
    public function update(Request $request, Purchase $purchase)
    {
        // This will be handled by the Livewire component
        return redirect()->route('admin.purchases.index');
    }

    /**
     * Remove the specified purchase from storage.
     */
    public function destroy(Purchase $purchase)
    {
        DB::transaction(function () use ($purchase) {
            // Use service to revert stock changes
            $stockService = new PurchaseStockService;
            $stockService->revertStockChanges($purchase);

            // Delete the purchase
            $purchase->delete();
        });

        return redirect()->route('admin.purchases.index')
            ->with('success', 'Purchase record deleted successfully!');
    }
}
