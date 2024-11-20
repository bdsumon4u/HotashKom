@extends('layouts.light.master')
@section('title', 'Edit Order')

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/prism.css') }}">
@endpush

@section('breadcrumb-title')
    <h3>Edit Order</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.orders.index') }}">Orders</a>
    </li>
    <li class="breadcrumb-item">Edit Order</li>
@endsection

@section('content')
    <div class="row mb-5">
        <div class="col-sm-12">
            <div class="orders-table">
                <div class="card rounded-0 shadow-sm">
                    <div class="card-header p-3 d-flex justify-content-between align-items-center">
                        <strong>Edit Order</strong>
                        <div>
                            <a href="{{ route('admin.orders.invoices', ['order_id' => $order->id]) }}" class="btn btn-sm btn-primary ml-1">Invoice</a>
                            <a href="{{ route('admin.orders.booking', ['order_id' => $order->id]) }}" class="btn btn-sm btn-primary ml-1">Send to Courier</a>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <livewire:edit-order :order="$order" />
                    </div>
                </div>
                <div class="card rounded-0 shadow-sm">

                    <div class="card-header p-3">
                        <h5 class="text-center">Other Orders</h5>
                    </div>
                    <div class="card-footer rounded-0 p-3">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Products</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Courier</th>
                                    <th>Staff</th>
                                    <th>Date</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                <tr>
                                    <td>
                                        <a class="btn btn-light btn-sm text-nowrap px-2" target="_blank" href="{{ route('admin.orders.edit', $order) }}">{{ $order->id }} <i class="fa fa-eye"></i></a>
                                    </td>
                                    <td>
                                        @foreach ($order->products as $product)
                                            <div>{{ $product->quantity }} x {{ $product->name }}</div>
                                        @endforeach
                                    </td>
                                    <td>{{ intval($order->data['subtotal']) + intval($order->data['shipping_cost']) - intval($order->data['advanced'] ?? 0) - intval($order->data['discount'] ?? 0) }}</td>
                                    <td>{{ $order->status }}</td>
                                    <td>{{ $order->courier }}</td>
                                    <td>{{ $order->admin->name }}</td>
                                    <td>{{ $order->created_at->format('d-M-Y') }}</td>
                                    <td>{{ $order->note }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/js/prism/prism.min.js') }}"></script>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('change', '[name="data[courier]"]', function(ev) {
                if (ev.target.value == 'Pathao') {
                    $('[Pathao]').removeClass('d-none');
                } else {
                    $('[Pathao]').addClass('d-none');
                }
            });
        });
    </script>
@endpush
