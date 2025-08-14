@extends('layouts.reseller.master')

@title('Order Status')

@push('styles')
<style>
    /* Order Status Table Styling - Matching Storefront Design */
    .order-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e9ecef;
    }

    .order-header__title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #343a40;
        margin-bottom: 0.5rem;
    }

    .order-header__actions {
        margin-bottom: 1rem;
    }

    .order-header__subtitle {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0;
    }

    .order-header__date,
    .order-header__status {
        background-color: #fff3cd;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-weight: 700;
    }

    .card-table {
        padding: 1.5rem;
    }

    .card-table table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 0.375rem;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .card-table thead {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .card-table thead th {
        padding: 0.75rem;
        font-weight: 600;
        color: #343a40;
        text-transform: uppercase;
        font-size: 0.875rem;
        letter-spacing: 0.5px;
        border: none;
        text-align: left;
    }

    .card-table thead th:last-child {
        text-align: right;
    }

    .card-table thead th:nth-child(2) {
        text-align: right;
    }

    .card-table tbody tr {
        border-bottom: 1px solid #dee2e6;
        transition: background-color 0.15s ease-in-out;
    }

    .card-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .card-table tbody tr:last-child {
        border-bottom: none;
    }

    .card-table tbody td,
    .card-table tbody th {
        padding: 0.75rem;
        vertical-align: middle;
        border: none;
    }

    .card-table tbody th {
        font-weight: 600;
        color: #343a40;
        text-align: left;
    }

    .card-table tbody td {
        color: #495057;
        text-align: left;
    }

    .card-table tbody td:last-child {
        text-align: right;
    }

    .card-table tbody td:nth-child(2) {
        text-align: right;
    }

    .card-table tfoot {
        border-top: 2px solid #dee2e6;
        background-color: #f8f9fa;
    }

    .card-table tfoot th {
        padding: 0.75rem;
        font-weight: 700;
        color: #343a40;
        text-align: left;
        font-size: 1.1rem;
    }

    .card-table tfoot td {
        padding: 0.75rem;
        font-weight: 700;
        color: #343a40;
        text-align: right;
        font-size: 1.1rem;
    }

    .card-table tbody a {
        color: #007bff;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.15s ease-in-out;
    }

    .card-table tbody a:hover {
        color: #0056b3;
        text-decoration: underline;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-table {
            padding: 1rem;
        }

        .card-table table {
            font-size: 0.875rem;
        }

        .card-table thead th,
        .card-table tbody td,
        .card-table tbody th,
        .card-table tfoot th,
        .card-table tfoot td {
            padding: 0.5rem;
        }

        .order-header {
            padding: 1rem;
        }

        .order-header__title {
            font-size: 1.25rem;
        }
    }

    /* Button styling */
    .btn-secondary {
        background-color: #e9ecef;
        border: 1px solid #ced4da;
        color: #343a40;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.25rem;
        transition: all 0.15s ease-in-out;
    }

    .btn-secondary:hover {
        background-color: #dae0e5;
        border-color: #adb5bd;
        color: #343a40;
    }

    /* Card styling */
    .card {
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem;
        text-align: center;
    }

    .card-divider {
        height: 1px;
        background-color: #e9ecef;
        margin: 0;
    }

    /* Success message styling */
    .text-success {
        color: #28a745 !important;
    }

    /* Table responsive wrapper */
    .table-responsive-sm {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>
@endpush

@section('breadcrumb-title')
    <h3>Order Status</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">
        <a href="{{ route('reseller.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Order Status</li>
@endsection

@section('content')
<div class="block mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    @if (session()->has('completed'))
                    <div class="card-header">
                        <div class="d-flex justify-content-center">
                            <img width="100" height="100" src="{{ asset('tik-mark.png') }}" alt="Tick Mark">
                        </div>
                        <h4 class="text-center text-success">আপনার অর্ডারটি সাবমিট করা হয়েছে।</h4>
                        <h4 class="text-center text-success">ধন্যবাদ।</h4>
                    </div>
                    @endif
                    <div class="order-header">
                        <h5 class="order-header__title">Order #{{ $order->id }}</h5>
                        <div class="order-header__subtitle">Was placed on <mark class="order-header__date">{{
                                $order->created_at->format('d-m-Y') }}</mark> and
                            currently status is <mark class="order-header__status">{{ $order->status }}</mark>.</div>
                        @if (false && $order->status == 'PENDING')
                        <div class="order-header__subtitle">
                            <form action="{{ route('track-order') }}" method="post">
                                @csrf
                                <input type="hidden" name="order" value="{{ old('order', $order->id) }}">
                                <div class="form-group">
                                    <label for="">Please check your sms for the code.</label>
                                    <div class="row">
                                        <div class="my-1 col-md-7">
                                            <input type="text" name="code" value="{{ old('code') }}"
                                                class="form-control" placeholder="Confirmation Code">
                                        </div>
                                        <div class="px-0 my-1 col d-flex align-items-center justify-content-around">
                                            <button name="action" value="resend" class="btn btn-sm btn-secondary">Resend
                                                Code</button>
                                            <div class="mx-1"></div>
                                            <button name="action" value="confirm" class="btn btn-sm btn-primary">Confirm
                                                Order</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endif
                    </div>
                    <div class="card-divider"></div>
                    <div class="card-table">
                        <div class="table-responsive-sm">
                            <table style="min-width: 320px;">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Buy Price</th>
                                        @if(isOninda() && config('app.resell'))
                                        <th>Sell Price</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="card-table__body card-table__body--merge-rows">
                                    @php($retail = 0)
                                    @foreach ($order->products as $product)
                                    <tr>
                                        <td><a href="{{ route('products.show', $product->slug) }}">{{ $product->name
                                                }}</a>
                                            × {{ $product->quantity }}</td>
                                        <td>{!! theMoney($product->quantity * $product->price) !!}</td>
                                        @if(isOninda() && config('app.resell'))
                                        <td>{!! theMoney($amount = $product->quantity * $product->retail_price) !!}</td>
                                        @php($retail += $amount)
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tbody class="card-table__body card-table__body--merge-rows">
                                    @php($data = $order->data)
                                    <tr>
                                        <th>Subtotal</th>
                                        <td>{!! theMoney($data['subtotal']) !!}</td>
                                        @if(isOninda() && config('app.resell'))
                                        <td>{!! theMoney($retail) !!}</td>
                                        @endif
                                    </tr>
                                    @if ($data['advanced'])
                                    <tr>
                                        <th>Advanced</th>
                                        <td>{!! theMoney(0) !!}</td>
                                        @if(isOninda() && config('app.resell'))
                                        <td>{!! theMoney($data['advanced']) !!}</td>
                                        @endif
                                    </tr>
                                    @endif
                                    @if ($data['retail_discount'])
                                    <tr>
                                        <th>Discount</th>
                                        <td>{!! theMoney($data['discount'] ?? 0) !!}</td>
                                        @if(isOninda() && config('app.resell'))
                                        <td>{!! theMoney($data['retail_discount']) !!}</td>
                                        @endif
                                    </tr>
                                    @endif
                                    @if(isOninda() && config('app.resell'))
                                    <tr>
                                        <th>Packaging Charge</th>
                                        <td>{!! theMoney($data['packaging_charge'] ?? 25) !!}</td>
                                        <td>{!! theMoney(0) !!}</td>
                                    </tr>
                                    @endif
                                    <!-- Packaging Charge -->
                                    <tr>
                                        <th>Delivery Charge</th>
                                        <td>{!! theMoney($data['shipping_cost']) !!}</td>
                                        @if(isOninda() && config('app.resell'))
                                        <td>{!! theMoney($data['retail_delivery_fee']) !!}</td>
                                        @endif
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Grand Total</th>
                                        @if(isOninda() && config('app.resell'))
                                        <td>{!! theMoney($data['subtotal'] + $data['shipping_cost'] +
                                            ($data['packaging_charge'] ?? 25) - ($data['discount'] ?? 0)) !!}</td>
                                        <td>{!! theMoney($retail + $data['retail_delivery_fee'] - ($data['advanced'] ??
                                            0) - ($data['retail_discount'] ?? 0)) !!}</td>
                                        @else
                                        <td>{!! theMoney($data['subtotal'] + $data['shipping_cost'] - ($data['discount']
                                            ?? 0)) !!}</td>
                                        @endif
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
