@extends('layouts.light.master')
@section('title', 'Tenant Stores Management')

@push('css')
<style>
    .saas-stat-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02);
        transition: all 0.2s ease-in-out;
    }
    .saas-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
    }
    .saas-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .saas-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        overflow: hidden;
    }
    .saas-table {
        margin-bottom: 0;
        width: 100%;
    }
    .saas-table th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border-top: none;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 16px;
        vertical-align: middle;
    }
    .saas-table td {
        padding: 16px;
        vertical-align: middle;
        border-top: 1px solid #f1f5f9;
        color: #334155;
        font-size: 13px;
    }
    .saas-table tbody tr:hover {
        background-color: #fbfcfe;
    }
    .store-avatar {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #ffffff;
        font-weight: 700;
        font-size: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);
    }
    .domain-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background-color: #f1f5f9;
        color: #1e293b;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none !important;
        transition: all 0.15s ease;
    }
    .domain-pill:hover {
        background-color: #e2e8f0;
        color: #0f172a;
        border-color: #cbd5e1;
    }
    .metric-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 9px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }
    .metric-badge-products {
        background-color: #eff6ff;
        color: #2563eb;
        border: 1px solid #dbeafe;
    }
    .metric-badge-orders {
        background-color: #ecfdf5;
        color: #059669;
        border: 1px solid #d1fae5;
    }
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background-color: #ffffff;
        color: #475569;
        font-size: 12px;
        transition: all 0.15s ease;
    }
    .action-btn:hover {
        background-color: #f8fafc;
        color: #0f172a;
        border-color: #cbd5e1;
    }
    .action-btn.btn-store:hover {
        background-color: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
    }
    .action-btn.btn-admin:hover {
        background-color: #f5f3ff;
        color: #7c3aed;
        border-color: #ddd6fe;
    }
    .action-btn.btn-danger-light:hover {
        background-color: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }
    .status-indicator-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #10b981;
        display: inline-block;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }
</style>
@endpush

@section('breadcrumb-title')
    <div class="d-flex align-items-center">
        <h3 class="mb-0 mr-2">Tenant Stores</h3>
        <span class="badge badge-light border text-muted" style="font-size: 11px; font-weight: 500;">
            <span class="status-indicator-dot mr-1"></span> Multi-Tenant SaaS
        </span>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">SaaS Management</li>
    <li class="breadcrumb-item active">Tenant Stores</li>
