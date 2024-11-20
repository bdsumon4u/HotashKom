@extends('layouts.light.master')
@section('title', 'Invoice')

@push('css')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/print.css')}}">
@endpush

@push('styles')
    <style>
        .only-print {
            display: none;
        }
        @media print {
            html, body {
                height:100vh;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden;
            }
            .main-nav {
                display: none !important;
                width: 0 !important;
            }
            .print-edit-buttons,
            .footer {
                display: none !important;
            }
            .page-body {
                font-size: 20px;
                margin-top: 0 !important;
                margin-left: 0 !important;
                page-break-after: always;
            }
            .page-body p {
                font-size: 16px !important;
            }
            .only-print {
                display: block;
                padding-top: 2rem;
            }
        }
    </style>
@endpush

@section('breadcrumb-title')
    <h3>Invoice</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.orders.index') }}">Orders</a>
    </li>
    <li class="breadcrumb-item">Invoice</li>
@endsection

@section('content')
    <div class="row mb-5">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="invoice">
                        <div>
                        @include('admin.orders.invoice')
                        </div>
                        <div class="col-sm-12 print-edit-buttons text-center mt-3">
                            <button class="btn btn btn-primary mr-2" type="button" onclick="myFunction()">Print</button>
                            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-success">Edit</a>
                        </div>
                        <!-- End Invoice-->
                        <!-- End Invoice Holder-->
                    </div>
                </div>
                <div class="card-footer d-print-none">
                    <h5 class="text-center">Other Orders</h5>
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Product</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                            <tr>
                                <td>
                                    <a target="_blank" href="{{ route('admin.orders.show', $order) }}">{{ $order->id }}</a>
                                </td>
                                <td>{{ $order->created_at->format('d-M-Y') }}</td>
                                <td>{{ $order->status }}</td>
                                <td>
                                    @foreach ($order->products as $product)
                                        <div>{{ $product->quantity }} x {{ $product->name }}</div>
                                    @endforeach
                                </td>
                                <td>{{ $order->note }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{asset('assets/js/print.js')}}"></script>
@endpush