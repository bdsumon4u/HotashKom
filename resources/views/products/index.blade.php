@extends('layouts.yellow.master')
@if (isset($category) && $category instanceof \Illuminate\Database\Eloquent\Model)
    @section('seo_tags')
        {!! seo()->for($category) !!}
    @endsection
@elseif(isset($brand) && $brand instanceof \Illuminate\Database\Eloquent\Model)
    @section('seo_tags')
        {!! seo()->for($brand) !!}
    @endsection
@elseif(isset($section) && $section instanceof \Illuminate\Database\Eloquent\Model)
    @php
        $brandName = $company->name ?? config('app.name');
        $sectionName = trim((string) $section->title);

        $sectionSeo = match ($sectionName) {
            'All Products' => [
                'title' => 'All Products | ' . $brandName,
                'description' => 'Browse all available products at ' . $brandName . ', including home, kitchen, gadgets, beauty, kids and everyday essentials with cash on delivery across Bangladesh.',
            ],
            'Latest Products' => [
                'title' => 'Latest Products | ' . $brandName,
                'description' => 'Discover the latest products added to ' . $brandName . ', with useful everyday items, gadgets, home essentials and cash on delivery across Bangladesh.',
            ],
            default => [
                'title' => $sectionName . ' | ' . $brandName,
                'description' => 'Browse ' . $sectionName . ' at ' . $brandName . ' with easy ordering and nationwide cash on delivery in Bangladesh.',
            ],
        };
    @endphp

    @section('seo_tags')
        <title>{{ $sectionSeo['title'] }}</title>
        <meta name="description" content="{{ $sectionSeo['description'] }}">
        <meta name="robots" content="noindex, follow">
    @endsection
@elseif (request()->is('shop'))
    @php
        $brandName = data_get(setting('company'), 'name') ?: config('app.name');
    @endphp
    @section('seo_tags')
        <title>{{ $brandName }} - Shop Online | প্রয়োজনীয় পণ্য সাশ্রয়ী দামে</title>
        <meta name="description" content="{{ $brandName }} Shop থেকে কিচেন, হোম, ইলেকট্রনিকস, বিউটি, কিডস ও লাইফস্টাইলের প্রয়োজনীয় পণ্য সাশ্রয়ী দামে কিনুন, সহজ অর্ডার, Cash on Delivery ও দেশব্যাপী ডেলিভারিসহ।">
    @endsection
@endif

@if (request()->is('shop'))
    @section('title', 'Shop Online | প্রয়োজনীয় পণ্য সাশ্রয়ী দামে')
@else
    @section('title', 'Products')
@endif


{{-- Category product price emphasis --}}
@if (!request()->is('shop'))
    @push('styles')
        <style>
            .products-view .product-card__new-price {
                font-weight: 800 !important;
            }

            .products-view .product-card__old-price {
                font-weight: 400 !important;
            }
        </style>
    @endpush
