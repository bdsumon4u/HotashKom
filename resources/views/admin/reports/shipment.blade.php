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
    <div class="theme-form m-t-10">
        <div style="max-width: 250px; margin-left: auto;">
            <div class="input-group">
                <input class="form-control" id="reportrange" type="text">
            </div>
        </div>
    </div>
@endsection

@section('content')
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
    </script>
@endpush
