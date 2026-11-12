@extends('layouts.light.master')

@section('title', 'Stock Adjustments & Damage Report')

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
            color: #ef4444;
            background: transparent;
            border-bottom: 3px solid #ef4444;
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
            #print-adjustments-header {
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
    <h3>Stock Adjustments & Damage Report</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Accounting</li>
    <li class="breadcrumb-item active">Stock Adjustments</li>
@endsection

@section('breadcrumb-right')
    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
        <!-- Date range picker filter -->
        <div id="reportrange" class="form-control d-flex align-items-center justify-content-between" style="cursor: pointer; min-width: 230px; background: #fff; font-size: 13px;">
            <i class="fa fa-calendar text-danger mr-2"></i>
            <span class="flex-grow-1 text-center font-weight-bold"></span>
            <i class="fa fa-angle-down text-muted ml-2"></i>
        </div>

        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fa fa-print mr-1"></i> Print Report
        </button>
        <a href="{{ route('admin.accounting.adjustments.create') }}" class="btn btn-danger btn-sm">
            <i class="fa fa-plus mr-1"></i> New Stock Adjustment
        </a>
    </div>
@endsection

@section('content')
<!-- Print Header (Visible only on print) -->
<div id="print-adjustments-header" style="display: none;">
    <div style="display: flex; justify-content-between; align-items: flex-start;">
        <div>
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e293b;">Stock Adjustments & Damage Summary</h2>
            <div style="font-size: 13px; color: #64748b; margin-top: 4px;">Warehouse write-offs, damaged goods, promotional samples, and personal drawings</div>
        </div>
        <div style="text-align: right; font-size: 12px; color: #475569;">
            <div><strong>Period:</strong> {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</div>
            <div><strong>Generated:</strong> {{ now()->format('M d, Y h:i A') }}</div>
        </div>
    </div>
    <div style="margin-top: 15px; padding: 10px; background: #f8fafc; border: 1px solid #cbd5e1; display: flex; justify-content: space-between; font-size: 13px;">
        <span><strong>Total Adjusted Value:</strong> {!! theMoney($grandTotals['total_amount']) !!}</span>
        <span><strong>Damaged Goods:</strong> {!! theMoney($grandTotals['damage_amount']) !!}</span>
        <span><strong>Gifts & Samples:</strong> {!! theMoney($grandTotals['gift_amount']) !!}</span>
        <span><strong>Personal Drawings:</strong> {!! theMoney($grandTotals['drawing_amount']) !!}</span>
        <span><strong>Total Entries:</strong> {{ $grandTotals['count'] }}</span>
    </div>
</div>

<div class="container-fluid mb-5">
    <!-- 4 KPI Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
            <div class="kpi-card p-3 shadow-sm h-100" style="border-left: 4px solid #ef4444 !important;">
                <span class="text-muted small d-block">Total Stock Adjustment Value</span>
                <h4 class="font-roboto font-weight-bold mb-0 mt-1 text-danger">{!! theMoney($grandTotals['total_amount']) !!}</h4>
                <small class="text-muted">{{ $grandTotals['count'] }} write-off transaction(s)</small>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
            <div class="kpi-card p-3 shadow-sm h-100" style="border-left: 4px solid #dc2626 !important;">
                <span class="text-muted small d-block">Damaged / Expired Goods</span>
                <h4 class="font-roboto font-weight-bold mb-0 mt-1 text-danger">{!! theMoney($grandTotals['damage_amount']) !!}</h4>
                <small class="text-muted">Charged to Damaged Goods Expense</small>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
            <div class="kpi-card p-3 shadow-sm h-100" style="border-left: 4px solid #3b82f6 !important;">
                <span class="text-muted small d-block">Promotional Gifts / Samples</span>
                <h4 class="font-roboto font-weight-bold mb-0 mt-1 text-primary">{!! theMoney($grandTotals['gift_amount']) !!}</h4>
                <small class="text-muted">Charged to Marketing Expense</small>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
            <div class="kpi-card p-3 shadow-sm h-100" style="border-left: 4px solid #8b5cf6 !important;">
                <span class="text-muted small d-block">Owner's Personal Drawings</span>
                <h4 class="font-roboto font-weight-bold mb-0 mt-1 text-dark">{!! theMoney($grandTotals['drawing_amount']) !!}</h4>
                <small class="text-muted">Deducted from Owner Equity</small>
            </div>
        </div>
    </div>

    <!-- Tabbed Ledger Views -->
    <div class="card shadow-sm border" style="background: #ffffff;">
        <div class="card-header p-2 bg-light border-bottom no-print">
            <ul class="nav nav-tabs card-header-tabs" id="adjustmentTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="monthly-tab" data-toggle="tab" href="#monthly-view" role="tab" aria-controls="monthly-view" aria-selected="true">
                        <i class="fa fa-calendar mr-1"></i> Monthly Summary ({{ count($monthlySummary) }} Months)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="daily-tab" data-toggle="tab" href="#daily-view" role="tab" aria-controls="daily-view" aria-selected="false">
                        <i class="fa fa-calendar-o mr-1"></i> Daily Summary ({{ count($dailySummary) }} Days)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="records-tab" data-toggle="tab" href="#records-view" role="tab" aria-controls="records-view" aria-selected="false">
                        <i class="fa fa-list-ul mr-1"></i> Detailed Records ({{ count($mapped) }})
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content" id="adjustmentTabsContent">
                <!-- TAB 1: MONTHLY SUMMARY -->
                <div class="tab-pane fade show active" id="monthly-view" role="tabpanel" aria-labelledby="monthly-tab">
                    <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fa fa-calendar text-danger mr-1"></i> Monthly Stock Adjustment Summary
                        </h6>
                        <span class="text-muted small">Aggregated write-off amounts and reasons grouped by month</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-statement mb-0">
                            <thead>
                                <tr>
                                    <th style="min-width: 150px;">Month</th>
                                    <th class="text-center" style="width: 120px;">Entries Count</th>
                                    <th class="text-right" style="width: 170px;">Total Adjusted Value</th>
                                    <th class="text-right" style="width: 170px;">Damage Expense</th>
                                    <th class="text-right" style="width: 170px;">Gift / Marketing</th>
                                    <th class="text-right" style="width: 170px;">Personal Drawing</th>
                                    <th class="text-right" style="width: 150px;">Other Adjustments</th>
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
                                        <td class="text-right font-roboto font-weight-bold text-danger">
                                            {!! theMoney($m['total_amount']) !!}
                                        </td>
                                        <td class="text-right font-roboto text-danger font-weight-bold">
                                            {{ $m['damage_amount'] > 0 ? theMoney($m['damage_amount']) : '—' }}
                                        </td>
                                        <td class="text-right font-roboto text-primary font-weight-bold">
                                            {{ $m['gift_amount'] > 0 ? theMoney($m['gift_amount']) : '—' }}
                                        </td>
                                        <td class="text-right font-roboto text-dark font-weight-bold">
                                            {{ $m['drawing_amount'] > 0 ? theMoney($m['drawing_amount']) : '—' }}
                                        </td>
                                        <td class="text-right font-roboto text-muted">
                                            {{ $m['other_amount'] > 0 ? theMoney($m['other_amount']) : '—' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No stock adjustments recorded in this period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="font-weight-bold text-dark">Grand Total Summation:</th>
                                    <th class="text-center font-roboto font-weight-bold text-dark">{{ $grandTotals['count'] }}</th>
                                    <th class="text-right font-roboto font-weight-bold text-danger">{!! theMoney($grandTotals['total_amount']) !!}</th>
                                    <th class="text-right font-roboto font-weight-bold text-danger">{!! theMoney($grandTotals['damage_amount']) !!}</th>
                                    <th class="text-right font-roboto font-weight-bold text-primary">{!! theMoney($grandTotals['gift_amount']) !!}</th>
                                    <th class="text-right font-roboto font-weight-bold text-dark">{!! theMoney($grandTotals['drawing_amount']) !!}</th>
                                    <th class="text-right font-roboto font-weight-bold text-muted">{!! theMoney($grandTotals['other_amount']) !!}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- TAB 2: DAILY SUMMARY -->
                <div class="tab-pane fade" id="daily-view" role="tabpanel" aria-labelledby="daily-tab">
                    <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fa fa-calendar-check-o text-danger mr-1"></i> Daily Stock Adjustment Summary
                        </h6>
                        <span class="text-muted small">Aggregated write-off values by date</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-statement mb-0">
                            <thead>
                                <tr>
                                    <th style="min-width: 160px;">Date</th>
                                    <th class="text-center" style="width: 150px;">Entries Count</th>
                                    <th class="text-right" style="width: 200px;">Daily Adjusted Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dailySummary as $day)
                                    <tr>
                                        <td class="font-roboto font-weight-bold text-dark">{{ $day['date_formatted'] }}</td>
                                        <td class="text-center font-roboto">
                                            <span class="badge badge-light border px-2 py-1 font-weight-bold">{{ $day['count'] }}</span>
                                        </td>
                                        <td class="text-right font-roboto font-weight-bold text-danger">
                                            {!! theMoney($day['total_amount']) !!}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">No daily activity recorded in this period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="font-weight-bold text-dark">Grand Total Summation:</th>
                                    <th class="text-center font-roboto font-weight-bold text-dark">{{ $grandTotals['count'] }}</th>
                                    <th class="text-right font-roboto font-weight-bold text-danger">{!! theMoney($grandTotals['total_amount']) !!}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- TAB 3: DETAILED RECORDS -->
                <div class="tab-pane fade" id="records-view" role="tabpanel" aria-labelledby="records-tab">
                    <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fa fa-list text-danger mr-1"></i> Detailed Stock Adjustment Records
                        </h6>
                        <span class="text-muted small">All stock adjustment journal vouchers with products and target accounts</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-statement mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 110px;">Date</th>
                                    <th style="width: 130px;">Voucher #</th>
                                    <th>Description / Reason</th>
                                    <th style="width: 220px;">Target Account</th>
                                    <th class="text-right" style="width: 160px;">Amount (৳)</th>
                                    <th class="text-center no-print" style="width: 90px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mapped as $row)
                                    <tr>
                                        <td class="font-roboto font-weight-bold text-muted">{{ $row['entry_date'] }}</td>
                                        <td class="font-roboto font-weight-bold">
                                            <a href="{{ route('admin.accounting.movements.show', $row['id']) }}" class="text-primary font-weight-bold">
                                                {{ $row['entry_number'] ?? ('#' . $row['id']) }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark">{{ $row['description'] }}</div>
                                            @if($row['notes'])
                                                <div class="text-muted small">{{ $row['notes'] }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-light border text-dark">{{ $row['target_account'] }}</span>
                                        </td>
                                        <td class="text-right font-roboto font-weight-bold text-danger">
                                            {!! theMoney($row['amount']) !!}
                                        </td>
                                        <td class="text-center no-print">
                                            <a href="{{ route('admin.accounting.movements.show', $row['id']) }}" class="btn btn-outline-primary btn-xs" title="View Voucher">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No adjustment entries found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right font-weight-bold text-dark">Grand Total Summation:</th>
                                    <th class="text-right font-roboto font-weight-bold text-danger">{!! theMoney($grandTotals['total_amount']) !!}</th>
                                    <th class="no-print"></th>
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
        window._start = moment('{{ $startDate }}');
        window._end = moment('{{ $endDate }}');

        window.reportRangeCB = function(start, end) {
            window._start = start;
            window._end = end;
            refresh();
        };

        function refresh() {
            window.location = "{!! route('admin.accounting.adjustments.index', [
                'start_d' => '_start',
                'end_d' => '_end',
            ]) !!}".replace('_start', window._start.format('YYYY-MM-DD'))
                .replace('_end', window._end.format('YYYY-MM-DD'));
        }
    </script>
    <script src="{{ asset('assets/js/datepicker/daterange-picker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/daterange-picker/daterange-picker.custom.js') }}"></script>
@endpush

