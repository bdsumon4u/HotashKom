<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class ResellerController extends Controller
{
    /**
     * Display a listing of the resellers.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $status = $request->query('status');

        return view('admin.resellers.index', compact('status'));
    }

    /**
     * Show the form for editing the specified reseller.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $reseller = User::findOrFail($id);
        $tenants = Tenant::with('domains')->get();

        return view('admin.resellers.edit', compact('reseller', 'tenants'));
    }

    /**
     * Update the specified reseller in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, User $reseller)
    {
        $validated = $request->mergeIfMissing([
            'is_verified' => 0,
            'is_active' => 0,
        ])->validate(array_merge([
            'name' => 'required|string|max:255',
            'shop_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$reseller->id,
            'phone_number' => 'required|string|max:255',
            'bkash_number' => 'required|string|max:255',
            'is_verified' => 'boolean',
            'new_password' => 'nullable|string|min:8',
            'tenant_id' => 'nullable|string|exists:tenants,id|unique:users,tenant_id,'.$reseller->id,
        ], config('app.demo') ? [] : [
            'is_active' => 'boolean',
            'order_prefix' => 'nullable|string|max:255',
            'domain' => 'nullable|string|max:255',
            'db_host' => 'nullable|string|max:255',
            'db_name' => 'nullable|string|max:255',
            'db_username' => 'nullable|string|max:255',
            'db_password' => 'nullable|string|min:6',
        ]));

        // Handle password reset
        if (! empty($validated['new_password'])) {
            $validated['password'] = Hash::make($validated['new_password']);
            unset($validated['new_password']);
        }

        // Only update database password if provided
        if (empty($validated['db_password'])) {
            unset($validated['db_password']);
        }

        // Auto-populate domain from tenant if linking to a tenant and domain is empty
        if (! empty($validated['tenant_id']) && empty($validated['domain'])) {
            $tenant = Tenant::with('domains')->find($validated['tenant_id']);
            if ($tenant && $firstDomain = $tenant->domains->first()) {
                $validated['domain'] = $firstDomain->domain;
            }
        }

        // Ensure empty string is converted to null for tenant_id
        if (empty($validated['tenant_id'])) {
            $validated['tenant_id'] = null;
        }

        $reseller->update($validated);

        return back()->with('success', 'Reseller updated successfully');
    }
}
