@extends('layouts.light.master')

@section('title', 'Register Investor')

@section('breadcrumb-title')
    <h3>Register Investor</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.investment.investors.index') }}">Investors</a></li>
    <li class="breadcrumb-item active">Register</li>
@endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/select2.css') }}">
    <style>
        .select2 { width: 100% !important; }
        .select2-container .select2-selection--single { height: 38px; border-color: #ced4da !important; display: flex; align-items: center; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { top: 6px; }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="font-weight-bold mb-0">Investor Registration Form</h5>
                    <small class="text-muted">Investors can only be added by the administrator.</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.investment.investors.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="name" class="font-weight-600">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Investor Name">
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="email" class="font-weight-600">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="investor@example.com">
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="phone_number" class="font-weight-600">Phone Number</label>
                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" placeholder="017xxxxxxxx">
                                @error('phone_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="bkash_number" class="font-weight-600">bKash Number</label>
                                <input type="text" class="form-control @error('bkash_number') is-invalid @enderror" id="bkash_number" name="bkash_number" value="{{ old('bkash_number') }}" placeholder="017xxxxxxxx">
                                @error('bkash_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="referred_by_id" class="font-weight-600">Referred By (Search by Referral Code or Name)</label>
                            <select class="form-control select2 @error('referred_by_id') is-invalid @enderror" id="referred_by_id" name="referred_by_id">
                                <option value="">-- No Referrer (Direct to Company) --</option>
                                @foreach($investors as $inv)
                                    <option value="{{ $inv->id }}" {{ old('referred_by_id') == $inv->id ? 'selected' : '' }}>
                                        [{{ $inv->referral_code }}] {{ $inv->name }} {{ $inv->phone_number ? '('.$inv->phone_number.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('referred_by_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Type referral code (e.g. INV123) or name to search. If assigned, this referrer will receive bonuses according to configured referral rates.
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="password" class="font-weight-600">Account Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Minimum 6 characters">
                                @error('password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="is_active" class="font-weight-600">Account Status</label>
                                <select class="form-control" id="is_active" name="is_active">
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="bank_details" class="font-weight-600">Bank Details</label>
                            <textarea class="form-control @error('bank_details') is-invalid @enderror" id="bank_details" name="bank_details" rows="2" placeholder="Bank Name, Account Holder Name, Account Number, Branch & Routing Number">{{ old('bank_details') }}</textarea>
                            @error('bank_details')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address" class="font-weight-600">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" placeholder="Street Address, City, Postal Code">{{ old('address') }}</textarea>
                            @error('address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('admin.investment.investors.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary font-weight-bold px-4">
                                <i class="fa fa-save mr-1"></i> Register Investor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script src="{{ asset('assets/js/select2/select2.full.min.js') }}" defer></script>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#referred_by_id').select2({
            placeholder: '-- Search by Referral Code, Name or Phone --',
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
