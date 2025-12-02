@extends('layouts.yellow.master')
@php $services = setting('services') @endphp

@push('head')
    {!! seo()->for($product) !!}
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('strokya/vendor/xzoom/xzoom.css') }}">
    <link rel="stylesheet" href="{{ asset('strokya/vendor/xZoom-master/example/css/demo.css') }}">
    <style>
        #accordion .card-link {
            display: block;
            font-size: 20px;
            padding: 18px 48px;
            border-bottom: 2px solid transparent;
            color: inherit;
            font-weight: 500;
            border-radius: 3px 3px 0 0;
            transition: all .15s;
        }

        #accordion .card-link:not(.collapsed) {
            border-bottom: 2px solid #000;
            color: #000;
        }

        iframe {
            width: 100%;
        }

        @media (max-width: 768px) {
            .product__option-label {
                display: block;
            }

            .product__actions {
                justify-content: center;
            }

            .product__actions-item {
                width: 100%;
            }
        }

        .product__content {
            @if ($services->enabled ?? false)
                grid-template-columns: [gallery] calc(40% - 30px) [info] calc(40% - 35px) [sidebar] calc(25% - 10px);
            @else
                grid-template-columns: [gallery] calc(50% - 30px) [info] calc(50% - 35px);
            @endif
            grid-column-gap: 10px;
        }

        img {
            max-width: 100%;
            /*height: auto;*/
        }

        .original {
            position: relative;
        }

        .zoom-nav {
            position: absolute;
            top: 0;
            height: 100%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .zoom-control {
            height: 40px;
            outline: none;
            border: 2px solid black;
            cursor: pointer;
            opacity: 0.8;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            width: 40px;
            border-radius: 5px;
            color: #ca3d1c;
            background: transparent;
        }

        .zoom-control:hover {
            opacity: 1;
        }

        .zoom-control:focus {
            outline: none;
        }
    </style>
@endpush

@section('title', $product->name)

@section('content')
    <div class="d-none d-md-block">
        @include('partials.page-header', [
            'paths' => [
                url('/') => 'Home',
                route('products.index') => 'Products',
            ],
            'active' => $product->name,
        ])
    </div>
    <div class="block mt-3 mt-md-0">
        <div class="container">
            <div class="product product--layout--standard" data-layout="standard">
                <div class="product__content">
                    <div class="xzoom-container d-flex flex-column">
                        <div class="original">
                            <img class="xzoom" id="xzoom-default" src="{{ asset($product->base_image->src) }}"
                                xoriginal="{{ asset($product->base_image->src) }}" />
                            <div class="zoom-nav">
                                <button class="zoom-control left">
                                    <i class="fa fa-chevron-left"></i>
                                </button>
                                <button class="zoom-control right">
                                    <i class="fa fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mt-2 xzoom-thumbs d-flex">
                            <a href="{{ asset($product->base_image->src) }}"><img
                                    data-detail="{{ route('products.show', $product) }}"
                                    class="xzoom-gallery product-base__image" width="80"
                                    src="{{ asset($product->base_image->src) }}"
                                    xpreview="{{ asset($product->base_image->src) }}"></a>
                            @php
                                // Collect all variant base images
                                $variantImages = $product->variations->pluck('base_image')->filter();

                                // Merge variant images with additional images and get unique ones
                                $allImages = $product->additional_images->merge($variantImages)->unique('id');
                            @endphp
                            @foreach ($allImages as $image)
                                @php
                                    // Find all variants that have this image (same image can belong to multiple variants)
                                    $variantIds = $product->variations
                                        ->filter(fn($v) => $v->base_image && $v->base_image->id === $image->id)
                                        ->pluck('id')
                                        ->toArray();
                                    $hasVariants = !empty($variantIds);
                                @endphp
                                <a href="{{ asset($image->src) }}"
                                    @if ($hasVariants) class="variant-image-link" data-variant-ids="{{ json_encode($variantIds) }}" @endif>
                                    <img class="xzoom-gallery @if ($hasVariants) variant-image @endif"
                                        width="80" src="{{ asset($image->src) }}"
                                        @if ($hasVariants) data-variant-ids="{{ json_encode($variantIds) }}" @endif>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <!-- .product__info -->
                    <livewire:product-detail :product="$product" :show-brand-category="!($services->enabled ?? false)" />
                    <!-- .product__info / end -->
                    @if ($services->enabled ?? false)
                        <div>
                            @if ($product->variations->isNotEmpty())
                                <div class="p-3 mt-2 mb-2 border product__footer">
                                    <div class="product__tags tags">
                                        @if ($product->brand)
                                            <p class="mb-0 text-secondary">
                                                Brand: <a href="{{ route('brands.products', $product->brand) }}"
                                                    class="text-primary badge badge-light"><big>{{ $product->brand->name }}</big></a>
                                            </p>
                                        @endif
                                        <div class="mt-2">
                                            <p class="mr-2 mb-0 text-secondary d-inline-block">Categories:</p>
                                            @foreach ($product->categories as $category)
                                                <a href="{{ route('categories.products', $category) }}"
                                                    class="badge badge-primary">{{ $category->name }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="block-features__list flex-column d-none d-md-block">
                                @foreach (config('services.services', []) as $num => $icon)
                                    <div class="block-features__item">
                                        <div class="block-features__icon">
                                            <svg width="48px" height="48px">
                                                <use xlink:href="{{ asset($icon) }}"></use>
                                            </svg>
                                        </div>
                                        <div class="block-features__content">
                                            <div class="block-features__title">{{ $services->$num->title }}</div>
                                            <div class="block-features__subtitle">{{ $services->$num->detail }}</div>
                                        </div>
                                    </div>
                                    @if (!$loop->last)
                                        <div class="block-features__divider"></div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div id="accordion" class="mt-3">
                <div class="card">
                    <div class="p-0 card-header">
                        <a class="px-4 card-link" datatoggle="collapse" href="javascript:void(false)">
                            Product Description
                        </a>
                    </div>
                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                        <div class="p-2 card-body">
                            @if ($product->desc_img && $product->desc_img_pos == 'before_content')
                                <div class="text-center">
                                    @foreach ($product->images as $image)
                                        <img src="{{ asset($image->src) }}" alt="{{ $product->name }}"
                                            class="my-2 border img-fluid">
                                    @endforeach
                                </div>
                            @endif

                            {!! $product->description !!}

                            @if ($product->desc_img && $product->desc_img_pos == 'after_content')
                                <div class="text-center">
                                    @foreach ($product->images as $image)
                                        <img src="{{ asset($image->src) }}" alt="{{ $product->name }}"
                                            class="my-2 border img-fluid">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-3 card">
                    <div class="p-0 card-header">
                        <a class="px-4 card-link" datatoggle="collapse" href="javascript:void(false)">
                            Delivery and Return Policy
                        </a>
                    </div>
                    <div id="collapseTwo" class="collapse show" data-parent="#accordion">
                        <div class="p-2 card-body">
                            {!! setting('show_option')->productwise_delivery_charge ?? false
                                ? $product->delivery_text ?? setting('delivery_text')
                                : setting('delivery_text') !!}
                        </div>
                    </div>
                </div>
                <div class="mt-3 card">
                    <div class="p-0 card-header">
                        <a class="px-4 card-link" data-toggle="collapse" href="javascript:void(false)" aria-expanded="false">
                            Customer Reviews
                            @php
                                $totalReviews = $product->totalReviews();
                            @endphp
                            @if ($totalReviews > 0)
                                <span class="ml-2 badge badge-primary">{{ $totalReviews }}</span>
                            @endif
                        </a>
                    </div>
                    <div id="collapseThree" class="collapse show" data-parent="#accordion">
                        <div class="p-3 card-body">
                            @include('products.reviews-section', ['product' => $product])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- .block-products-carousel -->
    @php($relatedProductsSetting = setting('related_products'))
    <div class="lazy-related-products" x-data="lazyRelatedProducts({{ $product->getKey() }}, {{ $relatedProductsSetting->cols ?? 5 }})" x-init="init()"
        data-show-option="{{ json_encode([
            'product_grid_button' => setting('show_option')->product_grid_button ?? 'add_to_cart',
            'add_to_cart_icon' => setting('show_option')->add_to_cart_icon ?? '',
            'add_to_cart_text' => setting('show_option')->add_to_cart_text ?? 'Add to Cart',
            'order_now_icon' => setting('show_option')->order_now_icon ?? '',
            'order_now_text' => setting('show_option')->order_now_text ?? 'Order Now',
            'discount_text' => setting('discount_text') ?? '<small>Discount:</small> [percent]%',
        ]) }}"
        data-is-oninda="{{ isOninda() ? 'true' : 'false' }}"
        data-guest-can-see-price="{{ (bool) (setting('show_option')->guest_can_see_price ?? false) ? 'true' : 'false' }}"
        data-user-guest="{{ auth('user')->guest() ? 'true' : 'false' }}"
        data-user-verified="{{ auth('user')->check() && auth('user')->user()->is_verified ? 'true' : 'false' }}">
        <div class="block block-products-carousel">
            <div class="container">
                <div class="block-header">
                    <h3 class="block-header__title" style="padding: 0.375rem 1rem;">
                        Related Products
                    </h3>
                    <div class="block-header__divider"></div>
                </div>
                <div class="products-view__list products-list"
                    data-layout="grid-{{ $relatedProductsSetting->cols ?? 5 }}-full" data-with-features="false">
                    <div class="products-list__body" id="related-products-container">
                        <div x-show="loading" class="py-5 text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- .block-products-carousel / end -->
@endsection
