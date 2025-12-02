@extends('layouts.yellow.master')

@push('head')
    @if (isset($category))
        {!! seo()->for($category) !!}
    @elseif(isset($brand))
        {!! seo()->for($brand) !!}
    @elseif(isset($section))
        {!! seo()->for($section) !!}
    @endif
@endpush

@section('title', 'Products')

@section('content')

    @include('partials.page-header', [
        'paths' => [
            url('/') => 'Home',
        ],
        'active' => 'Products',
        'page_title' => 'Products',
    ])

    <div class="block">
        <div class="products-view">
            <div class="container">
                <div class="row">
                    <!-- Filter Sidebar - Lazy Loaded -->
                    @php
                        $categoryModel = request()->route()->parameter('category');
                        $brandModel = request()->route()->parameter('brand');
                        $categoryFilters = request('filter_category');
                        if (is_string($categoryFilters)) {
                            $categoryFilters = array_map('intval', array_filter(explode(',', $categoryFilters)));
                        } elseif (is_array($categoryFilters)) {
                            $categoryFilters = array_map('intval', array_filter($categoryFilters));
                        } else {
                            $categoryFilters = [];
                        }
                        $optionFilters = request('filter_option');
                        if (is_string($optionFilters)) {
                            $optionFilters = array_map('intval', array_filter(explode(',', $optionFilters)));
                        } elseif (is_array($optionFilters)) {
                            $optionFilters = array_map('intval', array_filter($optionFilters));
                        } else {
                            $optionFilters = [];
                        }
                    @endphp
                    <div x-data="{
                        loaded: window.__filterSidebarLoaded ?? false,
                        init() {
                            if (this.loaded) {
                                return;
                            }
                    
                            const markLoaded = () => {
                                if (this.loaded) {
                                    return;
                                }
                    
                                this.loaded = true;
                                window.__filterSidebarLoaded = true;
                            };
                    
                            if (window.Livewire?.on) {
                                window.Livewire.on('filter-sidebar-loaded', markLoaded);
                            } else {
                                document.addEventListener('livewire:init', () => {
                                    window.Livewire.on('filter-sidebar-loaded', markLoaded);
                                }, { once: true });
                            }
                        },
                    }" class="pr-md-1 col-lg-3 col-md-4 w-100 position-relative">
                        <div x-show="!loaded" class="p-3 mb-4 bg-white rounded border filter-sidebar placeholder-glow"
                            style="min-height: 420px;">
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div class="placeholder w-50" style="height: 24px;"></div>
                                <div class="rounded-circle placeholder" style="width: 24px; height: 24px;"></div>
                            </div>
                            <div class="mb-2 placeholder w-75" style="height: 18px;"></div>
                            <div class="mb-2 placeholder w-100" style="height: 18px;"></div>
                            <div class="mb-2 w-60 placeholder" style="height: 18px;"></div>
                            <div class="mb-2 w-80 placeholder" style="height: 18px;"></div>
                            <div class="mb-2 placeholder w-50" style="height: 18px;"></div>
                        </div>

                        <div :class="{ 'invisible': !loaded }">
                            <livewire:filter-sidebar :category-id="$categoryModel?->id" :category-slug="$categoryModel?->slug" :brand-id="$brandModel?->id" :brand-slug="$brandModel?->slug"
                                :search="request('search')" :hide-category-filter="$hideCategoryFilter ?? false" :selected-categories="$categoryFilters" :selected-options="$optionFilters" lazy
                                wire:key="filter-sidebar-{{ $category?->id ?? 'all' }}-{{ $brand?->id ?? 'all' }}-{{ request('search') ?? 'all' }}" />
                        </div>
                    </div>

                    <!-- Products Content -->
                    <div class="pl-md-1 col-lg-9 col-md-8">
                        <div class="products-view__options">
                            <div class="view-options">
                                <div class="view-options__legend"
                                    @if (config('app.infinite_scroll_section', false)) x-data="productCountDisplay({{ $products->total() }}, {{ $products->count() }})"
                                 x-text="getDisplayText()"
                                 id="product-count-display"
                                 @else
                                 @if (request('search'))
                                 Found {{ $products->total() }} result(s) for "{{ request('search', 'NULL') }}"
                                 @elseif($category = request()->route()->parameter('category'))
                                 Showing from "{{ $category->name }}" category.
                                 @elseif($brand = request()->route()->parameter('brand'))
                                 Showing from "{{ $brand->name }}" brand.
                                 @else
                                 Showing {{ $products->count() }} of {{ $products->total() }} products @endif
                                    @endif
                                </div>
                                <div class="view-options__divider"></div>
                            </div>
                        </div>

                        @if (config('app.infinite_scroll_section', false))
                            <div class="products-view__list products-list" data-layout="grid-4-full"
                                data-with-features="false" data-shuffle="{{ request('shuffle') }}" x-data="shopInfiniteScroll({{ $products->currentPage() }}, @json($products->hasMorePages()), {{ $per_page ?? 20 }}, {{ $products->total() }})"
                                x-init="init()">
                                <div class="products-list__body" id="products-container-shop"
                                    data-show-option="{{ json_encode([
                                        'product_grid_button' => setting('show_option')->product_grid_button ?? 'add_to_cart',
                                        'add_to_cart_icon' => setting('show_option')->add_to_cart_icon ?? '',
                                        'add_to_cart_text' => setting('show_option')->add_to_cart_text ?? 'Add to Cart',
                                        'order_now_icon' => setting('show_option')->order_now_icon ?? '',
                                        'order_now_text' => setting('show_option')->order_now_text ?? 'Order Now',
                                        'discount_text' => setting('discount_text') ?? '<small>Discount:</small> [percent]%',
                                    ]) }}"
                                    data-initial-products='@json($products->pluck('id'))'
                                    data-is-oninda="{{ isOninda() ? 'true' : 'false' }}"
                                    data-guest-can-see-price="{{ (bool) (setting('show_option')->guest_can_see_price ?? false) ? 'true' : 'false' }}"
                                    data-user-guest="{{ auth('user')->guest() ? 'true' : 'false' }}"
                                    data-user-verified="{{ auth('user')->check() && auth('user')->user()->is_verified ? 'true' : 'false' }}">
                                    @foreach ($products as $product)
                                        <div class="products-list__item">
                                            <livewire:product-card :product="$product" :key="$product->id" />
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Loading trigger -->
                                <div class="load-more-trigger" x-show="hasMore" x-ref="loadMoreTrigger"
                                    style="height: 20px; margin: 20px 0;">
                                    <div x-show="loading" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            @include('partials.products.pure-grid', [
                                'title' => null,
                                'cols' => 4,
                            ])

                            <div class="pt-0 products-view__pagination">
                                {!! $products->appends(request()->query())->links() !!}
                            </div>
                        @endif
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


@endsection
