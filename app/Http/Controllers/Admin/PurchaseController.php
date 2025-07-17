<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;

class PurchaseController extends Controller
{
    /**
     * Display a listing of purchases with stock reports.
     */
    public function index()
    {
        // Get all parent products with should_track=true
        $parents = Product::whereNull('parent_id')->where('should_track', true)->with(['variations' => function ($q) {
            $q->where('should_track', true);
        }])->get();

        $totalStockCount = 0;
        $totalStockValue = 0;
        $totalPurchaseValue = 0;

        foreach ($parents as $product) {
            if ($product->variations->count() > 0) {
                foreach ($product->variations as $variant) {
                    $count = is_numeric($variant->stock_count) ? $variant->stock_count : 0;
                    $totalStockCount += $count;
                    $totalStockValue += $count * (is_numeric($variant->selling_price) ? $variant->selling_price : 0);
                    $totalPurchaseValue += $count * (is_numeric($variant->average_purchase_price) ? $variant->average_purchase_price : 0);
                }
            } else {
                $count = is_numeric($product->stock_count) ? $product->stock_count : 0;
                $totalStockCount += $count;
                $totalStockValue += $count * (is_numeric($product->selling_price) ? $product->selling_price : 0);
                $totalPurchaseValue += $count * (is_numeric($product->average_purchase_price) ? $product->average_purchase_price : 0);
            }
        }

        $totalPurchaseRecords = Purchase::count();

        return view('admin.purchases.index', compact(
            'totalStockCount',
            'totalStockValue',
            'totalPurchaseValue',
            'totalPurchaseRecords'
        ));
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
}
