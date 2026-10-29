@extends('layouts.yellow.master')
@php $services = setting('services') @endphp

@section('seo_tags')
    {!! seo()->for($product) !!}
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('strokya/vendor/xzoom/xzoom.css') }}">
    <link rel="stylesheet" href="{{ asset('strokya/vendor/xZoom-master/example/css/demo.css') }}">
    @if (isGbazTheme())
        <style>
            .review-rating-link {
                transition: opacity 0.2s ease;
            }
            .review-rating-link:hover {
                opacity: 0.7;
            }
            .review-rating-link:active {
                opacity: 0.5;
            }
            #accordion .card {
                border: 1px solid #f0f0f0;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
            }
            #accordion .card-header {
                background-color: #fafafa;
                border-bottom: 1px solid #f0f0f0;
            }
            #accordion .card-link {
                display: block;
                font-size: 18px;
                padding: 14px 24px;
                color: #111827;
                font-weight: 600;
                transition: all .15s;
                text-decoration: none;
            }

            iframe {
                width: 100%;
            }

            .gb-product-detail-card {
                background: #ffffff;
                border-radius: 10px;
                padding: 28px;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
                border: 1px solid #f0f0f0;
            }

            .product__content {
                display: grid;
                grid-template-columns: [gallery] 48% [info] 1fr;
                grid-column-gap: 36px;
                align-items: start;
            }

            .xzoom-container {
                display: flex !important;
                flex-direction: row !important;
                gap: 16px;
                align-items: flex-start;
                width: 100%;
            }

            .xzoom-thumbs {
                order: 1;
                display: flex !important;
                flex-direction: column !important;
                gap: 10px;
                margin: 0 !important;
                flex-shrink: 0;
            }

            .xzoom-thumbs a {
                display: block;
                margin: 0 !important;
                text-decoration: none;
            }

            .xzoom-gallery {
                width: 70px !important;
                height: 70px !important;
                object-fit: contain;
                border: 1px solid #e5e7eb !important;
                border-radius: 6px;
                padding: 4px;
                background: #ffffff;
                margin: 0 !important;
                box-shadow: none !important;
                transition: border-color 0.2s ease;
            }

            .xzoom-gallery.xactive,
            .xzoom-gallery:hover {
                border: 1.5px solid #f38020 !important;
                box-shadow: none !important;
            }

            .original {
                order: 2;
                flex: 1;
                min-width: 0;
                position: relative;
                border: 1px solid #f0f0f0;
                border-radius: 6px;
                background: #ffffff;
                padding: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .original img.xzoom {
                width: 100% !important;
                max-height: 440px;
                object-fit: contain;
                box-shadow: none !important;
            }

            .zoom-nav {
                position: absolute;
                top: 0;
                left: 0;
                height: 100%;
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
                pointer-events: none;
            }

            .zoom-control {
                pointer-events: auto;
                height: 34px;
                width: 34px;
                outline: none;
                border: none;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 8px;
                border-radius: 50%;
                color: #3b82f6;
                background: rgba(255, 255, 255, 0.85);
                font-size: 13px;
                transition: background 0.2s ease, color 0.2s ease;
            }

            .zoom-control:hover {
                background: #ffffff;
                color: #f38020;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            }

            .zoom-control:focus {
                outline: none;
            }

            /* GhorerBazar Product Detail Info Styles */
            .gb-detail-title {
                font-size: 26px;
                font-weight: 500;
                color: #111827;
                line-height: 1.3;
                margin-bottom: 14px;
            }

            .gb-detail-price-row {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 12px;
                margin-bottom: 20px;
            }

            .gb-detail-new-price {
                font-size: 26px;
                font-weight: 700;
                color: #f38020;
                line-height: 1.2;
            }

            .gb-detail-old-price {
                font-size: 22px;
                font-weight: 400;
                color: #9ca3af;
                text-decoration: line-through;
                line-height: 1.2;
            }

            .gb-detail-save-badge {
                display: inline-block;
                background-color: #26a65b;
                color: #ffffff;
                font-size: 12px;
                font-weight: 600;
                padding: 4px 10px;
                border-radius: 4px;
                line-height: 1.2;
            }

            .gb-quantity-row {
                display: flex;
                align-items: center;
                gap: 24px;
                margin: 20px 0 22px 0;
            }

            .gb-quantity-label {
                font-size: 15px;
                font-weight: 500;
                color: #1f2937;
                margin: 0;
            }

            .gb-quantity-box {
                display: inline-flex;
                align-items: center;
                border: 1px solid #e5e7eb;
                border-radius: 4px;
                padding: 4px 10px;
                height: 46px;
                background: #ffffff;
            }

            .gb-qty-btn {
                width: 26px;
                height: 26px;
                border-radius: 50%;
                background: #f3f4f6;
                border: none;
                color: #4b5563;
                font-size: 11px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: background 0.15s ease, color 0.15s ease;
                outline: none !important;
            }

            .gb-qty-btn:hover {
                background: #f38020;
                color: #ffffff;
            }

            .gb-qty-input {
                width: 64px;
                text-align: center;
                border: none !important;
                background: transparent !important;
                font-size: 16px;
                font-weight: 600;
                color: #111827;
                outline: none !important;
                box-shadow: none !important;
                -moz-appearance: textfield;
            }

            .gb-qty-input::-webkit-outer-spin-button,
            .gb-qty-input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            .gb-action-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 14px;
                margin-bottom: 12px;
            }

            .gb-btn {
                height: 48px;
                border-radius: 6px !important;
                border: none !important;
                font-size: 14px;
                font-weight: 600;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                gap: 8px;
                width: 100%;
                cursor: pointer;
                text-decoration: none !important;
                transition: opacity 0.2s ease, transform 0.15s ease;
                padding: 0 16px;
                box-shadow: none !important;
                animation: none !important;
            }

            .gb-btn:hover {
                opacity: 0.92;
                color: #ffffff !important;
            }

            .gb-btn--addtocart {
                background-color: #f38020 !important;
                color: #ffffff !important;
                text-transform: uppercase;
                letter-spacing: 0.3px;
            }

            .gb-btn--buynow {
                background-color: #0b2219 !important;
                color: #ffffff !important;
                text-transform: uppercase;
                letter-spacing: 0.3px;
            }

            .gb-btn--whatsapp {
                background-color: #1e874b !important;
                color: #ffffff !important;
                font-size: 15px;
                font-weight: 500;
            }

            .gb-btn--call {
                background-color: #1e3a8a !important;
                color: #ffffff !important;
                font-size: 15px;
                font-weight: 500;
            }

            .gb-brand-box {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                border: 1px solid #e5e7eb;
                border-radius: 6px;
                padding: 8px 18px;
                background: #ffffff;
                margin-top: 8px;
                margin-bottom: 12px;
            }

            .gb-brand-label {
                font-size: 14px;
                color: #374151;
                font-weight: 500;
            }

            .gb-brand-link {
                display: inline-flex;
                align-items: center;
                text-decoration: none !important;
                color: #111827;
            }

            .gb-brand-logo {
                max-height: 32px;
                width: auto;
                object-fit: contain;
            }

            .gb-brand-name {
                font-size: 15px;
                font-weight: 700;
                color: #111827;
            }

            @media (max-width: 991px) {
                .product__content {
                    grid-template-columns: 1fr;
                    grid-row-gap: 24px;
                }
            }

            @media (max-width: 768px) {
                .gb-product-detail-card {
                    padding: 16px;
                }

                .xzoom-container {
                    flex-direction: column !important;
                }

                .original {
                    order: 1;
                    width: 100%;
                }

                .xzoom-thumbs {
                    order: 2;
                    flex-direction: row !important;
                    overflow-x: auto;
                    width: 100%;
                    padding-bottom: 4px;
                }

                .gb-detail-title {
                    font-size: 21px;
                }

                .gb-detail-new-price {
                    font-size: 22px;
                }

                .gb-detail-old-price {
                    font-size: 18px;
                }

                .gb-action-grid {
                    grid-template-columns: 1fr;
                    gap: 10px;
                }
            }
        </style>
    @else
        <style>
            .review-rating-link {
                transition: opacity 0.2s ease;
            }
            .review-rating-link:hover {
                opacity: 0.7;
            }
            .review-rating-link:active {
                opacity: 0.5;
            }
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

            @media (max-width: 768px) {
                .zoom-control {
                    height: 48px;
                    width: 48px;
                    margin: 0 5px;
                    opacity: 0.9;
                    background: rgba(255, 255, 255, 0.8);
                    z-index: 10;
                    position: relative;
                    -webkit-tap-highlight-color: transparent;
                    touch-action: manipulation;
                }

                .zoom-control:active {
                    opacity: 1;
                    background: rgba(255, 255, 255, 0.95);
                }
            }
        </style>
    @endif
@endpush

@section('title', $product->name)

@section('content')
    @if (isGbazTheme())
        <div class="d-none d-md-block">
            @include('partials.page-header', [
                'paths' => [
                    url('/') => 'Home',
                ],
                'active' => 'Products',
            ])
        </div>
        <div class="block mt-3 mt-md-0">
            <div class="container">
                <div class="product product--layout--standard gb-product-detail-card" data-layout="standard">
                    <div class="product__content">
                        <div class="xzoom-container">
                            <div class="original">
                                <img class="xzoom" id="xzoom-default" src="{{ asset($product->base_image?->src ?? 'images/placeholder.jpg') }}"
                                    xoriginal="{{ asset($product->base_image?->src ?? 'images/placeholder.jpg') }}" />
                                <div class="zoom-nav">
                                    <button class="zoom-control left" type="button" aria-label="Previous image">
                                        <i class="fa fa-chevron-left"></i>
                                    </button>
                                    <button class="zoom-control right" type="button" aria-label="Next image">
                                        <i class="fa fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="xzoom-thumbs">
                                <a href="{{ asset($product->base_image?->src ?? 'images/placeholder.jpg') }}"><img
                                        data-detail="{{ route('products.show', $product) }}"
                                        class="xzoom-gallery product-base__image" width="70"
                                        src="{{ asset($product->base_image?->src ?? 'images/placeholder.jpg') }}"
                                        xpreview="{{ asset($product->base_image?->src ?? 'images/placeholder.jpg') }}"></a>
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
                                            width="70" src="{{ asset($image->src) }}"
                                            @if ($hasVariants) data-variant-ids="{{ json_encode($variantIds) }}" @endif>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        <!-- .product__info -->
                        <livewire:product-detail :product="$product" :show-brand-category="true" />
                        <!-- .product__info / end -->
                    </div>
                </div>
    @else
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
                        <div class="xzoom-container d-flex @unless(config('app.vertical_image_gallery')) flex-column @endunless">
                            <div class="original">
                                <img class="xzoom" id="xzoom-default" src="{{ asset($product->base_image?->src ?? 'images/placeholder.jpg') }}"
                                    xoriginal="{{ asset($product->base_image?->src ?? 'images/placeholder.jpg') }}" />
                                <div class="zoom-nav">
                                    <button class="zoom-control left">
                                        <i class="fa fa-chevron-left"></i>
                                    </button>
                                    <button class="zoom-control right">
                                        <i class="fa fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2 xzoom-thumbs d-flex @if(config('app.vertical_image_gallery')) flex-column @endif">
                                <a href="{{ asset($product->base_image?->src ?? 'images/placeholder.jpg') }}"><img
                                        data-detail="{{ route('products.show', $product) }}"
                                        class="xzoom-gallery product-base__image" width="80"
                                        src="{{ asset($product->base_image?->src ?? 'images/placeholder.jpg') }}"
                                        xpreview="{{ asset($product->base_image?->src ?? 'images/placeholder.jpg') }}"></a>
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
                                    @php
                                        $serviceIcons = config('services.service_icons', []);
                                    @endphp
                                    @foreach (config('services.services', []) as $num => $icon)
                                        <div class="block-features__item">
                                            <div class="block-features__icon">
                                                {!! str_replace('<svg ', '<svg width="48px" height="48px" ', $serviceIcons[$num] ?? '') !!}
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
    @endif
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

                            {!! fix_youtube_embeds($product->description) !!}

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
            'discount_text' => setting('discount_text') ?? '',
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

    @if(setting('meta_pixel') || config('meta-pixel.meta_pixel') || setting('pixel_ids'))
        <script>
            (function() {
                const productData = {
                    id: {{ $product->id }},
                    name: @json($product->name),
                    price: {{ $product->selling_price }},
                    url: @json(route('products.show', $product->slug))
                };
                const eventId = 'vc_' + productData.id + '_' + Date.now();
                const eventData = {
                    currency: 'BDT',
                    value: productData.price,
                    content_ids: [String(productData.id)],
                    content_name: productData.name,
                    content_type: 'product',
                    quantity: 1
                };

                // 1. Push to dataLayer for GTM
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({
                    event: 'meta_ViewContent',
                    meta_event_name: 'ViewContent',
                    meta_event_id: eventId,
                    meta_event_data: eventData,
                    ecommerce: {
                        currency: 'BDT',
                        value: productData.price,
                        items: [{
                            item_id: String(productData.id),
                            item_name: productData.name,
                            price: productData.price,
                            quantity: 1
                        }]
                    }
                });

                // 2. Fire browser fbq
                if (typeof fbq === 'function') {
                    fbq('track', 'ViewContent', eventData, { eventID: eventId });
                }

                // 3. Fire server CAPI via API call
                const getCookie = (name) => {
                    const match = document.cookie.match(new RegExp('(^|;\\s*)' + name + '=([^;]*)'));
                    return match ? decodeURIComponent(match[2]) : null;
                };
                const payload = JSON.stringify({
                    product_id: productData.id,
                    value: productData.price,
                    fbp: getCookie('_fbp'),
                    fbc: getCookie('_fbc'),
                    event_id: eventId
                });

                if (navigator.sendBeacon) {
                    navigator.sendBeacon('/api/track-view-content', new Blob([payload], { type: 'application/json' }));
                } else {
                    fetch('/api/track-view-content', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: payload
                    });
                }
            })();
        </script>
    @endif
@endsection

@push('scripts')
<script>
    function scrollToReviews(event) {
        event.preventDefault();

        const reviewFormContainer = document.getElementById('review-form-container');
        if (!reviewFormContainer) {
            return;
        }

        // Check if the reviews accordion is collapsed and expand it
        const collapseThree = document.getElementById('collapseThree');
        let needsExpansion = false;

        if (collapseThree && collapseThree.classList.contains('collapse') && !collapseThree.classList.contains('show')) {
            needsExpansion = true;
            // Expand the accordion if it's collapsed (using jQuery if available, otherwise Bootstrap 5)
            if (typeof jQuery !== 'undefined' && jQuery.fn.collapse) {
                jQuery(collapseThree).collapse('show');
            } else if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                const bsCollapse = new bootstrap.Collapse(collapseThree, {
                    toggle: true
                });
            } else {
                // Fallback: manually add show class
                collapseThree.classList.add('show');
            }
        }

        // Scroll to the review form after a delay to allow accordion to expand
        setTimeout(function() {
            reviewFormContainer.scrollIntoView({
                behavior: 'smooth',
                block: 'start',
                inline: 'nearest'
            });
        }, needsExpansion ? 400 : 100);
    }
</script>
@endpush
