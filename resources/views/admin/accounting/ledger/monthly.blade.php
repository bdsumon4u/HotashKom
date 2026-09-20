@extends('layouts.light.master')

@section('title', 'Monthly Ledger & Financial Balance')

@section('breadcrumb-title')
    <h3>Monthly Double-Entry Ledger</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Accounting</li>
    <li class="breadcrumb-item active">Monthly Ledger</li>
@endsection

@section('breadcrumb-right')
    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
        <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#accountingGuideModal">
            <i class="fa fa-graduation-cap mr-1"></i> Accounting Guide
        </button>
        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#courierPayoutModal">
            <i class="fa fa-truck mr-1"></i> Record Courier Payout / Sales
        </button>
        <a href="{{ route('admin.accounting.transactions.create') }}" class="btn btn-danger btn-sm">
            <i class="fa fa-plus mr-1"></i> Record Transaction
        </a>
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
            <i class="fa fa-print mr-1"></i> Print Ledger
        </button>
    </div>
@endsection

@section('content')
<div class="print-header" style="display: none;">
    <h2>{{ config('app.name', 'HotashKom') }} - Monthly Financial Ledger</h2>
    <div class="date">Period: {{ $ledgerData['month_name'] }} | Generated on: {{ now()->format('F j, Y \a\t g:i A') }}</div>
</div>

