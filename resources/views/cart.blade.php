@extends('layouts.yellow.master')

@title('Cart Details')

@push('styles')
<style>
    .btn {
        height: auto;
    }
    .cart-summary {
        position: sticky;
        top: 20px;
    }
    .retail-price-input {
        width: 120px;
    }
    .cart-item-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 4px;
    }
</style>
@endpush

@section('content')

@if(session('success'))
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
@endif

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
        <div class="pt-5 row">
            <div class="col-12 col-lg-8">
                @if(cart()->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">
                                <i class="mr-2 fa fa-shopping-cart"></i>
                                Shopping Cart ({{ cart()->count() }} items)
                            </h4>
                        </div>
                        <div class="p-0 card-body">
                            @include('partials.cart-table')
                        </div>
                    </div>

                    <div class="mt-3 text-center">
                        <a href="{{ route('reseller.products') }}" class="btn btn-outline-primary">
                            <i class="mr-2 fa fa-arrow-left"></i>Continue Shopping
                        </a>
                    </div>
                @else
                    <div class="card">
                        <div class="py-5 text-center card-body">
                            <i class="mb-3 fa fa-shopping-cart fa-3x text-muted"></i>
                            <h4>Your cart is empty</h4>
                            <p class="text-muted">Add some products to get started!</p>
                            <a href="{{ route('reseller.products') }}" class="btn btn-primary">
                                <i class="mr-2 fa fa-shopping-bag"></i>Browse Products
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            @if(cart()->count() > 0)
                <div class="col-12 col-lg-4">
                    <div class="card cart-summary">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="mb-2 d-flex justify-content-between">
                                    <span>Buying Subtotal:</span>
                                    <strong>{!! theMoney(cart()->subTotal()) !!}</strong>
                                </div>
                                @if(isOninda())
                                    <div class="mb-2 d-flex justify-content-between">
                                        <span>Selling Subtotal:</span>
                                        <strong id="selling-subtotal">Calculating...</strong>
                                    </div>
                                @endif
                            </div>

                            <hr>

                            <div class="mb-3">
                                <a href="{{ route('checkout') }}" class="btn btn-primary btn-block btn-lg">
                                    <i class="mr-2 fa fa-credit-card"></i>Proceed to Checkout
                                </a>
                            </div>

                            <div class="text-center">
                                <small class="text-muted">
                                    <i class="mr-1 fa fa-info-circle"></i>
                                    You can continue shopping or proceed to checkout
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calculate selling subtotal when retail prices change
    function calculateSellingSubtotal() {
        let sellingTotal = 0;
        const retailInputs = document.querySelectorAll('input[name^="retail_price"]');

        retailInputs.forEach(input => {
            const price = parseFloat(input.value) || 0;
            const quantity = parseInt(input.closest('tr').querySelector('input[name^="quantity"]').value) || 0;
            sellingTotal += price * quantity;
        });

        document.getElementById('selling-subtotal').textContent = '{{config('app.currency')}} ' + sellingTotal.toLocaleString('en-US', { maximumFractionDigits: 0 });
    }

    // Listen for changes in retail price inputs
    document.addEventListener('input', function(e) {
        if (e.target.name && e.target.name.startsWith('retail_price')) {
            calculateSellingSubtotal();
        }
    });

    // Initial calculation
    calculateSellingSubtotal();
});
</script>
@endpush
