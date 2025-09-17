@extends('layouts.yellow.master')

@title(session()->has('completed') ? 'Purchase' : 'Order Status')

@section('content')
    <div class="block mt-4">
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
                            <div class="order-header__actions"><a href="{{ url('/') }}"
                                    class="btn btn-xs btn-secondary">Back to Home</a></div>
                            <div class="order-header__subtitle">Was placed on <mark
                                    class="order-header__date">{{ $order->created_at->format('d-m-Y') }}</mark> and
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
                                                    <button name="action" value="resend"
                                                        class="btn btn-sm btn-secondary">Resend Code</button>
                                                    <div class="mx-1"></div>
                                                    <button name="action" value="confirm"
                                                        class="btn btn-sm btn-primary">Confirm Order</button>
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
                                                <td><a
                                                        href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                                                    × {{ $product->quantity }}</td>
                                                <td>{!! theMoney($product->quantity * $product->price) !!}</td>
                                                @if(isOninda() && config('app.resell'))
                                                <td>{!! theMoney($amount = $product->quantity * $product->retail_price) !!}</td>
                                                @php($retail += (float) $amount)
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tbody class="card-table__body card-table__body--merge-rows">
                                        @php($data = $order->data)
                                        <tr>
                                            <th>Subtotal</th>
                                                                                            <td>{!! theMoney((float) ($data['subtotal'] ?? 0)) !!}</td>
                                            @if(isOninda() && config('app.resell'))
                                            <td>{!! theMoney($retail) !!}</td>
                                            @endif
                                        </tr>
                                        @if ($data['advanced'])
                                            <tr>
                                                <th>Advanced</th>
                                                <td>{!! theMoney(0) !!}</td>
                                                @if(isOninda() && config('app.resell'))
                                                <td>{!! theMoney((float) ($data['advanced'] ?? 0)) !!}</td>
                                                @endif
                                            </tr>
                                        @endif
                                                                                @if ($data['retail_discount'])
                                        <tr>
                                            <th>Discount</th>
                                            <td>{!! theMoney($data['discount'] ?? 0) !!}</td>
                                            @if(isOninda() && config('app.resell'))
                                                                                            <td>{!! theMoney((float) ($data['retail_discount'] ?? 0)) !!}</td>
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
                                                                                            <td>{!! theMoney((float) ($data['shipping_cost'] ?? 0)) !!}</td>
                                            @if(isOninda() && config('app.resell'))
                                                                                            <td>{!! theMoney((float) ($data['retail_delivery_fee'] ?? 0)) !!}</td>
                                            @endif
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Grand Total</th>
                                            @if(isOninda() && config('app.resell'))
                                            <td>{!! theMoney((float) ($data['subtotal'] ?? 0) + (float) ($data['shipping_cost'] ?? 0) + (float) ($data['packaging_charge'] ?? 25) - (float) ($data['discount'] ?? 0)) !!}</td>
                                            <td>{!! theMoney((float) $retail + (float) ($data['retail_delivery_fee'] ?? 0) - (float) ($data['advanced'] ?? 0) - (float) ($data['retail_discount'] ?? 0)) !!}</td>
                                            @else
                                            <td>{!! theMoney((float) ($data['subtotal'] ?? 0) + (float) ($data['shipping_cost'] ?? 0) - (float) ($data['discount'] ?? 0)) !!}</td>
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