<div class="container-fluid mb-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <!-- Month Filter Bar (Screen Only) -->
    <div class="card shadow-sm border mb-4 no-print" style="background: #ffffff;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.accounting.ledger.monthly') }}" class="form-row align-items-end">
                <div class="form-group col-md-4 mb-2">
                    <label class="small font-weight-bold text-dark">Select Month & Year</label>
                    <input type="month" name="month" class="form-control form-control-sm text-dark font-weight-bold" value="{{ $monthYear }}" onchange="this.form.submit()">
                </div>
                <div class="form-group col-md-4 mb-2">
                    <label class="small font-weight-bold text-dark">Filter by Specific Account</label>
                    <select name="account_id" class="form-control form-control-sm text-dark font-weight-bold" onchange="this.form.submit()">
                        <option value="">-- All Accounts --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ $accountId == $acc->id ? 'selected' : '' }}>{{ $acc->name }} ({{ strtoupper($acc->type) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4 mb-2 d-flex">
                    <button type="submit" class="btn btn-primary btn-sm mr-2 flex-grow-1"><i class="fa fa-sync mr-1"></i> Refresh</button>
                    <a href="{{ route('admin.accounting.ledger.monthly') }}" class="btn btn-light btn-sm border text-dark">Current Month</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Company Financial Summary KPI Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm border-left-primary bg-white">
                <div class="p-3 card-body">
                    <span class="text-muted small font-weight-bold text-uppercase">Total Assets / Balance</span>
                    <h4 class="mb-0 font-roboto font-weight-bold text-primary">{!! theMoney($ledgerData['summary']['total_assets']) !!}</h4>
                    <small class="text-secondary font-weight-500">Cash, Bank, Wallets & Inventory</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm border-left-danger bg-white">
                <div class="p-3 card-body">
                    <span class="text-muted small font-weight-bold text-uppercase">Total Liabilities / Dues</span>
                    <h4 class="mb-0 font-roboto font-weight-bold text-danger">{!! theMoney($ledgerData['summary']['total_liabilities']) !!}</h4>
                    <small class="text-secondary font-weight-500">Supplier & Payable Dues</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm border-left-success bg-white">
                <div class="p-3 card-body">
                    <span class="text-muted small font-weight-bold text-uppercase">Income this Month</span>
                    <h4 class="mb-0 font-roboto font-weight-bold text-success">{!! theMoney($ledgerData['summary']['total_income_month']) !!}</h4>
                    <small class="text-secondary font-weight-500">Sales & Delivery Revenue</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm border-left-warning bg-white">
                <div class="p-3 card-body">
                    <span class="text-muted small font-weight-bold text-uppercase">Expenses this Month</span>
                    <h4 class="mb-0 font-roboto font-weight-bold text-warning">{!! theMoney($ledgerData['summary']['total_expense_month']) !!}</h4>
                    <small class="text-secondary font-weight-500">Utilities, Ads, Salaries, Snacks</small>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 col-sm-12 mb-3">
            <div class="card h-100 shadow-sm bg-white border">
                <div class="p-3 card-body d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small font-weight-bold text-uppercase">Net Profit / (Loss) for {{ $ledgerData['month_name'] }}</span>
                        <h3 class="mb-0 font-roboto font-weight-bold {{ $ledgerData['summary']['net_profit_month'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {!! theMoney($ledgerData['summary']['net_profit_month']) !!}
                        </h3>
                    </div>
                    <div class="p-3 bg-light rounded-circle">
                        <i class="fa fa-calculator fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 col-sm-12 mb-3">
            <div class="card h-100 shadow-sm bg-white border">
                <div class="p-3 card-body d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small font-weight-bold text-uppercase">Net Company Worth (Assets - Liabilities)</span>
                        <h3 class="mb-0 font-roboto font-weight-bold {{ $ledgerData['summary']['company_net_balance'] >= 0 ? 'text-primary' : 'text-danger' }}">
                            {!! theMoney($ledgerData['summary']['company_net_balance']) !!}
                        </h3>
                    </div>
                    <div class="p-3 bg-light rounded-circle">
                        <i class="fa fa-briefcase fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account-Wise Double-Entry Ledger -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 font-weight-bold text-dark">
                    <i class="fa fa-book text-primary mr-2"></i> General Ledger Breakdown for {{ $ledgerData['month_name'] }}
                </h5>
                <span class="text-muted small font-weight-bold">{{ count($ledgerData['accounts']) }} Accounts Active</span>
            </div>

            @forelse($ledgerData['accounts'] as $accLedger)
                @php
                    $acc = $accLedger['account'];
                    $badgeColor = match($acc->type) {
                        'asset' => 'primary',
                        'liability' => 'danger',
                        'equity' => 'info',
                        'income' => 'success',
                        'expense' => 'warning',
                        default => 'secondary'
                    };
                @endphp
                <div class="card shadow-sm border mb-4 ledger-account-card">
                    <div class="card-header p-3 ledger-card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                        <div class="d-flex align-items-center">
                            <span class="badge badge-{{ $badgeColor }} mr-2 px-2 py-1 font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">{{ strtoupper($acc->type) }}</span>
                            <span class="ledger-account-title">{{ $acc->name }}</span>
                            @if($acc->code)
                                <span class="badge badge-light border text-dark ml-2" style="font-size: 11px;">#{{ $acc->code }}</span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                            <span class="ledger-stat-pill">Opening: <strong class="font-roboto">{!! theMoney($accLedger['opening_balance']) !!}</strong></span>
                            <span class="ledger-stat-pill text-danger">Total Dr: <strong class="font-roboto">{!! theMoney($accLedger['total_debit']) !!}</strong></span>
                            <span class="ledger-stat-pill text-success">Total Cr: <strong class="font-roboto">{!! theMoney($accLedger['total_credit']) !!}</strong></span>
                            <span class="ledger-ending-pill">Ending Balance: <span class="font-roboto">{!! theMoney($accLedger['ending_balance']) !!}</span></span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-sm mb-0 ledger-table">
                                <thead>
                                    <tr>
                                        <th style="width: 110px;">Date</th>
                                        <th style="width: 140px;">Entry #</th>
                                        <th>Description / Reference</th>
                                        <th style="width: 160px;">Category</th>
                                        <th class="text-right" style="width: 140px;">Debit (Dr)</th>
                                        <th class="text-right" style="width: 140px;">Credit (Cr)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="background-color: #f8fafc; font-style: italic;">
                                        <td class="font-roboto font-weight-bold text-muted">{{ $ledgerData['start_date'] }}</td>
                                        <td colspan="3"><strong class="text-dark">Opening Balance Brought Forward</strong></td>
                                        <td colspan="2" class="text-right font-roboto font-weight-bold text-dark">{!! theMoney($accLedger['opening_balance']) !!}</td>
                                    </tr>
                                    @forelse($accLedger['items'] as $item)
                                        <tr>
                                            <td class="font-roboto font-weight-bold text-dark">{{ \Carbon\Carbon::parse($item->entry_date)->format('M d, Y') }}</td>
                                            <td class="font-roboto small text-primary font-weight-bold">{{ $item->entry_number }}</td>
                                            <td>
                                                <div class="font-weight-500 text-dark">{{ $item->notes ?: ($item->entry_description ?: '—') }}</div>
                                                @if($item->reference)
                                                    <span class="badge badge-light border text-muted mt-1">Ref: {{ $item->reference }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->category_name)
                                                    <span class="badge badge-light-primary text-primary font-weight-bold border">#{{ $item->category_name }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="text-right font-roboto text-danger font-weight-bold">
                                                {{ $item->debit > 0 ? number_format((float)$item->debit, 2) : '—' }}
                                            </td>
                                            <td class="text-right font-roboto text-success font-weight-bold">
                                                {{ $item->credit > 0 ? number_format((float)$item->credit, 2) : '—' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-3 small">No journal transactions recorded in this month.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right text-dark">Month Totals & Net Movement:</th>
                                        <th class="text-right font-roboto text-danger font-weight-bold">{!! theMoney($accLedger['total_debit']) !!}</th>
                                        <th class="text-right font-roboto text-success font-weight-bold">{!! theMoney($accLedger['total_credit']) !!}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                @if(!$loop->last)
                    <div class="account-divider my-4 d-flex align-items-center justify-content-center no-print">
                        <div class="divider-line flex-grow-1"></div>
                        <span class="divider-badge mx-3"><i class="fa fa-ellipsis-h text-muted"></i></span>
                        <div class="divider-line flex-grow-1"></div>
                    </div>
                @endif
            @empty
                <div class="card p-5 text-center text-muted shadow-sm">
                    <h5>No accounts found.</h5>
                    <p class="mb-0">Create your initial accounts in <a href="{{ route('admin.accounting.accounts.index') }}">Chart of Accounts</a>.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal: Record Courier Payout / Sales Settlement -->
<div class="modal fade" id="courierPayoutModal" tabindex="-1" role="dialog" aria-labelledby="courierPayoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('admin.accounting.ledger.courier-payout') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white p-3">
                    <h5 class="modal-title font-weight-bold text-white" id="courierPayoutModalLabel">
                        <i class="fa fa-truck mr-2"></i> Record Courier Payout / Sales Settlement
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4 bg-white text-dark">
                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="fa fa-info-circle mr-1"></i> Use this form to post courier payout settlements (Steadfast, Pathao, Redx, Paperfly) into your double-entry accounting ledger.
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="entry_date_cp" class="font-weight-bold text-dark">Payout Date <span class="text-danger">*</span></label>
                            <input type="date" name="entry_date" id="entry_date_cp" class="form-control text-dark font-weight-bold" value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="courier_name_cp" class="font-weight-bold text-dark">Courier Service Name <span class="text-danger">*</span></label>
                            <input type="text" name="courier_name" id="courier_name_cp" class="form-control text-dark" placeholder="e.g. Steadfast Courier, Pathao Courier" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="total_sales_cp" class="font-weight-bold text-dark">Total Delivered Sales (COD Amount) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="total_sales" id="total_sales_cp" class="form-control font-weight-bold text-success" placeholder="e.g. 50000.00" required>
                            <small class="text-muted">Total value of orders delivered to customers.</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="courier_charge_cp" class="font-weight-bold text-dark">Delivery Fee Deducted by Courier</label>
                            <input type="number" step="0.01" name="courier_charge" id="courier_charge_cp" class="form-control font-weight-bold text-danger" placeholder="e.g. 3500.00" value="0">
                            <small class="text-muted">Delivery fee and COD charge subtracted by courier.</small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="deposit_account_id_cp" class="font-weight-bold text-dark">Deposit To Account (Received Net Amount) <span class="text-danger">*</span></label>
                            <select name="deposit_account_id" id="deposit_account_id_cp" class="form-control text-dark font-weight-bold" required>
                                @foreach($accounts->where('type', 'asset') as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} ({{ number_format($acc->current_balance, 2) }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Bank account or bKash wallet where money was deposited.</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="reference_cp" class="font-weight-bold text-dark">Payout / Settlement Invoice #</label>
                            <input type="text" name="reference" id="reference_cp" class="form-control text-dark" placeholder="e.g. STDF-PAYOUT-9823">
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label for="description_cp" class="font-weight-bold text-dark">Notes / Remarks</label>
                        <input type="text" name="description" id="description_cp" class="form-control text-dark" placeholder="e.g. Weekly settlement payment received via bank transfer">
                    </div>
                </div>
                <div class="modal-footer p-3 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 font-weight-bold"><i class="fa fa-check mr-1"></i> Post Payout to Ledger</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin.accounting.partials.tutorial-modal')
@endsection

@push('css')
<style>
    .border-left-primary { border-left: 4px solid #4f46e5 !important; }
    .border-left-danger  { border-left: 4px solid #ef4444 !important; }
    .border-left-success { border-left: 4px solid #10b981 !important; }
    .border-left-warning { border-left: 4px solid #f59e0b !important; }

    /* Guarantee high contrast text in all ledger card headers */
    .ledger-account-card {
        border: 1px solid #cbd5e1 !important;
        background: #ffffff !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
        border-radius: 8px !important;
        overflow: hidden;
    }
    .account-divider {
        position: relative;
        margin: 2.75rem 0 !important;
    }
    .divider-line {
        height: 2px;
        background: linear-gradient(to right, transparent, #cbd5e1 20%, #94a3b8 50%, #cbd5e1 80%, transparent);
    }
    .divider-badge {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 11px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .ledger-card-header {
        background-color: #f1f5f9 !important;
        border-bottom: 1.5px solid #cbd5e1 !important;
    }
    .ledger-account-title {
        color: #0f172a !important;
        font-size: 1.05rem !important;
        font-weight: 700 !important;
    }
    .ledger-stat-pill {
        background-color: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        color: #334155 !important;
        padding: 4px 10px !important;
        border-radius: 4px !important;
        font-size: 12.5px !important;
        display: inline-block;
    }
    .ledger-stat-pill strong {
        color: inherit !important;
    }
    .ledger-ending-pill {
        background-color: #eff6ff !important;
        border: 1.5px solid #3b82f6 !important;
        color: #1d4ed8 !important;
        padding: 4px 12px !important;
        border-radius: 4px !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        display: inline-block;
    }
    .ledger-ending-pill span {
        color: #1d4ed8 !important;
        font-weight: 700 !important;
    }
    .ledger-table thead th {
        background-color: #f8fafc !important;
        color: #1e293b !important;
        font-weight: 700 !important;
        border-bottom: 2px solid #cbd5e1 !important;
        font-size: 12px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }
    .ledger-table tbody td {
        color: #1e293b !important;
        vertical-align: middle !important;
    }
    .ledger-table tbody tr:hover {
        background-color: #f8fafc !important;
    }
    .ledger-table tfoot th {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        font-weight: 700 !important;
    }

    @media print {
        .main-nav, .page-main-header, .footer, .breadcrumb, .breadcrumb-right, .no-print, .modal {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        .print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .page-body {
            font-size: 14px;
            margin: 0 !important;
            padding: 0 !important;
        }

        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
            page-break-inside: avoid !important;
            margin-bottom: 15px !important;
        }

        .table {
            border-collapse: collapse !important;
            width: 100% !important;
        }

        .table th, .table td {
            border: 1px solid #000 !important;
            padding: 6px !important;
        }
    }
</style>
@endpush
