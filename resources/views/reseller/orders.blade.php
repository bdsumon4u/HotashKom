@extends('layouts.reseller.master')

@section('title', 'Orders')

@section('breadcrumb-title')
    <h3>Orders</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Orders</li>
@endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatable/datatables/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatable/datatable-extension/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
    <style>
        .dt-buttons.btn-group {
            margin-bottom: 10px;
        }
        .dataTables_length {
            margin-bottom: 10px;
        }
        .dataTables_filter {
            margin-bottom: 10px;
        }
        .datatable td {
            vertical-align: top;
        }
        .datatable .fa {
            width: 16px;
            text-align: center;
        }
        .text-underline {
            text-decoration: underline;
        }
        .text-underline:hover {
            text-decoration: none;
        }
        /* Status Filter Buttons */
        .status-filter-buttons {
            margin-bottom: 1rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .status-filter-buttons .btn {
            font-size: 0.75rem;
            padding: 0.25rem 0.35rem;
            border-radius: 4px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .status-filter-buttons .btn.active {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }
        .status-filter-buttons .btn:not(.active) {
            background-color: #6f42c1;
            border-color: #6f42c1;
            color: white;
        }
        .status-filter-buttons .btn:hover {
            opacity: 0.8;
            transform: translateY(-1px);
        }
        /* Admin Panel Pagination Styling */
        .dataTables_wrapper .dataTables_paginate {
            border: 1px solid #f4f4f4;
            border-radius: 0.25rem;
            padding-top: 0;
        }
        /* Fix DataTables layout */
        .dataTables_wrapper .row {
            margin: 0;
        }
        .dataTables_wrapper .col-sm-6,
        .dataTables_wrapper .col-sm-12,
        .dataTables_wrapper .col-sm-5,
        .dataTables_wrapper .col-sm-7 {
            padding: 0 15px;
        }
        /* Ensure controls are on same line */
        .dataTables_length,
        .dataTables_filter,
        .dt-buttons {
            display: inline-block;
            margin-right: 15px;
            float: left;
        }
        .dataTables_filter {
            float: right;
        }
        .dataTables_info {
            clear: both;
            padding-top: 10px;
        }
        /* Fix pagination layout to be on same line as info */
        .dataTables_wrapper .dataTables_info {
            float: left;
            padding-top: 0;
            margin-right: 15px;
        }
        .dataTables_wrapper .dataTables_paginate {
            float: right;
            margin-left: 0 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            margin: 0;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            border-radius: 0.25rem;
            color: #2c323f;
            background: transparent;
            border: none;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button:active {
            background: #7366ff;
            color: #fff !important;
            -webkit-box-shadow: none;
            box-shadow: none;
            border: 1px solid #7366ff;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            border: 1px solid #7366ff;
            color: #2c323f !important;
            background: transparent !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            color: #98a6ad !important;
            cursor: not-allowed;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            color: #98a6ad !important;
            background: transparent !important;
            border: none;
        }
        .dataTables_wrapper .dataTables_info {
            color: #2c323f;
            font-size: 0.875rem;
            padding: 0.5rem 0;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="mb-5 row">
            <div class="col-sm-12">
                <div class="shadow-sm card rounded-0">
                    <div class="p-3 card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Orders</h5>
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <strong>Total Orders:</strong> {{ $totalOrders ?? 0 }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 card-body">
                        <!-- Status Filter Buttons -->
                        <div class="status-filter-buttons">
                            @php
                                $statuses = config('app.orders', []);
                                $currentStatus = request('status', '');
                                $user = auth('user')->user();

                                // Get all order counts in a single efficient query
                                $orderCounts = \App\Models\Order::where('user_id', $user->id)
                                    ->selectRaw('status, COUNT(*) as count')
                                    ->groupBy('status')
                                    ->pluck('count', 'status')
                                    ->toArray();

                                $totalCount = array_sum($orderCounts);
                            @endphp
                            @foreach($statuses as $status)
                                <a href="{{ request()->fullUrlWithQuery(['status' => $status]) }}"
                                   class="btn btn-xs {{ $currentStatus === $status ? 'active' : '' }}">
                                    {{ $status }} ({{ $orderCounts[$status] ?? 0 }})
                                </a>
                            @endforeach
                            <a href="{{ request()->fullUrlWithQuery(['status' => '']) }}"
                               class="btn btn-xs {{ $currentStatus === '' ? 'active' : '' }}">
                                All ({{ $totalCount }})
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th width="80">Order ID</th>
                                        <th>Customer</th>
                                        <th>Products</th>
                                        <th width="10">Status</th>
                                        <th width="10">Subtotal</th>
                                        <th width="10">Total</th>
                                        <th style="white-space: nowrap; min-width: 150px;">Date and Time</th>
                                        <th width="10">Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/buttons.bootstrap4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <script>
        var table = $('.datatable').DataTable({
            search: [{
                bRegex: true,
                bSmart: false,
            }],
            dom: 'lBftip',
            buttons: [{
                text: 'Export',
                className: 'btn btn-primary btn-sm',
                action: function(e, dt, node, config) {
                    // Add export functionality if needed
                }
            }],
            processing: true,
            serverSide: true,
            ajax: "{{ route('reseller.orders') }}" + (window.location.search ? window.location.search : ''),
            columns: [{
                    data: 'id',
                    name: 'id',
                    render: function(data) {
                        return '<a href="' + "{{ route('reseller.orders.show', ':id') }}".replace(':id', data) + '">#' + data + '</a>';
                    }
                },
                {
                    data: 'customer',
                    name: 'customer'
                },
                {
                    data: 'products',
                    name: 'products'
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data) {
                        let badgeClass = 'badge-info';
                        if (data === 'PENDING') badgeClass = 'badge-warning';
                        else if (data === 'CONFIRMED' || data === 'DELIVERED') badgeClass = 'badge-success';
                        else if (data === 'CANCELLED' || data === 'LOST') badgeClass = 'badge-danger';
                        else if (data === 'RETURNED') badgeClass = 'badge-warning';
                        else if (data === 'INVOICED' || data === 'SHIPPING' || data === 'WAITING') badgeClass = 'badge-info';
                        return '<span class="badge' + ' ' + badgeClass + '">' + data + '</span>';
                    }
                },
                {
                    data: 'subtotal',
                    name: 'subtotal'
                },
                {
                    data: 'total',
                    name: 'total'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let buttons = '<div class="d-flex flex-column"><a href="' + "{{ route('reseller.orders.show', ':id') }}".replace(':id', data) + '" class="btn btn-primary btn-sm btn-block">View</a>';

                        // Add Edit button for PENDING and CONFIRMED orders
                        if (row.status === 'PENDING' || row.status === 'CONFIRMED') {
                            buttons += ' <a href="' + "{{ route('reseller.orders.edit', ':id') }}".replace(':id', data) + '" class="btn btn-warning btn-sm btn-block">Edit</a>';
                        }

                        // Add Cancel button for orders that can be cancelled (PENDING, CONFIRMED)
                        if (row.status === 'PENDING' || row.status === 'CONFIRMED') {
                            buttons += ' <form class="btn-block" method="POST" action="' + "{{ route('reseller.orders.cancel', ':id') }}".replace(':id', data) + '" style="display: inline;" onsubmit="return confirm(\'Are you sure you want to cancel this order?\')">' +
                                '{{ csrf_field() }}' +
                                '<button type="submit" class="btn btn-danger btn-sm btn-block">Cancel</button>' +
                                '</form>';
                        }

                        return buttons + '</div>';
                    }
                }
            ],
            order: [
                [1, 'desc']
            ],
            pageLength: 25,
        });
    </script>
@endpush
