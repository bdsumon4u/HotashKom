@extends('layouts.light.master')
@section('title', 'Coupon Details')

@section('breadcrumb-title')
    <h3>Coupon Details</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>
    <li class="breadcrumb-item">{{ $coupon->name }}</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Coupon Details: {{ $coupon->name }}</h5>
                        <div class="card-header-right">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-info btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="150">Coupon Code:</th>
                                        <td><strong>{{ $coupon->code }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Name:</th>
                                        <td>{{ $coupon->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Description:</th>
                                        <td>{{ $coupon->description ?: 'No description' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Discount Amount:</th>
                                        <td><strong>{{ $coupon->discount_text }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="150">Status:</th>
                                        <td>
                                            @if($coupon->isValid())
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Usage:</th>
                                        <td>
                                            {{ $coupon->used_count }}
                                            @if($coupon->max_usages)
                                                / {{ $coupon->max_usages }}
                                            @else
                                                / Unlimited
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Expires:</th>
                                        <td>
                                            @if($coupon->expires_at)
                                                {{ $coupon->expires_at->format('M d, Y H:i') }}
                                                @if($coupon->expires_at->isPast())
                                                    <br><small class="text-danger">Expired</small>
                                                @endif
                                            @else
                                                <span class="text-muted">No expiry</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created:</th>
                                        <td>{{ $coupon->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Updated:</th>
                                        <td>{{ $coupon->updated_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
