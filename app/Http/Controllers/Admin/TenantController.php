<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Tenant\CreateTenantAction;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Domain;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function __construct()
    {
        abort_unless(config('tenancy.enabled', true), 404);
    }

    /**
     * Display a listing of tenants.
     */
    public function index(): View
    {
        $tenants = Tenant::query()
            ->with(['domains', 'admins'])
            ->withCount(['products', 'orders'])
            ->latest()
            ->paginate(15);

        $totalTenants = Tenant::count();
        $totalDomains = Domain::count();
        $totalAdmins = Admin::whereNotNull('tenant_id')->count();

        return view('admin.tenants.index', compact('tenants', 'totalTenants', 'totalDomains', 'totalAdmins'));
    }

    /**
     * Store a newly created tenant in storage.
     */
    public function store(Request $request, CreateTenantAction $createTenantAction): RedirectResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:tenants,id'],
            'name' => ['required', 'string', 'max:255'],
            'custom_domain' => ['nullable', 'string', 'max:255', 'unique:domains,domain'],
            'admin_name' => ['nullable', 'string', 'max:255'],
            'admin_email' => ['nullable', 'email', 'max:255', 'unique:admins,email'],
            'admin_password' => ['nullable', 'string', 'min:6'],
        ]);

        $createTenantAction->execute($validated);

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant website created successfully!');
    }

    /**
     * Display the specified tenant.
     */
    public function show(Tenant $tenant): View
    {
        $tenant->load(['domains', 'admins']);

        return view('admin.tenants.show', compact('tenant'));
    }

    /**
     * Update the specified tenant in storage.
     */
    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $data = $tenant->data ?? [];
        $data['name'] = $validated['name'];
        $tenant->update(['data' => $data]);

        return back()->with('success', 'Tenant website updated successfully!');
    }

    /**
     * Remove the specified tenant from storage.
     */
    public function destroy(Tenant $tenant): RedirectResponse
    {
        $tenant->delete();

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant website deleted successfully.');
    }

    /**
     * Add a custom domain to the tenant.
     */
    public function addDomain(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:255', 'unique:domains,domain'],
        ]);

        $domain = preg_replace('#^https?://#', '', trim($validated['domain']));
        $domain = rtrim($domain, '/');

        $tenant->domains()->create([
            'domain' => $domain,
        ]);

        return back()->with('success', "Domain {$domain} added successfully.");
    }

    /**
     * Delete a domain from the tenant.
     */
    public function deleteDomain(Domain $domain): RedirectResponse
    {
        // Don't delete if it's the only domain
        if ($domain->tenant->domains()->count() <= 1) {
            return back()->with('error', 'Cannot delete the primary domain. Tenant must have at least one domain.');
        }

        $domainName = $domain->domain;
        $domain->delete();

        return back()->with('success', "Domain {$domainName} removed successfully.");
    }
}
