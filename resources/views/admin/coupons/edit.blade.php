@extends('layouts.light.master')
@section('title', 'Edit Coupon')

@section('breadcrumb-title')
    <h3>Edit Coupon</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>
    <li class="breadcrumb-item">Edit</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Coupon: {{ $coupon->name }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Coupon Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               id="name" name="name" value="{{ old('name', $coupon->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="code">Coupon Code <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                                   id="code" name="code" value="{{ old('code', $coupon->code) }}" required>
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
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="3">{{ old('description', $coupon->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="discount">Discount Amount (৳) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control @error('discount') is-invalid @enderror"
                                               id="discount" name="discount" value="{{ old('discount', $coupon->discount) }}" required>
                                        @error('discount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="max_usages">Maximum Usages</label>
                                        <input type="number" class="form-control @error('max_usages') is-invalid @enderror"
                                               id="max_usages" name="max_usages" value="{{ old('max_usages', $coupon->max_usages) }}"
                                               placeholder="Leave empty for unlimited">
                                        @error('max_usages')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="expires_at">Expiry Date</label>
                                        <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror"
                                               id="expires_at" name="expires_at"
                                               value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}">
                                        @error('expires_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                           {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Active</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Update Coupon</button>
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
