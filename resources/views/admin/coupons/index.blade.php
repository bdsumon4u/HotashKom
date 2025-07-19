@extends('layouts.light.master')
@section('title', 'Coupons')

@section('breadcrumb-title')
    <h3>Coupons</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Coupons</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>All Coupons</h5>
                        <div class="card-header-right">
                            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Create Coupon
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Discount</th>
                                        <th>Usage</th>
                                        <th>Expires</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($coupons as $coupon)
                                        <tr>
                                            <td>
                                                <strong>{{ $coupon->code }}</strong>
                                                @if($coupon->description)
                                                    <br><small class="text-muted">{{ $coupon->description }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $coupon->name }}</td>
                                            <td>{{ $coupon->discount_text }}</td>
                                            <td>
                                                {{ $coupon->used_count }}
                                                @if($coupon->max_usages)
                                                    / {{ $coupon->max_usages }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($coupon->expires_at)
                                                    {{ $coupon->expires_at->format('M d, Y') }}
                                                    @if($coupon->expires_at->isPast())
                                                        <br><small class="text-danger">Expired</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No expiry</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($coupon->isValid())
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                                       class="btn btn-sm btn-info" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.coupons.show', $coupon) }}"
                                                       class="btn btn-sm btn-secondary" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <form action="{{ route('admin.coupons.toggle-status', $coupon) }}"
                                                          method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-{{ $coupon->is_active ? 'warning' : 'success' }}"
                                                                title="{{ $coupon->is_active ? 'Deactivate' : 'Activate' }}">
                                                            <i class="fa fa-{{ $coupon->is_active ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.coupons.destroy', $coupon) }}"
                                                          method="POST" style="display: inline;"
                                                          onsubmit="return confirm('Are you sure you want to delete this coupon?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No coupons found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $coupons->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
