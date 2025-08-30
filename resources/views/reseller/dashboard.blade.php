@extends('layouts.reseller.master')

@section('title', 'Dashboard')

@section('breadcrumb-title')
    <h3>Reseller Dashboard</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
@endsection

@section('breadcrumb-right')
    <div class="theme-form m-t-10">
        <div class="text-right">
            <span class="text-muted">Welcome back, {{ auth('user')->user()->name }}!</span>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/animate.css') }}">
    <style>
        .ecommerce-widgets {
            display: flex;
            align-items: center;
        }
        .ecommerce-box {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
        }
        .light-bg-primary {
            background-color: #007bff;
            color: white;
        }
        .light-bg-success {
            background-color: #28a745;
            color: white;
        }
        .light-bg-warning {
            background-color: #ffc107;
            color: white;
        }
        .f-w-500 {
            font-weight: 500;
        }
        .f-26 {
            font-size: 26px;
        }
        .font-roboto {
            font-family: 'Roboto', sans-serif;
        }
        /* Dashboard specific styles only */
    </style>
@endpush

@section('content')
    <div class="mb-5 container-fluid">
        <x-reseller-verification-alert />
        <div class="row size-column">
            <div class="col-xl-7 box-col-12 xl-100">
                <div class="row dash-chart">
                    <div class="col-12">
                        <div class="mb-3">
                            @foreach (config('app.orders', []) as $status)
                                <a class="px-2 py-1 m-1 btn btn-light"
                                    href="{{ route('reseller.orders', ['status' => $status == 'All' ? '' : $status]) }}">
                                    <span>{{ $status }}</span>
                                </a>
                            @endforeach
                            <a href="{{ route('reseller.orders') }}" class="px-2 py-1 m-1 btn btn-light">
                                <span>All</span>
                            </a>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="col-xl-4 box-col-4 col-lg-4 col-md-4">
                        <div class="rounded-sm card o-hidden">
                            <div class="p-3 card-body">
                                <div class="ecommerce-widgets media">
                                    <div class="media-body">
                                        <p class="mb-2 f-w-500 font-roboto">Total Orders</p>
                                        <h4 class="mb-0 f-w-500 f-26"><span class="counter">{{ $totalOrders ?? 0 }}</span></h4>
                                    </div>
                                    <div class="ecommerce-box light-bg-primary"><i class="fa fa-shopping-bag" aria-hidden="true"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 box-col-4 col-lg-4 col-md-4">
                        <div class="rounded-sm card o-hidden">
                            <div class="p-3 card-body">
                                <div class="ecommerce-widgets media">
                                    <div class="media-body">
                                        <p class="mb-2 f-w-500 font-roboto">Total Sales</p>
                                        <h4 class="mb-0 f-w-500 f-26">{{ number_format((float) ($totalSales ?? 0), 2) }} tk</h4>
                                    </div>
                                    <div class="ecommerce-box light-bg-success"><i class="fa fa-money-bill" aria-hidden="true"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 box-col-4 col-lg-4 col-md-4">
                        <div class="rounded-sm card o-hidden">
                            <div class="p-3 card-body">
                                <div class="ecommerce-widgets media">
                                    <div class="media-body">
                                        <p class="mb-2 f-w-500 font-roboto">Available Balance</p>
                                        <h4 class="mb-0 f-w-500 f-26"><span class="counter">{{ number_format((float) ($availableBalance ?? 0), 2) }}</span> tk</h4>
                                    </div>
                                    <div class="ecommerce-box light-bg-warning"><i class="fa fa-wallet" aria-hidden="true"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

            <div class="col-xl-5 box-col-12 xl-50">
                <div class="row dash-chart">
                    <div class="col-xl-12 box-col-12 col-lg-12 col-md-12">
                        <div class="rounded-sm card o-hidden">
                            <div class="p-3 card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Recent Orders</h6>
                                    <a href="{{ route('reseller.orders') }}" class="btn btn-primary btn-sm">View All</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentOrders ?? [] as $order)
                                                <tr>
                                                    <td><a href="{{ route('reseller.orders.show', $order->id) }}">#{{ $order->id }}</a></td>
                                                    <td>{{ $order->created_at->format('d-M-Y') }}</td>
                                                    <td><span class="badge badge-{{ $order->status == 'PENDING' ? 'warning' : ($order->status == 'CONFIRMED' ? 'success' : 'info') }}">{{ $order->status }}</span></td>
                                                    <td>{{ number_format((float) ($order->data['subtotal'] ?? 0), 2) }} tk</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">No orders found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Continue Shopping Section -->
            <div class="mt-4 col-12">
                <div class="card">
                    <div class="text-center card-body">
                        <h5 class="card-title">Ready to place an order?</h5>
                        <p class="card-text">Browse our product catalog and add items to your cart</p>
                        <div class="gap-2 d-flex justify-content-center">
                            <a href="{{ route('reseller.products') }}" class="btn btn-primary">
                                <i class="mr-2 fa fa-shopping-bag"></i>Browse Products
                            </a>
                            @if(cart()->count() > 0)
                                <a href="{{ route('reseller.checkout') }}" class="btn btn-outline-primary">
                                    <i class="mr-2 fa fa-shopping-cart"></i>View Cart ({{ cart()->count() }})
                                </a>
                                <a href="{{ route('checkout') }}" class="btn btn-success">
                                    <i class="mr-2 fa fa-credit-card"></i>Checkout
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="row">
            <div class="col-xl-12 box-col-12">
                <div class="rounded-sm card o-hidden">
                    <div class="p-3 card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Recent Transactions</h6>
                            <a href="{{ route('reseller.transactions') }}" class="btn btn-primary btn-sm">View All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTransactions ?? [] as $transaction)
                                        <tr>
                                            <td>{{ ucfirst($transaction->type) }}</td>
                                            <td>{{ number_format($transaction->amount, 2) }} tk</td>
                                            <td>{{ $transaction->created_at->format('d-M-Y H:i') }}</td>
                                            <td><span class="badge badge-{{ $transaction->confirmed ? 'success' : 'warning' }}">{{ $transaction->confirmed ? 'COMPLETED' : 'PENDING' }}</span></td>
                                            <td>{{ $transaction->meta['reason'] ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No transactions found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Initialize any dashboard-specific functionality here
        $(document).ready(function() {
            // Add any reseller dashboard specific JavaScript here
        });
    </script>
@endpush
