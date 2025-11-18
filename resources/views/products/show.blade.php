@extends('layouts.yellow.master')
@php $services = setting('services') @endphp
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
                url('/')                => 'Home',
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
                            <img class="xzoom" id="xzoom-default" src="{{ asset($product->base_image->src) }}" xoriginal="{{ asset($product->base_image->src) }}" />
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
                            <a href="{{ asset($product->base_image->src) }}"><img data-detail="{{ route('products.show', $product) }}" class="xzoom-gallery product-base__image" width="80" src="{{ asset($product->base_image->src) }}"  xpreview="{{ asset($product->base_image->src) }}"></a>
                            @php
                                // Collect all variant base images
                                $variantImages = $product->variations->pluck('base_image')->filter();

                                // Merge variant images with additional images and get unique ones
                                $allImages = $product->additional_images->merge($variantImages)->unique('id');
                            @endphp
                            @foreach($allImages as $image)
                                @php
                                    // Find all variants that have this image (same image can belong to multiple variants)
                                    $variantIds = $product->variations
                                        ->filter(fn($v) => $v->base_image && $v->base_image->id === $image->id)
                                        ->pluck('id')
                                        ->toArray();
                                    $hasVariants = !empty($variantIds);
                                @endphp
                                <a href="{{ asset($image->src) }}" @if($hasVariants) class="variant-image-link" data-variant-ids="{{ json_encode($variantIds) }}" @endif>
                                    <img class="xzoom-gallery @if($hasVariants) variant-image @endif" width="80" src="{{ asset($image->src) }}" @if($hasVariants) data-variant-ids="{{ json_encode($variantIds) }}" @endif>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <!-- .product__info -->
                    <livewire:product-detail :product="$product" :show-brand-category="!($services->enabled ?? false)" />
                    <!-- .product__info / end -->
                    @if($services->enabled ?? false)
                    <div>
                        @if($product->variations->isNotEmpty())
                        <div class="p-3 mt-2 mb-2 border product__footer">
                            <div class="product__tags tags">
                                @if($product->brand)
                                    <p class="mb-0 text-secondary">
                                        Brand: <a href="{{ route('brands.products', $product->brand) }}" class="text-primary badge badge-light"><big>{{ $product->brand->name }}</big></a>
                                    </p>
                                @endif
                                <div class="mt-2">
                                    <p class="mr-2 mb-0 text-secondary d-inline-block">Categories:</p>
                                    @foreach($product->categories as $category)
                                        <a href="{{ route('categories.products', $category) }}" class="badge badge-primary">{{ $category->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="block-features__list flex-column d-none d-md-block">
                            @foreach(config('services.services', []) as $num => $icon)
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
                                @if(!$loop->last)
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
                            @if($product->desc_img && $product->desc_img_pos == 'before_content')
                            <div class="text-center">
                                @foreach ($product->images as $image)
                                    <img src="{{ asset($image->src) }}" alt="{{ $product->name }}" class="my-2 border img-fluid">
                                @endforeach
                            </div>
                            @endif

                            {!! $product->description !!}

                            @if($product->desc_img && $product->desc_img_pos == 'after_content')
                            <div class="text-center">
                                @foreach ($product->images as $image)
                                    <img src="{{ asset($image->src) }}" alt="{{ $product->name }}" class="my-2 border img-fluid">
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
                            {!! (setting('show_option')->productwise_delivery_charge ?? false) ? ($product->delivery_text ?? setting('delivery_text')) : setting('delivery_text') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- .block-products-carousel -->
    @php($relatedProductsSetting = setting('related_products'))
    <div class="lazy-related-products"
         x-data="lazyRelatedProducts('{{ $product->slug }}', {{ $relatedProductsSetting->cols ?? 5 }})"
         x-init="init()"
         data-show-option="{{ json_encode([
             'product_grid_button' => setting('show_option')->product_grid_button ?? 'add_to_cart',
             'add_to_cart_icon' => setting('show_option')->add_to_cart_icon ?? '',
             'add_to_cart_text' => setting('show_option')->add_to_cart_text ?? 'Add to Cart',
             'order_now_icon' => setting('show_option')->order_now_icon ?? '',
             'order_now_text' => setting('show_option')->order_now_text ?? 'Order Now',
             'discount_text' => setting('discount_text') ?? '<small>Discount:</small> [percent]%',
         ]) }}"
         data-is-oninda="{{ isOninda() ? 'true' : 'false' }}"
         data-guest-can-see-price="{{ (bool)(setting('show_option')->guest_can_see_price ?? false) ? 'true' : 'false' }}">
        <div class="block block-products-carousel">
            <div class="container">
                <div class="block-header">
                    <h3 class="block-header__title" style="padding: 0.375rem 1rem;">
                        Related Products
                    </h3>
                    <div class="block-header__divider"></div>
                </div>
                <div class="products-view__list products-list" data-layout="grid-{{ $relatedProductsSetting->cols ?? 5 }}-full" data-with-features="false">
                    <div class="products-list__body" id="related-products-container">
                        <div x-show="loading" class="text-center py-5">
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

@push('scripts')
    <script src="{{ asset('strokya/vendor/xzoom/xzoom.min.js') }}"></script>
    <script src="{{ asset('strokya/vendor/xZoom-master/example/js/vendor/modernizr.js') }}"></script>
    <script src="{{ asset('strokya/vendor/xZoom-master/example/js/setup.js') }}"></script>
    <script>
        $(document).ready(function () {
            let activeG = 0;
            let lastG = 0;
            let autoNavigationTimer = null;

            // Function to update active gallery index
            function updateActiveIndex() {
                let gallery = $('.xzoom-gallery');
                gallery.each(function (g, e) {
                    if ($(e).hasClass('xactive')) {
                        activeG = g;
                    }
                    lastG = g;
                });
            }

            // Function to navigate to next image
            function navigateToNext() {
                updateActiveIndex();
                let gallery = $('.xzoom-gallery');
                const next = activeG === lastG ? 0 : (activeG + 1);
                gallery.eq(next).trigger('click');
            }

            // Function to schedule next auto navigation
            function scheduleNextNavigation() {
                if (autoNavigationTimer) {
                    clearTimeout(autoNavigationTimer);
                }
                autoNavigationTimer = setTimeout(() => {
                    navigateToNext();
                    // Note: scheduleNextNavigation() is not called here because
                    // navigateToNext() triggers a click, which fires the click handler
                    // that calls resetAutoNavigation(), which schedules the next one
                }, 3000);
            }

            // Function to reset auto navigation timer
            function resetAutoNavigation() {
                if (autoNavigationTimer) {
                    clearTimeout(autoNavigationTimer);
                }
                scheduleNextNavigation();
            }

            // Listen for variant change event from Livewire
            Livewire.on('variantChanged', (event) => {
                const variantId = event.variantId;
                const variantImageSrc = event.variantImageSrc;

                // Navigate to the variant's base image if exists
                if (variantImageSrc) {
                    // Find image that belongs to this variant (check if variant ID is in the array)
                    let variantImage = null;
                    $('.variant-image').each(function() {
                        const variantIds = $(this).data('variant-ids');
                        if (variantIds && Array.isArray(variantIds) && variantIds.includes(variantId)) {
                            variantImage = $(this);
                            return false; // break the loop
                        }
                    });

                    if (variantImage && variantImage.length > 0) {
                        // Reset auto navigation to prevent immediate jump
                        resetAutoNavigation();

                        // Trigger click on the variant image to display it in xzoom
                        setTimeout(() => {
                            variantImage.trigger('click');
                            updateActiveIndex();
                        }, 100);
                    }
                }
            });

            $('.zoom-control.left').click(function () {
                updateActiveIndex();
                let gallery = $('.xzoom-gallery');
                const prev = activeG === 0 ? lastG : (activeG - 1);
                gallery.eq(prev).trigger('click');
                resetAutoNavigation();
            });

            $('.zoom-control.right').click(function () {
                navigateToNext();
                resetAutoNavigation();
            });

            // Handle manual image clicks
            $('.xzoom-gallery').on('click', function() {
                resetAutoNavigation();
            });

            // Start automatic navigation
            scheduleNextNavigation();
        });

        // Lazy load related products
        document.addEventListener('alpine:init', () => {
            Alpine.data('lazyRelatedProducts', (productSlug, cols) => ({
                productSlug: productSlug,
                cols: cols,
                loading: false,
                loaded: false,
                observer: null,

                init() {
                    // Set up Intersection Observer to load when section is near viewport
                    this.observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting && !this.loaded && !this.loading) {
                                this.loadProducts();
                            }
                        });
                    }, {
                        rootMargin: '200px' // Start loading 200px before section enters viewport
                    });

                    // Observe the lazy-related-products container
                    this.$nextTick(() => {
                        const container = this.$el;
                        if (container) {
                            this.observer.observe(container);
                        }
                    });
                },

                async loadProducts() {
                    if (this.loading || this.loaded) return;

                    this.loading = true;
                    const container = document.getElementById('related-products-container');
                    if (!container) {
                        this.loading = false;
                        return;
                    }

                    try {
                        const response = await fetch(`/api/products/${this.productSlug}/related.json`);

                        if (response.ok) {
                            const products = await response.json();
                            this.renderProducts(products, container);
                            this.loaded = true;
                            this.observer?.disconnect();
                        } else {
                            container.innerHTML = '<div class="text-center py-5 text-muted">Unable to load related products.</div>';
                        }
                    } catch (error) {
                        console.error('Error loading related products:', error);
                        container.innerHTML = '<div class="text-center py-5 text-muted">Unable to load related products.</div>';
                    }

                    this.loading = false;
                },

                renderProducts(products, container) {
                    if (!products || products.length === 0) {
                        container.innerHTML = '<div class="text-center py-5 text-muted">No related products found.</div>';
                        return;
                    }

                    // Clear loading indicator
                    container.innerHTML = '';

                    products.forEach((product) => {
                        const productElement = this.createProductElement(product);
                        container.appendChild(productElement);
                        this.attachNavigationHandlers(productElement);
                    });
                },

                createProductElement(product) {
                    const div = document.createElement('div');
                    div.className = 'products-list__item';
                    div.innerHTML = this.getProductHTML(product);
                    return div;
                },

                attachNavigationHandlers(element) {
                    const productLinks = element.querySelectorAll('a.product-link[data-navigate]');
                    productLinks.forEach(link => {
                        link.addEventListener('click', (e) => {
                            const href = link.getAttribute('href');
                            if (href && window.Livewire && window.Livewire.navigate) {
                                e.preventDefault();
                                window.Livewire.navigate(href);
                            }
                        });
                    });
                },

                getProductHTML(product) {
                    const productId = product.id;
                    const productName = product.name || 'Product';
                    const productSlug = product.slug || productId;
                    const productPrice = product.compareAtPrice || product.price || 0;
                    const productSellingPrice = product.price || productPrice;
                    const productImage = product.base_image_url || (product.images && product.images.length > 0 
                        ? `/storage/${product.images[0]}` 
                        : '/images/placeholder.jpg');
                    const productUrl = `/products/${productSlug}`;
                    const inStock = product.availability !== 'Out of Stock' && (product.availability === 'In Stock' || (product.availability && parseInt(product.availability) > 0));
                    const stockCount = typeof product.availability === 'number' ? product.availability : (product.availability === 'In Stock' ? null : 0);
                    const shouldTrack = typeof product.availability === 'number' || product.availability !== 'In Stock';
                    const hasDiscount = productPrice !== productSellingPrice && productPrice > 0;
                    const discountPercent = hasDiscount ? Math.round(((productPrice - productSellingPrice) * 100) / productPrice) : 0;

                    // Get settings from data attributes
                    const showOption = JSON.parse(this.$el.dataset.showOption || '{}');
                    const isOninda = this.$el.dataset.isOninda === 'true';
                    const guestCanSeePrice = this.$el.dataset.guestCanSeePrice === 'true';

                    // Format price to match theMoney() function format: "TK <span>amount</span>"
                    const formatPrice = (price) => {
                        return `TK&nbsp;<span>${parseFloat(price).toLocaleString('en-US')}</span>`;
                    };

                    // Generate buttons HTML
                    let buttonsHTML = '';
                    if (!isOninda) {
                        const available = inStock;
                        const disabledAttr = available ? '' : 'disabled';

                        if (showOption.product_grid_button === 'add_to_cart') {
                            buttonsHTML = `
                                <div class="product-card__buttons">
                                    <button class="btn btn-primary product-card__addtocart" type="button" ${disabledAttr}
                                            data-product-id="${productId}" data-action="add" onclick="handleAddToCart(this)">
                                        ${showOption.add_to_cart_icon || ''}
                                        <span class="ml-1">${showOption.add_to_cart_text || 'Add to Cart'}</span>
                                    </button>
                                </div>
                            `;
                        } else if (showOption.product_grid_button === 'order_now') {
                            buttonsHTML = `
                                <div class="product-card__buttons">
                                    <button class="btn btn-primary product-card__ordernow" type="button" ${disabledAttr}
                                            data-product-id="${productId}" data-action="kart" onclick="handleAddToCart(this)">
                                        ${showOption.order_now_icon || ''}
                                        <span class="ml-1">${showOption.order_now_text || 'Order Now'}</span>
                                    </button>
                                </div>
                            `;
                        }
                    }

                    // Generate price HTML
                    let priceHTML = '';
                    if (isOninda && !guestCanSeePrice) {
                        priceHTML = '<span class="product-card__new-price text-danger">Login to see price</span>';
                    } else if (isOninda && guestCanSeePrice) {
                        priceHTML = '<small class="product-card__new-price text-danger">Verify account to see price</small>';
                    } else if (hasDiscount) {
                        priceHTML = `<span class="product-card__new-price">${formatPrice(productSellingPrice)}</span><span class="product-card__old-price">${formatPrice(productPrice)}</span>`;
                    } else {
                        priceHTML = formatPrice(productSellingPrice);
                    }

                    const discountText = (showOption.discount_text || '<small>Discount:</small> [percent]%').replace('[percent]', discountPercent);

                    return `
                        <div class="product-card" data-id="${productId}" data-max="${shouldTrack ? (stockCount || 0) : -1}">
                            <div class="product-card__badges-list">
                                ${!inStock ? '<div class="product-card__badge product-card__badge--sale">Sold</div>' : ''}
                                ${hasDiscount ? `<div class="product-card__badge product-card__badge--sale">${discountText}</div>` : ''}
                            </div>
                            <div class="product-card__image">
                                <a href="${productUrl}" class="product-link" data-navigate>
                                    <img src="${productImage}" alt="Base Image" style="width: 100%; height: 100%;">
                                </a>
                            </div>
                            <div class="product-card__info">
                                <div class="product-card__name">
                                    <a href="${productUrl}" class="product-link" data-navigate data-name="${product.var_name || productName}">${productName}</a>
                                </div>
                            </div>
                            <div class="product-card__actions">
                                <div class="product-card__availability">Availability:
                                    ${!shouldTrack ?
                                        '<span class="text-success">In Stock</span>' :
                                        `<span class="text-${(stockCount || 0) > 0 ? 'success' : 'danger'}">${stockCount || 0} In Stock</span>`
                                    }
                                </div>
                                <div class="product-card__prices ${hasDiscount ? 'has-special' : ''}">
                                    ${priceHTML}
                                </div>
                                ${buttonsHTML}
                            </div>
                        </div>
                    `;
                }
            }));
        });
    </script>
@endpush
