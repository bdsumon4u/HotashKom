@extends('layouts.light.master')
@section('title', 'Create Coupon')

@section('breadcrumb-title')
    <h3>Create Coupon</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>
    <li class="breadcrumb-item">Create</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Create New Coupon</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.coupons.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Coupon Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="code">Coupon Code <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                                   id="code" name="code" value="{{ old('code') }}" required>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary" id="generate-code">
                                                    Generate
                                                </button>
                                            </div>
                                        </div>
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="expires_at">Expiry Date</label>
                                        <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror"
                                               id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                                        @error('expires_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="coupon_type">Coupon Type <span class="text-danger">*</span></label>
                                        <select id="coupon_type" name="coupon_type" class="form-control @error('coupon_type') is-invalid @enderror" required>
                                            <option value="purchase" {{ old('coupon_type', 'purchase') === 'purchase' ? 'selected' : '' }}>Purchase</option>
                                            @if(isOninda() && config('app.resell'))
                                            <option value="subscription" {{ old('coupon_type') === 'subscription' ? 'selected' : '' }}>Subscription</option>
                                            @endif
                                        </select>
                                        @error('coupon_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="discount_type">Discount Type <span class="text-danger">*</span></label>
                                        <select id="discount_type" name="discount_type" class="form-control @error('discount_type') is-invalid @enderror" required>
                                            <option value="fixed" {{ old('discount_type', 'fixed') === 'fixed' ? 'selected' : '' }}>Fixed (৳)</option>
                                            <option value="percent" {{ old('discount_type') === 'percent' ? 'selected' : '' }}>Percent (%)</option>
                                        </select>
                                        @error('discount_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="discount">Discount Value <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control @error('discount') is-invalid @enderror"
                                               id="discount" name="discount" value="{{ old('discount') }}" required>
                                        @error('discount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="max_usages">Maximum Usages</label>
                                        <input type="number" class="form-control @error('max_usages') is-invalid @enderror"
                                               id="max_usages" name="max_usages" value="{{ old('max_usages') }}"
                                               placeholder="Leave empty for unlimited">
                                        @error('max_usages')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                           {{ old('is_active') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Active</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Create Coupon</button>
                                <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#generate-code').click(function() {
        $.get('{{ route("admin.coupons.generate-code") }}')
            .done(function(data) {
                $('#code').val(data.code);
            })
            .fail(function() {
                alert('Failed to generate code. Please try again.');
            });
    });
});
</script>
@endpush
