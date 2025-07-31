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
}
