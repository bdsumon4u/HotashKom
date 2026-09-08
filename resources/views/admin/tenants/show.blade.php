@extends('layouts.light.master')
@section('title', 'Manage Store: ' . ($tenant->data['name'] ?? $tenant->id))

@push('css')
<style>
    .saas-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        overflow: hidden;
    }
    .store-avatar-lg {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #ffffff;
        font-weight: 700;
        font-size: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
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
        padding: 12px 16px;
    }
    .saas-table td {
        padding: 14px 16px;
        vertical-align: middle;
        border-top: 1px solid #f1f5f9;
        font-size: 13px;
    }
    .action-btn-sm {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s ease;
    }
</style>
@endpush

@section('breadcrumb-title')
    <div class="d-flex align-items-center">
        <a href="{{ route('admin.tenants.index') }}" class="btn btn-light btn-sm mr-2 border shadow-sm" style="border-radius: 6px;">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        <h3 class="mb-0 font-weight-bold text-dark">{{ $tenant->data['name'] ?? $tenant->id }}</h3>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('admin.tenants.index') }}">Tenant Stores</a></li>
    <li class="breadcrumb-item active">{{ $tenant->id }}</li>
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

    @php
        $primaryDomain = $tenant->domains->first()?->domain;
        $scheme = request()->getScheme();
        $storeName = $tenant->data['name'] ?? $tenant->id;
    @endphp

    <!-- Store Summary Hero Banner -->
    <div class="saas-card p-4 mb-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center" style="gap: 16px;">
            <div class="d-flex align-items-center">
                <div class="store-avatar-lg mr-3">
                    {{ strtoupper(substr($storeName, 0, 1)) }}
                </div>
                <div>
                    <h4 class="mb-1 font-weight-bold text-dark">{{ $storeName }}</h4>
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <span class="badge badge-light border text-muted" style="font-family: monospace;">ID: {{ $tenant->id }}</span>
                        <span class="text-muted" style="font-size: 12px;"><i class="fa fa-calendar-alt mr-1"></i> Created {{ $tenant->created_at?->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
            @if($primaryDomain)
                <div class="d-flex align-items-center" style="gap: 8px;">
                    <a href="{{ $scheme }}://{{ $primaryDomain }}" target="_blank" class="btn btn-outline-primary btn-sm px-3 shadow-sm font-weight-bold" style="border-radius: 6px;">
                        <i class="fa fa-external-link-alt mr-1"></i> Visit Store
                    </a>
                    <a href="{{ route('admin.tenants.impersonate', $tenant) }}" target="_blank" class="btn btn-primary btn-sm px-3 shadow-sm font-weight-bold" style="border-radius: 6px;">
                        <i class="fa fa-tachometer-alt mr-1"></i> Store Admin
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Store Information -->
        <div class="col-lg-6 mb-4">
            <div class="saas-card h-100">
                <div class="p-3 border-bottom bg-light">
                    <h6 class="mb-0 font-weight-bold text-dark"><i class="fa fa-cog mr-1 text-primary"></i> Store Configuration</h6>
                </div>
                <div class="p-4">
                    <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label class="font-weight-bold text-dark" style="font-size: 13px;">Tenant Database ID (Slug)</label>
                            <input type="text" class="form-control bg-light" value="{{ $tenant->id }}" disabled style="font-family: monospace;">
                            <small class="text-muted">Unique immutable tenant key for multi-tenant database records.</small>
                        </div>
                        <div class="form-group mb-4">
                            <label for="name" class="font-weight-bold text-dark" style="font-size: 13px;">Store Display Name</label>
                            <input type="text" name="name" id="name" class="form-control font-weight-bold" value="{{ old('name', $tenant->data['name'] ?? $tenant->id) }}" required>
                            <small class="text-muted">Public facing name shown across customer storefront.</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm px-3 font-weight-bold shadow-sm" style="border-radius: 6px;">
                            <i class="fa fa-save mr-1"></i> Update Store Name
                        </button>
                    </form>

                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 font-weight-bold text-dark" style="font-size: 13px;">
                            <i class="fa fa-user-shield mr-1 text-primary"></i> Assigned Store Administrator(s)
                        </h6>
                    </div>
                    <div class="border rounded" style="overflow: hidden;">
                        @forelse($tenant->admins as $admin)
                            <div class="p-3 d-flex justify-content-between align-items-center @if(!$loop->last) border-bottom @endif" style="background-color: #fafbfc;">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3 text-muted" style="font-size: 18px;">
                                        <i class="fa fa-user-circle"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark">{{ $admin->name }}</div>
                                        <div class="small text-muted">{{ $admin->email }}</div>
                                    </div>
                                </div>
                                <span class="badge badge-success px-2 py-1" style="border-radius: 6px; font-weight: 600;">Active</span>
                            </div>
                        @empty
                            <div class="p-3 text-center text-muted small">No store administrator assigned.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Domain Management -->
        <div class="col-lg-6 mb-4">
            <div class="saas-card h-100">
                <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-dark"><i class="fa fa-globe mr-1 text-primary"></i> Domains & Subdomains</h6>
                    <button class="btn btn-primary btn-sm px-3 shadow-sm font-weight-bold" style="border-radius: 6px;" data-toggle="modal" data-target="#addDomainModal">
                        <i class="fa fa-plus-circle mr-1"></i> Add Domain
                    </button>
                </div>
                <div>
                    <div class="table-responsive">
                        <table class="saas-table">
                            <thead>
                                <tr>
                                    <th>Domain Name</th>
                                    <th>Access Links</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tenant->domains as $domain)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-link mr-2 text-primary"></i>
                                                <span class="font-weight-bold text-dark">{{ $domain->domain }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-inline-flex" style="gap: 4px;">
                                                <a href="{{ $scheme }}://{{ $domain->domain }}" target="_blank" class="btn btn-light btn-sm border action-btn-sm" title="Visit Store">
                                                    <i class="fa fa-external-link-alt text-primary"></i> Store
                                                </a>
                                                <a href="{{ $scheme }}://{{ $domain->domain }}/admin" target="_blank" class="btn btn-light btn-sm border action-btn-sm" title="Visit Admin">
                                                    <i class="fa fa-tachometer-alt text-purple"></i> Admin
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            @if($tenant->domains->count() > 1)
                                                <form action="{{ route('admin.tenants.domains.delete', $domain) }}" method="POST" onsubmit="return confirm('Delete domain {{ $domain->domain }}?');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-light btn-sm border text-danger action-btn-sm" title="Delete Domain">
                                                        <i class="fa fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge badge-light border text-muted">Primary</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 bg-light border-top">
                        <div class="d-flex align-items-start" style="gap: 10px;">
                            <i class="fa fa-info-circle text-primary mt-1" style="font-size: 16px;"></i>
                            <div>
                                <h6 class="font-weight-bold text-dark mb-1" style="font-size: 13px;">Custom Domain DNS Instructions</h6>
                                <p class="text-muted small mb-0">
                                    To map a custom domain (e.g. <code>mystore.com</code>), create a DNS <code>CNAME</code> pointing to your central domain, or an <code>A Record</code> pointing to your server's public IP address.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danger Zone Card -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="saas-card" style="border-color: #fecaca;">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="background-color: #fef2f2;">
                    <h6 class="mb-0 font-weight-bold text-danger">
                        <i class="fa fa-exclamation-triangle mr-1"></i> Danger Zone
                    </h6>
                    <span class="badge badge-danger px-2 py-1" style="border-radius: 6px;">Irreversible Action</span>
                </div>
                <div class="p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center" style="gap: 16px;">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-1">Delete this Tenant Store</h6>
                        <p class="text-muted small mb-0">
                            Permanently deletes the store, custom domains, settings, catalog (products, categories, brands), customer orders, and administrator accounts. All data with <code>tenant_id = {{ $tenant->id }}</code> will be purged from all tables.
                        </p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-danger btn-sm px-3 font-weight-bold shadow-sm" style="border-radius: 6px; white-space: nowrap;" data-toggle="modal" data-target="#deleteTenantModal">
                            <i class="fa fa-trash-alt mr-1"></i> Delete Store
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Tenant Modal -->
<div class="modal fade" id="deleteTenantModal" tabindex="-1" role="dialog" aria-labelledby="deleteTenantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-danger text-white px-4 py-3">
                <h5 class="modal-title font-weight-bold" id="deleteTenantModalLabel">
                    <i class="fa fa-exclamation-triangle mr-1"></i> Delete Tenant Store
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-4 py-3">
                <p class="text-dark font-weight-bold mb-2">Are you sure you want to permanently delete store "{{ $storeName }}"?</p>
                <div class="alert alert-warning border-0 small mb-3" style="border-radius: 8px;">
                    <i class="fa fa-info-circle mr-1"></i> <strong>This action cannot be undone.</strong> All records associated with <code>tenant_id = "{{ $tenant->id }}"</code> across all tables will be immediately purged.
                </div>
                <p class="text-muted small mb-0">
                    Click the button below to confirm store deletion.
                </p>
            </div>
            <div class="modal-footer bg-light border-top px-4 py-3">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm px-4 shadow-sm font-weight-bold">
                        <i class="fa fa-trash-alt mr-1"></i> Yes, Delete Store
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Domain Modal -->
<div class="modal fade" id="addDomainModal" tabindex="-1" role="dialog" aria-labelledby="addDomainModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
            <form action="{{ route('admin.tenants.domains.add', $tenant) }}" method="POST">
                @csrf
                <div class="modal-header bg-light border-bottom px-4 py-3">
                    <h5 class="modal-title font-weight-bold text-dark" id="addDomainModalLabel">
                        <i class="fa fa-globe text-primary mr-1"></i> Add Domain / Subdomain
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="form-group mb-0">
                        <label for="domain" class="font-weight-bold text-dark" style="font-size: 13px;">Domain Name <span class="text-danger">*</span></label>
                        <input type="text" name="domain" id="domain" class="form-control" required placeholder="e.g. shop.customdomain.com or mybrand.com">
                        <small class="form-text text-muted">Enter host only (without <code>https://</code> or trailing slash).</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-3">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 font-weight-bold shadow-sm">
                        <i class="fa fa-plus mr-1"></i> Add Domain
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
