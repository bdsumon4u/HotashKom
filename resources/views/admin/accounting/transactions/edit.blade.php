@extends('layouts.light.master')

@section('title', 'Edit Transaction - ' . ($entry->entry_number ?? '#' . $entry->id))

@section('breadcrumb-title')
    <h3>Edit Transaction</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Accounting</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.accounting.transactions.index') }}">Transactions</a></li>
    <li class="breadcrumb-item active">{{ $entry->entry_number ?? '#' . $entry->id }}</li>
@endsection

@section('content')
<div class="container-fluid mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm border" style="background: #ffffff;">
                <div class="card-header p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fa fa-pencil-square text-primary mr-1"></i> Edit Entry: <span class="text-primary">{{ $entry->entry_number }}</span>
                        </h6>
                    </div>
                    <span class="badge badge-light-primary text-primary font-weight-bold border px-2 py-1">
                        Manual Entry
                    </span>
                </div>

                <div class="card-body p-4 text-dark">
                    @if ($errors->any())
                        <div class="alert alert-danger shadow-sm">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.accounting.transactions.update', $entry) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Date & Amount -->
                        <div class="form-row mb-3">
                            <div class="form-group col-md-6">
                                <label for="entry_date" class="font-weight-bold text-dark">Transaction Date <span class="text-danger">*</span></label>
                                <input type="date" name="entry_date" id="entry_date" class="form-control text-dark font-weight-bold" value="{{ old('entry_date', $entry->entry_date->toDateString()) }}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="amount" class="font-weight-bold text-dark">Amount (BDT) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" id="amount" class="form-control text-dark font-weight-bold" style="font-size: 1.15rem;" placeholder="0.00" min="0.01" value="{{ old('amount', $amount) }}" required>
                            </div>
                        </div>

                        <!-- Debit (Receiving / Expense) & Credit (Paying / Source) Accounts -->
                        <div class="form-row mb-3">
                            <div class="form-group col-md-6">
                                <label for="debit_account_id" class="font-weight-bold text-dark">
                                    <i class="fa fa-arrow-down text-danger mr-1"></i> Debit Account (Receiving / Expense) <span class="text-danger">*</span>
                                </label>
                                <select name="debit_account_id" id="debit_account_id" class="form-control text-dark font-weight-bold" required>
                                    <option value="">-- Select Debit Account --</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}" {{ old('debit_account_id', $debitAccountId) == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->name }} ({{ strtoupper($acc->type) }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Account being debited (e.g. Snack Expense, Ad Spend, Bank Deposit)</small>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="credit_account_id" class="font-weight-bold text-dark">
                                    <i class="fa fa-arrow-up text-success mr-1"></i> Credit Account (Paying / Source) <span class="text-danger">*</span>
                                </label>
                                <select name="credit_account_id" id="credit_account_id" class="form-control text-dark font-weight-bold" required>
                                    <option value="">-- Select Credit Account --</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}" {{ old('credit_account_id', $creditAccountId) == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->name }} ({{ strtoupper($acc->type) }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Account being credited (e.g. Cash in Hand, Bank Account, Sales)</small>
                            </div>
                        </div>

                        <!-- Category & Reference -->
                        <div class="form-row mb-3">
                            <div class="form-group col-md-6">
                                <label for="category_id" class="font-weight-bold text-dark">Category (Optional)</label>
                                <select name="category_id" id="category_id" class="form-control text-dark font-weight-bold">
                                    <option value="">-- None / No Category --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $categoryId) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }} ({{ ucfirst($cat->type) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="reference" class="font-weight-bold text-dark">Reference / Voucher #</label>
                                <input type="text" name="reference" id="reference" class="form-control text-dark" placeholder="e.g. Receipt #, Bank Tran ID" value="{{ old('reference', $entry->reference) }}">
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-group mb-4">
                            <label for="description" class="font-weight-bold text-dark">Description / Remarks</label>
                            <textarea name="description" id="description" class="form-control text-dark" rows="2" placeholder="Explain what this transaction was for...">{{ old('description', $entry->description) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('admin.accounting.transactions.index') }}" class="btn btn-light border text-dark font-weight-bold">
                                <i class="fa fa-times mr-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4 font-weight-bold">
                                <i class="fa fa-save mr-1"></i> Update Transaction & Balance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
