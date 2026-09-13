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

<div class="block cart py-4" style="background: #f8fafc;">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-8">
                @if(cart()->count() > 0)
                    <div class="card" style="border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 20px -2px rgba(15,23,42,0.06); overflow: hidden;">
                        <div class="card-header bg-white" style="border-bottom: 1px solid #f1f5f9; padding: 18px 24px;">
                            <h4 class="mb-0 font-weight-bold text-dark" style="font-size: 18px;">
                                <i class="fas fa-shopping-cart text-success mr-2"></i>
                                Shopping Cart ({{ cart()->count() }} {{ Str::plural('item', cart()->count()) }})
                            </h4>
                        </div>
                        <div class="card-body p-3">
                            @include('partials.cart-table')
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('reseller.products') }}" class="btn btn-outline-secondary font-weight-bold" style="border-radius: 10px;">
                            <i class="fa fa-arrow-left mr-2"></i>Continue Shopping
                        </a>
                    </div>
                @else
                    <div class="card" style="border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 20px -2px rgba(15,23,42,0.06);">
                        <div class="card-body text-center py-5">
                            <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; border-radius: 50%; background: #f1f5f9; color: #94a3b8; font-size: 32px;">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h4 class="font-weight-bold text-dark">Your cart is empty</h4>
                            <p class="text-muted">Explore our collection and add your favorite bags to the cart.</p>
                            <a href="{{ url('/') }}" class="btn font-weight-bold text-white px-4 py-2" style="background: linear-gradient(135deg, var(--brand), var(--brand-dark)); border-radius: 10px;">
                                <i class="fas fa-bag-shopping mr-2"></i>Browse Products
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            @if(cart()->count() > 0)
                <div class="col-12 col-lg-4 mt-4 mt-lg-0">
                    <div class="card cart-summary" style="border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 20px -2px rgba(15,23,42,0.06); position: sticky; top: 20px;">
                        <div class="card-header bg-white" style="border-bottom: 1px solid #f1f5f9; padding: 18px 24px;">
                            <h5 class="mb-0 font-weight-bold text-dark" style="font-size: 17px;">Order Summary</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted font-weight-bold">Subtotal:</span>
                                    <strong class="text-dark" style="font-size: 16px;">{!! theMoney(cart()->subTotal()) !!}</strong>
                                </div>
                                @if(isOninda())
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted font-weight-bold">Selling Subtotal:</span>
                                        <strong id="selling-subtotal" class="text-success">Calculating...</strong>
                                    </div>
                                @endif
                            </div>

                            <hr style="border-color: #f1f5f9;">

                            <div class="mb-3">
                                <a href="{{ route('checkout') }}" class="btn btn-block btn-lg font-weight-bold text-white d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(135deg, var(--brand), var(--brand-dark)); border: none; border-radius: 12px; min-height: 48px; box-shadow: 0 4px 14px rgba(var(--brand-rgb), 0.3);" wire:navigate.hover>
                                    <i class="fas fa-lock"></i>
                                    <span>Proceed to Checkout</span>
                                </a>
                            </div>

                            <div class="text-center">
                                <small class="text-muted">
                                    <i class="fas fa-shield-halved text-success mr-1"></i>
                                    100% Safe & Secure Checkout
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

        document.getElementById('selling-subtotal').textContent = 'TK ' + sellingTotal.toLocaleString('en-US', { maximumFractionDigits: 0 });
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
