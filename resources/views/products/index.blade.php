@extends('layouts.yellow.master')

@section('title', 'Products')

@section('content')

@include('partials.page-header', [
    'paths' => [
        url('/') => 'Home',
    ],
    'active' => 'Products',
    'page_title' => 'Products'
])

<div class="block">
    <div class="products-view">
        <div class="container">
            <div class="row">
                <!-- Filter Sidebar -->
                <div class="pr-md-1 col-lg-3 col-md-4" x-data="filterSidebar()">
                    <div class="p-3 filter-sidebar">
                        <div class="filter-sidebar__header">
                            <h3 class="filter-sidebar__title">Filters</h3>
                            <button type="button" class="filter-sidebar__toggle d-md-none" @click="mobileOpen = !mobileOpen">
                                <i class="fa" :class="mobileOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>
                        </div>

                        <form method="GET" action="{{ route('products.index') }}" id="filter-form"
                              x-show="mobileOpen || isDesktop"
                              x-transition
                              class="filter-sidebar__content"
                              x-init="checkDesktop()">

                            <!-- Preserve search parameter -->
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif

                            <!-- Categories Filter -->
                            <div class="filter-block">
                                <div class="filter-block__header" @click="categoriesOpen = !categoriesOpen">
                                    <h4 class="filter-block__title">Categories</h4>
                                    <i class="fa" :class="categoriesOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                </div>
                                <div class="filter-block__content" x-show="categoriesOpen" x-transition>
                                    @php
                                        $filterCategory = request('filter_category');
                                        $selectedCategories = [];

                                        if ($filterCategory) {
                                            if (is_array($filterCategory)) {
                                                $selectedCategories = array_map('intval', array_filter($filterCategory));
                                            } elseif (is_numeric(str_replace(',', '', $filterCategory))) {
                                                $selectedCategories = array_map('intval', explode(',', $filterCategory));
                                            }
                                        }
                                    @endphp
                                    @foreach($categories ?? [] as $category)
                                        <div class="filter-item">
                                            <label class="filter-checkbox">
                                                <input type="checkbox"
                                                       name="filter_category[]"
                                                       value="{{ $category->id }}"
                                                       @if(in_array((int)$category->id, $selectedCategories)) checked @endif
                                                       @change="updateFilter()">
                                                <span class="filter-checkbox__label">{{ $category->name }}</span>
                                                <span class="filter-checkbox__count">({{ $category->products()->whereIsActive(1)->whereNull('parent_id')->count() }})</span>
                                            </label>
                                            @if($category->childrens->isNotEmpty())
                                                <div class="ml-3 filter-item__children">
                                                    @foreach($category->childrens as $child)
                                                        <label class="filter-checkbox">
                                                            <input type="checkbox"
                                                                   name="filter_category[]"
                                                                   value="{{ $child->id }}"
                                                                   @if(in_array((int)$child->id, $selectedCategories)) checked @endif
                                                                   @change="updateFilter()">
                                                            <span class="filter-checkbox__label">{{ $child->name }}</span>
                                                            <span class="filter-checkbox__count">({{ $child->products()->whereIsActive(1)->whereNull('parent_id')->count() }})</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Price Range Filter -->
                            @if(isset($priceRange) && $priceRange && $priceRange->min_price && $priceRange->max_price)
                            <div class="filter-block">
                                <div class="filter-block__header" @click="priceOpen = !priceOpen">
                                    <h4 class="filter-block__title">Price</h4>
                                    <i class="fa" :class="priceOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                </div>
                                <div class="filter-block__content" x-show="priceOpen" x-transition>
                                    <div class="price-filter">
                                        <div class="price-filter__display">
                                            <span x-text="formatPrice(minPrice)"></span> - <span x-text="formatPrice(maxPrice)"></span>
                                        </div>
                                        <div class="price-filter__slider-container">
                                            <div class="price-filter__track">
                                                <div class="price-filter__track-fill"
                                                     :style="'left: ' + ((minPrice - {{ $priceRange->min_price }}) / ({{ $priceRange->max_price }} - {{ $priceRange->min_price }}) * 100) + '%; width: ' + ((maxPrice - minPrice) / ({{ $priceRange->max_price }} - {{ $priceRange->min_price }}) * 100) + '%;'"></div>
                                            </div>
                                            <input type="range"
                                                   class="price-filter__slider price-filter__slider--min"
                                                   min="{{ $priceRange->min_price }}"
                                                   max="{{ $priceRange->max_price }}"
                                                   step="100"
                                                   :value="minPrice"
                                                   @input="minPrice = Math.min(parseInt($event.target.value), maxPrice); updatePriceDisplay()">
                                            <input type="range"
                                                   class="price-filter__slider price-filter__slider--max"
                                                   min="{{ $priceRange->min_price }}"
                                                   max="{{ $priceRange->max_price }}"
                                                   step="100"
                                                   :value="maxPrice"
                                                   @input="maxPrice = Math.max(parseInt($event.target.value), minPrice); updatePriceDisplay()">
                                        </div>
                                        <input type="hidden" name="min_price" :value="minPrice">
                                        <input type="hidden" name="max_price" :value="maxPrice">
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Brands Filter -->
                            <div class="filter-block">
                                <div class="filter-block__header" @click="brandsOpen = !brandsOpen">
                                    <h4 class="filter-block__title">Brand</h4>
                                    <i class="fa" :class="brandsOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                </div>
                                <div class="filter-block__content" x-show="brandsOpen" x-transition>
                                    @php
                                        $filterBrand = request('filter_brand');
                                        $selectedBrands = [];

                                        if ($filterBrand) {
                                            if (is_array($filterBrand)) {
                                                $selectedBrands = array_map('intval', array_filter($filterBrand));
                                            } else {
                                                $selectedBrands = array_map('intval', explode(',', $filterBrand));
                                            }
                                        }
                                    @endphp
                                    @foreach($brands ?? [] as $brand)
                                        <label class="filter-checkbox">
                                            <input type="checkbox"
                                                   name="filter_brand[]"
                                                   value="{{ $brand->id }}"
                                                   @if(in_array((int)$brand->id, $selectedBrands)) checked @endif
                                                   @change="updateFilter()">
                                            <span class="filter-checkbox__label">{{ $brand->name }}</span>
                                            <span class="filter-checkbox__count">({{ $brand->products()->whereIsActive(1)->whereNull('parent_id')->count() }})</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Filter Actions -->
                            <div class="filter-actions">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('products.index', request()->only('search')) }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Products Content -->
                <div class="pl-md-1 col-lg-9 col-md-8">
                    <div class="products-view__options">
                        <div class="view-options">
                            <div class="view-options__legend">
                                @if(request('search'))
                                Found {{ $products->total() }} result(s) for "{{ request('search', 'NULL') }}"
                                @elseif($category = request()->route()->parameter('category'))
                                Showing from "{{ $category->name }}" category.
                                @elseif($brand = request()->route()->parameter('brand'))
                                Showing from "{{ $brand->name }}" brand.
                                @else
                                Showing {{ $products->count() }} of {{ $products->total() }} products
                                @endif
                            </div>
                            <div class="view-options__divider"></div>
                        </div>
                    </div>

                    @include('partials.products.pure-grid', [
                        'title' => null,
                        'cols' => 4,
                    ])

                    <div class="pt-0 products-view__pagination">
                        {!! $products->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.filter-sidebar {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    position: sticky;
    top: 20px;
    max-height: calc(100vh - 40px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.filter-sidebar__content {
    overflow-y: auto;
    overflow-x: hidden;
    flex: 1;
    padding-right: 0.5rem;
    margin-right: -0.5rem;
}

.filter-sidebar__content::-webkit-scrollbar {
    width: 6px;
}

.filter-sidebar__content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.filter-sidebar__content::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.filter-sidebar__content::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.filter-sidebar__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}

.filter-sidebar__title {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
}

.filter-sidebar__toggle {
    background: none;
    border: none;
    font-size: 1.2rem;
    color: #6c757d;
}

.filter-block {
    border-bottom: 1px solid #e9ecef;
}

.filter-block:last-child {
    border-bottom: none;
}

.filter-block__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    padding: 0.5rem 0;
}

.filter-block__title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
}

