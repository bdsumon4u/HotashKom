<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Tenant\CreateTenantAction;
use App\Actions\Tenant\DeleteTenantAction;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Domain;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
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
            'clone_products' => ['nullable', 'boolean'],
            'clone_categories' => ['nullable', 'boolean'],
            'clone_brands' => ['nullable', 'boolean'],
            'clone_landing_pages' => ['nullable', 'boolean'],
            'clone_sliders' => ['nullable', 'boolean'],
            'clone_home_sections' => ['nullable', 'boolean'],
            'clone_menus' => ['nullable', 'boolean'],
            'clone_pages' => ['nullable', 'boolean'],
            'clone_blogs' => ['nullable', 'boolean'],
        ]);

        $validated['clone_products'] = $request->boolean('clone_products');
        $validated['clone_categories'] = $request->boolean('clone_categories');
        $validated['clone_brands'] = $request->boolean('clone_brands');
        $validated['clone_landing_pages'] = $request->boolean('clone_landing_pages');
        $validated['clone_sliders'] = $request->boolean('clone_sliders');
        $validated['clone_home_sections'] = $request->boolean('clone_home_sections');
        $validated['clone_menus'] = $request->boolean('clone_menus');
        $validated['clone_pages'] = $request->boolean('clone_pages');
        $validated['clone_blogs'] = $request->boolean('clone_blogs');

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
     * Remove the specified tenant from storage and completely purge its data.
     */
    public function destroy(Tenant $tenant, DeleteTenantAction $deleteTenantAction): RedirectResponse
    {
        $tenantName = $tenant->data['name'] ?? $tenant->id;
        $deleteTenantAction->execute($tenant);

        return redirect()->route('admin.tenants.index')
            ->with('success', "Tenant website '{$tenantName}' and all associated data deleted successfully.");
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

    /**
     * Impersonate a tenant store by redirecting to a signed URL on the tenant domain.
     */
    public function impersonate(Tenant $tenant, Request $request): RedirectResponse
    {
        /** @var Admin|null $admin */
        $admin = auth('admin')->user();
        abort_unless($admin && ($admin->isSuperAdmin() || $admin->canAccessTenant($tenant)), 403, 'Unauthorized');

        $domain = $tenant->domains->first()?->domain;
        if (! $domain) {
            return back()->with('error', 'This store has no active domain configured.');
        }

        $scheme = $request->getScheme() ?: 'https';
        $tenantBase = "{$scheme}://{$domain}";

        URL::forceRootUrl($tenantBase);
        $signedUrl = URL::temporarySignedRoute(
            'admin.tenants.impersonate.login',
            now()->addMinutes(2),
            ['admin' => $admin->id]
        );
        URL::forceRootUrl(null);

        return redirect()->away($signedUrl);
    }

    /**
     * Authenticate impersonation request on the tenant domain.
     */
    public function impersonateLogin(Request $request, Admin $admin): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired impersonation link.');
        }

        if (! $admin->is_active) {
            abort(403, 'Admin account is inactive.');
        }

        if (tenancy()->initialized && ! $admin->canAccessTenant(tenant())) {
            abort(403, 'Unauthorized access for this tenant.');
        }

        auth('admin')->login($admin);
        $request->session()->regenerate();

        return redirect()->route('admin.home');
    }
}
