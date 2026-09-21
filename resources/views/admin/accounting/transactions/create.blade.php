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
        <div class="col-lg-10 col-xl-9">
            <div class="card shadow-sm border" style="background: #ffffff;">
                
                <!-- 1. High-Level Transaction Type Selector -->
                <div class="card-header p-3 bg-light border-bottom">
                    <div class="row" style="gap: 8px 0;">
                        <!-- Type 1: Expense -->
                        <div class="col-md-4 col-12">
                            <div class="type-card p-3 rounded border text-center cursor-pointer active" id="btn-type-expense" onclick="selectMainType('expense')">
                                <div class="d-flex align-items-center justify-content-center mb-1">
                                    <span class="type-icon-badge bg-danger text-white mr-2"><i class="fa fa-arrow-up"></i></span>
                                    <strong class="text-dark" style="font-size: 15px;">Business Expense</strong>
                                </div>
                                <small class="text-muted d-block">Bills, Snacks, Ads, COGS, Salary</small>
                            </div>
                        </div>

                        <!-- Type 2: Income -->
                        <div class="col-md-4 col-12">
                            <div class="type-card p-3 rounded border text-center cursor-pointer" id="btn-type-income" onclick="selectMainType('income')">
                                <div class="d-flex align-items-center justify-content-center mb-1">
                                    <span class="type-icon-badge bg-success text-white mr-2"><i class="fa fa-arrow-down"></i></span>
                                    <strong class="text-dark" style="font-size: 15px;">Business Income</strong>
                                </div>
                                <small class="text-muted d-block">Sales, Courier & Extra Revenue</small>
                            </div>
                        </div>

                        <!-- Type 3: Transfer & Owner -->
                        <div class="col-md-4 col-12">
                            <div class="type-card p-3 rounded border text-center cursor-pointer" id="btn-type-transfer" onclick="selectMainType('transfer')">
                                <div class="d-flex align-items-center justify-content-center mb-1">
                                    <span class="type-icon-badge bg-primary text-white mr-2"><i class="fa fa-exchange"></i></span>
                                    <strong class="text-dark" style="font-size: 15px;">Transfer / Owner</strong>
                                </div>
                                <small class="text-muted d-block">Transfer & Capital / Drawings</small>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Toggle for Transfer / Owner / Adjustments (Shows only when Transfer/Owner is active) -->
                    <div id="transferSubOptions" class="mt-3 p-2 bg-white rounded border d-none">
                        <div class="d-flex justify-content-center flex-wrap" style="gap: 6px;">
                            <button type="button" class="btn btn-sm btn-primary font-weight-bold" id="sub-transfer" onclick="setSubTransferType('transfer')">
                                <i class="fa fa-exchange mr-1"></i> Bank/Cash Transfer
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary font-weight-bold" id="sub-investment" onclick="setSubTransferType('investment')">
                                <i class="fa fa-university mr-1"></i> Owner Investment
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary font-weight-bold" id="sub-withdrawal" onclick="setSubTransferType('withdrawal')">
                                <i class="fa fa-user mr-1"></i> Owner Withdrawal
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary font-weight-bold" id="sub-return_restock" onclick="setSubTransferType('return_restock')">
                                <i class="fa fa-cubes mr-1"></i> Customer Return Restock
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary font-weight-bold" id="sub-adjustment" onclick="setSubTransferType('adjustment')">
                                <i class="fa fa-sliders mr-1"></i> Custom / Adjustment
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 2. Single Unified Smart Form (Zero field duplication) -->
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

                    <form action="{{ route('admin.accounting.transactions.store') }}" method="POST" id="unifiedTransactionForm">
                        @csrf
                        <input type="hidden" name="transaction_type" id="transaction_type_input" value="expense">

                        <!-- Date & Amount Row -->
                        <div class="form-row mb-3">
                            <div class="form-group col-md-6">
                                <label for="entry_date" class="font-weight-bold text-dark">Transaction Date <span class="text-danger">*</span></label>
                                <input type="date" name="entry_date" id="entry_date" class="form-control text-dark font-weight-bold" value="{{ old('entry_date', now()->toDateString()) }}" required onchange="updateVisualizer()">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="amount" class="font-weight-bold text-dark">Amount (BDT) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" id="amount" class="form-control text-dark font-weight-bold" style="font-size: 1.15rem;" placeholder="0.00" min="0.01" value="{{ old('amount') }}" required oninput="updateVisualizer()">
                            </div>
                        </div>

                        <!-- Dynamic Two-Account Row -->
                        <div class="form-row mb-3">
                            <!-- Account 1: Source / Paying / From -->
                            <div class="form-group col-md-6">
                                <label for="from_account_id" id="label_from_account" class="font-weight-bold text-dark">
                                    <i class="fa fa-arrow-up text-danger mr-1"></i> Paid From (Payment Account) <span class="text-danger">*</span>
                                </label>
                                <select name="from_account_id" id="from_account_id" class="form-control text-dark font-weight-bold" required onchange="updateVisualizer()">
                                    <!-- Populated via JS based on transaction type -->
                                </select>
                                <small class="text-muted d-block mt-1" id="hint_from_account">Account from which money or value is disbursed (Cash, Bank, Inventory).</small>
                            </div>

                            <!-- Account 2: Destination / Expense / Income / To -->
                            <div class="form-group col-md-6">
                                <label for="to_account_id" id="label_to_account" class="font-weight-bold text-dark">
                                    <i class="fa fa-arrow-down text-primary mr-1"></i> Expense Account <span class="text-danger">*</span>
                                </label>
                                <select name="to_account_id" id="to_account_id" class="form-control text-dark font-weight-bold" required onchange="updateVisualizer()">
                                    <!-- Populated via JS based on transaction type -->
                                </select>
                                <small class="text-muted d-block mt-1" id="hint_to_account">Account receiving the charge or expense.</small>
                            </div>
                        </div>

                        <!-- Category Row (Contextual) -->
                        <div class="form-group mb-3" id="category_wrapper">
                            <label for="category_id" id="label_category" class="font-weight-bold text-dark">Category (Optional)</label>
                            <select name="category_id" id="category_id" class="form-control text-dark font-weight-bold">
                                <option value="">-- None / No Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" data-type="{{ $cat->type }}">{{ $cat->name }} ({{ ucfirst($cat->type) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Visualizer Box -->
                        <div class="p-3 mb-4 rounded border" style="background: #f8fafc;">
                            <div class="small font-weight-bold text-muted text-uppercase mb-1">
                                <i class="fa fa-eye mr-1"></i> Transaction Accounting Impact Preview:
                            </div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap text-dark font-weight-bold" style="gap: 8px;">
                                <div class="bg-white p-2 rounded border flex-grow-1 text-center">
                                    <span class="text-muted small d-block">CREDIT (-) Disbursing Account</span>
                                    <span class="text-danger" id="vis_from">Select Account</span>
                                </div>
                                <div class="px-2 text-center text-primary">
                                    <i class="fa fa-long-arrow-right fa-lg"></i>
                                    <div class="font-roboto h6 mb-0 text-primary" id="vis_amount">৳0.00</div>
                                </div>
                                <div class="bg-white p-2 rounded border flex-grow-1 text-center">
                                    <span class="text-muted small d-block">DEBIT (+) Receiving Account</span>
                                    <span class="text-success" id="vis_to">Select Account</span>
                                </div>
                            </div>
                        </div>

                        <!-- Reference & Description -->
                        <div class="form-row mb-3">
                            <div class="form-group col-md-6">
                                <label for="reference" class="font-weight-bold text-dark">Reference / Voucher #</label>
                                <input type="text" name="reference" id="reference" class="form-control text-dark" placeholder="e.g. Return Parcel #, Receipt #, Check #">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="description" class="font-weight-bold text-dark">Description / Remarks</label>
                                <input type="text" name="description" id="description" class="form-control text-dark" placeholder="e.g. Customer returned order #1024, Restocked to Warehouse">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('admin.accounting.transactions.index') }}" class="btn btn-light border text-dark font-weight-bold">
                                <i class="fa fa-times mr-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4 font-weight-bold">
                                <i class="fa fa-check mr-1"></i> Post Journal Entry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.accounting.partials.tutorial-modal')
