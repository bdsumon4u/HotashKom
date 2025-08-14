@extends('layouts.reseller.master')

@section('title', 'Products')

@section('breadcrumb-title')
    <h3>Products</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">
        <a href="{{ route('reseller.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Products</li>
@endsection

@section('breadcrumb-right')
    <div class="d-flex align-items-center">
        <a href="{{ route('reseller.checkout') }}" class="btn btn-primary">
            <i class="mr-2 fa fa-shopping-cart"></i>
            Cart <livewire:cart-count />
        </a>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Search Section -->
            <div class="mb-4 shadow-sm card rounded-0">
                <div class="p-3 card-body">
                    <form method="GET" action="{{ route('reseller.products') }}" class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control search-box"
                                   placeholder="Search by name, SKU, or description"
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="my-2 form-check filter-checkbox">
                                <input type="checkbox" class="form-check-input" id="hot_sale" name="hot_sale" value="1" {{ request('hot_sale')
                                    ? 'checked' : '' }}>
                                <label class="form-check-label" for="hot_sale">
                                    <span class="badge badge-warning">
                                        <i class="mr-1 fa fa-fire"></i>Hot Sale
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-2 col-6">
                            <div class="my-2 form-check filter-checkbox">
                                <input type="checkbox" class="form-check-input" id="new_arrival" name="new_arrival" value="1" {{
                                    request('new_arrival') ? 'checked' : '' }}>
                                <label class="form-check-label" for="new_arrival">
                                    <span class="badge badge-info">
                                        <i class="mr-1 fa fa-star"></i>New Arrival
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="mr-2 fa fa-search"></i>Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row">
                @forelse($products as $product)
                    @php
                        $selectedVar = $product;
                        if ($product->variations->isNotEmpty()) {
                            $selectedVar = $product->variations->random();
                        }
                        $available = !$selectedVar->should_track || $selectedVar->stock_count > 0;
                    @endphp

                    <div class="px-2 mb-2 col-lg-3 col-md-4 col-sm-6">
                        <div class="product-card">
                            <!-- Product Status Badges -->
                            <div class="product-badges">
                                @if($product->hot_sale)
                                    <span class="badge badge-warning position-absolute" style="top: 10px; left: 10px; z-index: 1;">
                                        <i class="mr-1 fa fa-fire"></i>Hot Sale
                                    </span>
                                @endif
                                @if($product->new_arrival)
                                    <span class="badge badge-info position-absolute" style="top: 10px; right: 10px; z-index: 1;">
                                        <i class="mr-1 fa fa-star"></i>New
                                    </span>
                                @endif
                            </div>

                            <img src="{{ asset(optional($selectedVar->baseImage)->src ?? 'assets/images/no-image.png') }}"
                                 alt="{{ $product->name }}" class="product-image">

                            <div class="product-name">
                                <a href="{{ route('products.show', $selectedVar->slug) }}" target="_blank" class="text-decoration-none">
                                    {{ $product->name }}
                                </a>
                            </div>

                            <div class="product-sku">
                                SKU: {{ $selectedVar->sku }}
                            </div>

                            <div class="product-price">
                                {!! theMoney($selectedVar->selling_price) !!}
                                @if($selectedVar->selling_price != $selectedVar->price)
                                    <del class="text-muted">{!! theMoney($selectedVar->price) !!}</del>
                                @endif
                            </div>

                            <div class="product-stock">
                                @if(!$selectedVar->should_track)
                                    <span class="text-success">In Stock</span>
                                @else
                                    <span class="text-{{ $selectedVar->stock_count ? 'success' : 'danger' }}">
                                        {{ $selectedVar->stock_count }} In Stock
                                    </span>
                                @endif
                            </div>

                            @if($available)
                                <div class="product-actions">
                                    <livewire:reseller-product-card :product="$selectedVar" />
                                </div>
                            @else
                                <div class="product-actions">
                                    <button class="btn btn-secondary w-100" disabled>
                                        Out of Stock
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="py-5 text-center">
                            <i class="mb-3 fa fa-box fa-3x text-muted"></i>
                            <h4 class="text-muted">No products found</h4>
                            <p class="text-muted">
                                @if(request('search'))
                                    No products match your search criteria.
                                @else
                                    No products available at the moment.
                                @endif
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="mt-2 d-flex justify-content-center">
                    {{ $products->onEachSide(1)->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<div class="mb-5">.</div><div class="mb-5">.</div>

<style>
.product-card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.3s ease;
    /* height: 100%; */
    display: flex;
    flex-direction: column;
    position: relative;
}

.product-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.product-badges {
    position: relative;
}

.product-badges .badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-badges .badge-warning {
    background: linear-gradient(45deg, #ffc107, #ff8f00);
    color: #fff;
    border: none;
}

.product-badges .badge-info {
    background: linear-gradient(45deg, #17a2b8, #0d6efd);
    color: #fff;
    border: none;
}

.product-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 6px;
    margin-bottom: 1rem;
}

.product-name {
    margin-bottom: 0.5rem;
    line-clamp: 2;
    overflow: hidden;
    text-overflow: ellipsis;
}

.product-name a {
    color: #333;
    font-weight: 600;
    font-size: 1.1rem;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.product-name a:hover {
    color: #007bff;
}

.product-sku {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.product-price {
    font-size: 1.2rem;
    font-weight: 700;
    color: #28a745;
    margin-bottom: 0.5rem;
}

.product-stock {
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.product-actions {
    margin-top: auto;
}

.search-box {
    border-radius: 2px;
    padding: 0.75rem 1.5rem;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.search-box:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.form-check-label {
    cursor: pointer;
    user-select: none;
}

.form-check-label .badge {
    font-size: 0.8rem;
    padding: 0.3rem 0.6rem;
}

.filter-checkbox .form-check-input:checked + .form-check-label .badge {
    background-color: #007bff;
    border-color: #007bff;
}

.filter-checkbox .form-check-input:not(:checked) + .form-check-label .badge {
    background-color: #e9ecef;
    border-color: #e9ecef;
    color: #6c757d;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit search form when typing stops
    let searchTimeout;
    const searchInput = document.querySelector('.search-box');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.closest('form').submit();
            }, 500);
        });
    }

    // Auto-submit filter form when checkboxes change
    const filterCheckboxes = document.querySelectorAll('input[name="hot_sale"], input[name="new_arrival"]');
    filterCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Add a small delay to prevent rapid submissions
            setTimeout(() => {
                this.closest('form').submit();
            }, 100);
        });
    });

    // Debug: Log current filter state
    console.log('Current filters:', {
        hot_sale: '{{ request("hot_sale") }}',
        new_arrival: '{{ request("new_arrival") }}',
        search: '{{ request("search") }}'
    });
});
</script>
@endsection