@endif
@push('styles')
<style>
/* Premium home section products */
.nm-home-section-products-page {
    padding: 22px 0 40px;
    background:
        radial-gradient(circle at top right, rgba(29,191,115,.10), transparent 32%),
        linear-gradient(180deg, #fbfffc 0%, #ffffff 65%);
}

.nm-home-section-products-page .products-view__options {
    margin-bottom: 18px;
    padding: 14px 16px;
    border: 1px solid #dcebe3;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 6px 16px rgba(22,58,39,.06);
}

.nm-home-section-products-page .view-options__legend {
    color: #2b4a38;
    font-size: 14px;
    font-weight: 700;
}

.nm-home-section-products-page .filter-sidebar,
.nm-home-section-products-page .filter-sidebar.placeholder-glow {
    border: 1px solid #dcebe3 !important;
    border-radius: 13px !important;
    background: #ffffff !important;
    box-shadow: 0 7px 18px rgba(22,58,39,.07);
}

.nm-home-section-products-page .filter-sidebar__title {
    color: #173c2a;
    font-weight: 800;
}

.nm-home-section-products-page .filter-block {
    border-bottom-color: #e8f1eb;
}

.nm-home-section-products-page .products-list__body {
    row-gap: 18px;
}

.nm-home-section-products-page .product-card {
    overflow: hidden;
    border: 1px solid #d9e9df;
    border-radius: 13px;
    background: #ffffff;
    box-shadow: 0 5px 15px rgba(22,58,39,.08);
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
}

.nm-home-section-products-page .product-card:hover {
    transform: translateY(-4px);
    border-color: #8bdab0;
    box-shadow: 0 13px 25px rgba(22,58,39,.15);
}

.nm-home-section-products-page .product-card__image {
    background: linear-gradient(145deg, #f8fdf9, #edf8f1);
}

.nm-home-section-products-page .product-card__image img {
    transition: transform .28s ease;
}

.nm-home-section-products-page .product-card:hover .product-card__image img {
    transform: scale(1.035);
}

.nm-home-section-products-page .product-card__info {
    padding-top: 13px;
    padding-bottom: 6px;
}

.nm-home-section-products-page .product-card__name a {
    display: -webkit-box;
    overflow: hidden;
    min-height: 42px;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    color: #263d30;
    font-weight: 700;
    line-height: 1.35;
}

.nm-home-section-products-page .product-card__prices,
.nm-home-section-products-page .product-card__new-price {
    color: #078d4d;
    font-size: 16px;
    font-weight: 800 !important;
}

.nm-home-section-products-page .product-card__old-price {
    margin-left: 7px;
    color: #e94a4a;
    font-size: 13px;
    font-weight: 600 !important;
}

.nm-home-section-products-page .product-card__buttons {
    margin-top: 11px;
}

.nm-home-section-products-page .product-card__addtocart,
.nm-home-section-products-page .product-card__ordernow {
    width: 100%;
    min-height: 44px;
    border: 0;
    border-radius: 8px;
    box-shadow: 0 7px 14px rgba(22,185,108,.18);
    font-weight: 800;
}

.nm-home-section-products-page .category-brand-content-section {
    border: 1px solid #dcebe3 !important;
    border-radius: 13px;
    box-shadow: 0 7px 18px rgba(22,58,39,.07) !important;
}

@media (max-width: 767.98px) {
    .nm-home-section-products-page {
        padding: 12px 0 26px;
    }

    .nm-home-section-products-page .products-view__options {
        margin-bottom: 12px;
        padding: 11px 12px;
        border-radius: 9px;
    }

    .nm-home-section-products-page .filter-sidebar {
        margin-bottom: 12px !important;
        border-radius: 10px !important;
    }

    .nm-home-section-products-page .products-list__body {
        row-gap: 12px;
    }

    .nm-home-section-products-page .product-card {
        border-radius: 10px;
    }

    .nm-home-section-products-page .product-card__info {
        padding-top: 10px;
        padding-bottom: 4px;
    }

    .nm-home-section-products-page .product-card__name a {
        min-height: 37px;
        font-size: 13px;
    }

    .nm-home-section-products-page .product-card__prices,
    .nm-home-section-products-page .product-card__new-price {
        font-size: 14px;
    }

    .nm-home-section-products-page .product-card__addtocart,
    .nm-home-section-products-page .product-card__ordernow {
        min-height: 39px;
        border-radius: 7px;
        font-size: 13px;
    }
}
</style>
@endpush
@section('content')
    {{-- Category Page Context START --}}
    @php
        $currentCategory = isset($category) && is_object($category)
            ? $category
            : request()->route('category');

        $categoryPagePaths = [url('/') => 'Home'];
        $categoryPageActive = 'Products';
        $categoryPageTitle = 'Products';
        $categoryContextName = null;
        $categoryContentHeadingUsed = false;

        if (isset($section) && $section instanceof \Illuminate\Database\Eloquent\Model && trim((string) $section->title) !== '') {
            $categoryPageActive = trim((string) $section->title);
            $categoryPageTitle = trim((string) $section->title);
        }

        if ($currentCategory) {
            if (is_object($currentCategory)) {
                $categoryContextName = data_get($currentCategory, 'name');
                $categoryContent = data_get($currentCategory, 'content');
            } else {
                $categoryContextName = ucwords(
                    str_replace(['-', '_'], ' ', (string) $currentCategory)
                );

                $categoryContent = isset($category) && is_object($category)
                    ? data_get($category, 'content')
                    : null;
            }

            if ($categoryContextName) {
                $categoryPagePaths[route('categories')] = 'Categories';
                $categoryPageActive = $categoryContextName;
                $categoryPageTitle = $categoryContextName . ' Products';

                if (
                    !empty($categoryContent) &&
                    preg_match(
                        '/<h1\b[^>]*>(.*?)<\/h1>/is',
                        $categoryContent,
                        $categoryHeadingMatch
                    )
                ) {
                    $categoryHeadingText = trim(
                        strip_tags($categoryHeadingMatch[1])
                    );

                    if ($categoryHeadingText !== '') {
                        $categoryPageTitle = html_entity_decode(
                            $categoryHeadingText,
                            ENT_QUOTES | ENT_HTML5,
                            'UTF-8'
                        );

                        $categoryContentHeadingUsed = true;
                    }
                }
            }
        }
        /*
        |--------------------------------------------------------------------------
        | NM PREMIUM CATEGORY CONDITION FINAL
        |--------------------------------------------------------------------------
        | Resolve this only from the actual Category model passed by
        | CategoryProductController. This avoids route/context ambiguity.
        */
        $isPremiumCategoryPage =
            isset($category)
            && $category instanceof \App\Models\Category
            && in_array(
                $category->slug,
                [
                    'gadget-and-electronics',
                    'kids-zone',
                    'home-and-lifestyle',
                    'health-and-beauty',
                    'camera',
                    'fashion',
                    'watches-and-clock',
                    'shaver-and-trimmer',
                    'content-tools',
                    'foods',
                    'kitchen-accessories',
                    'home-appliance',
                    'tools-and-hardware',
                    'cleaning-and-maintenance',
                    'garden-accessories',
                    'lighting-and-electrical',
                    'car-and-bike-accessories',
                    'fishing-accessories',
                    'pest-control',
                    'islamic-corner',
                    'rain-item',
                    'fan-item',
                    'computer-item',
                    'mobile-accessories',
                    'torch-light',
                    'speaker',
                    'projector-and-display',
                    'solar-lamp',
                    'security-and-tracking',
                    'kids-toy',
                    'baby-care',
                    'sports-and-gym',
                    'personal-care-and-health-devices',
                    'massager',
                    'tripod-and-stand',
                    'microphone',
                    'video-kit',
                    'smart-watch',
                    'clocks',
                    'watches',
                    'mens-fashion',
                    'womens-fashion',
                    'sunglasses',
                    'mens-shaver-and-trimmer',
                    'womens-shaver-and-trimmer',
                    'ip-camera',
                    'wireless-camera',
                    'spy-camera',
                    'manicure-and-pedicure-set',
                    'hot-water-bag',
                ],
                true
            );

    @endphp
    {{-- Category Page Context END --}}

    @if (request()->is('shop'))
        @include('partials.shop-premium-header')
    @elseif ($isPremiumCategoryPage)
        @include('partials.category-premium-header', [
            'category' => $category,
            'products' => $products,
            'categoryPageTitle' => $categoryPageTitle,
            'categoryPageIntro' => $categoryPageIntro ?? null,
        ])
    @else
        @include('partials.page-header', [
            'paths' => $categoryPagePaths,
            'active' => $categoryPageActive,
            'page_title' => $categoryPageTitle,
        ])
    @endif

    @if ($categoryContextName && $isPremiumCategoryPage === false)
        <div class="container category-context-indicator-container"
             style="margin-top:-4px; margin-bottom:20px;">

            <div class="category-context-indicator"
                 aria-label="Current category"
                 style="
                    display:inline-flex;
                    align-items:center;
                    flex-wrap:wrap;
                    gap:8px;
                    padding:10px 16px;
                    color:#3d464d;
                    font-size:15px;
                    background:var(--brand-soft);
                    border:1px solid var(--brand-border);
                    border-left:4px solid var(--brand);
                    border-radius:6px;
                 ">

                <i class="fa fa-folder-open"
                   aria-hidden="true"
                   style="color:var(--brand-dark);"></i>

                <span>বর্তমান বিভাগ:</span>

                <strong style="color:var(--brand-dark);">
                    {{ $categoryContextName }}
                </strong>
            </div>
        </div>
    @endif

    <div @if($isPremiumCategoryPage) id="nm-category-products" @endif class="block {{ request()->is('shop') ? 'nm-shop-products-block' : '' }} {{ isset($section) ? 'nm-home-section-products-page' : '' }}" style="@if($isPremiumCategoryPage) scroll-margin-top: 115px; @endif">
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
                        <div x-show="!loaded" class="p-3 mb-4 bg-white rounded border filter-sidebar placeholder-glow">
                        
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

                        @php
                            $descriptionContent = null;
                            if (isset($category) && $category instanceof \Illuminate\Database\Eloquent\Model && !empty($category->content)) {
                                $descriptionContent = $category->content;
                            } elseif (isset($brand) && $brand instanceof \Illuminate\Database\Eloquent\Model && !empty($brand->content)) {
                                $descriptionContent = $brand->content;
                            } elseif (isset($section) && $section instanceof \Illuminate\Database\Eloquent\Model && !empty($section->content)) {
                                $descriptionContent = $section->content;
                            }
                        @endphp


                        {{-- Category H1 Normalisation START --}}
                        @php
                            if ($descriptionContent && $categoryContextName) {
                                if ($categoryContentHeadingUsed) {
                                    $descriptionContent = preg_replace(
                                        '/<h1\b[^>]*>.*?<\/h1>/is',
                                        '',
                                        $descriptionContent,
                                        1
                                    );
                                }

                                $descriptionContent = preg_replace(
                                    '/<h1\b([^>]*)>/i',
                                    '<h2>',
                                    $descriptionContent
                                );

                                $descriptionContent = preg_replace(
                                    '/<\/h1>/i',
                                    '</h2>',
                                    $descriptionContent
                                );
                            }
                        @endphp
                        {{-- Category H1 Normalisation END --}}
                        @if ($descriptionContent)
                            <div
                                @if($isPremiumCategoryPage)
                                    id="nm-category-buying-guide"
                                @endif
                                class="card mt-4 category-brand-content-section border-0 shadow-sm {{ $isPremiumCategoryPage ? 'nm-category-seo-content' : '' }}"
                            >
                                <div class="card-body p-4 text-justify">
                                    {!! $descriptionContent !!}
                                </div>
                            </div>
                        @endif

                        @if ($isPremiumCategoryPage)
                            @include('partials.category-premium-guides')
                            @include('partials.category-faq-schema')
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
