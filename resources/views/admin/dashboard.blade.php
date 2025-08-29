@extends('layouts.light.master')
@section('title', 'Ecommerce')

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/daterange-picker.css') }}">
    <style>
        .daterangepicker {
            border: 2px solid #d7d7d7 !important;
        }
    </style>
@endpush

@section('breadcrumb-title')
    <h3>Ecommerce</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
@endsection

@section('breadcrumb-right')
    <div class="theme-form m-t-10">
        <div style="max-width: 600px; margin-right: auto;">
            <div class="input-group">
                <select name="date_type" id="datetype" class="form-control input-group-prepend" style="max-width: 150px;">
                    <option value="created_at" @if (request('date_type') == 'created_at') selected @endif>ORDER DATE</option>
                    <option value="status_at" @if (request('date_type', 'status_at') == 'status_at') selected @endif>UPDATE DATE</option>
                </select>
                <input class="form-control" id="reportrange" type="text">

                <select name="staff_id" id="staff-id" class="form-control input-group-append" style="max-width: 150px;">
                    <option value="">Select Salesman</option>
                    @foreach (\App\Models\Admin::where('role_id', \App\Models\Admin::SALESMAN)->get() as $admin)
                        <option value="{{ $admin->id }}" @if (request()->get('staff_id') == $admin->id) selected @endif>
                            {{ $admin->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="mb-5 container-fluid">
        <div class="row size-column">
            <div class="col-xl-7 box-col-12 xl-100">
                <div class="row dash-chart">
                    <div class="col-12">
                        <div class="mb-3">
                            @foreach (config('app.orders', []) as $status)
                                <a class="px-2 py-1 m-1 btn btn-light"
                                    href="{{ route(
                                        'admin.orders.index',
                                        array_merge(
                                            array_merge(
                                                [
                                                    'start_d' => date('Y-m-d'),
                                                    'end_d' => date('Y-m-d'),
                                                ],
                                                request()->query(),
                                            ),
                                            ['status' => $status == 'All' ? '' : $status],
                                        ),
                                    ) }}">
                                    <span>{{ $status }}</span>
                                </a>
                            @endforeach
                            <a href="{{ route(
                                'admin.orders.index',
                                array_merge(
                                    array_merge(
                                        [
                                            'start_d' => date('Y-m-d'),
                                            'end_d' => date('Y-m-d'),
                                        ],
                                        request()->query(),
                                    ),
                                    ['status' => ''],
                                ),
                            ) }}"
                                class="px-2 py-1 m-1 btn btn-light">
                                <span>All</span>
                            </a>
                        </div>
                    </div>
                    <div class="col-xl-4 box-col-4 col-lg-4 col-md-4">
                        <div class="rounded-sm card o-hidden">
                            <div class="p-3 card-body">
                                <div class="ecommerce-widgets media">
                                    <div class="media-body">
                                        <p class="mb-2 f-w-500 font-roboto">Total Products</p>
                                        <h4 class="mb-0 f-w-500 f-26"><span class="counter">{{ $productsCount }}</span></h4>
                                    </div>
                                    <div class="ecommerce-box light-bg-primary"><i class="fa fa-heart"
                                            aria-hidden="true"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 box-col-4 col-lg-4 col-md-4">
                        <div class="rounded-sm card o-hidden">
                            <div class="p-3 card-body">
                                <div class="ecommerce-widgets media">
                                    <div class="media-body">
                                        <p class="mb-2 f-w-500 font-roboto">Inactive Products</p>
                                        <h4 class="mb-0 f-w-500 f-26"><span
                                                class="counter">{{ $inactiveProducts->count() }}</span></h4>
                                    </div>
                                    <div class="ecommerce-box light-bg-primary"><i class="fa fa-heart"
                                            aria-hidden="true"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 box-col-4 col-lg-4 col-md-4">
                        <div class="rounded-sm card o-hidden">
                            <div class="p-3 card-body">
                                <div class="ecommerce-widgets media">
                                    <div class="media-body">
                                        <p class="mb-2 f-w-500 font-roboto">Low Stock</p>
                                        <h4 class="mb-0 f-w-500 f-26"><span
                                                class="counter">{{ $lowStockProducts->count() }}</span></h4>
                                    </div>
                                    <div class="ecommerce-box light-bg-primary"><i class="fa fa-heart"
                                            aria-hidden="true"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @foreach ($orders as $status => $count)
                        <div class="col-xl-3 box-col- col-lg-3 col-md-3">
                            <div class="rounded-sm card o-hidden">
                                <div class="p-3 card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            @php
                                                $statData = compact('status');
                                                if ($loop->index == 0) {
                                                    $statData = ['status' => ''];
                                                } elseif ($loop->index < 3) {
                                                    $statData = ['status' => '', 'type' => $status];
                                                }
                                            @endphp
                                            <a
                                                href="{{ route(
                                                    'admin.orders.index',
                                                    array_merge(
                                                        array_merge(
                                                            [
                                                                'start_d' => date('Y-m-d'),
                                                                'end_d' => date('Y-m-d'),
                                                            ],
                                                            request()->query(),
                                                        ),
                                                        $statData,
                                                    ),
                                                ) }}">
                                                <p class="mb-2 f-w-500 font-roboto">{{ $status }} Orders</p>
                                                <h4 class="mb-0 f-w-500 f-26"><span
                                                        class="-counter-">{{ $count }}</span></h4>
                                                <span class="-counter-">Taka: {{ $amounts[$status] }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(isOninda() && !config('app.resell'))
                    <div class="mb-3 alert alert-info">
                        <i class="mr-2 fa fa-info-circle"></i>
                        <strong>Note:</strong> Dashboard amounts and product reports display retail pricing (end customer amounts) as configured for this platform.
                    </div>
                @endif
                <div class="shadow-sm card rounded-0">
                    <div class="p-3 card-header d-flex justify-content-between align-items-center">
                        <strong>Processing Products</strong>
                        <small>CONFIRMED+INVOICED+SHIPPING</small>
                    </div>
                    <div class="p-3 card-body">
                        @if(!empty($products))
                            @include('admin.reports.filtered', [
                                'products' => $products,
                                'productInOrders' => $productInOrders
                            ])
                        @else
                            <div class="py-4 text-center text-muted">
                                <i class="mb-2 fa fa-box fa-2x"></i>
                                <p>No processing products found for the selected date range</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-4 xl-50 box-col-12">
                <div class="rounded-sm card">
                    <div class="p-4 card-header card-no-border">
                        <h5>Inactive Products</h5>
                    </div>
                    <div class="p-3 card-body">
                        <div class="our-product">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody class="f-w-500">
                                        @foreach ($inactiveProducts as $product)
                                            <tr>
                                                <td class="pl-2">
                                                    <div class="media">
                                                        <img class="img-fluid m-r-15 rounded-circle"
                                                            src="{{ asset(optional($product->base_image)->src) }}"
                                                            width="42" height="42" alt="">
                                                        <div class="media-body">
                                                            <a
                                                                href="{{ route('admin.products.edit', $product) }}">{{ $product->name }}</a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p>SKU</p>
                                                    <span>{{ $product->sku }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if ($product->price == $product->selling_price)
                                                        <p>{!! theMoney($product->price) !!}</p>
                                                    @else
                                                        <del style="color: #ff0000;">{!! theMoney($product->price) !!}</del>
                                                        <br>
                                                        <ins style="text-decoration: none;">{!! theMoney($product->selling_price) !!}</ins>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rounded-sm card">
                    <div class="p-3 card-header">
                        <h5>Staffs</h5>
                    </div>
                    <div class="p-3 card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ONLINE</th>
                                        <th>OFFLINE</th>
                                    </tr>
                                </thead>
                                <tbody class="f-w-500">
                                    <tr>
                                        <td>
                                            <ul style="list-style: disc; padding-left: 1rem;">
                                                @foreach ($staffs['online'] as $staff)
                                                <li
                                                    class="@if ($staff->role_id == \App\Models\Admin::SALESMAN && !$staff->is_active) text-danger @endif">
                                                    {{ $staff->name }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td>
                                            <ul style="list-style: disc; padding-left: 1rem;">
                                                @foreach ($staffs['offline'] as $staff)
                                                <li
                                                    class="@if ($staff->role_id == \App\Models\Admin::SALESMAN && !$staff->is_active) text-danger @endif">
                                                    {{ $staff->name }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 xl-50 box-xl-12">
                <div class="rounded-sm card">
                    <div class="p-4 card-header card-no-border">
                        <h5>Low Stock</h5>
                    </div>
                    <div class="p-3 card-body">
                        <div class="our-product">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody class="f-w-500">
                                        @foreach ($lowStockProducts as $product)
                                            <tr>
                                                <td class="pl-2">
                                                    <div class="media">
                                                        <img class="img-fluid m-r-15 rounded-circle"
                                                            src="{{ asset(optional($product->base_image)->src) }}"
                                                            width="42" height="42" alt="">
                                                        <div class="media-body">
                                                            <a
                                                                href="{{ route('admin.products.edit', $product->parent_id ?? $product->id) }}">{{ $product->var_name }}</a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-nowrap">{{ $product->sku }}</p>
                                                    <p class="font-weight-bold">Stock:
                                                        <span>{{ $product->stock_count }}</span></p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @if(isOninda() && config('app.resell'))
                <div class="rounded-sm card">
                    <div class="p-3 card-header d-flex justify-content-between align-items-center">
                        <h5>Money Requests</h5>
                        <a href="{{ route('admin.money-requests.index') }}" class="btn btn-sm btn-primary">
                            <i class="mr-1 fa fa-eye"></i>View All
                        </a>
                    </div>
                    <div class="p-3 card-body">
                        @if($pendingWithdrawalAmount > 0)
                        <div class="text-center">
                            <h3 class="mb-2 text-warning">{{ number_format($pendingWithdrawalAmount, 2) }} tk</h3>
                            <p class="mb-0 text-muted">Total pending withdrawal amount</p>
                            <a href="{{ route('admin.money-requests.index') }}" class="mt-2 btn btn-warning btn-sm">
                                <i class="mr-1 fa fa-dollar-sign"></i>Process Requests
                            </a>
                        </div>
                        @else
                        <div class="py-3 text-center text-muted">
                            <i class="mb-2 fa fa-check-circle fa-2x"></i>
                            <p>No pending withdrawal requests</p>
                            <a href="{{ route('admin.money-requests.index') }}" class="mt-2 btn btn-outline-info btn-sm">
                                <i class="mr-1 fa fa-eye"></i>View Money Requests
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/js/datepicker/daterange-picker/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/daterange-picker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/daterange-picker/daterange-picker.custom.js') }}"></script>
    <script>
        window._status = '{{ request('status') }}';
        window._staff = '{{ request('staff_id') }}';
        window._type = '{{ request('date_type', 'created_at') }}';
        window._start = moment('{{ $start }}');
        window._end = moment('{{ $end }}');
        window.reportRangeCB = function(start, end) {
            window._start = start;
            window._end = end;
            refresh();
        };

        $('#datetype').on('change', function() {
            window._type = $(this).val();
            refresh();
        });

        $('#staff-id').on('change', function() {
            window._staff = $(this).val();
            refresh();
        });

        function refresh() {
            window.location = "{!! route('admin.home', [
                'status' => '_status',
                'date_type' => 'd_type',
                'start_d' => '_start',
                'end_d' => '_end',
                'staff_id' => '_staff_id',
            ]) !!}".replace('_status', window._status).replace('d_type', window._type)
                .replace('_start', window._start.format('YYYY-MM-DD')).replace('_end', window._end.format('YYYY-MM-DD'))
                .replace('_staff_id', window._staff);
        }
    </script>
@endpush
