<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransactionCategory;
use Illuminate\Http\Request;

class TransactionCategoryController extends Controller
{
    public function index()
    {
        abort_unless(request()->user()->is('admin'), 403);

        $categories = TransactionCategory::withCount('journalEntryItems')->latest('id')->get();

        return view('admin.accounting.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        abort_unless(request()->user()->is('admin'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:expense,income,both'],
            'description' => ['nullable', 'string'],
        ]);

        TransactionCategory::create($validated);

        return back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, TransactionCategory $category)
    {
        abort_unless(request()->user()->is('admin'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:expense,income,both'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $category->update($validated);

        return back()->with('success', 'Category updated successfully.');
    }

    public function destroy(TransactionCategory $category)
    {
        abort_unless(request()->user()->is('admin'), 403);

        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }
}