.filter-block__content {
    margin-top: 0.5rem;
}

.filter-item {
    margin-bottom: 0.75rem;
}

.filter-item__children {
    margin-top: 0.5rem;
}

.filter-checkbox {
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 0;
    user-select: none;
}

.filter-checkbox input[type="checkbox"] {
    margin-right: 0.5rem;
    cursor: pointer;
}

.filter-checkbox__label {
    flex: 1;
    color: #333;
}

.filter-checkbox__count {
    color: #6c757d;
    font-size: 0.9rem;
}

.price-filter {
    padding: 0.5rem 0;
}

.price-filter__slider-container {
    position: relative;
    height: 40px;
}

.price-filter__track {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    transform: translateY(-50%);
    z-index: 1;
}

.price-filter__track-fill {
    position: absolute;
    top: 0;
    height: 100%;
    background: #007bff;
    border-radius: 3px;
    z-index: 2;
}

.price-filter__slider {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    width: 100%;
    height: 0;
    margin: 0;
    padding: 0;
    outline: none;
    -webkit-appearance: none;
    appearance: none;
    background: transparent;
    pointer-events: none;
    z-index: 3;
    transform: translateY(-50%);
}

.price-filter__slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    background: #007bff;
    border: 2px solid #fff;
    border-radius: 50%;
    cursor: pointer;
    pointer-events: all;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    transition: transform 0.1s ease;
}

