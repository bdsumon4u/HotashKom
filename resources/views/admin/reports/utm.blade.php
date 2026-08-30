@extends('layouts.light.master')
@section('title', 'Marketing & UTM Campaign Reports')

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/daterange-picker.css') }}">
    <style>
        .daterangepicker {
            border: 2px solid #d7d7d7 !important;
        }
        .kpi-card {
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            transition: all 0.2s ease;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.12);
        }
    </style>
@endpush

@section('breadcrumb-title')
    <h3>Marketing & UTM Reports</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
    <li class="breadcrumb-item">UTM Reports</li>
@endsection

@section('content')
    <div class="mb-5 row">
        <div class="mx-auto col-md-12">
            <!-- Filter Bar -->
            <div class="mb-4 shadow-sm card rounded-0">
                <div class="p-3 card-header">
                    <form action="" method="GET" class="form-inline">
                        <div class="mr-2 form-group">
                            <label class="mr-2 font-weight-bold" for="datetype">Date Field:</label>
                            <select name="date_type" id="datetype" class="form-control form-control-sm">
                                <option value="created_at" @if($dateType === 'created_at') selected @endif>Order Created Date</option>
                                <option value="status_at" @if($dateType === 'status_at') selected @endif>Status Update Date</option>
                            </select>
                        </div>
                        <div class="mr-2 form-group">
                            <label class="mr-2 font-weight-bold" for="reportrange">Date Range:</label>
                            <input class="form-control form-control-sm" id="reportrange" type="text" style="min-width: 210px;">
                            <input type="hidden" name="start_d" value="{{ $start }}">
                            <input type="hidden" name="end_d" value="{{ $end }}">
                        </div>
                        <div class="mr-2 form-group">
                            <label class="mr-2 font-weight-bold" for="source">Source:</label>
                            <select name="source" id="source" class="form-control form-control-sm">
                                <option value="">All Sources</option>
                                @foreach ($availableSources as $source)
                                    <option value="{{ $source }}" @if($selectedSource === $source) selected @endif>{{ strtoupper($source) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fa fa-filter mr-1"></i> Apply Filter
                        </button>
                    </form>
                </div>
            </div>

            <!-- KPI Cards Row -->
            <div class="mb-4 row">
                <div class="col-sm-6 col-xl-3">
                    <div class="p-3 card kpi-card bg-primary text-white mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 text-white-50 text-uppercase" style="font-size: 11px;">UTM Orders / Total</h6>
                                <h3 class="mb-0 font-weight-bold">{{ $totalUtmOrdersCount }} <small class="text-white-50" style="font-size: 14px;">/ {{ $totalOrdersCount }}</small></h3>
                                <small class="text-white-50">{{ $totalOrdersCount > 0 ? round(($totalUtmOrdersCount / $totalOrdersCount) * 100, 1) : 0 }}% Attributed</small>
                            </div>
                            <i class="fa fa-bullhorn fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="p-3 card kpi-card bg-info text-white mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 text-white-50 text-uppercase" style="font-size: 11px;">Top Traffic Source</h6>
                                <h4 class="mb-0 font-weight-bold text-uppercase text-truncate" style="max-width: 170px;">{{ $topSource }}</h4>
                                <small class="text-white-50">{{ $topSourceCount }} Orders</small>
                            </div>
                            <i class="fa fa-globe fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="p-3 card kpi-card bg-success text-white mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 text-white-50 text-uppercase" style="font-size: 11px;">Top Campaign</h6>
                                <h4 class="mb-0 font-weight-bold text-truncate" style="max-width: 170px;" title="{{ $topCampaign }}">{{ $topCampaign }}</h4>
                                <small class="text-white-50">{{ $topCampaignCount }} Orders</small>
                            </div>
                            <i class="fa fa-tags fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="p-3 card kpi-card bg-dark text-white mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 text-white-50 text-uppercase" style="font-size: 11px;">Delivered UTM Revenue</h6>
                                <h3 class="mb-0 font-weight-bold">{!! theMoney($utmDeliveredRevenue) !!}</h3>
                                <small class="text-success">{{ $overallDeliveryRate }}% Delivery Rate</small>
                            </div>
                            <i class="fa fa-money fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campaign Performance Table -->
            <div class="shadow-sm card rounded-0">
                <div class="p-3 card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 card-title">Campaign Performance Breakdown</h5>
                    <span class="badge badge-light border">{{ count($campaigns) }} Campaign Rows</span>
                </div>
                <div class="p-0 card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="min-width: 160px;">Campaign</th>
                                    <th style="min-width: 100px;">Source</th>
                                    <th style="min-width: 90px;">Medium</th>
                                    <th class="text-center" style="min-width: 70px;">Total</th>
                                    <th class="text-center text-info" style="min-width: 70px;">Confirmed</th>
                                    <th class="text-center text-primary" style="min-width: 70px;">Shipping</th>
                                    <th class="text-center text-success" style="min-width: 70px;">Delivered</th>
                                    <th class="text-center text-danger" style="min-width: 70px;">Cancelled</th>
                                    <th class="text-center text-warning" style="min-width: 70px;">Returned</th>
                                    <th class="text-right" style="min-width: 120px;">Revenue</th>
                                    <th class="text-center" style="min-width: 90px;">Success Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($campaigns as $item)
                                    @php
                                        $rate = $item['total'] > 0 ? round(($item['delivered'] / $item['total']) * 100, 1) : 0;
                                        $source = strtolower($item['source']);
                                        $badgeClass = match($source) {
                                            'facebook', 'fb' => 'badge-primary',
                                            'google' => 'badge-danger',
                                            'tiktok' => 'badge-dark',
                                            'instagram' => 'badge-info',
                                            'youtube' => 'badge-danger',
                                            default => 'badge-secondary',
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="font-weight-bold">{{ $item['campaign'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $badgeClass }} text-uppercase">{{ $item['source'] }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $item['medium'] }}</span>
                                        </td>
                                        <td class="text-center font-weight-bold">{{ $item['total'] }}</td>
                                        <td class="text-center">{{ $item['confirmed'] }}</td>
                                        <td class="text-center">{{ $item['shipping'] + $item['packaging'] }}</td>
                                        <td class="text-center font-weight-bold text-success">{{ $item['delivered'] }}</td>
                                        <td class="text-center text-danger">{{ $item['cancelled'] }}</td>
                                        <td class="text-center text-warning">{{ $item['returned'] }}</td>
                                        <td class="text-right font-weight-bold">{!! theMoney($item['revenue']) !!}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $rate >= 50 ? 'badge-success' : ($rate >= 25 ? 'badge-warning' : 'badge-light border') }}">
                                                {{ $rate }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="py-4 text-center text-muted">
                                            <i class="fa fa-info-circle mr-1"></i> No campaign-attributed orders found in this date range.
                                        </td>
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

@push('js')
    <script src="{{ asset('assets/js/datepicker/daterange-picker/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/daterange-picker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/daterange-picker/daterange-picker.custom.js') }}"></script>
    <script>
        window._start = moment('{{ $start }}');
        window._end = moment('{{ $end }}');
        window.reportRangeCB = function (start, end) {
            $('input[name="start_d"]').val(start.format('YYYY-MM-DD'));
            $('input[name="end_d"]').val(end.format('YYYY-MM-DD'));
        }
    </script>
@endpush
