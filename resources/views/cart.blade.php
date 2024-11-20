@extends('layouts.yellow.master')

@title('Cart Details')

@push('styles')
<style>
    .btn {
        height: auto;
    }
</style>
@endpush

@content

@include('partials.page-header', [
    'paths' => [
        url('/')                => 'Home',
        route('products.index') => 'Products',
    ],
    'active' => 'Cart Details',
    'page_title' => 'Cart Details'
])

<div class="cart block">
    <div class="container">
        <div class="row justify-content-end pt-5">
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
                        <a class="btn btn-primary btn-xl btn-block cart__checkout-button px-2" href="{{ route('checkout') }}">Proceed to checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection