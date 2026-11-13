@extends('layouts.light.master')

@section('title', $title)

@push('css')
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
        @media print {
            .page-main-header,
            .main-nav,
            .sidebar-wrapper,
            .breadcrumb,
            .breadcrumb-right,
            .no-print,
            .btn,
            .card-footer,
            footer {
                display: none !important;
            }
            .page-body, .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
            }
            #print-movements-header {
                display: block !important;
                margin-bottom: 20px;
                border-bottom: 2px solid #000;
                padding-bottom: 12px;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
                margin-bottom: 20px !important;
            }
            .card-header {
                background: #f8fafc !important;
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
    <h3>{{ $title }}</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Accounting</li>
    <li class="breadcrumb-item active">{{ $title }}</li>
@endsection

@section('breadcrumb-right')
    <div class="d-flex align-items-center flex-wrap no-print" style="gap: 8px;">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fa fa-print mr-1"></i> Print Report
        </button>
        @if($sourceType === 'Shipment')
            <a href="{{ route('admin.accounting.shipments.create') }}" class="btn btn-primary btn-sm font-weight-bold">
                <i class="fa fa-truck mr-1"></i> Ship to Courier
            </a>
            <a href="{{ route('admin.reports.shipment') }}" class="btn btn-outline-primary btn-sm font-weight-bold">
                <i class="fa fa-bar-chart mr-1"></i> Shipment Report
            </a>
        @else
            <a href="{{ route('admin.accounting.returns.create') }}" class="btn btn-primary btn-sm font-weight-bold">
                <i class="fa fa-undo mr-1"></i> Receive Courier Return
            </a>
            <a href="{{ route('admin.reports.return') }}" class="btn btn-outline-primary btn-sm font-weight-bold">
                <i class="fa fa-bar-chart mr-1"></i> Return Report
            </a>
        @endif
        <a href="{{ route('admin.accounting.transactions.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa fa-list mr-1"></i> Transactions
        </a>
    </div>
@endsection

@section('content')
<!-- Print Header (Visible only on print) -->
<div id="print-movements-header" style="display: none;">
    <div style="display: flex; justify-content-between; align-items: flex-start;">
        <div>
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e293b;">{{ $title }} — Product Summary</h2>
            <div style="font-size: 13px; color: #64748b; margin-top: 4px;">
                {{ $sourceType === 'Shipment' ? 'Products dispatched to courier with quantities and purchase cost' : 'Products returned from courier with quantities and purchase cost' }}
            </div>
        </div>
        <div style="text-align: right; font-size: 12px; color: #475569;">
            <div><strong>Selected Period:</strong> {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</div>
            <div><strong>Generated:</strong> {{ now()->format('M d, Y h:i A') }}</div>
        </div>
    </div>
    <div style="margin-top: 15px; padding: 10px; background: #f8fafc; border: 1px solid #cbd5e1; display: flex; justify-content: space-between; font-size: 13px;">
        <span><strong>Batches:</strong> {{ $periodStats['count'] }}</span>
        <span><strong>Unique Products:</strong> {{ $periodStats['unique_products'] }}</span>
        <span><strong>Total Units:</strong> {{ number_format($periodStats['total_qty']) }} pcs</span>
        <span><strong>Total Purchase Cost:</strong> {!! theMoney($periodStats['total_cost']) !!}</span>
    </div>
</div>

<div class="container-fluid mb-5">
    <!-- Statistics for Selected Period KPI Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="kpi-card p-3 shadow-sm h-100" style="border-left: 4px solid #3b82f6 !important;">
                <span class="text-muted small d-block">Period Batches / Entries</span>
                <h4 class="font-roboto font-weight-bold mb-0 mt-1 text-primary">{{ number_format($periodStats['count']) }}</h4>
                <small class="text-muted">{{ \Carbon\Carbon::parse($startDate)->format('M d') }} — {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</small>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="kpi-card p-3 shadow-sm h-100" style="border-left: 4px solid #8b5cf6 !important;">
                <span class="text-muted small d-block">Unique Products</span>
                <h4 class="font-roboto font-weight-bold mb-0 mt-1" style="color: #8b5cf6;">{{ number_format($periodStats['unique_products']) }}</h4>
                <small class="text-muted">Distinct products in period</small>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="kpi-card p-3 shadow-sm h-100" style="border-left: 4px solid #10b981 !important;">
                <span class="text-muted small d-block">Total Units {{ $sourceType === 'Shipment' ? 'Shipped' : 'Returned' }}</span>
                <h4 class="font-roboto font-weight-bold mb-0 mt-1 text-success">{{ number_format($periodStats['total_qty']) }} <small class="text-muted" style="font-size: 13px;">pcs</small></h4>
                <small class="text-muted">Total physical quantity in period</small>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="kpi-card p-3 shadow-sm h-100" style="border-left: 4px solid {{ $sourceType === 'Shipment' ? '#f59e0b' : '#8b5cf6' }} !important;">
                <span class="text-muted small d-block">Total Purchase Cost (Inventory Value)</span>
                <h4 class="font-roboto font-weight-bold mb-0 mt-1 text-dark">{!! theMoney($periodStats['total_cost']) !!}</h4>
                <small class="text-muted">{{ $sourceType === 'Shipment' ? 'Transferred from Inventory (#1004)' : 'Restored back to Inventory (#1004)' }}</small>
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown Card & Table -->
    <div class="card shadow-sm border mb-4" style="background: #ffffff;">
        <div class="card-header p-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
            <h5 class="mb-0 font-weight-bold text-dark">
                <i class="fa fa-calendar {{ $sourceType === 'Shipment' ? 'text-primary' : 'text-danger' }} mr-2"></i>
                {{ $sourceType === 'Shipment' ? 'Shipment Monthly Breakdown' : 'Courier Return Monthly Breakdown' }}
            </h5>
            <span class="text-muted small">Aggregated volume, quantities and inventory purchase value grouped by month</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="min-width: 160px;">Month</th>
                            <th class="text-center" style="width: 150px;">Batches / Entries Count</th>
                            <th class="text-center" style="width: 170px;">Total Quantity (Units)</th>
                            <th class="text-right" style="width: 220px;">Total Purchase Cost Value</th>
                            <th class="text-center no-print" style="width: 120px;">Filter Month</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlyBreakdown as $m)
                            @php
                                $isCurrentSelection = (request('month') === $m['month']);
                            @endphp
                            <tr class="{{ $isCurrentSelection ? 'table-primary font-weight-bold' : '' }}">
                                <td class="font-roboto font-weight-bold text-dark">
                                    <i class="fa fa-calendar-check-o text-muted mr-1"></i> {{ $m['month_label'] }}
                                    @if($isCurrentSelection)
                                        <span class="badge badge-primary ml-1">Selected</span>
                                    @endif
                                </td>
                                <td class="text-center font-roboto font-weight-bold">{{ number_format($m['count']) }}</td>
                                <td class="text-center font-roboto font-weight-bold">{{ number_format($m['total_qty']) }} pcs</td>
                                <td class="text-right font-roboto font-weight-bold text-dark">{!! theMoney($m['total_cost']) !!}</td>
                                <td class="text-center no-print">
                                    <a href="{{ request()->url() }}?month={{ $m['month'] }}" class="btn btn-outline-primary btn-xs font-weight-bold" title="Filter this month">
                                        <i class="fa fa-filter mr-1"></i> View Month
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No monthly records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot style="background-color: #f1f5f9; border-top: 2px solid #94a3b8;">
                        <tr>
                            <th class="font-weight-bold text-dark">Grand Total Summation:</th>
                            <th class="text-center font-roboto font-weight-bold text-dark">{{ number_format($monthTotals['count']) }}</th>
                            <th class="text-center font-roboto font-weight-bold text-dark">{{ number_format($monthTotals['total_qty']) }} pcs</th>
                            <th class="text-right font-roboto font-weight-bold text-dark">{!! theMoney($monthTotals['total_cost']) !!}</th>
                            <th class="no-print"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Product Summary for Selected Period -->
    <div class="card shadow-sm border mb-4" style="background: #ffffff;">
        <div class="card-header p-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
            <h5 class="mb-0 font-weight-bold text-dark">
                <i class="fa fa-cubes {{ $sourceType === 'Shipment' ? 'text-primary' : 'text-danger' }} mr-1"></i>
                Product {{ $sourceType === 'Shipment' ? 'Shipment' : 'Return' }} Summary
                <span class="badge badge-light-primary text-primary ml-2 font-weight-normal" style="font-size: 13px;">
                    {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                </span>
            </h5>
            <div class="no-print">
                <form method="GET" class="form-inline d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <div class="form-group mb-0">
                        <label for="filter_month" class="mr-2 small font-weight-bold text-muted">Month:</label>
                        <input id="filter_month" type="month" name="month" class="form-control form-control-sm" style="height: 32px;" value="{{ request('month') }}" onchange="this.form.submit()">
                    </div>
                    <div class="form-group mb-0">
                        <label for="start_d" class="mx-2 small font-weight-bold text-muted">From:</label>
                        <input id="start_d" type="date" name="start_d" class="form-control form-control-sm" style="height: 32px;" value="{{ $startDate }}">
                    </div>
                    <div class="form-group mb-0">
                        <label for="end_d" class="mx-2 small font-weight-bold text-muted">To:</label>
                        <input id="end_d" type="date" name="end_d" class="form-control form-control-sm" style="height: 32px;" value="{{ $endDate }}">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm ml-1">Filter</button>
                    <a href="{{ $sourceType === 'Shipment' ? route('admin.accounting.shipments.index') : route('admin.accounting.returns.index') }}" class="btn btn-light border btn-sm ml-1" title="Reset Filters">
                        <i class="fa fa-refresh"></i>
                    </a>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 100px;">SKU</th>
                            <th>Product Name</th>
                            <th class="text-right" style="width: 130px;">Purchase Price</th>
                            <th class="text-center" style="width: 130px;">Qty {{ $sourceType === 'Shipment' ? 'Shipped' : 'Returned' }}</th>
                            <th class="text-center" style="width: 100px;">Batches</th>
                            <th class="text-right" style="width: 170px;">Total Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productSummary as $index => $product)
                            <tr>
                                <td class="text-muted font-roboto">{{ $index + 1 }}</td>
                                <td class="font-roboto font-weight-bold text-muted">{{ $product['sku'] }}</td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $product['product_name'] }}</div>
                                </td>
                                <td class="text-right font-roboto text-muted">{!! theMoney($product['purchase_price']) !!}</td>
                                <td class="text-center font-roboto font-weight-bold text-dark">{{ number_format($product['total_qty']) }} pcs</td>
                                <td class="text-center font-roboto">
                                    <span class="badge badge-light border px-2 py-1 font-weight-bold">{{ $product['shipment_count'] }}</span>
                                </td>
                                <td class="text-right font-roboto font-weight-bold text-dark">{!! theMoney($product['total_cost']) !!}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No product records found for this date range.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot style="background-color: #f1f5f9; border-top: 2px solid #94a3b8;">
                        <tr>
                            <th colspan="4" class="font-weight-bold text-dark">
                                Period Summation ({{ $productSummary->count() }} {{ \Illuminate\Support\Str::plural('product', $productSummary->count()) }}):
                            </th>
                            <th class="text-center font-roboto font-weight-bold text-dark">{{ number_format($periodStats['total_qty']) }} pcs</th>
                            <th class="text-center font-roboto font-weight-bold text-dark">{{ $periodStats['count'] }} batches</th>
                            <th class="text-right font-roboto font-weight-bold text-dark">{!! theMoney($periodStats['total_cost']) !!}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Daily Batch Entries (Collapsible) -->
    <div class="card shadow-sm border mb-4" style="background: #ffffff;">
        <div class="card-header p-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
            <h5 class="mb-0 font-weight-bold text-dark">
                <i class="fa fa-list text-muted mr-1"></i> Batch Entries
                <span class="badge badge-light border ml-1 text-muted" style="font-size: 12px;">{{ $periodEntries->count() }} entries</span>
            </h5>
            <button class="btn btn-outline-secondary btn-sm no-print" type="button" data-toggle="collapse" data-target="#batchEntries" aria-expanded="false" aria-controls="batchEntries">
                <i class="fa fa-chevron-down mr-1"></i> Toggle Details
            </button>
        </div>
        <div class="collapse" id="batchEntries">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 120px;">Date</th>
                                <th style="width: 160px;">Entry #</th>
                                <th>Description</th>
                                <th class="text-center" style="width: 110px;">Products</th>
                                <th class="text-center no-print" style="width: 100px;">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($periodEntries as $entry)
                                @php
                                    $details = $entry->details ?? [];
                                    $qty = (int) collect($details)->sum(fn ($d) => (int) ($d['quantity'] ?? 0));
                                @endphp
                                <tr>
                                    <td class="font-roboto font-weight-bold text-muted">{{ $entry->entry_date->format('M d, Y') }}</td>
                                    <td class="font-roboto font-weight-bold">
                                        <a href="{{ route('admin.accounting.movements.show', $entry) }}" class="text-primary font-weight-bold">
                                            {{ $entry->entry_number }}
                                        </a>
                                    </td>
                                    <td>{{ $entry->description ?: '—' }}</td>
                                    <td class="text-center">
                                        @if(count($details) > 0)
                                            <span class="badge badge-light border text-dark">{{ count($details) }} {{ \Illuminate\Support\Str::plural('product', count($details)) }}</span>
                                            <div class="small text-muted">{{ $qty }} pcs</div>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center no-print">
                                        <a href="{{ route('admin.accounting.movements.show', $entry) }}" class="btn btn-outline-primary btn-xs font-weight-bold">
                                            <i class="fa fa-eye mr-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No batch entries found for this date range.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