@endsection

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 10px;">
            <i class="fa fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 10px;">
            <i class="fa fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 10px;">
            <div class="font-weight-bold mb-1"><i class="fa fa-exclamation-triangle mr-1"></i> Please fix the following errors:</div>
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Top Stats Overview Cards -->
    <div class="row mb-4">
        <div class="col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="saas-stat-card p-3 d-flex align-items-center">
                <div class="saas-icon-box mr-3" style="background-color: #e0e7ff; color: #4338ca;">
                    <i class="fa fa-store"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">Total Stores</div>
                    <h4 class="mb-0 font-weight-bold" style="color: #0f172a;">{{ $totalTenants ?? $tenants->total() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="saas-stat-card p-3 d-flex align-items-center">
                <div class="saas-icon-box mr-3" style="background-color: #e0f2fe; color: #0369a1;">
                    <i class="fa fa-globe"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">Active Domains</div>
                    <h4 class="mb-0 font-weight-bold" style="color: #0f172a;">{{ $totalDomains ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3 mb-sm-0">
            <div class="saas-stat-card p-3 d-flex align-items-center">
                <div class="saas-icon-box mr-3" style="background-color: #fef3c7; color: #b45309;">
                    <i class="fa fa-user-shield"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">Store Admins</div>
                    <h4 class="mb-0 font-weight-bold" style="color: #0f172a;">{{ $totalAdmins ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="saas-stat-card p-3 d-flex align-items-center">
                <div class="saas-icon-box mr-3" style="background-color: #dcfce7; color: #15803d;">
                    <i class="fa fa-database"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">Architecture</div>
                    <div class="font-weight-bold" style="color: #15803d; font-size: 14px;">Single DB Isolation</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="row">
        <div class="col-sm-12">
            <div class="saas-card">
                <!-- Header with Title & Search & Action Button -->
                <div class="p-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center" style="background-color: #ffffff; gap: 12px;">
                    <div>
                        <h5 class="mb-0 font-weight-bold text-dark">Tenant Stores Directory</h5>
                        <small class="text-muted">Manage storefronts, subdomains, custom domains, and admin access</small>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <div class="input-group input-group-sm" style="width: 220px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0 text-muted"><i class="fa fa-search"></i></span>
                            </div>
                            <input type="text" id="tenantSearchInput" class="form-control border-left-0" placeholder="Filter stores..." onkeyup="filterTenants()">
                        </div>
                        <button class="btn btn-primary btn-sm px-3 shadow-sm font-weight-bold d-inline-flex align-items-center" style="border-radius: 6px; height: 33px;" data-toggle="modal" data-target="#createTenantModal">
                            <i class="fa fa-plus-circle mr-1"></i> Create Store
                        </button>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="table-responsive">
                    <table class="saas-table" id="tenantsTable">
                        <thead>
                            <tr>
                                <th>Store Name & ID</th>
                                <th>Domains / Subdomain</th>
                                <th>Store Admin</th>
                                <th class="text-center">Products</th>
                                <th class="text-center">Orders</th>
                                <th>Created</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tenants as $tenant)
                                @php
                                    $primaryDomain = $tenant->domains->first()?->domain;
                                    $scheme = request()->getScheme();
                                    $storeName = $tenant->data['name'] ?? $tenant->id;
                                    $admin = $tenant->admins->first();
                                @endphp
                                <tr class="tenant-row" data-search="{{ strtolower($storeName . ' ' . $tenant->id . ' ' . $tenant->domains->pluck('domain')->implode(' ') . ' ' . ($admin?->email ?? '')) }}">
                                    <!-- Store Info -->
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="store-avatar mr-3">
                                                {{ strtoupper(substr($storeName, 0, 1)) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.tenants.show', $tenant) }}" class="font-weight-bold text-dark d-block text-decoration-none" style="font-size: 14px;">
                                                    {{ $storeName }}
                                                </a>
                                                <div class="d-flex align-items-center mt-1" style="gap: 6px;">
                                                    <span class="badge badge-light border text-muted" style="font-family: monospace; font-size: 11px; padding: 2px 6px;">
                                                        ID: {{ $tenant->id }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Domains -->
                                    <td>
                                        <div class="d-flex flex-column" style="gap: 6px;">
                                            @foreach($tenant->domains as $domain)
                                                <div>
                                                    <a href="{{ $scheme }}://{{ $domain->domain }}" target="_blank" class="domain-pill" title="Open {{ $domain->domain }}">
                                                        <i class="fa fa-globe text-primary" style="font-size: 11px;"></i>
                                                        <span>{{ $domain->domain }}</span>
                                                        <i class="fa fa-external-link-alt text-muted" style="font-size: 9px;"></i>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    <!-- Admin User -->
                                    <td>
                                        @if($admin)
                                            <div class="d-flex align-items-center">
                                                <div class="mr-2 text-muted" style="font-size: 13px;">
                                                    <i class="fa fa-user-circle"></i>
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold text-dark" style="font-size: 13px;">{{ $admin->name }}</div>
                                                    <div class="text-muted small">{{ $admin->email }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="badge badge-light text-muted border">No Admin Assigned</span>
                                        @endif
                                    </td>

                                    <!-- Products Count -->
                                    <td class="text-center">
                                        <span class="metric-badge metric-badge-products">
                                            <i class="fa fa-box-open"></i> {{ $tenant->products_count }}
                                        </span>
                                    </td>

                                    <!-- Orders Count -->
                                    <td class="text-center">
                                        <span class="metric-badge metric-badge-orders">
                                            <i class="fa fa-shopping-bag"></i> {{ $tenant->orders_count }}
                                        </span>
                                    </td>

                                    <!-- Created At -->
                                    <td>
                                        <div class="text-dark" style="font-size: 12px; font-weight: 500;">
                                            {{ $tenant->created_at?->format('d M Y') }}
                                        </div>
                                        <small class="text-muted">{{ $tenant->created_at?->diffForHumans() }}</small>
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="text-right">
                                        <div class="d-inline-flex align-items-center" style="gap: 6px;">
                                            @if($primaryDomain)
                                                <a href="{{ $scheme }}://{{ $primaryDomain }}" target="_blank" class="action-btn btn-store" title="Open Storefront" data-toggle="tooltip">
                                                    <i class="fa fa-external-link-alt"></i>
                                                </a>
                                                <a href="{{ $scheme }}://{{ $primaryDomain }}/admin" target="_blank" class="action-btn btn-admin" title="Login to Store Admin" data-toggle="tooltip">
                                                    <i class="fa fa-tachometer-alt"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('admin.tenants.show', $tenant) }}" class="action-btn" title="Manage Store & Domains" data-toggle="tooltip">
                                                <i class="fa fa-sliders-h"></i>
                                            </a>
                                            <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete \'{{ $storeName }}\' and all its isolated data?');" class="d-inline m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn btn-danger-light" title="Delete Store" data-toggle="tooltip">
                                                    <i class="fa fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="my-3">
                                            <div class="mb-3 text-muted" style="font-size: 42px;">
                                                <i class="fa fa-store-slash"></i>
                                            </div>
                                            <h6 class="font-weight-bold text-dark">No Tenant Stores Found</h6>
                                            <p class="text-muted small mb-3">Get started by creating your first independent tenant store.</p>
                                            <button class="btn btn-primary btn-sm px-3 shadow-sm" data-toggle="modal" data-target="#createTenantModal">
                                                <i class="fa fa-plus-circle mr-1"></i> Create First Store
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer Pagination -->
                @if($tenants->hasPages())
                    <div class="p-3 border-top d-flex justify-content-between align-items-center" style="background-color: #fafbfc;">
                        <small class="text-muted">Showing {{ $tenants->firstItem() }} to {{ $tenants->lastItem() }} of {{ $tenants->total() }} stores</small>
                        <div>
                            {{ $tenants->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modern Create Tenant Modal -->
<div class="modal fade" id="createTenantModal" tabindex="-1" role="dialog" aria-labelledby="createTenantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
            <form action="{{ route('admin.tenants.store') }}" method="POST" id="createTenantForm">
                @csrf
                <div class="modal-header bg-light border-bottom px-4 py-3">
                    <div>
                        <h5 class="modal-title font-weight-bold text-dark" id="createTenantModalLabel">
                            <i class="fa fa-plus-circle text-primary mr-1"></i> Create New Tenant Store
                        </h5>
                        <small class="text-muted">Provisions an independent store with isolated catalog, settings, and orders</small>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-4 py-3">
                    @php
                        $centralHost = parse_url(config('app.url', 'http://localhost'), PHP_URL_HOST) ?: 'localhost';
                    @endphp

                    <!-- Store Details Section -->
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="name" class="font-weight-bold text-dark" style="font-size: 13px;">
                                Store Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name" class="form-control" required placeholder="e.g. Trendy Fashion" value="{{ old('name') }}" onkeyup="autoGenerateSlug(this.value)">
                            <small class="form-text text-muted">Brand or business name displayed on the storefront.</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="id" class="font-weight-bold text-dark" style="font-size: 13px;">
                                Store Subdomain / Slug <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="text" name="id" id="id" class="form-control font-weight-bold text-primary" required placeholder="e.g. trendy-fashion" value="{{ old('id') }}">
                                <div class="input-group-append">
                                    <span class="input-group-text bg-light text-muted font-weight-bold" style="font-size: 12px;">.{{ $centralHost }}</span>
                                </div>
                            </div>
                            <small class="form-text text-muted">Full URL: <span class="text-primary font-weight-bold" id="urlPreview">https://trendy-fashion.{{ $centralHost }}</span></small>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="custom_domain" class="font-weight-bold text-dark" style="font-size: 13px;">
                            Custom Domain (Optional)
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light text-muted"><i class="fa fa-globe"></i></span>
                            </div>
                            <input type="text" name="custom_domain" id="custom_domain" class="form-control" placeholder="e.g. trendyfashion.com" value="{{ old('custom_domain') }}">
                        </div>
                        <small class="form-text text-muted">You can attach custom domains anytime. Point DNS CNAME/A record to this server.</small>
                    </div>

                    <!-- Admin User Credentials Section -->
                    <div class="p-3 mb-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fa fa-user-shield text-primary mr-2"></i>
                            <h6 class="mb-0 font-weight-bold text-dark" style="font-size: 13px;">Store Admin Credentials (Optional)</h6>
                        </div>
                        <p class="text-muted small mb-3">Credentials for the store manager to log in to <code>/admin</code>. Defaults are generated automatically.</p>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="admin_name" style="font-size: 12px; font-weight: 600;">Manager Name</label>
                                <input type="text" name="admin_name" id="admin_name" class="form-control form-control-sm" placeholder="Store Manager" value="{{ old('admin_name') }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="admin_email" style="font-size: 12px; font-weight: 600;">Login Email</label>
                                <input type="email" name="admin_email" id="admin_email" class="form-control form-control-sm" placeholder="Default: slug@{{ $centralHost }}" value="{{ old('admin_email') }}">
                            </div>
                            <div class="col-md-12 form-group mb-0">
                                <label for="admin_password" style="font-size: 12px; font-weight: 600;">Password</label>
                                <input type="password" name="admin_password" id="admin_password" class="form-control form-control-sm" placeholder="Default: password">
                                <small class="text-muted">Leave empty to use default password: <code>password</code></small>
                            </div>
                        </div>
                    </div>

                    <div class="p-2 px-3 d-flex align-items-center" style="background-color: #eff6ff; border-radius: 6px; border: 1px solid #dbeafe;">
                        <i class="fa fa-magic text-primary mr-2" style="font-size: 14px;"></i>
                        <small class="text-primary font-weight-bold">
                            Automatic Baseline Provisioning: Settings (delivery charges, checkout options, theme) are cloned automatically.
                        </small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-3">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm font-weight-bold">
                        <i class="fa fa-check mr-1"></i> Provision & Create Store
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const centralHost = '{{ $centralHost }}';

    function autoGenerateSlug(name) {
        let slugField = document.getElementById('id');
        if (!slugField.dataset.customized) {
            let slug = name.toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');
            slugField.value = slug;
            updatePreview(slug);
        }
    }

    function updatePreview(slug) {
        let preview = document.getElementById('urlPreview');
        let emailField = document.getElementById('admin_email');
        if (slug) {
            if (preview) preview.innerText = `https://${slug}.${centralHost}`;
            if (emailField && !emailField.value) emailField.placeholder = `Default: ${slug}@${centralHost}`;
        } else {
            if (preview) preview.innerText = `https://your-store.${centralHost}`;
        }
    }

    document.getElementById('id').addEventListener('input', function() {
        this.dataset.customized = 'true';
        updatePreview(this.value);
    });

    function filterTenants() {
        let filter = document.getElementById('tenantSearchInput').value.toLowerCase();
        let rows = document.querySelectorAll('.tenant-row');
        rows.forEach(function(row) {
            let searchContent = row.getAttribute('data-search') || '';
            if (searchContent.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Initialize tooltips if Bootstrap tooltip is active
    document.addEventListener('DOMContentLoaded', function() {
        if (window.jQuery && jQuery.fn.tooltip) {
            jQuery('[data-toggle="tooltip"]').tooltip();
        }
    });
</script>
@endsection
