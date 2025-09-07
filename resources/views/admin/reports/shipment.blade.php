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
        <div class="col-xl-3 col-md-6">
            <div class="card o-hidden">
                <div class="p-3 card-body">
                    <div class="d-flex static-top-widget">
                        <div class="align-self-center">
                            <i data-feather="truck" class="font-primary"></i>
                        </div>
                        <div class="ml-2 flex-grow-1">
                            <span class="font-roboto">Total Shipped</span>
                            <h4 class="font-roboto">{{ $report['total_shipped'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card o-hidden">
                <div class="p-3 card-body">
                    <div class="d-flex static-top-widget">
                        <div class="align-self-center">
                            <i data-feather="clock" class="font-warning"></i>
                        </div>
                        <div class="ml-2 flex-grow-1">
                            <span class="font-roboto">Shipping</span>
                            <h4 class="font-roboto">{{ $report['status_breakdown']['SHIPPING']['count'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card o-hidden">
                <div class="p-3 card-body">
                    <div class="d-flex static-top-widget">
                        <div class="align-self-center">
                            <i data-feather="check-circle" class="font-success"></i>
                        </div>
                        <div class="ml-2 flex-grow-1">
                            <span class="font-roboto">Delivered</span>
                            <h4 class="font-roboto">{{ $report['status_breakdown']['DELIVERED']['count'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card o-hidden">
                <div class="p-3 card-body">
                    <div class="d-flex static-top-widget">
                        <div class="align-self-center">
                            <i data-feather="rotate-ccw" class="font-danger"></i>
                        </div>
                        <div class="ml-2 flex-grow-1">
                            <span class="font-roboto">Returned</span>
                            <h4 class="font-roboto">{{ $report['status_breakdown']['RETURNED']['count'] ?? 0 }}</h4>
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
                                <tr>
                                    <td>
                                        <span class="badge badge-{{ $status === 'DELIVERED' ? 'success' : ($status === 'SHIPPING' ? 'warning' : ($status === 'RETURNED' ? 'danger' : 'secondary')) }}">
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
                <div class="p-3 card-header d-flex justify-content-between align-items-center">
                    <h5>Shipped Products</h5>
                    <div>
                        <a href="{{ route('admin.reports.shipment', array_merge(request()->query(), ['product_status' => 'ALL'])) }}"
                           class="btn btn-sm {{ request('product_status', 'ALL') == 'ALL' ? 'btn-primary' : 'btn-outline-primary' }}">
                            ALL
                        </a>
                        <a href="{{ route('admin.reports.shipment', array_merge(request()->query(), ['product_status' => 'SHIPPING'])) }}"
                           class="btn btn-sm {{ request('product_status') == 'SHIPPING' ? 'btn-warning' : 'btn-outline-warning' }}">
                            SHIPPING
                        </a>
                        <a href="{{ route('admin.reports.shipment', array_merge(request()->query(), ['product_status' => 'DELIVERED'])) }}"
                           class="btn btn-sm {{ request('product_status') == 'DELIVERED' ? 'btn-success' : 'btn-outline-success' }}">
                            DELIVERED
                        </a>
                        <a href="{{ route('admin.reports.shipment', array_merge(request()->query(), ['product_status' => 'RETURNED'])) }}"
                           class="btn btn-sm {{ request('product_status') == 'RETURNED' ? 'btn-danger' : 'btn-outline-danger' }}">
                            RETURNED
                        </a>
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

        /* Print styles */
        @media print {
            html, body {
                height: 100vh;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden;
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
                page-break-after: always;
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
                page-break-after: always;
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
                page-break-inside: avoid;
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
    <script src="{{ asset('assets/js/datepicker/daterange-picker/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/daterange-picker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/daterange-picker/daterange-picker.custom.js') }}"></script>
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
@endpush