.price-filter__slider::-webkit-slider-thumb:hover {
    transform: scale(1.1);
}

.price-filter__slider::-moz-range-thumb {
    width: 18px;
    height: 18px;
    background: #007bff;
    border: 2px solid #fff;
    border-radius: 50%;
    cursor: pointer;
    pointer-events: all;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    transition: transform 0.1s ease;
}

.price-filter__slider::-moz-range-thumb:hover {
    transform: scale(1.1);
}

.price-filter__slider--min {
    z-index: 4;
}

.price-filter__slider--max {
    z-index: 3;
}

.price-filter__display {
    text-align: center;
    font-weight: 600;
    color: #007bff;
    font-size: 1rem;
}

.filter-actions {
    margin-top: 1.5rem;
    display: flex;
    gap: 0.5rem;
}

.filter-actions .btn {
    flex: 1;
}

@media (max-width: 767px) {
    .filter-sidebar {
        position: relative;
        top: 0;
        margin-bottom: 1rem;
        max-height: none;
    }

    .filter-sidebar__content {
        max-height: 70vh;
    }
}
</style>
@endpush

@push('scripts')
<script>
function filterSidebar() {
    return {
        mobileOpen: false,
        isDesktop: window.innerWidth >= 768,
        categoriesOpen: true,
        priceOpen: true,
        brandsOpen: true,
        minPrice: {{ request('min_price', isset($priceRange) && $priceRange ? $priceRange->min_price : 0) }},
        maxPrice: {{ request('max_price', isset($priceRange) && $priceRange ? $priceRange->max_price : 100000) }},

        init() {
            // Set initial price values
            this.minPrice = {{ request('min_price', isset($priceRange) && $priceRange ? $priceRange->min_price : 0) }};
            this.maxPrice = {{ request('max_price', isset($priceRange) && $priceRange ? $priceRange->max_price : 100000) }};

            // Handle window resize
            window.addEventListener('resize', () => {
                this.checkDesktop();
            });
        },

        checkDesktop() {
            this.isDesktop = window.innerWidth >= 768;
            if (this.isDesktop) {
                this.mobileOpen = true;
            }
        },

        updateFilter() {
            // Auto-submit on filter change (optional - remove if you want manual filter button)
            // this.$el.closest('form').submit();
        },

        updatePriceDisplay() {
            // Values are already constrained in the @input handlers
            // This method is kept for potential future use
        },

        formatPrice(price) {
            return '৳' + parseInt(price).toLocaleString('en-US');
        }
    }
}
</script>
@endpush
@endsection
