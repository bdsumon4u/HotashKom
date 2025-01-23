@extends('layouts.yellow.master')

@title('Cart Details')

@push('styles')
<style>
    .btn {
        height: auto;
    }
</style>
@endpush

@section('content')

@include('partials.page-header', [
    'paths' => [
        url('/')                => 'Home',
        route('products.index') => 'Products',
    ],
    'active' => 'Cart Details',
    'page_title' => 'Cart Details'
])

<div class="block cart">
    <div class="container">
        <div class="pt-5 row justify-content-end">
            <div class="col-12 col-md-8">
                @include('partials.cart-table')
            </div>
            <div class="col-12 col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Cart Details</h3>
                        <table class="cart__totals">
                            <thead class="cart__totals-header">
                                <tr>
                                    <th>Subtotal</th>
                                    <td class="cart-subtotal">{!!  theMoney(0)  !!}</td>
                                </tr>
                            </thead>
                        </table>
                        <a class="px-2 btn btn-primary btn-xl btn-block cart__checkout-button" href="{{ route('checkout') }}">Proceed to checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection