@extends('layouts.yellow.master')

@push('head')
    @include('schema.product', ['product' => $product])
@endpush
@php
    $services = setting('services');
@endphp

@section('seo_tags')
    {!! seo()->for($product) !!}
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('strokya/vendor/xzoom/xzoom.css') }}">
    <link rel="stylesheet" href="{{ asset('strokya/vendor/xZoom-master/example/css/demo.css') }}">
        @once
            <link rel="stylesheet" href="{{ asset('css/hk-customer-reviews-premium.css') }}">
        @endonce
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
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
            margin-bottom: 16px;
        }
        #accordion .card-header {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
        }
        #accordion .card-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 16px;
            padding: 16px 20px;
            color: #0f172a;
            font-weight: 700;
            text-decoration: none;
            transition: all .2s;
        }
        #accordion .card-link:hover {
            color: var(--brand);
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
                grid-template-columns: [gallery] minmax(0, 4.2fr) [info] minmax(0, 4.8fr) [sidebar] minmax(0, 3fr);
            @else
                grid-template-columns: [gallery] minmax(0, 5fr) [info] minmax(0, 5fr);
            @endif
            grid-column-gap: 16px;
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
                    <div class="xzoom-container d-flex @unless(config('app.vertical_image_gallery')) flex-column @endunless">
                        <div class="original">
                            <img class="xzoom" id="xzoom-default" src="{{ asset($product->base_image->src) }}" alt="{{ $product->base_image->alt_text ?: $product->name }}"
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
                        <div class="mt-2 xzoom-thumbs d-flex @if(config('app.vertical_image_gallery')) flex-column @endif">
                            <a href="{{ asset($product->base_image->src) }}"><img
                                    data-detail="{{ route('products.show', $product) }}"
                                    class="xzoom-gallery product-base__image" width="80"
                                    src="{{ asset($product->base_image->src) }}"
                                    alt="{{ $product->base_image->alt_text ?: $product->name }}"
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
                                        alt="{{ $image->alt_text ?: $product->name }}"
                                        @if ($hasVariants) data-variant-ids="{{ json_encode($variantIds) }}" @endif>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <!-- .product__info -->
                    <livewire:product-detail :product="$product" :show-brand-category="!($services->enabled ?? false)" />
                    <!-- .product__info / end -->
                    @if ($services->enabled ?? false)
                        <div class="product__sidebar-features">
                            @if ($product->variations->isNotEmpty())
                                <div class="p-3 mb-3 product__categories-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);">
                                    <div class="product__tags tags">
                                        <div class="d-flex flex-wrap align-items-center gap-1">
                                            <span class="mr-1 text-muted font-weight-bold" style="font-size: 13px;">Categories:</span>
                                            @foreach ($product->categories as $category)
                                                <a href="{{ route('category.show', $category) }}"
                                                    class="badge px-2 py-1" style="background: rgba(var(--brand-rgb), 0.1); color: var(--brand-dark); border-radius: 3px; font-size: 12px; font-weight: 700; text-decoration: none;" wire:navigate.hover>{{ $category->name }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="bb-product-services-card d-none d-md-block">
                                <div class="bb-services-card-header d-flex align-items-center">
                                    <i class="fas fa-shield-halved bb-services-shield-icon mr-2"></i>
                                    <span>আমাদের সেবা ও নিশ্চয়তা</span>
                                </div>
                                <div class="bb-services-list">
                                    @php
                                        $serviceIcons = config('services.service_icons', []);
                                        $fallbackIcons = [
                                            'one' => '<i class="fas fa-clipboard-check"></i>',
                                            'two' => '<i class="fas fa-headset"></i>',
                                            'three' => '<i class="fas fa-hand-holding-dollar"></i>',
                                            'four' => '<i class="fas fa-star-half-stroke"></i>',
                                        ];
                                    @endphp
                                    @foreach (config('services.services', []) as $num => $icon)
                                        @if (!empty($services->$num->title))
                                            <div class="bb-service-item d-flex align-items-start">
                                                <div class="bb-service-icon-box mr-3">
                                                    @if (!empty($serviceIcons[$num]))
                                                        {!! str_replace('<svg ', '<svg width="20px" height="20px" fill="currentColor" ', $serviceIcons[$num]) !!}
                                                    @else
                                                        {!! $fallbackIcons[$num] ?? '<i class="fas fa-check"></i>' !!}
                                                    @endif
                                                </div>
                                                <div class="bb-service-content flex-grow-1">
                                                    <div class="bb-service-title font-weight-bold">{{ $services->$num->title }}</div>
                                                    <div class="bb-service-desc">{{ $services->$num->detail }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div id="accordion" class="mt-3">
                <div class="card bb-product-description-card">
                    @once
                        <link rel="stylesheet" href="{{ asset('css/hk-product-description-premium.css') }}">
                    @endonce
                    <div class="p-0 card-header">
                        <a class="px-4 card-link bb-product-description-title" datatoggle="collapse" href="javascript:void(false)">
                            Product Description
                        </a>
                    </div>
                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                        <div class="p-2 card-body bb-product-description-body">
                            @if ($product->desc_img && $product->desc_img_pos == 'before_content')
                                <div class="text-center">
                                    @foreach ($product->images as $image)
                                        <img src="{{ asset($image->src) }}" alt="{{ $image->alt_text ?: $product->name }}"
                                            class="my-2 border img-fluid">
                                    @endforeach
                                </div>
                            @endif

                            <div class="bb-product-description">
                                {!! fix_youtube_embeds($product->description) !!}
                            </div>

                            @if ($product->desc_img && $product->desc_img_pos == 'after_content')
                                <div class="text-center">
                                    @foreach ($product->images as $image)
                                        <img src="{{ asset($image->src) }}" alt="{{ $image->alt_text ?: $product->name }}"
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
                            Delivery & Return Policy
                        </a>
                    </div>
                    <div id="collapseTwo" class="collapse show" data-parent="#accordion">
                        <div class="p-2 card-body">
                            @include('products.partials.delivery-return-premium', ['product' => $product])
                        </div>
                    </div>
                </div>
                <div class="mt-3 card bb-customer-reviews-card">
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
    @php
        $relatedProductsSetting = setting('related_products');
    @endphp
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
