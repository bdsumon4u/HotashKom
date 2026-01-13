<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.coupons.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'description' => ['nullable', 'string'],
            'coupon_type' => ['required', 'in:subscription,purchase'],
            'discount_type' => ['required', 'in:fixed,percent'],
            'discount' => ['required', 'numeric', 'min:0'],
            'max_usages' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        Coupon::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'coupon_type' => $request->coupon_type,
            'discount_type' => $request->discount_type,
            'discount' => $request->discount,
            'max_usages' => $request->max_usages,
            'expires_at' => $request->expires_at,
            'is_active' => $request->has('is_active'),
        ]);

        return to_route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        return view('admin.coupons.show', compact('coupon'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => 'required|string|max:50|unique:coupons,code,'.$coupon->id,
            'description' => ['nullable', 'string'],
            'coupon_type' => ['required', 'in:subscription,purchase'],
            'discount_type' => ['required', 'in:fixed,percent'],
            'discount' => ['required', 'numeric', 'min:0'],
            'max_usages' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $coupon->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'coupon_type' => $request->coupon_type,
            'discount_type' => $request->discount_type,
            'discount' => $request->discount,
            'max_usages' => $request->max_usages,
            'expires_at' => $request->expires_at,
            'is_active' => $request->has('is_active'),
        ]);

        return to_route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return to_route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }

    /**
     * Generate a random coupon code
     */
    public function generateCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Coupon::where('code', $code)->exists());

        return response()->json(['code' => $code]);
    }

    /**
     * Toggle coupon status
     */
    public function toggleStatus(Coupon $coupon)
    {
        $coupon->update(['is_active' => ! $coupon->is_active]);

        return to_route('admin.coupons.index')
            ->with('success', 'Coupon status updated successfully.');
    }
}