@endsection

@push('css')
<style>
    .type-card {
        background: #ffffff;
        transition: all 0.2s ease-in-out;
    }
    .type-card:hover {
        border-color: #3b82f6 !important;
        background: #f8fafc;
    }
    .type-card.active {
        border: 2px solid #3b82f6 !important;
        background: #eff6ff !important;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.15) !important;
    }
    .type-icon-badge {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>
@endpush

@push('js')
<script>
    // Data structures prepared from server
    const assetAccounts = @json($assetAccounts->values());
    const expenseAccounts = @json($expenseAccounts->values());
    const incomeAccounts = @json($incomeAccounts->values());
    const equityAccounts = @json($equityAccounts->values());
    const allAccounts = @json($accounts->values());

    let currentType = 'expense';

    function selectMainType(type) {
        currentType = type;
        
        // Update active card styles
        document.querySelectorAll('.type-card').forEach(el => el.classList.remove('active'));
        const activeCard = document.getElementById('btn-type-' + (['investment', 'withdrawal', 'return_restock', 'adjustment'].includes(type) ? 'transfer' : type));
        if (activeCard) activeCard.classList.add('active');

        const transferSub = document.getElementById('transferSubOptions');
        if (['transfer', 'investment', 'withdrawal', 'return_restock', 'adjustment'].includes(type)) {
            transferSub.classList.remove('d-none');
            setSubTransferType(type === 'expense' || type === 'income' ? 'transfer' : type);
        } else {
            transferSub.classList.add('d-none');
            applyFormConfiguration(type);
        }
    }

    function setSubTransferType(subType) {
        currentType = subType;

        // Update sub-buttons
        ['transfer', 'investment', 'withdrawal', 'return_restock', 'adjustment'].forEach(t => {
            const btn = document.getElementById('sub-' + t);
            if (btn) {
                if (t === subType) {
                    btn.className = 'btn btn-sm btn-primary font-weight-bold';
                } else {
                    btn.className = 'btn btn-sm btn-outline-secondary font-weight-bold';
                }
            }
        });

        applyFormConfiguration(subType);
    }

    function applyFormConfiguration(type) {
        document.getElementById('transaction_type_input').value = type;

        const fromSelect = document.getElementById('from_account_id');
        const toSelect = document.getElementById('to_account_id');
        const fromLabel = document.getElementById('label_from_account');
        const toLabel = document.getElementById('label_to_account');
        const fromHint = document.getElementById('hint_from_account');
        const toHint = document.getElementById('hint_to_account');
        const catWrapper = document.getElementById('category_wrapper');

        fromSelect.innerHTML = '';
        toSelect.innerHTML = '';

        if (type === 'expense') {
            fromLabel.innerHTML = '<i class="fa fa-arrow-up text-danger mr-1"></i> Paid From (Payment / Source Account) <span class="text-danger">*</span>';
            toLabel.innerHTML = '<i class="fa fa-arrow-down text-primary mr-1"></i> Expense Account <span class="text-danger">*</span>';
            fromHint.innerText = 'Account money was paid from (Cash, Bank, bKash, or Inventory for COGS).';
            toHint.innerText = 'Category/Expense being charged (Ads, Snacks, COGS, Salary, Rent).';
            catWrapper.classList.remove('d-none');

            // Assets in fromSelect
            assetAccounts.forEach(acc => {
                fromSelect.add(new Option(`${acc.name} (Balance: ${Number(acc.current_balance).toLocaleString('en-US', {minimumFractionDigits: 2})})`, acc.id));
            });
            // Expenses in toSelect
            expenseAccounts.forEach(acc => {
                toSelect.add(new Option(`${acc.name}`, acc.id));
            });

        } else if (type === 'income') {
            fromLabel.innerHTML = '<i class="fa fa-arrow-down text-success mr-1"></i> Deposit To (Asset Account) <span class="text-danger">*</span>';
            toLabel.innerHTML = '<i class="fa fa-arrow-up text-primary mr-1"></i> Income Account <span class="text-danger">*</span>';
            fromHint.innerText = 'Account where money is received/deposited (Cash, Bank, bKash).';
            toHint.innerText = 'Source of revenue (Sales Revenue, Delivery Income, Extra Income).';
            catWrapper.classList.remove('d-none');

            assetAccounts.forEach(acc => {
                fromSelect.add(new Option(`${acc.name} (Balance: ${Number(acc.current_balance).toLocaleString('en-US', {minimumFractionDigits: 2})})`, acc.id));
            });
            incomeAccounts.forEach(acc => {
                toSelect.add(new Option(`${acc.name}`, acc.id));
            });

        } else if (type === 'transfer') {
            fromLabel.innerHTML = '<i class="fa fa-arrow-up text-danger mr-1"></i> Transfer From Account <span class="text-danger">*</span>';
            toLabel.innerHTML = '<i class="fa fa-arrow-down text-success mr-1"></i> Transfer To Account <span class="text-danger">*</span>';
            fromHint.innerText = 'Account money is taken out of.';
            toHint.innerText = 'Account money is transferred into.';
            catWrapper.classList.add('d-none');

            assetAccounts.forEach(acc => {
                fromSelect.add(new Option(`${acc.name} (Balance: ${Number(acc.current_balance).toLocaleString('en-US', {minimumFractionDigits: 2})})`, acc.id));
            });
            assetAccounts.forEach((acc, idx) => {
                const opt = new Option(`${acc.name} (Balance: ${Number(acc.current_balance).toLocaleString('en-US', {minimumFractionDigits: 2})})`, acc.id);
                if (idx === 1) opt.selected = true;
                toSelect.add(opt);
            });

        } else if (type === 'investment') {
            fromLabel.innerHTML = '<i class="fa fa-arrow-down text-success mr-1"></i> Deposit To (Bank / Cash) <span class="text-danger">*</span>';
            toLabel.innerHTML = '<i class="fa fa-university text-primary mr-1"></i> Capital / Equity Account <span class="text-danger">*</span>';
            fromHint.innerText = 'Business account receiving owner capital.';
            toHint.innerText = 'Owner Capital Account.';
            catWrapper.classList.add('d-none');

            assetAccounts.forEach(acc => {
                fromSelect.add(new Option(`${acc.name} (Balance: ${Number(acc.current_balance).toLocaleString('en-US', {minimumFractionDigits: 2})})`, acc.id));
            });
            equityAccounts.forEach(acc => {
                toSelect.add(new Option(`${acc.name}`, acc.id));
            });

        } else if (type === 'withdrawal') {
            fromLabel.innerHTML = '<i class="fa fa-arrow-up text-danger mr-1"></i> Withdraw From (Bank / Cash) <span class="text-danger">*</span>';
            toLabel.innerHTML = '<i class="fa fa-user text-primary mr-1"></i> Drawings / Equity Account <span class="text-danger">*</span>';
            fromHint.innerText = 'Business account money is withdrawn from.';
            toHint.innerText = 'Owner Drawings Account.';
            catWrapper.classList.add('d-none');

            assetAccounts.forEach(acc => {
                fromSelect.add(new Option(`${acc.name} (Balance: ${Number(acc.current_balance).toLocaleString('en-US', {minimumFractionDigits: 2})})`, acc.id));
            });
            equityAccounts.forEach(acc => {
                toSelect.add(new Option(`${acc.name}`, acc.id));
            });

        } else if (type === 'return_restock') {
            fromLabel.innerHTML = '<i class="fa fa-reply text-warning mr-1"></i> Cost of Goods Sold / COGS Reversal (Credit) <span class="text-danger">*</span>';
            toLabel.innerHTML = '<i class="fa fa-cubes text-success mr-1"></i> Inventory Restock Account (Debit) <span class="text-danger">*</span>';
            fromHint.innerText = 'Product Purchase Cost / COGS account being reduced (Credit).';
            toHint.innerText = 'Inventory / Product Stock account receiving returned products (Debit).';
            catWrapper.classList.remove('d-none');

            // From: Expense Accounts (COGS preselected)
            expenseAccounts.forEach(acc => {
                const opt = new Option(`${acc.name}`, acc.id);
                if (acc.code === '5001' || acc.name.toLowerCase().includes('purchase cost') || acc.name.toLowerCase().includes('cogs')) {
                    opt.selected = true;
                }
                fromSelect.add(opt);
            });

            // To: Asset Accounts (Inventory preselected)
            assetAccounts.forEach(acc => {
                const opt = new Option(`${acc.name} (Balance: ${Number(acc.current_balance).toLocaleString('en-US', {minimumFractionDigits: 2})})`, acc.id);
                if (acc.code === '1004' || acc.name.toLowerCase().includes('inventory') || acc.name.toLowerCase().includes('stock')) {
                    opt.selected = true;
                }
                toSelect.add(opt);
            });

        } else if (type === 'adjustment') {
            fromLabel.innerHTML = '<i class="fa fa-arrow-up text-danger mr-1"></i> Credited Account (-) <span class="text-danger">*</span>';
            toLabel.innerHTML = '<i class="fa fa-arrow-down text-success mr-1"></i> Debited Account (+) <span class="text-danger">*</span>';
            fromHint.innerText = 'Any account to credit in the general ledger.';
            toHint.innerText = 'Any account to debit in the general ledger.';
            catWrapper.classList.remove('d-none');

            allAccounts.forEach(acc => {
                fromSelect.add(new Option(`[${acc.type.toUpperCase()}] ${acc.name} (${acc.code})`, acc.id));
            });
            allAccounts.forEach((acc, idx) => {
                const opt = new Option(`[${acc.type.toUpperCase()}] ${acc.name} (${acc.code})`, acc.id);
                if (idx === 1) opt.selected = true;
                toSelect.add(opt);
            });
        }

        updateVisualizer();
    }

    function updateVisualizer() {
        const fromSelect = document.getElementById('from_account_id');
        const toSelect = document.getElementById('to_account_id');
        const amount = document.getElementById('amount').value;

        const fromText = fromSelect.options[fromSelect.selectedIndex] ? fromSelect.options[fromSelect.selectedIndex].text.split(' (Balance:')[0] : 'Select Account';
        const toText = toSelect.options[toSelect.selectedIndex] ? toSelect.options[toSelect.selectedIndex].text.split(' (Balance:')[0] : 'Select Account';

        if (currentType === 'income') {
            document.getElementById('vis_from').innerText = toText; // Credit: Income
            document.getElementById('vis_to').innerText = fromText; // Debit: Bank/Cash Asset
        } else if (currentType === 'investment') {
            document.getElementById('vis_from').innerText = toText; // Credit: Equity
            document.getElementById('vis_to').innerText = fromText; // Debit: Bank/Cash Asset
        } else {
            document.getElementById('vis_from').innerText = fromText; // Credit: Payment / Reversal Account
            document.getElementById('vis_to').innerText = toText;   // Debit: Expense / Inventory / Receiving Account
        }

        document.getElementById('vis_amount').innerText = '৳' + (amount ? Number(amount).toLocaleString('en-US', {minimumFractionDigits: 2}) : '0.00');
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        applyFormConfiguration('expense');
    });
</script>
@endpush
