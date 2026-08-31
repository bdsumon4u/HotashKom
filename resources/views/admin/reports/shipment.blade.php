@extends('layouts.light.master')

@section('title', 'Shipment Report')

@section('breadcrumb-title')
    <h3>Shipment Report</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Reports</li>
    <li class="breadcrumb-item active">Shipment</li>
@endsection

@section('breadcrumb-right')
    <div class="theme-form m-t-10 d-flex align-items-center">
        <div style="max-width: 250px; margin-right: 15px;">
            <div class="input-group">
                <input class="form-control" id="reportrange" type="text">
            </div>
        </div>
        <button type="button" class="btn btn-primary btn-sm" onclick="printShipmentPage()">
            <i class="fa fa-print"></i> Print Report
        </button>
    </div>
@endsection

@section('content')
<!-- Print Header (hidden on screen, visible when printing) -->
<div class="print-header" style="display: none;">
    <h1>Shipment Report</h1>
    <div class="date">Generated on: {{ now()->format('F j, Y \a\t g:i A') }}</div>
</div>

<div class="mb-5 container-fluid">

    <!-- Summary Cards -->
    <div class="row">
        <div class="mb-3 col-xl-3 col-md-6 col-sm-6">
            <div class="card o-hidden h-100 kpi-card">
                <div class="p-3 card-body">
                    <div class="d-flex static-top-widget align-items-center">
                        <div class="align-self-center kpi-icon-wrap kpi-primary">
                            <i data-feather="truck" class="font-primary"></i>
                        </div>
                        <div class="ml-3 flex-grow-1">
                            <span class="font-roboto text-muted" style="font-size: 13px;">Total Shipped</span>
                            <h4 class="mb-0 font-roboto font-weight-bold">{{ $report['total_shipped'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 col-xl-3 col-md-6 col-sm-6">
            <div class="card o-hidden h-100 kpi-card">
                <div class="p-3 card-body">
                    <div class="d-flex static-top-widget align-items-center">
                        <div class="align-self-center kpi-icon-wrap kpi-warning">
                            <i data-feather="clock" class="font-warning"></i>
                        </div>
                        <div class="ml-3 flex-grow-1">
                            <span class="font-roboto text-muted" style="font-size: 13px;">Shipping</span>
                            <h4 class="mb-0 font-roboto font-weight-bold">{{ $report['status_breakdown']['SHIPPING']['count'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 col-xl-3 col-md-6 col-sm-6">
            <div class="card o-hidden h-100 kpi-card">
                <div class="p-3 card-body">
                    <div class="d-flex static-top-widget align-items-center">
                        <div class="align-self-center kpi-icon-wrap kpi-success">
                            <i data-feather="check-circle" class="font-success"></i>
                        </div>
                        <div class="ml-3 flex-grow-1">
                            <span class="font-roboto text-muted" style="font-size: 13px;">Delivered</span>
                            <h4 class="mb-0 font-roboto font-weight-bold">{{ $report['status_breakdown']['DELIVERED']['count'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 col-xl-3 col-md-6 col-sm-6">
            <div class="card o-hidden h-100 kpi-card">
                <div class="p-3 card-body">
                    <div class="d-flex static-top-widget align-items-center">
                        <div class="align-self-center kpi-icon-wrap kpi-danger">
                            <i data-feather="rotate-ccw" class="font-danger"></i>
                        </div>
                        <div class="ml-3 flex-grow-1">
                            <span class="font-roboto text-muted" style="font-size: 13px;">Returned</span>
                            <h4 class="mb-0 font-roboto font-weight-bold">{{ $report['status_breakdown']['RETURNED']['count'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 col-xl-4 col-md-4 col-sm-6">
            <div class="card o-hidden h-100 kpi-card">
                <div class="p-3 card-body">
                    <div class="d-flex static-top-widget align-items-center">
                        <div class="align-self-center kpi-icon-wrap kpi-info">
                            <i data-feather="repeat" class="font-info"></i>
                        </div>
                        <div class="ml-3 flex-grow-1">
                            <span class="font-roboto text-muted" style="font-size: 13px;">Paid Return</span>
                            <h4 class="mb-0 font-roboto font-weight-bold">{{ $report['status_breakdown']['PAID_RETURN']['count'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 col-xl-4 col-md-4 col-sm-6">
            <div class="card o-hidden h-100 kpi-card">
                <div class="p-3 card-body">
                    <div class="d-flex static-top-widget align-items-center">
                        <div class="align-self-center kpi-icon-wrap kpi-secondary">
                            <i data-feather="package" class="font-secondary"></i>
                        </div>
                        <div class="ml-3 flex-grow-1">
                            <span class="font-roboto text-muted" style="font-size: 13px;">Return Received</span>
                            <h4 class="mb-0 font-roboto font-weight-bold">{{ $report['status_breakdown']['RETURN_RECEIVED']['count'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 col-xl-4 col-md-4 col-sm-6">
            <div class="card o-hidden h-100 kpi-card">
                <div class="p-3 card-body">
                    <div class="d-flex static-top-widget align-items-center">
                        <div class="align-self-center kpi-icon-wrap kpi-dark">
                            <i data-feather="check-square" class="font-dark"></i>
                        </div>
                        <div class="ml-3 flex-grow-1">
                            <span class="font-roboto text-muted" style="font-size: 13px;">Paid Return Rcv</span>
                            <h4 class="mb-0 font-roboto font-weight-bold">{{ $report['status_breakdown']['PAID_RETURN_RCV']['count'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Breakdown Chart -->
    <div class="row">
        <div class="col-xl-6">
            <div class="shadow-sm rounded-0 card">
                <div class="p-3 card-header">
                    <h5>Status Breakdown</h5>
                </div>
                <div class="p-3 card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Count</th>
                                    <th>Purchase</th>
                                    <th>Subtotal</th>
                                    <th>Profit</th>
                                    <th>Percent</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report['status_breakdown'] as $status => $data)
                                @php
                                    $badgeClass = match($status) {
                                        'DELIVERED' => 'badge-success',
                                        'SHIPPING' => 'badge-warning',
                                        'RETURNED' => 'badge-danger',
                                        'PAID_RETURN' => 'badge-info',
                                        'RETURN_RECEIVED' => 'badge-secondary',
                                        'PAID_RETURN_RCV' => 'badge-dark',
                                        default => 'badge-light',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td>{{ $data['count'] }}</td>
                                    <td>{!! theMoney($data['total_purchase_cost']) !!}</td>
                                    <td>{!! theMoney($data['total_subtotal']) !!}</td>
                                    <td class="{{ ((float) ($data['total_subtotal'] ?? 0) - (float) ($data['total_purchase_cost'] ?? 0)) >= 0 ? 'text-success' : 'text-danger' }}">
                                        {!! theMoney((float) ($data['total_subtotal'] ?? 0) - (float) ($data['total_purchase_cost'] ?? 0)) !!}
                                    </td>
                                    <td>{{ $report['total_shipped'] > 0 ? round(($data['count'] / $report['total_shipped']) * 100, 1) : 0 }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courier Breakdown -->
        <div class="col-xl-6">
            <div class="shadow-sm rounded-0 card">
                <div class="p-3 card-header">
                    <h5>Courier Breakdown</h5>
                </div>
                <div class="p-3 card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Courier</th>
                                    <th>Total</th>
                                    <th>Purchase</th>
                                    <th>Subtotal</th>
                                    <th>Profit</th>
                                    <th>Delivered</th>
                                    <th>Shipping</th>
                                    <th>Returned</th>
                                    <th>Paid Return</th>
                                    <th>Return Rcv</th>
                                    <th>Paid Return Rcv</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report['courier_breakdown'] as $courier => $data)
                                <tr>
                                    <td>{{ $courier }}</td>
                                    <td>{{ $data['total'] }}</td>
                                    <td>{!! theMoney($data['total_purchase_cost']) !!}</td>
                                    <td>{!! theMoney($data['total_subtotal']) !!}</td>
                                    <td class="{{ ((float) ($data['total_subtotal'] ?? 0) - (float) ($data['total_purchase_cost'] ?? 0)) >= 0 ? 'text-success' : 'text-danger' }}">
                                        {!! theMoney((float) ($data['total_subtotal'] ?? 0) - (float) ($data['total_purchase_cost'] ?? 0)) !!}
                                    </td>
                                    <td class="text-success">{{ $data['delivered'] }}</td>
                                    <td class="text-warning">{{ $data['shipping'] }}</td>
                                    <td class="text-danger">{{ $data['returned'] }}</td>
                                    <td class="text-info">{{ $data['paid_return'] ?? 0 }}</td>
                                    <td class="text-secondary">{{ $data['return_received'] ?? 0 }}</td>
                                    <td class="text-dark">{{ $data['paid_return_rcv'] ?? 0 }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Breakdown -->
    <div class="row">
        <div class="col-12">
            <div class="shadow-sm rounded-0 card">
                <div class="p-3 card-header">
                    <h5>Daily Breakdown</h5>
                </div>
                <div class="p-3 card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Total Shipped</th>
                                    <th>Purchase</th>
                                    <th>Subtotal</th>
                                    <th>Profit</th>
                                    <th>Shipping</th>
                                    <th>Delivered</th>
                                    <th>Returned</th>
                                    <th>Paid Return</th>
                                    <th>Return Rcv</th>
                                    <th>Paid Return Rcv</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report['daily_breakdown'] as $date => $data)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.index', ['shipped_at' => $date, 'status' => '']) }}"
                                           class="text-primary font-weight-bold">
                                            {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
                                        </a>
                                    </td>
                                    <td>{{ $data['total'] }}</td>
                                    <td>{!! theMoney($data['total_purchase_cost']) !!}</td>
                                    <td>{!! theMoney($data['total_subtotal']) !!}</td>
                                    <td class="{{ ((float) ($data['total_subtotal'] ?? 0) - (float) ($data['total_purchase_cost'] ?? 0)) >= 0 ? 'text-success' : 'text-danger' }}">
                                        {!! theMoney((float) ($data['total_subtotal'] ?? 0) - (float) ($data['total_purchase_cost'] ?? 0)) !!}
                                    </td>
                                    <td class="text-warning">{{ $data['shipping'] }}</td>
                                    <td class="text-success">{{ $data['delivered'] }}</td>
                                    <td class="text-danger">{{ $data['returned'] }}</td>
                                    <td class="text-info">{{ $data['paid_return'] ?? 0 }}</td>
                                    <td class="text-secondary">{{ $data['return_received'] ?? 0 }}</td>
                                    <td class="text-dark">{{ $data['paid_return_rcv'] ?? 0 }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shipped Products List -->
    <div class="row">
        <div class="col-12">
            <div class="shadow-sm rounded-0 card">
                <div class="p-3 card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                    <h5 class="mb-0">Shipped Products</h5>
                    <div class="report-status-nav">
                        @php
                            $activeStatus = request('product_status', 'ALL');
                            $filterStatuses = [
                                'ALL' => ['label' => 'ALL', 'class' => 'status-pill-all', 'count' => $report['total_shipped']],
                                'SHIPPING' => ['label' => 'Shipping', 'class' => 'status-pill-shipping', 'count' => $report['status_breakdown']['SHIPPING']['count'] ?? 0],
                                'DELIVERED' => ['label' => 'Delivered', 'class' => 'status-pill-delivered', 'count' => $report['status_breakdown']['DELIVERED']['count'] ?? 0],
                                'RETURNED' => ['label' => 'Returned', 'class' => 'status-pill-returned', 'count' => $report['status_breakdown']['RETURNED']['count'] ?? 0],
                                'PAID_RETURN' => ['label' => 'Paid Return', 'class' => 'status-pill-paid-return', 'count' => $report['status_breakdown']['PAID_RETURN']['count'] ?? 0],
                                'RETURN_RECEIVED' => ['label' => 'Return Received', 'class' => 'status-pill-return-rcv', 'count' => $report['status_breakdown']['RETURN_RECEIVED']['count'] ?? 0],
                                'PAID_RETURN_RCV' => ['label' => 'Paid Return Rcv', 'class' => 'status-pill-paid-return-rcv', 'count' => $report['status_breakdown']['PAID_RETURN_RCV']['count'] ?? 0],
                            ];
                        @endphp

                        @foreach($filterStatuses as $statusKey => $statusConfig)
                            @php
                                $isActive = ($activeStatus === $statusKey);
                            @endphp
                            <a href="{{ route('admin.reports.shipment', array_merge(request()->query(), ['product_status' => $statusKey])) }}"
                               class="status-pill {{ $statusConfig['class'] }} {{ $isActive ? 'active' : '' }}">
                                <span class="status-pill-label">{{ $statusConfig['label'] }}</span>
                                <span class="status-pill-count">{{ $statusConfig['count'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="p-3 card-body">
                    @if(!empty($shippedProductsData['products']))
                        @include('admin.reports.filtered', [
                            'products' => $shippedProductsData['products'],
                            'productInOrders' => $shippedProductsData['productInOrders']
                        ])
                    @else
                        <div class="py-4 text-center text-muted">
                            <i class="mb-2 fa fa-box fa-2x"></i>
                            <p>No shipped products found for the selected date range</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/daterange-picker.css') }}">
    <style>
        .daterangepicker {
            border: 2px solid #d7d7d7 !important;
        }

        /* KPI Card Styling */
        .kpi-card {
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
            border: 1px solid #eef2f6;
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        }

        .kpi-icon-wrap {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .kpi-primary { background-color: rgba(99, 102, 241, 0.12); }
        .kpi-warning { background-color: rgba(245, 158, 11, 0.12); }
        .kpi-success { background-color: rgba(16, 185, 129, 0.12); }
        .kpi-danger  { background-color: rgba(239, 68, 68, 0.12); }
        .kpi-info    { background-color: rgba(6, 182, 212, 0.12); }
        .kpi-secondary { background-color: rgba(139, 92, 246, 0.12); }
        .kpi-dark    { background-color: rgba(30, 41, 59, 0.12); }

        /* Modern Filter Pills */
        .report-status-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none !important;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            transition: all 0.2s ease;
            line-height: 1.3;
        }

        .status-pill:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.06);
            color: #1e293b;
        }

        .status-pill-count {
            margin-left: 6px;
            padding: 1px 7px;
            border-radius: 10px;
            font-size: 11px;
            background-color: rgba(0, 0, 0, 0.06);
            color: inherit;
            font-weight: 700;
        }

        /* Pill Active Themes */
        .status-pill-all.active {
            background-color: #4f46e5;
            border-color: #4f46e5;
            color: #ffffff;
        }
        .status-pill-all.active .status-pill-count {
            background-color: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .status-pill-shipping.active {
            background-color: #f59e0b;
            border-color: #f59e0b;
            color: #ffffff;
        }
        .status-pill-shipping.active .status-pill-count {
            background-color: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .status-pill-delivered.active {
            background-color: #10b981;
            border-color: #10b981;
            color: #ffffff;
        }
        .status-pill-delivered.active .status-pill-count {
            background-color: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .status-pill-returned.active {
            background-color: #ef4444;
            border-color: #ef4444;
            color: #ffffff;
        }
        .status-pill-returned.active .status-pill-count {
            background-color: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .status-pill-paid-return.active {
            background-color: #06b6d4;
            border-color: #06b6d4;
            color: #ffffff;
        }
        .status-pill-paid-return.active .status-pill-count {
            background-color: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .status-pill-return-rcv.active {
            background-color: #8b5cf6;
            border-color: #8b5cf6;
            color: #ffffff;
        }
        .status-pill-return-rcv.active .status-pill-count {
            background-color: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .status-pill-paid-return-rcv.active {
            background-color: #1e293b;
            border-color: #1e293b;
            color: #ffffff;
        }
        .status-pill-paid-return-rcv.active .status-pill-count {
            background-color: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        /* Print styles */
        @media print {
            html, body {
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
            }

            .main-nav {
                display: none !important;
                width: 0 !important;
            }

            .page-main-header {
                display: none !important;
                width: 0 !important;
            }

            .print-edit-buttons,
            .footer {
                display: none !important;
            }

            .page-body {
                font-size: 16px;
                margin-top: 0 !important;
                margin-left: 0 !important;
            }

            .page-body p {
                font-size: 14px !important;
            }

            /* Hide DataTable elements */
            .dt-buttons,
            .dataTables_paginate,
            .dataTables_info,
            .dataTables_filter,
            .dataTables_length,
            .card-header .d-flex,
            .no-print {
                display: none !important;
            }

            /* Remove any horizontal lines or borders at the top */
            hr,
            .hr,
            [class*="border-top"],
            [class*="border-bottom"],
            .border-top,
            .border-bottom {
                display: none !important;
                border: none !important;
            }

            /* Reset page body for printing */
            .page-body {
                font-size: 16px;
                margin-top: 0 !important;
                margin-left: 0 !important;
            }

            .page-body p {
                font-size: 14px !important;
            }

            /* Container adjustments */
            .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }

            /* Style the card for printing */
            .card {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
                margin: 0 0 15px 0 !important;
                padding: 0 !important;
                /* Allow large cards (tables) to flow across pages to avoid big blanks */
                page-break-inside: auto !important;
                break-inside: auto !important;
            }

            .card-body {
                padding: 15px !important;
            }

            /* Summary cards specific styling */
            .card.o-hidden {
                border: 1px solid #ddd !important;
                margin-bottom: 15px !important;
            }

            .static-top-widget {
                display: flex !important;
                align-items: center !important;
            }

            .static-top-widget i {
                font-size: 24px !important;
                margin-right: 10px !important;
            }

            .static-top-widget .font-roboto {
                font-family: 'Roboto', sans-serif !important;
            }

            .static-top-widget h4 {
                margin: 0 !important;
                font-size: 24px !important;
                font-weight: bold !important;
            }

            .static-top-widget span {
                display: block !important;
                font-size: 14px !important;
                color: #666 !important;
                margin-bottom: 5px !important;
            }

            /* Table styles for printing */
            .table {
                border-collapse: collapse !important;
                width: 100% !important;
            }

            .table th,
            .table td {
                border: 1px solid #000 !important;
                padding: 8px !important;
                text-align: left !important;
            }

            .table thead th {
                background-color: #f8f9fa !important;
                font-weight: bold !important;
            }

            /* Print header styles */
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
                padding-bottom: 10px;
            }

            .print-header h1 {
                margin: 0;
                font-size: 24px;
                font-weight: bold;
            }

            .print-header .date {
                font-size: 14px;
                color: #666;
                margin-top: 5px;
            }

            /* Ensure only the card content is visible */
            .row {
                margin: 0 !important;
                display: flex !important;
                flex-wrap: wrap !important;
            }

            .col-sm-12 {
                padding: 0 !important;
            }

            /* Grid layout for summary cards - keep all 4 in one line for print */
            .col-xl-3,
            .col-md-6 {
                width: 25% !important;
                padding: 0 5px !important;
                margin-bottom: 15px !important;
                flex: 0 0 25% !important;
                max-width: 25% !important;
            }

            /* Force all summary cards to stay in one line for print */
            .row:first-of-type {
                display: flex !important;
                flex-wrap: nowrap !important;
                justify-content: space-between !important;
            }

            .row:first-of-type .col-xl-3,
            .row:first-of-type .col-md-6 {
                flex: 1 !important;
                max-width: 23% !important;
                margin-right: 10px !important;
            }

            .row:first-of-type .col-xl-3:last-child,
            .row:first-of-type .col-md-6:last-child {
                margin-right: 0 !important;
            }

            /* Hide specific navigation and layout elements */
            .main-nav,
            .sidebar-wrapper,
            .sidebar,
            .main-header,
            .page-header,
            .page-title,
            .breadcrumb,
            footer,
            .footer,
            .main-footer,
            .dt-buttons,
            .dataTables_paginate,
            .dataTables_info,
            .dataTables_filter,
            .dataTables_length,
            .card-header .d-flex,
            .no-print,
            /* Additional header selectors */
            header,
            .header,
            .top-header,
            .navbar,
            .navbar-header,
            .navbar-nav,
            .nav-header,
            .page-header-wrapper,
            .main-header-wrapper,
            /* Hide breadcrumb right section */
            .breadcrumb-right {
                display: none !important;
                width: 0 !important;
            }

            /* Hide specific elements that might be showing */
            .page-wrapper,
            .page-body-wrapper,
            .page-body,
            .container-fluid {
                margin-left: 0 !important;
                padding-left: 0 !important;
            }

            /* Ensure content takes full width */
            .page-body {
                margin-left: 0 !important;
                padding-left: 0 !important;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        /**
         * IMPORTANT:
         * `daterange-picker.custom.js` expects global `_start`, `_end` and `window.reportRangeCB`
         * to exist at execution time, and it also depends on `moment` being available.
         *
         * Therefore: load moment first (no `defer`), then define globals, then load the picker scripts.
         */
    </script>
    <script src="{{ asset('assets/js/datepicker/daterange-picker/moment.min.js') }}"></script>
    <script>
        window._start = moment('{{ $start }}');
        window._end = moment('{{ $end }}');
        window.reportRangeCB = function(start, end) {
            window._start = start;
            window._end = end;
            refresh();
        };

        function refresh() {
            window.location = "{!! route('admin.reports.shipment', [
                'start_d' => '_start',
                'end_d' => '_end',
            ]) !!}".replace('_start', window._start.format('YYYY-MM-DD'))
                .replace('_end', window._end.format('YYYY-MM-DD'));
        }

        function printShipmentPage() {
            // Show print header
            $('.print-header').show();

            // Hide elements that shouldn't be printed
            $('.main-nav, .page-main-header, .footer, .card-header .d-flex, .dt-buttons, .dataTables_paginate, .dataTables_info, .dataTables_filter, .dataTables_length, .breadcrumb-right').addClass('no-print');

            // Print the page
            window.print();

            // Hide print header and remove no-print classes after printing
            setTimeout(function() {
                $('.print-header').hide();
                $('.main-nav, .page-main-header, .footer, .card-header .d-flex, .dt-buttons, .dataTables_paginate, .dataTables_info, .dataTables_filter, .dataTables_length, .breadcrumb-right').removeClass('no-print');
            }, 1000);
        }
    </script>
    <script src="{{ asset('assets/js/datepicker/daterange-picker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/daterange-picker/daterange-picker.custom.js') }}"></script>
@endpush


