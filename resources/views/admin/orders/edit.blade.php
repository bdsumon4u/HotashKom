@extends('layouts.light.master')
@section('title', 'Edit Order')

@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/select2.css')}}">
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
    <div class="mb-5 row">
        <div class="col-sm-12">
            <div class="orders-table">
                <div class="shadow-sm card rounded-0">
                    <div class="p-3 card-header d-flex justify-content-between align-items-center">
                        <strong>Edit Order</strong>
                        <div>
                            <a href="{{ route('admin.orders.invoices', ['order_id' => $order->id]) }}" class="ml-1 btn btn-sm btn-primary">Invoice</a>
                            <a href="{{ route('admin.orders.booking', ['order_id' => $order->id]) }}" class="ml-1 btn btn-sm btn-primary">Send to Courier</a>
                            @if($order->status == 'CONFIRMED' && isReseller() && is_null($order->source_id))
                                <form id="forward-to-oninda-form" method="POST" action="{{ route('admin.orders.forward-to-oninda') }}" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="order_id[]" value="{{ $order->id }}">
                                    <button type="submit" class="ml-1 btn btn-sm btn-primary">Forward to Wholesaler</button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="p-3 card-body">
                        <div class="container-fluid">
                            @if($order->source_id === 0)
                            <div class="alert alert-warning">
                                This order is on queue to be forwarded to the Wholesaler. Editing is restricted.
                            </div>
                            @elseif($order->source_id && ! isOninda())
                            <div class="alert alert-warning">
                                This order is managed by the Wholesaler. Editing is restricted.
                            </div>
                            @endif

                            <div class="mb-5 row">
                                <div class="col-sm-12">
                                    <div class="orders-table">
                                        <livewire:edit-order :order="$order" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="shadow-sm card rounded-0">

                    <div class="p-3 card-header">
                        <h5 class="text-center">Other Orders</h5>
                    </div>
                    <div class="p-3 card-footer rounded-0">
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
                                        <a class="px-2 btn btn-light btn-sm text-nowrap" target="_blank" href="{{ route('admin.orders.edit', $order) }}">{{ $order->id }} <i class="fa fa-eye"></i></a>
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
    <script src="{{asset('assets/js/select2/select2.full.min.js')}}"></script>
    <script src="{{asset('assets/js/select2/select2-custom.js')}}"></script>
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
