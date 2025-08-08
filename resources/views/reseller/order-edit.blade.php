@extends('layouts.reseller.master')

@section('title', 'Edit Order')

@section('breadcrumb-title')
    <h3>Edit Order #{{ $order->id }}</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Orders</li>
    <li class="breadcrumb-item">Edit Order</li>
@endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/daterange-picker.css') }}">
    <style>
        .daterangepicker {
            border: 2px solid #d7d7d7 !important;
        }
    </style>
@endpush

@section('content')
    <div class="mb-5 row">
        <div class="col-sm-12">
            <div class="shadow-sm card rounded-0">
                <div class="p-3 card-header">
                    <div class="px-3 row justify-content-between align-items-center">
                        <div>Edit Order #{{ $order->id }}</div>
                        <div>
                            <a href="{{ route('reseller.orders.show', $order) }}" class="btn btn-sm btn-secondary">Back to Order</a>
                        </div>
                    </div>
                </div>
                <div class="p-3 card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Order Status Alert -->
                    <div class="alert alert-info">
                        <strong>Order Status:</strong> {{ $order->status }}
                        <br>
                        <small>You can only edit orders with PENDING or CONFIRMED status.</small>
                        <br>
                        <small>Once an order is packed or the invoice is printed, you can't edit/cancel the order.</small>
                    </div>

                    <!-- Livewire Component -->
                    @livewire('reseller-edit-order', ['order' => $order])
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/datepicker/daterange-picker/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/daterange-picker/daterangepicker.js') }}"></script>
@endpush
