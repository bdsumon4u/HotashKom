@extends('layouts.light.master')

@section('title', 'Account Ledger: ' . $account->name)

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/daterangepicker.css') }}">
    <style>
        .kpi-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
            transition: all 0.2s ease;
        }
        .kpi-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .nav-tabs .nav-link {
            font-weight: 600;
            color: #64748b;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 10px 18px;
        }
        .nav-tabs .nav-link.active {
            color: #3b82f6;
            background: transparent;
            border-bottom: 3px solid #3b82f6;
        }
        .table-statement thead th {
            background-color: #f8fafc;
            color: #1e293b;
            font-weight: 700;
            font-size: 13px;
            border-bottom: 2px solid #cbd5e1;
            padding: 10px 12px;
        }
        .table-statement tbody td {
            vertical-align: middle;
            padding: 9px 12px;
            font-size: 13.5px;
        }
        .table-statement tfoot th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 800;
            font-size: 14px;
            border-top: 2px solid #94a3b8;
            padding: 12px;
        }
        @media print {
            .page-main-header,
            .main-nav,
            .sidebar-wrapper,
            .breadcrumb,
            .breadcrumb-right,
            .no-print,
            .nav-tabs,
            .btn,
            footer {
                display: none !important;
            }
            .page-body, .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
            }
            .tab-pane {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
            }
            #print-account-header {
                display: block !important;
                margin-bottom: 20px;
                border-bottom: 2px solid #000;
                padding-bottom: 12px;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            .card-header {
                background: #f1f5f9 !important;
                color: #000 !important;
                border: 1px solid #ccc !important;
            }
            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            th, td {
                border: 1px solid #ddd !important;
                color: #000 !important;
            }
        }
    </style>
@endpush

@section('breadcrumb-title')
    <h3>Account Statement & Ledger</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Accounting</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.accounting.accounts.index') }}">Accounts</a></li>
    <li class="breadcrumb-item active">{{ $account->name }}</li>
@endsection

@section('breadcrumb-right')
    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
        <!-- Date range picker filter -->
        <div id="reportrange" class="form-control d-flex align-items-center justify-content-between" style="cursor: pointer; min-width: 230px; background: #fff; font-size: 13px;">
            <i class="fa fa-calendar text-primary mr-2"></i>
            <span class="flex-grow-1 text-center font-weight-bold"></span>
            <i class="fa fa-angle-down text-muted ml-2"></i>
        </div>

        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fa fa-print mr-1"></i> Print Statement
        </button>
        <a href="{{ route('admin.accounting.transactions.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus mr-1"></i> New Transaction
        </a>
        <a href="{{ route('admin.accounting.accounts.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="fa fa-list mr-1"></i> All Accounts
        </a>
    </div>
@endsection

@section('content')
<!-- Print Header (Visible only on print) -->
<div id="print-account-header" style="display: none;">
    <div style="display: flex; justify-content-between; align-items: flex-start;">
        <div>
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e293b;">Account Ledger Statement</h2>
            <div style="font-size: 16px; font-weight: bold; margin-top: 4px; color: #0f172a;">
                {{ $account->code ? '[' . $account->code . '] ' : '' }}{{ $account->name }}
                <span style="font-size: 13px; font-weight: normal; color: #475569;">({{ strtoupper($account->type) }})</span>
            </div>
            @if($account->description)
                <div style="font-size: 12px; color: #64748b;">{{ $account->description }}</div>
            @endif
        </div>
        <div style="text-align: right; font-size: 12px; color: #475569;">
            <div><strong>Statement Period:</strong> {{ $startDate->format('M d, Y') }} — {{ $endDate->format('M d, Y') }}</div>
            <div><strong>Generated:</strong> {{ now()->format('M d, Y h:i A') }}</div>
            <div><strong>Account Nature:</strong> {{ $isDebitNature ? 'Debit Normal (Asset/Expense)' : 'Credit Normal (Liability/Equity/Income)' }}</div>
        </div>
    </div>
    <div style="margin-top: 15px; padding: 10px; background: #f8fafc; border: 1px solid #cbd5e1; display: flex; justify-content: space-between; font-size: 13px;">
        <span><strong>Opening Balance:</strong> {!! theMoney($openingBalance) !!}</span>
        <span><strong>Total Debits (Dr):</strong> {!! theMoney($totalDebit) !!}</span>
        <span><strong>Total Credits (Cr):</strong> {!! theMoney($totalCredit) !!}</span>
        <span><strong>Net Movement:</strong> {!! theMoney($netMovement) !!}</span>
        <span><strong>Closing Balance:</strong> {!! theMoney($closingBalance) !!}</span>
    </div>
