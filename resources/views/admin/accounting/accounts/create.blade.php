@extends('layouts.light.master')

@section('title', 'Create Account')

@section('breadcrumb-title')
    <h3>Create Account</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Accounting</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.accounting.accounts.index') }}">Accounts</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="container-fluid mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm border" style="background: #ffffff;">
                <div class="card-header p-3 bg-light border-bottom">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-plus-circle text-primary mr-1"></i> New Account Details</h5>
                </div>
                <div class="card-body p-4 text-dark">
                    <form action="{{ route('admin.accounting.accounts.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name" class="font-weight-bold text-dark">Account Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control text-dark font-weight-bold @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. City Bank, Office Snacks, Electricity Bill" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="type" class="font-weight-bold text-dark">Account Classification <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-control text-dark font-weight-bold @error('type') is-invalid @enderror" required>
                                    <option value="asset" {{ old('type') == 'asset' ? 'selected' : '' }}>Asset (Cash, Bank, Wallet, Inventory)</option>
                                    <option value="liability" {{ old('type') == 'liability' ? 'selected' : '' }}>Liability (Accounts Payable, Supplier Due)</option>
                                    <option value="equity" {{ old('type') == 'equity' ? 'selected' : '' }}>Equity (Owner Capital, Investments, Drawings)</option>
                                    <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Income (Sales Revenue, Delivery Charge Income)</option>
                                    <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Expense (Rent, Utilities, Food, Marketing, Salary)</option>
                                </select>
                                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="code" class="font-weight-bold text-dark">Account Code (Optional)</label>
                                <input type="text" name="code" id="code" class="form-control text-dark @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="e.g. 1001, 5002">
                                @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="opening_balance" class="font-weight-bold text-dark">Opening Balance (BDT)</label>
                            <input type="number" step="0.01" name="opening_balance" id="opening_balance" class="form-control text-dark font-weight-bold @error('opening_balance') is-invalid @enderror" value="{{ old('opening_balance', '0.00') }}">
                            <small class="form-text text-muted">Initial balance when setting up this account.</small>
                            @error('opening_balance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="description" class="font-weight-bold text-dark">Description / Notes</label>
                            <textarea name="description" id="description" rows="3" class="form-control text-dark @error('description') is-invalid @enderror" placeholder="Optional description">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label font-weight-bold text-dark" for="is_active">Account is Active</label>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.accounting.accounts.index') }}" class="btn btn-light border text-dark mr-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 font-weight-bold"><i class="fa fa-save mr-1"></i> Save Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
