@extends('layouts.light.master')
@section('title', 'Edit Reseller')

@section('breadcrumb-title')
    <h3>Edit Reseller</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Resellers</li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="p-3 card-header">
                        <h5>Edit Reseller Information</h5>
                    </div>
                    <div class="p-3 card-body">
                        <form method="POST" action="{{ route('admin.resellers.update', $reseller->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $reseller->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="shop_name">Shop Name</label>
                                        <input type="text" class="form-control @error('shop_name') is-invalid @enderror"
                                            id="shop_name" name="shop_name" value="{{ old('shop_name', $reseller->shop_name) }}"
                                            required>
                                        @error('shop_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone_number">Phone Number</label>
                                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                            id="phone_number" name="phone_number"
                                            value="{{ old('phone_number', $reseller->phone_number) }}" required>
                                        @error('phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bkash_number">bKash Number</label>
                                        <input type="text" class="form-control @error('bkash_number') is-invalid @enderror"
                                            id="bkash_number" name="bkash_number"
                                            value="{{ old('bkash_number', $reseller->bkash_number) }}" required>
                                        @error('bkash_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="form-group">
                                                    <label for="domain">Domain</label>
                                                    <input type="text" class="form-control @error('domain') is-invalid @enderror" id="domain" name="domain"
                                                        placeholder="e.g. myshop.com" value="{{ old('domain', $reseller->domain) }}">
                                                    @error('domain')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="order_prefix">Order Prefix</label>
                                                <input type="text" class="form-control @error('order_prefix') is-invalid @enderror" id="order_prefix" name="order_prefix"
                                                    placeholder="e.g. ORD" value="{{ old('order_prefix', $reseller->order_prefix) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="is_verified" name="is_verified"
                                                value="1" {{ $reseller->is_verified ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="is_verified">Verified</label>
                                        </div>
                                        <div class="mt-2 custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                                value="1" {{ $reseller->is_active ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="is_active">Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="p-3 card-header">
                                    <h6 class="mb-0">Database Configuration</h6>
                                </div>
                                <div class="p-3 card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="db_name">Database Name</label>
                                                <input type="text" class="form-control @error('db_name') is-invalid @enderror" id="db_name"
                                                    name="db_name" value="{{ old('db_name', $reseller->db_name) }}">
                                                @error('db_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="db_username">Database Username</label>
                                                <input type="text" class="form-control @error('db_username') is-invalid @enderror" id="db_username"
                                                    name="db_username" value="{{ old('db_username', $reseller->db_username) }}">
                                                @error('db_username')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="db_password">Database Password</label>
                                                <input type="password" class="form-control @error('db_password') is-invalid @enderror"
                                                    id="db_password" name="db_password" placeholder="Leave blank to keep current password">
                                                @error('db_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 form-group">
                                <button type="submit" class="btn btn-primary">Update Reseller</button>
                                <a href="{{ route('admin.resellers.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