</div>

<div class="container-fluid mb-5">
    <!-- Account Information & KPI Cards -->
    <div class="card shadow-sm border mb-4 no-print" style="background: #ffffff;">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 15px;">
                <div class="d-flex align-items-center">
                    <div class="mr-3 p-3 bg-light-primary text-primary rounded-circle">
                        <i class="fa fa-university fa-2x"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center">
                            <h4 class="font-weight-bold text-dark mb-0 mr-2">{{ $account->name }}</h4>
                            <span class="badge badge-primary px-2 py-1 mr-1">{{ strtoupper($account->type) }}</span>
                            @if($account->code)
                                <span class="badge badge-light border text-muted">#{{ $account->code }}</span>
                            @endif
                            @if($account->is_system)
                                <span class="badge badge-light border text-muted ml-1">System Account</span>
                            @endif
                        </div>
                        <span class="text-muted small">
                            Nature: <strong>{{ $isDebitNature ? 'Debit Normal (+ with Debit, - with Credit)' : 'Credit Normal (+ with Credit, - with Debit)' }}</strong>
                            @if($account->description) | {{ $account->description }} @endif
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-muted small">Period: <strong>{{ $startDate->format('M d, Y') }} — {{ $endDate->format('M d, Y') }}</strong></div>
                    <div class="font-roboto font-weight-bold text-dark" style="font-size: 15px;">
                        All-Time Current Balance: <span class="text-primary font-weight-bold">{!! theMoney($account->current_balance) !!}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5 KPI Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl col-md-4 col-sm-6 mb-3">
            <div class="kpi-card p-3 shadow-sm h-100" style="border-left: 4px solid #64748b !important;">
                <span class="text-muted small d-block">Opening Balance ({{ $startDate->format('M d') }})</span>
                <h4 class="font-roboto font-weight-bold mb-0 mt-1 text-dark">{!! theMoney($openingBalance) !!}</h4>
                <small class="text-muted">Balance prior to period</small>
            </div>
        </div>
        <div class="col-xl col-md-4 col-sm-6 mb-3">
            <div class="kpi-card p-3 shadow-sm h-100" style="border-left: 4px solid #3b82f6 !important;">
                <span class="text-muted small d-block">Total Period Debits (Dr)</span>
                <h4 class="font-roboto font-weight-bold mb-0 mt-1 text-primary">{!! theMoney($totalDebit) !!}</h4>
                <small class="text-muted">{{ count($transactions) }} transaction item(s)</small>
            </div>
        </div>
        <div class="col-xl col-md-4 col-sm-6 mb-3">
            <div class="kpi-card p-3 shadow-sm h-100" style="border-left: 4px solid #ef4444 !important;">
                <span class="text-muted small d-block">Total Period Credits (Cr)</span>
                <h4 class="font-roboto font-weight-bold mb-0 mt-1 text-danger">{!! theMoney($totalCredit) !!}</h4>
                <small class="text-muted">Outflows / Reductions</small>
            </div>
        </div>
        <div class="col-xl col-md-6 col-sm-6 mb-3">
            <div class="kpi-card p-3 shadow-sm h-100" style="border-left: 4px solid {{ $netMovement >= 0 ? '#10b981' : '#f59e0b' }} !important;">
                <span class="text-muted small d-block">Period Net Movement</span>
                <h4 class="font-roboto font-weight-bold mb-0 mt-1 {{ $netMovement >= 0 ? 'text-success' : 'text-warning' }}">
                    {!! theMoney($netMovement) !!}
                </h4>
                <small class="text-muted">{{ $netMovement >= 0 ? 'Net positive inflow' : 'Net reduction in period' }}</small>
            </div>
        </div>
        <div class="col-xl col-md-6 col-sm-12 mb-3">
            <div class="kpi-card p-3 shadow-sm h-100" style="border-left: 4px solid #8b5cf6 !important; background: #faf5ff;">
                <span class="text-muted small d-block font-weight-bold text-dark">Closing Balance ({{ $endDate->format('M d') }})</span>
                <h4 class="font-roboto font-weight-bold mb-0 mt-1 text-primary">{!! theMoney($closingBalance) !!}</h4>
                <small class="text-muted">As of end of selected period</small>
            </div>
        </div>
    </div>

    <!-- Tabbed Ledger Views -->
    <div class="card shadow-sm border" style="background: #ffffff;">
        <div class="card-header p-2 bg-light border-bottom no-print">
            <ul class="nav nav-tabs card-header-tabs" id="accountTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="transactions-tab" data-toggle="tab" href="#transactions-view" role="tab" aria-controls="transactions-view" aria-selected="true">
                        <i class="fa fa-list-ul mr-1"></i> Detailed Transactions ({{ count($transactions) }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="daily-tab" data-toggle="tab" href="#daily-view" role="tab" aria-controls="daily-view" aria-selected="false">
                        <i class="fa fa-calendar-o mr-1"></i> Daily Summary ({{ count($dailySummary) }} Days)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="monthly-tab" data-toggle="tab" href="#monthly-view" role="tab" aria-controls="monthly-view" aria-selected="false">
                        <i class="fa fa-calendar mr-1"></i> Monthly Summary ({{ count($monthlySummary) }} Months)
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content" id="accountTabsContent">
                <!-- TAB 1: ALL TRANSACTIONS (DETAILED LEDGER) -->
                <div class="tab-pane fade show active" id="transactions-view" role="tabpanel" aria-labelledby="transactions-tab">
                    <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fa fa-exchange text-primary mr-1"></i> Transaction History (General Ledger)
                        </h6>
                        <span class="text-muted small">All journal items affecting this account in date order</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-statement mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 110px;">Date</th>
                                    <th style="width: 130px;">Entry #</th>
                                    <th style="width: 130px;">Reference</th>
                                    <th>Description / Notes</th>
                                    <th style="width: 140px;">Category</th>
                                    <th class="text-right" style="width: 140px;">Debit Dr (+)</th>
                                    <th class="text-right" style="width: 140px;">Credit Cr (-)</th>
                                    <th class="text-right" style="width: 160px;">Running Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Opening Balance Row -->
                                <tr class="bg-light font-weight-bold" style="background-color: #f8fafc !important;">
                                    <td class="font-roboto text-muted">{{ $startDate->format('Y-m-d') }}</td>
                                    <td colspan="4" class="text-dark">
                                        <i class="fa fa-clock-o text-muted mr-1"></i> Opening Balance Brought Forward
                                    </td>
                                    <td class="text-right font-roboto text-muted">—</td>
                                    <td class="text-right font-roboto text-muted">—</td>
                                    <td class="text-right font-roboto font-weight-bold text-dark" style="font-size: 1.05rem;">
                                        {!! theMoney($openingBalance) !!}
                                    </td>
                                </tr>

                                @forelse($transactions as $txn)
                                    <tr>
                                        <td class="font-roboto font-weight-bold text-muted">{{ $txn['date'] }}</td>
                                        <td>
                                            @if($txn['source_type'] && str_ends_with($txn['source_type'], 'Movement'))
                                                <a href="{{ route('admin.accounting.movements.show', $txn['journal_entry_id']) }}" class="font-roboto font-weight-bold text-primary">
                                                    {{ $txn['entry_number'] ?? ('#' . $txn['journal_entry_id']) }}
                                                </a>
                                            @else
                                                <span class="font-roboto font-weight-bold text-dark">{{ $txn['entry_number'] ?? ('#' . $txn['journal_entry_id']) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-muted font-roboto small">{{ $txn['reference'] ?? '—' }}</td>
                                        <td>
                                            <div class="font-weight-bold text-dark">{{ $txn['description'] ?: 'Transaction' }}</div>
                                            @if($txn['notes'])
                                                <div class="text-muted small">{{ $txn['notes'] }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($txn['category'])
                                                <span class="badge badge-light border text-dark">{{ $txn['category'] }}</span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td class="text-right font-roboto font-weight-bold {{ $txn['debit'] > 0 ? 'text-primary' : 'text-muted' }}">
                                            {{ $txn['debit'] > 0 ? theMoney($txn['debit']) : '—' }}
                                        </td>
                                        <td class="text-right font-roboto font-weight-bold {{ $txn['credit'] > 0 ? 'text-danger' : 'text-muted' }}">
                                            {{ $txn['credit'] > 0 ? theMoney($txn['credit']) : '—' }}
                                        </td>
                                        <td class="text-right font-roboto font-weight-bold text-dark">
                                            {!! theMoney($txn['balance']) !!}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            No transactions recorded for this account in the selected date range.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-right font-weight-bold">Total Period Summation:</th>
                                    <th class="text-right font-roboto font-weight-bold text-primary">{!! theMoney($totalDebit) !!}</th>
                                    <th class="text-right font-roboto font-weight-bold text-danger">{!! theMoney($totalCredit) !!}</th>
                                    <th class="text-right font-roboto font-weight-bold text-dark">{!! theMoney($closingBalance) !!}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- TAB 2: DAILY SUMMARY -->
                <div class="tab-pane fade" id="daily-view" role="tabpanel" aria-labelledby="daily-tab">
                    <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fa fa-calendar-check-o text-primary mr-1"></i> Daily Transaction Summary
                        </h6>
                        <span class="text-muted small">Aggregated debits, credits, and ending balance by day</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-statement mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 160px;">Date</th>
                                    <th class="text-center" style="width: 140px;">Transactions Count</th>
                                    <th class="text-right" style="width: 180px;">Total Debit Dr (+)</th>
                                    <th class="text-right" style="width: 180px;">Total Credit Cr (-)</th>
                                    <th class="text-right" style="width: 180px;">Daily Net Movement</th>
                                    <th class="text-right" style="width: 200px;">Day Ending Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dailySummary as $day)
                                    <tr>
                                        <td class="font-roboto font-weight-bold text-dark">{{ $day['date'] }}</td>
                                        <td class="text-center font-roboto">
                                            <span class="badge badge-light border px-2 py-1 font-weight-bold">{{ $day['count'] }}</span>
                                        </td>
                                        <td class="text-right font-roboto font-weight-bold text-primary">
                                            {{ $day['debit'] > 0 ? theMoney($day['debit']) : '—' }}
                                        </td>
                                        <td class="text-right font-roboto font-weight-bold text-danger">
                                            {{ $day['credit'] > 0 ? theMoney($day['credit']) : '—' }}
                                        </td>
                                        <td class="text-right font-roboto font-weight-bold {{ $day['net_movement'] >= 0 ? 'text-success' : 'text-warning' }}">
                                            {!! theMoney($day['net_movement']) !!}
                                        </td>
                                        <td class="text-right font-roboto font-weight-bold text-dark">
                                            {!! theMoney($day['closing_balance']) !!}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No daily activity recorded in this period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-right font-weight-bold">Grand Total Summation:</th>
                                    <th class="text-center font-roboto font-weight-bold">{{ count($transactions) }}</th>
                                    <th class="text-right font-roboto font-weight-bold text-primary">{!! theMoney($totalDebit) !!}</th>
                                    <th class="text-right font-roboto font-weight-bold text-danger">{!! theMoney($totalCredit) !!}</th>
                                    <th class="text-right font-roboto font-weight-bold {{ $netMovement >= 0 ? 'text-success' : 'text-warning' }}">{!! theMoney($netMovement) !!}</th>
                                    <th class="text-right font-roboto font-weight-bold text-dark">{!! theMoney($closingBalance) !!}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- TAB 3: MONTHLY SUMMARY -->
                <div class="tab-pane fade" id="monthly-view" role="tabpanel" aria-labelledby="monthly-tab">
                    <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fa fa-calendar text-primary mr-1"></i> Monthly Transaction Summary
                        </h6>
                        <span class="text-muted small">Aggregated debits, credits, and ending balance by month</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-statement mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 200px;">Month</th>
                                    <th class="text-center" style="width: 140px;">Transactions Count</th>
                                    <th class="text-right" style="width: 180px;">Total Debit Dr (+)</th>
                                    <th class="text-right" style="width: 180px;">Total Credit Cr (-)</th>
                                    <th class="text-right" style="width: 180px;">Monthly Net Movement</th>
                                    <th class="text-right" style="width: 200px;">Month Ending Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monthlySummary as $m)
                                    <tr>
                                        <td class="font-roboto font-weight-bold text-dark">
                                            <i class="fa fa-calendar-check-o text-muted mr-1"></i> {{ $m['month_label'] }}
                                        </td>
                                        <td class="text-center font-roboto">
                                            <span class="badge badge-light border px-2 py-1 font-weight-bold">{{ $m['count'] }}</span>
                                        </td>
                                        <td class="text-right font-roboto font-weight-bold text-primary">
                                            {{ $m['debit'] > 0 ? theMoney($m['debit']) : '—' }}
                                        </td>
                                        <td class="text-right font-roboto font-weight-bold text-danger">
                                            {{ $m['credit'] > 0 ? theMoney($m['credit']) : '—' }}
                                        </td>
                                        <td class="text-right font-roboto font-weight-bold {{ $m['net_movement'] >= 0 ? 'text-success' : 'text-warning' }}">
                                            {!! theMoney($m['net_movement']) !!}
                                        </td>
                                        <td class="text-right font-roboto font-weight-bold text-dark">
                                            {!! theMoney($m['closing_balance']) !!}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No monthly activity recorded in this period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-right font-weight-bold">Grand Total Summation:</th>
                                    <th class="text-center font-roboto font-weight-bold">{{ count($transactions) }}</th>
                                    <th class="text-right font-roboto font-weight-bold text-primary">{!! theMoney($totalDebit) !!}</th>
                                    <th class="text-right font-roboto font-weight-bold text-danger">{!! theMoney($totalCredit) !!}</th>
                                    <th class="text-right font-roboto font-weight-bold {{ $netMovement >= 0 ? 'text-success' : 'text-warning' }}">{!! theMoney($netMovement) !!}</th>
                                    <th class="text-right font-roboto font-weight-bold text-dark">{!! theMoney($closingBalance) !!}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script src="{{ asset('assets/js/datepicker/daterange-picker/moment.min.js') }}"></script>
    <script>
        window._start = moment('{{ $startDate->format('Y-m-d') }}');
        window._end = moment('{{ $endDate->format('Y-m-d') }}');

        window.reportRangeCB = function(start, end) {
            window._start = start;
            window._end = end;
            refresh();
        };

        function refresh() {
            window.location = "{!! route('admin.accounting.accounts.show', [
                'account' => $account->id,
                'start_d' => '_start',
                'end_d' => '_end',
            ]) !!}".replace('_start', window._start.format('YYYY-MM-DD'))
                .replace('_end', window._end.format('YYYY-MM-DD'));
        }
    </script>
    <script src="{{ asset('assets/js/datepicker/daterange-picker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/daterange-picker/daterange-picker.custom.js') }}"></script>
@endpush

