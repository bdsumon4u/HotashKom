@extends('layouts.investor.master')

@section('title', 'My Profile')

@section('breadcrumb-title')
    <h3>Investor Profile</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('investor.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-4 col-lg-5 mb-3">
            <div class="card text-center">
                <div class="card-body p-3">
                    <div class="avatar-lg mb-2">
                        <div class="p-2 bg-light-primary rounded-circle d-inline-block text-primary">
                            <i class="fa fa-user-circle fa-3x"></i>
                        </div>
                    </div>
                    <h5 class="font-weight-bold mb-1">{{ $investor->name }}</h5>
                    <p class="text-muted small mb-2"><i class="fa fa-envelope"></i> {{ $investor->email }}</p>
                    <span class="badge badge-info px-2 py-1 font-weight-600">Referral: {{ $investor->referral_code }}</span>

                    <hr>

                    <div class="text-left small">
                        <p class="mb-2"><strong>Phone:</strong> {{ $investor->phone_number ?: 'Not provided' }}</p>
                        <p class="mb-2"><strong>bKash Number:</strong> {{ $investor->bkash_number ?: 'Not provided' }}</p>
                        <p class="mb-2"><strong>Joined On:</strong> {{ $investor->created_at->format('d M Y') }}</p>
                        <p class="mb-0"><strong>Referred By:</strong> {{ $investor->referrer ? $investor->referrer->name : 'Direct / Company' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="font-weight-bold mb-0">Update Profile Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('investor.profile.update') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="name" class="font-weight-600">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $investor->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="font-weight-600">Email Address (Read-only)</label>
                                <input type="email" class="form-control bg-light" value="{{ $investor->email }}" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="phone_number" class="font-weight-600">Phone Number</label>
                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $investor->phone_number) }}">
                                @error('phone_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="bkash_number" class="font-weight-600">bKash Account Number</label>
                                <input type="text" class="form-control @error('bkash_number') is-invalid @enderror" id="bkash_number" name="bkash_number" value="{{ old('bkash_number', $investor->bkash_number) }}">
                                @error('bkash_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="bank_details" class="font-weight-600">Bank Account Details</label>
                            <textarea class="form-control @error('bank_details') is-invalid @enderror" id="bank_details" name="bank_details" rows="2" placeholder="Bank Name, Account Holder Name, Account Number, Branch & Routing Number">{{ old('bank_details', $investor->bank_details) }}</textarea>
                            @error('bank_details')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address" class="font-weight-600">Mailing / Physical Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address', $investor->address) }}</textarea>
                            @error('address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <hr class="my-4">
                        <h6 class="font-weight-bold mb-3"><i class="fa fa-lock mr-1"></i> Change Password <small class="text-muted font-weight-normal">(Leave blank to keep current password)</small></h6>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="current_password" class="font-weight-600">Current Password</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
                                @error('current_password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="new_password" class="font-weight-600">New Password</label>
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password">
                                @error('new_password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="new_password_confirmation" class="font-weight-600">Confirm New Password</label>
                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                            </div>
                        </div>

                        <div class="text-right mt-3">
                            <button type="submit" class="btn btn-primary px-4 font-weight-bold">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
