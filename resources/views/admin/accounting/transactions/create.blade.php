@extends('layouts.light.master')

@section('title', 'Record Transaction')

@section('breadcrumb-title')
    <h3>Record Transaction</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Accounting</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.accounting.transactions.index') }}">Transactions</a></li>
    <li class="breadcrumb-item active">Record</li>
@endsection

@section('breadcrumb-right')
    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#accountingGuideModal">
        <i class="fa fa-graduation-cap mr-1"></i> Accounting Guide
    </button>
@endsection

@section('content')
<div class="container-fluid mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-11">
            <div class="card shadow-sm border" style="background: #ffffff;">
                <div class="card-header p-3 bg-light border-bottom">
                    <ul class="nav nav-pills card-header-pills" id="transactionTypeTabs" role="tablist">
                        <li class="nav-item mr-2 mb-1">
                            <a class="nav-link active font-weight-bold" id="expense-tab" data-toggle="pill" href="#expense-pane" role="tab" onclick="setType('expense')">
                                <i class="fa fa-arrow-up mr-1 text-danger"></i> Office Expense
                            </a>
                        </li>
                        <li class="nav-item mr-2 mb-1">
                            <a class="nav-link font-weight-bold" id="income-tab" data-toggle="pill" href="#income-pane" role="tab" onclick="setType('income')">
                                <i class="fa fa-arrow-down mr-1 text-success"></i> Income / Revenue
                            </a>
                        </li>
                        <li class="nav-item mr-2 mb-1">
                            <a class="nav-link font-weight-bold" id="transfer-tab" data-toggle="pill" href="#transfer-pane" role="tab" onclick="setType('transfer')">
                                <i class="fa fa-exchange mr-1 text-info"></i> Account Transfer
                            </a>
                        </li>
                        <li class="nav-item mr-2 mb-1">
                            <a class="nav-link font-weight-bold" id="investment-tab" data-toggle="pill" href="#investment-pane" role="tab" onclick="setType('investment')">
                                <i class="fa fa-university mr-1 text-primary"></i> Owner Investment
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link font-weight-bold" id="withdrawal-tab" data-toggle="pill" href="#withdrawal-pane" role="tab" onclick="setType('withdrawal')">
                                <i class="fa fa-money mr-1 text-warning"></i> Owner Withdrawal
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4 text-dark">
                    <form action="{{ route('admin.accounting.transactions.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="transaction_type" id="transaction_type_input" value="expense">

                        <!-- Common Date & Amount -->
                        <div class="form-row mb-3">
                            <div class="form-group col-md-6">
                                <label for="entry_date" class="font-weight-bold text-dark">Transaction Date <span class="text-danger">*</span></label>
                                <input type="date" name="entry_date" id="entry_date" class="form-control text-dark font-weight-bold" value="{{ old('entry_date', now()->toDateString()) }}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="amount" class="font-weight-bold text-dark">Amount (BDT) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" id="amount" class="form-control text-dark font-weight-bold" style="font-size: 1.15rem;" placeholder="0.00" min="0.01" value="{{ old('amount') }}" required>
                            </div>
                        </div>

                        <!-- Dynamic Section based on Tabs -->
                        <div class="tab-content" id="transactionContent">
                            <!-- Expense Pane -->
                            <div class="tab-pane fade show active" id="expense-pane" role="tabpanel">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="from_account_id" class="font-weight-bold text-dark">Paid From (Payment Account) <span class="text-danger">*</span></label>
                                        <select name="from_account_id" id="from_account_id" class="form-control text-dark font-weight-bold">
                                            @foreach($assetAccounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->name }} (Balance: {{ number_format($acc->current_balance, 2) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="expense_account_id" class="font-weight-bold text-dark">Expense Account <span class="text-danger">*</span></label>
                                        <select name="expense_account_id" id="expense_account_id" class="form-control text-dark font-weight-bold">
                                            @foreach($expenseAccounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="expense_category_id" class="font-weight-bold text-dark">Expense Category</label>
                                    <select name="expense_category_id" id="expense_category_id" class="form-control text-dark">
                                        <option value="">-- Optional Category --</option>
                                        @foreach($categories->whereIn('type', ['expense', 'both']) as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Income Pane -->
                            <div class="tab-pane fade" id="income-pane" role="tabpanel">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="to_account_id_inc" class="font-weight-bold text-dark">Deposit To (Asset Account) <span class="text-danger">*</span></label>
                                        <select name="to_account_id" id="to_account_id_inc" class="form-control text-dark font-weight-bold">
                                            @foreach($assetAccounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->name }} (Balance: {{ number_format($acc->current_balance, 2) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="income_account_id" class="font-weight-bold text-dark">Income Account <span class="text-danger">*</span></label>
                                        <select name="income_account_id" id="income_account_id" class="form-control text-dark font-weight-bold">
                                            @foreach($incomeAccounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="income_category_id" class="font-weight-bold text-dark">Income Category</label>
                                    <select name="income_category_id" id="income_category_id" class="form-control text-dark">
                                        <option value="">-- Optional Category --</option>
                                        @foreach($categories->whereIn('type', ['income', 'both']) as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Transfer Pane -->
                            <div class="tab-pane fade" id="transfer-pane" role="tabpanel">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="from_account_id_tr" class="font-weight-bold text-dark">Transfer From <span class="text-danger">*</span></label>
                                        <select name="from_account_id" id="from_account_id_tr" class="form-control text-dark font-weight-bold">
                                            @foreach($assetAccounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->name }} (Balance: {{ number_format($acc->current_balance, 2) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="to_account_id_tr" class="font-weight-bold text-dark">Transfer To <span class="text-danger">*</span></label>
                                        <select name="to_account_id" id="to_account_id_tr" class="form-control text-dark font-weight-bold">
                                            @foreach($assetAccounts as $acc)
                                                <option value="{{ $acc->id }}" {{ $loop->iteration == 2 ? 'selected' : '' }}>{{ $acc->name }} (Balance: {{ number_format($acc->current_balance, 2) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Investment Pane -->
                            <div class="tab-pane fade" id="investment-pane" role="tabpanel">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="to_account_id_inv" class="font-weight-bold text-dark">Deposit To Account <span class="text-danger">*</span></label>
                                        <select name="to_account_id" id="to_account_id_inv" class="form-control text-dark font-weight-bold">
                                            @foreach($assetAccounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->name }} (Balance: {{ number_format($acc->current_balance, 2) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="equity_account_id_inv" class="font-weight-bold text-dark">Capital / Equity Account <span class="text-danger">*</span></label>
                                        <select name="equity_account_id" id="equity_account_id_inv" class="form-control text-dark font-weight-bold">
                                            @foreach($equityAccounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Withdrawal Pane -->
                            <div class="tab-pane fade" id="withdrawal-pane" role="tabpanel">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="from_account_id_wd" class="font-weight-bold text-dark">Withdraw From Account <span class="text-danger">*</span></label>
                                        <select name="from_account_id" id="from_account_id_wd" class="form-control text-dark font-weight-bold">
                                            @foreach($assetAccounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->name }} (Balance: {{ number_format($acc->current_balance, 2) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="equity_account_id_wd" class="font-weight-bold text-dark">Drawings / Equity Account <span class="text-danger">*</span></label>
                                        <select name="equity_account_id" id="equity_account_id_wd" class="form-control text-dark font-weight-bold">
                                            @foreach($equityAccounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes & Ref -->
                        <div class="form-row mt-3">
                            <div class="form-group col-md-6">
                                <label for="reference" class="font-weight-bold text-dark">Reference / Voucher / Invoice #</label>
                                <input type="text" name="reference" id="reference" class="form-control text-dark" placeholder="Optional voucher # or receipt code">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="description" class="font-weight-bold text-dark">Description / Remarks</label>
                                <input type="text" name="description" id="description" class="form-control text-dark" placeholder="e.g. Paid Electricity bill, Snacks, Delivery income">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('admin.accounting.transactions.index') }}" class="btn btn-light border text-dark mr-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 font-weight-bold"><i class="fa fa-save mr-1"></i> Post Journal Entry</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.accounting.partials.tutorial-modal')
@endsection

@push('js')
<script>
    function setType(type) {
        document.getElementById('transaction_type_input').value = type;
    }
</script>
@endpush
