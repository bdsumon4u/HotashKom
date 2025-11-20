<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $company->name }} - @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset($logo->favicon) }}">

    @php
        $bootstrapCss = cdnAsset('bootstrap.css', 'strokya/vendor/bootstrap-4.2.1/css/bootstrap.min.css');
        $fontawesomeCss = cdnAsset('fontawesome.css', 'strokya/vendor/fontawesome-5.6.1/css/all.min.css');
        $jqueryJs = cdnAsset('jquery', 'strokya/vendor/jquery-3.3.1/jquery.min.js');
    @endphp

    @include('layouts.partials.cdn-fallback', [
        'fallbackAssets' => [
            'jquery' => asset('strokya/vendor/jquery-3.3.1/jquery.min.js'),
            'bootstrap' => asset('strokya/vendor/bootstrap-4.2.1/js/bootstrap.bundle.min.js'),
            'owl' => asset('strokya/vendor/owl-carousel-2.3.4/owl.carousel.min.js'),
            'svg4everybody' => asset('strokya/vendor/svg4everybody-2.1.9/svg4everybody.min.js'),
        ],
    ])
    {{-- Preload critical CSS --}}
    <link rel="preload" href="{{ $bootstrapCss }}" as="style" crossorigin="anonymous">
    <link rel="preload" href="{{ $fontawesomeCss }}" as="style" crossorigin="anonymous">
    <link rel="preload" href="{{ versionedAsset('strokya/css/style.css') }}" as="style">

    {{-- Preload critical JavaScript --}}
    <link rel="preload" href="{{ $jqueryJs }}" as="script" crossorigin="anonymous">

    {{-- Preconnect to CDN domains for faster DNS resolution --}}
    @if(config('cdn.enabled', true))
        <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
        <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
        <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
        <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    @endif

    {{-- Global jQuery (needed for SPA navigation) --}}
    <script
        src="{{ $jqueryJs }}"
        data-navigate-once
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
        onerror="window.__loadLocalAsset && window.__loadLocalAsset('jquery')"
    ></script>
    <script data-navigate-once>
        (function () {
            if (window.runWhenJQueryReady) {
                window.__flushRunWhenJQueryQueue && window.__flushRunWhenJQueryQueue();
                return;
            }

            const queue = [];

            function flushQueue() {
                if (typeof window.jQuery === 'undefined') {
                    return;
                }

                while (queue.length) {
                    const callback = queue.shift();
                    try {
                        callback(window.jQuery);
                    } catch (error) {
                        console.error(error);
                    }
                }
            }

            function scheduleFlush() {
                queueMicrotask(flushQueue);
            }

            window.__flushRunWhenJQueryQueue = flushQueue;

            window.runWhenJQueryReady = function (callback) {
                if (typeof window.jQuery !== 'undefined') {
                    callback(window.jQuery);
                } else {
                    queue.push(callback);
                }
            };

            document.addEventListener('DOMContentLoaded', scheduleFlush, { once: true });
            document.addEventListener('livewire:navigate', scheduleFlush);
            scheduleFlush();
            if (document.readyState !== 'loading') {
                scheduleFlush();
            }
        })();
    </script>
    </script>

    <!-- css -->
    @include('googletagmanager::head')
    <x-metapixel-head/>
    @include('layouts.yellow.css')
    <!-- js -->
    <!-- font - fontawesome -->
    <link rel="stylesheet" href="{{ $fontawesomeCss }}" crossorigin="anonymous" referrerpolicy="no-referrer"><!-- font - stroyka -->
    <link rel="stylesheet" href="{{ asset('strokya/fonts/stroyka/stroyka.css') }}">
    @include('layouts.yellow.color')
    <style>
        [x-cloak] { display: none !important; }
        .topbar__item {
            flex: none;
        }
        .page-header__container {
            padding-bottom: 12px;
        }
        .products-list__item {
            justify-content: space-between;
        }
        @media (max-width: 479px) {
            /* .products-list[data-layout=grid-5-full] .products-list__item {
                width: 46%;
                margin: 8px 6px;
            } */
            .product-card__buttons .btn {
                font-size: 0.75rem;
            }
        }
        @media (max-width: 575px) {
            .mobile-header__search {
                top: 55px;
            }
            .mobile-header__search-form .aa-input-icon {
                display: none;
            }
            .mobile-header__search-form .aa-hint, .mobile-header__search-form .aa-input {
                padding-right: 15px !important;
            }
            .block-products-carousel[data-layout=grid-4] .product-card .product-card__buttons .btn {
                height: auto;
            }
        }
        .product-card:before,
        .owl-carousel {
            z-index: 0;
        }
        .block-products-carousel[data-layout^=grid-] .product-card .product-card__info,
        .products-list[data-layout^=grid-] .product-card .product-card__info {
            padding: 0 14px;
        }
        .block-products-carousel[data-layout^=grid-] .product-card .product-card__actions,
        .products-list[data-layout^=grid-] .product-card .product-card__actions {
            padding: 0 14px 14px 14px;
        }
        .product-card__badges-list {
            flex-direction: row;
        }
        .product-card__name {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .product-card__buttons {
            margin-right: -12px !important;
            /* margin-bottom: -12px !important; */
            margin-left: -12px !important;
        }
        .product-card__buttons .btn {
            height: auto !important;
            font-size: 20px !important;
            padding: 0.25rem 0.15rem !important;
            border-radius: 0 !important;
            display: block;
            width: 100%;
        }
        .aa-input-container {
            width: 100%;
        }
        .algolia-autocomplete {
            width: 100%;
            display: flex !important;
        }
        #aa-search-input {
            box-shadow: none;
        }
        .indicator__area {
            padding: 0 8px;
        }
        .mobile-header__search.mobile-header__search--opened {
            height: 100%;
            display: flex;
            align-items: center;
        }
        .mobile-header__search-form {
            width: 100%;
        }
        .mobile-header__search-form .aa-input-container {
            display: flex;
        }
        .mobile-header__search-form .aa-input-search {
            box-shadow: none;
        }
        .mobile-header__search-form .aa-hint,
        .mobile-header__search-form .aa-input {
            height: 54px;
            padding-right: 32px;
        }
        .mobile-header__search-form .aa-input-icon {
            right: 62px;
        }
        .mobile-header__search-form .aa-dropdown-menu {
            background-color: #f7f8f9;
            z-index: 9999 !important;
        }
        .aa-input-container input {
            font-size: 15px;

        }
        .toast {
            position: absolute;
            top: 10%;
            right: 10%;
            z-index: 9999;
        }
        .header-fixed .site__body {
            padding-top: 11rem;
        }
        @media (max-width: 991px) {
            .header-fixed .site__header {
                position: fixed;
                width: 100%;
                z-index: 9999;
            }
            .header-fixed .site__body {
                padding-top: 85px;
            }
            .header-fixed .mobilemenu__body {
                top: 85px;
            }
        }

        .dropcart__products-list {
            max-height: 300px;
            overflow-y: auto;
        }

        /** StickyNav **/
        .site-header.sticky {
            position: fixed;
            top: 0;
            min-width: 100%;
        }
        .site-header.sticky .site-header__middle {
            height: 65px;
        }
        /*.site-header.sticky .site-header__nav-panel,*/
        .site-header.sticky .site-header__topbar {
            display: none;
        }
        ::placeholder {
            color: #777 !important;
        }


        .widget-connect{position:fixed;bottom:30px;z-index:99 !important;cursor:pointer}.widget-connect-right{right:27px;bottom:22px}.widget-connect-left{left:20px}@media (max-width:768px){.widget-connect-left{left:10px;bottom:10px}}.widget-connect.active .widget-connect__button{display:grid;place-content:center;padding-top:5px}.widget-connect__button{display:none;height:55px;width:55px;margin:auto;margin-bottom:15px;border-radius:50%;overflow:hidden;box-shadow:2px 2px 6px rgba(0, 0, 0, .4);font-size:28px;text-align:center;line-height:50px;color:#fff;outline:0
!important;background-position:center center;background-repeat:no-repeat;transition:all;transition-duration: .2s}@media (max-width:768px){.widget-connect__button{height:50px;width:50px}}.widget-connect__button-activator:hover,.widget-connect__button:hover{box-shadow:2px 2px 8px 2px rgba(0,0,0,.4)}.widget-connect__button:active{height:48px;width:48px;box-shadow:2px 2px 6px rgba(0, 0, 0, 0);transition:all;transition-duration: .2s}@media (max-width:768px){.widget-connect__button:active{height:45px;width:45px}}.widget-connect__button-activator{margin:auto;border-radius:50%;box-shadow:2px 2px 6px rgba(0, 0, 0, .4);background-position:center center;background-repeat:no-repeat;transition:all;transition-duration: .2s;text-align:right;z-index:99!important}.widget-connect__button-activator-icon{height:55px;width:55px;background-image:url(/multi-chat.svg);background-size:55%;background-position:center center;background-repeat:no-repeat;-webkit-transition-duration: .2s;-moz-transition-duration: .2s;-o-transition-duration: .2s;transition-duration: .2s}@media (max-width:768px){.widget-connect__button-activator-icon{height:50px;width:50px}}.widget-connect__button-activator-icon.active{background-image:url(/multi-chat.svg);background-size:45%;transform:rotate(90deg)}.widget-connect__button-telephone{background-color:#FFB200;background-image:url(/catalog/view/theme/default/image/widget-multi-chat/call.svg);background-size:55%}.widget-connect__button-messenger{background-color:#0866FF;background-image:url(/catalog/view/theme/default/image/widget-multi-chat/messenger.svg);background-size:65%;background-position-x:9px}.widget-connect__button-whatsapp{background-color:#25d366;background-image:url(/catalog/view/theme/default/image/widget-multi-chat/whatsapp.svg);background-size:65%}@-webkit-keyframes button-slide{0%{opacity:0;display:none;margin-top:0;margin-bottom:0;-ms-transform:translateY(15px);-webkit-transform:translateY(15px);-moz-transform:translateY(15px);-o-transform:translateY(15px);transform:translateY(15px)}to{opacity:1;display:block;margin-top:0;margin-bottom:10px;-ms-transform:translateY(0);-webkit-transform:translateY(0);-moz-transform:translateY(0);-o-transform:translateY(0);transform:translateY(0)}}@-moz-keyframes button-slide{0%{opacity:0;display:none;margin-top:0;margin-bottom:0;-ms-transform:translateY(15px);-webkit-transform:translateY(15px);-moz-transform:translateY(15px);-o-transform:translateY(15px);transform:translateY(15px)}to{opacity:1;display:block;margin-top:0;margin-bottom:9px;-ms-transform:translateY(0);-webkit-transform:translateY(0);-moz-transform:translateY(0);-o-transform:translateY(0);transform:translateY(0)}}@-o-keyframes button-slide{0%{opacity:0;display:none;margin-top:0;margin-bottom:0;-ms-transform:translateY(15px);-webkit-transform:translateY(15px);-moz-transform:translateY(15px);-o-transform:translateY(15px);transform:translateY(15px)}to{opacity:1;display:block;margin-top:0;margin-bottom:10px;-ms-transform:translateY(0);-webkit-transform:translateY(0);-moz-transform:translateY(0);-o-transform:translateY(0);transform:translateY(0)}}@keyframes button-slide{0%{opacity:0;display:none;margin-top:0;margin-bottom:0;-ms-transform:translateY(15px);-webkit-transform:translateY(15px);-moz-transform:translateY(15px);-o-transform:translateY(15px);transform:translateY(15px)}to{opacity:1;display:block;margin-top:0;margin-bottom:10px;-ms-transform:translateY(0);-webkit-transform:translateY(0);-moz-transform:translateY(0);-o-transform:translateY(0);transform:translateY(0)}}.button-slide{-webkit-animation-name:button-slide;-moz-animation-name:button-slide;-o-animation-name:button-slide;animation-name:button-slide;-webkit-animation-duration: .2s;-moz-animation-duration: .2s;-o-animation-duration: .2s;animation-duration: .2s;-webkit-animation-fill-mode:forwards;-moz-animation-fill-mode:forwards;-o-animation-fill-mode:forwards;animation-fill-mode:forwards}.button-slide-out{-webkit-animation-name:button-slide;-moz-animation-name:button-slide;-o-animation-name:button-slide;animation-name:button-slide;-webkit-animation-duration: .2s;-moz-animation-duration: .2s;-o-animation-duration: .2s;animation-duration: .2s;-webkit-animation-fill-mode:forwards;-moz-animation-fill-mode:forwards;-o-animation-fill-mode:forwards;animation-fill-mode:forwards;-webkit-animation-direction:reverse;-moz-animation-direction:reverse;-o-animation-direction:reverse;animation-direction:reverse}.widget-connect
.tooltip{position:absolute;z-index:99 !important;display:block;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:12px;font-style:normal;font-weight:400;line-height:1.42857143;text-align:left;text-align:start;text-decoration:none;text-shadow:none;text-transform:none;letter-spacing:normal;word-break:normal;word-spacing:normal;word-wrap:normal;white-space:normal;filter:alpha(opacity=0);opacity:0;line-break:auto;padding:5px}.tooltip-inner{max-width:200px;padding:5px
10px;color:#fff;text-align:center;background-color:#333;border-radius:4px}.tooltip.left .tooltip-arrow{top:50%;right:0;margin-top:-5px;border-width:5px 0 5px 5px;border-left-color:#333}.tooltip.right .tooltip-arrow{top:50%;left:0;margin-top:-5px;border-width:5px 5px 5px 0;border-right-color:#333}@media only screen and (max-width: 575px){.widget-connect-right{bottom:50px !important}}
    </style>
    @stack('styles')
    @livewireStyles
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@100..900&display=swap" rel="stylesheet">
{!! $scripts ?? null !!}
</head>

<body class="header-fixed" style="margin: 0; padding: 0;">
    <x-livewire-progress bar-class="bg-warning" track-class="bg-white/50" />
    @include('googletagmanager::body')
    <x-metapixel-body/>
    <!-- quickview-modal -->
    <div id="quickview-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content"></div>
        </div>
    </div><!-- quickview-modal / end -->
    <!-- mobilemenu -->
    <div class="mobilemenu">
        <div class="mobilemenu__backdrop"></div>
        <div class="mobilemenu__body">
            <div class="mobilemenu__header">
                <div class="mobilemenu__title">Menu</div>
                <button type="button" class="mobilemenu__close">
                    <svg width="20px" height="20px">
                        <use xlink:href="{{ asset('strokya/images/sprite.svg#cross-20') }}"></use>
                    </svg>
                </button>
            </div>
            <div class="mobilemenu__content">
                <ul class="mobile-links mobile-links--level--0" data-collapse data-collapse-opened-class="mobile-links__item--open">
                    @include('partials.mobile-menu-categories')
                    @include('partials.header.menu.mobile')
                </ul>
            </div>
        </div>
    </div><!-- mobilemenu / end -->
    <!-- site -->
    <div class="site">
        <!-- mobile site__header -->
        @include('partials.header.mobile')
        <!-- mobile site__header / end -->
        <!-- desktop site__header -->
        @include('partials.header.desktop')
        <!-- desktop site__header / end -->
        <!-- site__body -->
        <div class="site__body">
            <div class="container">
                @if(!request()->routeIs('/'))
                <x-reseller-verification-alert />
                @endif
                <x-alert-box class="mt-2 row" />
            </div>
            @yield('content')
        </div>
        <!-- site__body / end -->
        <!-- site__footer -->
        @include('partials.footer')
        <!-- site__footer / end -->
    </div><!-- site / end -->
    @livewireScripts
    @include('layouts.yellow.js')
    <script src="{{ asset('strokya/vendor/xzoom/xzoom.min.js') }}"></script>
    <script src="{{ asset('strokya/vendor/xZoom-master/example/js/vendor/modernizr.js') }}"></script>
    <script src="{{ asset('strokya/vendor/xZoom-master/example/js/setup.js') }}"></script>
    <script>
        (function () {
            function registerLazyRelatedProductsComponent() {
                if (window.__lazyRelatedProductsComponentRegistered) {
                    return;
                }

                const initComponent = () => {
                    if (window.__lazyRelatedProductsComponentRegistered) {
                        return;
                    }

                    window.__lazyRelatedProductsComponentRegistered = true;

                    window.Alpine.data('lazyRelatedProducts', (productId, cols) => ({
                        productId: productId,
                        cols: cols,
                        loading: false,
                        loaded: false,
                        observer: null,

                        init() {
                            this.observer = new IntersectionObserver((entries) => {
                                entries.forEach(entry => {
                                    if (entry.isIntersecting && !this.loaded && !this.loading) {
                                        this.loadProducts();
                                    }
                                });
                            }, {
                                rootMargin: '200px'
                            });

                            this.$nextTick(() => {
                                const container = this.$el;
                                if (container) {
                                    this.observer.observe(container);
                                }
                            });
                        },

                        async loadProducts() {
                            if (this.loading || this.loaded) {
                                return;
                            }

                            this.loading = true;
                            const container = document.getElementById('related-products-container');
                            if (!container) {
                                this.loading = false;
                                return;
                            }

                            try {
                                const response = await fetch(`/api/products/${encodeURIComponent(this.productId)}/related.json`);

                                if (response.ok) {
                                    const products = await response.json();
                                    this.renderProducts(products, container);
                                    this.loaded = true;
                                    this.observer?.disconnect();
                                } else {
                                    container.innerHTML = '<div class="py-5 text-center text-muted">Unable to load related products.</div>';
                                }
                            } catch (error) {
                                console.error('Error loading related products:', error);
                                container.innerHTML = '<div class="py-5 text-center text-muted">Unable to load related products.</div>';
                            }

                            this.loading = false;
                        },

                        renderProducts(products, container) {
                            if (!products || products.length === 0) {
                                container.innerHTML = '<div class="py-5 text-center text-muted">No related products found.</div>';
                                return;
                            }

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
                            const productUrl = `/products/${encodeURIComponent(productSlug)}`;
                            const inStock = product.availability !== 'Out of Stock' && (product.availability === 'In Stock' || (product.availability && parseInt(product.availability) > 0));
                            const stockCount = typeof product.availability === 'number' ? product.availability : (product.availability === 'In Stock' ? null : 0);
                            const shouldTrack = typeof product.availability === 'number' || product.availability !== 'In Stock';
                            const hasDiscount = productPrice !== productSellingPrice && productPrice > 0;
                            const discountPercent = hasDiscount ? Math.round(((productPrice - productSellingPrice) * 100) / productPrice) : 0;

                            const showOption = JSON.parse(this.$el.dataset.showOption || '{}');
                            const isOninda = this.$el.dataset.isOninda === 'true';
                            const guestCanSeePrice = this.$el.dataset.guestCanSeePrice === 'true';

                            const formatPrice = (price) => {
                                return `TK&nbsp;<span>${parseFloat(price).toLocaleString('en-US')}</span>`;
                            };

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
                };

                if (window.Alpine) {
                    initComponent();
                } else {
                    document.addEventListener('alpine:init', initComponent, { once: true });
                }
            }

            function initializeProductShowScripts() {
                if (!document.querySelector('.xzoom-container')) {
                    if (typeof window.__productShowCleanup === 'function') {
                        window.__productShowCleanup();
                        window.__productShowCleanup = null;
                    }
                    return;
                }

                runWhenJQueryReady(($) => {
                    if (typeof window.__productShowCleanup === 'function') {
                        window.__productShowCleanup();
                        window.__productShowCleanup = null;
                    }

                    const namespace = '.productShow';
                    const $galleries = $('.xzoom-gallery');

                    if (! $galleries.length) {
                        return;
                    }

                    if (typeof $.fn.xzoom === 'function') {
                        // Destroy existing xZoom instances to prevent conflicts on SPA navigation
                        $('.xzoom, .xzoom-gallery').each(function () {
                            const xzoom = $(this).data('xzoom');
                            if (xzoom && typeof xzoom.destroy === 'function') {
                                try {
                                    xzoom.destroy();
                                } catch (e) {
                                    // Ignore errors during cleanup
                                }
                            }
                        });

                        $('.xzoom, .xzoom-gallery').xzoom({
                            zoomWidth: 400,
                            title: true,
                            tint: '#333',
                            Xoffset: 15,
                        });
                    }

                    let activeG = 0;
                    let lastG = 0;
                    let autoNavigationTimer = null;

                    function updateActiveIndex() {
                        $galleries.each(function (g, e) {
                            if ($(e).hasClass('xactive')) {
                                activeG = g;
                            }
                            lastG = g;
                        });
                    }

                    function navigateToNext() {
                        updateActiveIndex();
                        const next = activeG === lastG ? 0 : (activeG + 1);
                        $galleries.eq(next).trigger('click');
                    }

                    function scheduleNextNavigation() {
                        clearTimeout(autoNavigationTimer);
                        autoNavigationTimer = setTimeout(() => {
                            navigateToNext();
                        }, 3000);
                    }

                    function resetAutoNavigation() {
                        clearTimeout(autoNavigationTimer);
                        scheduleNextNavigation();
                    }

                    $('.zoom-control.left').off('click'+namespace).on('click'+namespace, function () {
                        updateActiveIndex();
                        const prev = activeG === 0 ? lastG : (activeG - 1);
                        $galleries.eq(prev).trigger('click');
                        resetAutoNavigation();
                    });

                    $('.zoom-control.right').off('click'+namespace).on('click'+namespace, function () {
                        navigateToNext();
                        resetAutoNavigation();
                    });

                    $galleries.off('click'+namespace).on('click'+namespace, function () {
                        resetAutoNavigation();
                    });

                    scheduleNextNavigation();

                    window.__handleVariantChange = function (event) {
                        const variantId = event.variantId;
                        const variantImage = $('.variant-image').filter(function () {
                            const ids = $(this).data('variant-ids');
                            return Array.isArray(ids) && ids.includes(variantId);
                        }).first();

                        if (variantImage.length) {
                            resetAutoNavigation();
                            setTimeout(() => {
                                variantImage.trigger('click');
                                updateActiveIndex();
                            }, 100);
                        }
                    };

                    if (!window.__variantChangeListenerRegistered) {
                        window.__variantChangeListenerRegistered = true;

                        const registerVariantListener = () => {
                            Livewire.on('variantChanged', (event) => {
                                if (typeof window.__handleVariantChange === 'function') {
                                    window.__handleVariantChange(event);
                                }
                            });
                        };

                        if (window.Livewire) {
                            registerVariantListener();
                        } else {
                            document.addEventListener('livewire:load', registerVariantListener, { once: true });
                        }
                    }

                    window.__productShowCleanup = function () {
                        clearTimeout(autoNavigationTimer);
                        $('.zoom-control.left').off('click'+namespace);
                        $('.zoom-control.right').off('click'+namespace);
                        $galleries.off('click'+namespace);
                    };
                });
            }

            function runInitializers() {
                registerLazyRelatedProductsComponent();
                requestAnimationFrame(initializeProductShowScripts);
            }

            registerLazyRelatedProductsComponent();

            document.addEventListener('DOMContentLoaded', runInitializers);
            document.addEventListener('livewire:navigate', runInitializers);
        })();
    </script>
    <script>
        runWhenJQueryReady(function ($) {
            $(window)
                .off('notify.storefront')
                .on('notify.storefront', function (ev) {
                    for (let item of ev.detail) {
                        $.notify(item.message, {
                            type: item.type ?? 'info',
                        });
                    }
                });

            $(window)
                .off('dataLayer.storefront')
                .on('dataLayer.storefront', function (ev) {
                    for (let item of ev.detail) {
                        window.dataLayer.push(item);
                    }
                });

            function onScroll() {
                const scrollTop = $(window).scrollTop();

                if (scrollTop > 32) {
                    $('.site__header.position-fixed .topbar').hide();
                } else {
                    $('.site__header.position-fixed .topbar').show();
                }

                if (scrollTop > 100) {
                    $('.departments').removeClass('departments--opened departments--fixed');
                    $('.departments__body').attr('style', '');
                } else {
                    if ($('.departments').data('departments-fixed-by') !== '') {
                        $('.departments').addClass('departments--opened departments--fixed');
                    }
                    $('.departments--opened.departments--fixed .departments__body').css('min-height', '458px');
                }
            }

            $(window).off('scroll.siteHeader').on('scroll.siteHeader', onScroll);
            onScroll();
        });
    </script>
    <script data-navigate-once>
        (function () {
            if (window.__storefrontComponentsRegistered) {
                return;
            }

            window.__storefrontComponentsRegistered = true;

            const registerPaginationLinks = () => {
                document.querySelectorAll('.pagination a').forEach(link => {
                    if (link.hasAttribute('wire:navigate') || link.hasAttribute('wire:navigate.hover')) {
                        return;
                    }

                    if (link.getAttribute('href')) {
                        link.setAttribute('wire:navigate.hover', '');
                    }
                });
            };

            document.addEventListener('DOMContentLoaded', registerPaginationLinks);
            document.addEventListener('livewire:navigate', () => queueMicrotask(registerPaginationLinks));
            document.addEventListener('livewire:navigated', registerPaginationLinks);

            document.addEventListener('alpine:init', () => {
                Alpine.data('filterSidebar', (attributeIds = []) => ({
                    mobileOpen: window.innerWidth >= 768,
                    isDesktop: window.innerWidth >= 768,
                    categoriesOpen: true,
                    attributesOpen: attributeIds.reduce((acc, id) => {
                        acc[id] = true;
                        return acc;
                    }, {}),

                    init() {
                        this.checkDesktop();
                        window.addEventListener('resize', () => this.checkDesktop());
                    },

                    checkDesktop() {
                        this.isDesktop = window.innerWidth >= 768;
                        if (this.isDesktop) {
                            this.mobileOpen = true;
                        }
                    },

                    updateFilter() {},
                }));

                Alpine.data('productCountDisplay', (totalProducts, initialCount) => ({
                    totalProducts,
                    loadedProducts: initialCount,

                    updateCount(count) {
                        this.loadedProducts = count;
                    },

                    getDisplayText() {
                        if (this.loadedProducts >= this.totalProducts) {
                            return `Showing all ${this.totalProducts} products`;
                        }

                        return `Showing ${this.loadedProducts} of ${this.totalProducts} products`;
                    },
                }));

                Alpine.data('sumPrices', (initialState = {}) => ({
                    retail: initialState.retail ?? {},
                    advanced: Number(initialState.advanced ?? 0),
                    retail_delivery: Number(initialState.retail_delivery ?? initialState.retailDeliveryFee ?? 0),
                    retailDiscount: Number(initialState.retailDiscount ?? 0),

                    init() {
                        const sync = (field, value) => {
                            if (this?.$wire && typeof this.$wire.updateField === 'function') {
                                this.$wire.updateField(field, value);
                            }
                        };

                        this.$watch('retail', (value) => sync('retail', value), { deep: true });
                        this.$watch('advanced', (value) => sync('advanced', value));
                        this.$watch('retail_delivery', (value) => sync('retailDeliveryFee', value));
                        this.$watch('retailDiscount', (value) => sync('retailDiscount', value));
                    },

                    get subtotal() {
                        if (!this.retail || typeof this.retail !== 'object') {
                            return 0;
                        }

                        return Object.values(this.retail).reduce((total, item) => {
                            if (!item || typeof item !== 'object') {
                                return total;
                            }

                            return total + (parseFloat(item.price) || 0) * (parseInt(item.quantity) || 0);
                        }, 0);
                    },

                    format(price) {
                        return 'TK ' + (parseFloat(price) || 0).toLocaleString('en-US', { maximumFractionDigits: 0 });
                    },
                }));

                Alpine.data('shopInfiniteScroll', (initialPage = 1, initialHasMore = false, perPage = 20, totalProducts = 0) => ({
                    currentPage: initialPage,
                    hasMore: !!initialHasMore,
                    loading: false,
                    perPage,
                    totalProducts,
                    loadedProductIds: new Set(),
                    observer: null,

                    init() {
                        this.markInitialProducts();
                        this.$nextTick(() => {
                            this.updateProductCount();
                            this.setupIntersectionObserver();
                        });
                    },

                    markInitialProducts() {
                        const container = this.getContainer();
                        if (!container) {
                            return;
                        }

                        const ids = container.dataset.initialProducts;
                        if (!ids) {
                            return;
                        }

                        try {
                            JSON.parse(ids).forEach(id => this.loadedProductIds.add(Number(id)));
                        } catch (error) {
                            console.error('Failed to parse initial product IDs', error);
                        }
                    },

                    async loadProducts() {
                        if (!this.hasMore || this.loading) {
                            if (!this.hasMore) {
                                this.disconnectObserver();
                            }
                            return;
                        }

                        this.loading = true;

                        try {
                            const params = new URLSearchParams(window.location.search);
                            params.set('page', this.currentPage + 1);
                            params.set('per_page', this.perPage);
                            const shuffleSeed = this.getShuffleSeed();
                            if (shuffleSeed) {
                                params.set('shuffle', shuffleSeed);
                            }

                            const response = await fetch(`/api/shop/products?${params.toString()}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });

                            if (response.ok) {
                                const data = await response.json();
                                this.handleResponse(data);
                            } else {
                                this.hasMore = false;
                                this.disconnectObserver();
                            }
                        } catch (error) {
                            console.error('Error loading products:', error);
                            this.hasMore = false;
                            this.disconnectObserver();
                        } finally {
                            this.loading = false;
                        }
                    },

                    handleResponse(data) {
                        if (!data.data || !Array.isArray(data.data) || data.data.length === 0) {
                            this.hasMore = false;
                            this.disconnectObserver();
                            return;
                        }

                        const currentPage = data.pagination?.current_page || (this.currentPage + 1);
                        const lastPage = data.pagination?.last_page || 1;
                        const hasMorePages = currentPage < lastPage;

                        this.currentPage = currentPage;

                        const before = this.loadedProductIds.size;
                        this.appendProducts(data.data);
                        const after = this.loadedProductIds.size;
                        const newlyAdded = after - before;

                        if (!hasMorePages || after >= this.totalProducts || (newlyAdded === 0 && after >= this.totalProducts * 0.95)) {
                            this.hasMore = false;
                            this.disconnectObserver();
                        } else {
                            this.hasMore = hasMorePages;
                        }
                    },

                    appendProducts(products) {
                        const container = document.getElementById('products-container-shop');
                        if (!container) {
                            return;
                        }

                        products.forEach((product, index) => {
                            const productId = product.id || index;

                            if (this.loadedProductIds.has(productId)) {
                                return;
                            }

                            this.loadedProductIds.add(productId);
                            const element = this.createProductElement(product, index);
                            container.appendChild(element);
                            this.attachNavigationHandlers(element);
                        });

                        this.updateProductCount();

                        if (this.hasMore && this.observer) {
                            this.$nextTick(() => {
                                if (!this.observer) {
                                    return;
                                }
                                const trigger = this.$refs.loadMoreTrigger || this.$el.querySelector('.load-more-trigger');
                                if (trigger) {
                                    try {
                                        this.observer.unobserve(trigger);
                                    } catch (error) {
                                        console.error(error);
                                    }
                                    if (this.observer) {
                                    this.observer.observe(trigger);
                                    }
                                }
                            });
                        } else if (!this.hasMore) {
                            this.disconnectObserver();
                        }
                    },

                    updateProductCount() {
                        const countElement = document.getElementById('product-count-display');
                        if (countElement && countElement._x_dataStack && countElement._x_dataStack[0]) {
                            const alpineData = countElement._x_dataStack[0];
                            if (alpineData && typeof alpineData.updateCount === 'function') {
                                alpineData.updateCount(this.loadedProductIds.size);
                            }
                        }
                    },

                    createProductElement(product, index) {
                        const div = document.createElement('div');
                        div.className = 'products-list__item';
                        div.innerHTML = this.getProductHTML(product, index);
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

                    getProductHTML(product, index) {
                        const productId = product.id || index;
                        const productName = product.name || 'Product';
                        const productSlug = product.slug || productId;
                        const productPrice = product.price || 0;
                        const productSellingPrice = product.selling_price || productPrice;
                        const productImage = product.base_image_url || '/images/placeholder.jpg';
                        const productUrl = `/products/${encodeURIComponent(productSlug)}`;
                        const inStock = !product.should_track || (product.stock_count || 0) > 0;
                        const hasDiscount = productPrice !== productSellingPrice && productPrice > 0;
                        const discountPercent = hasDiscount ? Math.round(((productPrice - productSellingPrice) * 100) / productPrice) : 0;

                        const showOption = this.getShowOption();
                        const isOninda = this.getIsOninda();
                        const guestCanSeePrice = this.getGuestCanSeePrice();

                        const formatPrice = (price) => {
                            return `TK&nbsp;<span>${parseFloat(price).toLocaleString('en-US')}</span>`;
                        };

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

                        const discountText = (showOption.discount_text || '<small>Discount:</small> [percent]%').replace(/\[percent\]/g, discountPercent);

                        return `
                            <div class="product-card" data-id="${productId}" data-max="${product.should_track ? (product.stock_count || 0) : -1}">
                                <div class="product-card__badges-list">
                                    ${!inStock ? '<div class="product-card__badge product-card__badge--sale">Sold</div>' : ''}
                                    ${hasDiscount ? `<div class="product-card__badge product-card__badge--sale">${discountText}</div>` : ''}
                                </div>
                                <div class="product-card__image">
                                    <a href="${productUrl}" class="product-link" data-navigate>
                                        <img src="${productImage}" alt="Base Image" style="width: 100%; height: 100%;" loading="lazy">
                                    </a>
                                </div>
                                <div class="product-card__info">
                                    <div class="product-card__name">
                                        <a href="${productUrl}" class="product-link" data-navigate data-name="${product.var_name || productName}">${productName}</a>
                                    </div>
                                </div>
                                <div class="product-card__actions">
                                    <div class="product-card__availability">Availability:
                                        ${!product.should_track ?
                                            '<span class="text-success">In Stock</span>' :
                                            `<span class="text-${(product.stock_count || 0) > 0 ? 'success' : 'danger'}">${product.stock_count || 0} In Stock</span>`
                                        }
                                    </div>
                                    <div class="product-card__prices ${hasDiscount ? 'has-special' : ''}">
                                        ${priceHTML}
                                    </div>
                                    ${buttonsHTML}
                                </div>
                            </div>
                        `;
                    },

                    getContainer() {
                        return this.$el.querySelector('#products-container-shop');
                    },

                    getShowOption() {
                        const container = this.getContainer();
                        if (container && container.dataset.showOption) {
                            return JSON.parse(container.dataset.showOption);
                        }
                        return {};
                    },

                    getIsOninda() {
                        const container = this.getContainer();
                        return container && container.dataset.isOninda === 'true';
                    },

                    getGuestCanSeePrice() {
                        const container = this.getContainer();
                        return container && container.dataset.guestCanSeePrice === 'true';
                    },

                    getShuffleSeed() {
                        return this.$el?.dataset.shuffle || '';
                    },

                    setupIntersectionObserver() {
                        this.observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting && !this.loading && this.hasMore) {
                                    this.loadProducts();
                                } else if (!this.hasMore) {
                                    this.disconnectObserver();
                                }
                            });
                        }, {
                            root: null,
                            rootMargin: '200px',
                            threshold: 0.01,
                        });

                        this.$nextTick(() => {
                            const trigger = this.$refs.loadMoreTrigger || this.$el.querySelector('.load-more-trigger');
                            if (trigger && this.observer) {
                                this.observer.observe(trigger);
                            }
                        });
                    },

                    disconnectObserver() {
                        if (this.observer) {
                            try {
                                this.observer.disconnect();
                            } catch (error) {
                                console.error(error);
                            }
                            this.observer = null;
                        }
                    },
                }));
            });
        })();
    </script>
    @stack('scripts')
    @php
        function phone88($phone) {
            $phone = preg_replace('/[^\d]/', '', $phone);
            if (strlen($phone) == 11) {
                $phone = '88' . $phone;
            }
            return $phone;
        }
        $messenger = $company->messenger ?? '';
        $phone = phone88($company->whatsapp ?? '');
    @endphp
    @if ($phone && strlen($messenger) > 13)
    <div class="widget-connect widget-connect-right">
        @if($messenger)
        <a class="widget-connect__button widget-connect__button-telemessenger button-slide-out" style="background: white; color: blue;" href="{{$messenger}}" data-toggle="tooltip" data-placement="left" title="" target="_blank" data-original-title="Messenger">
            <i class="fab fa-facebook-messenger"></i>
        </a>
        @endif
        @if($phone)
        <a class="widget-connect__button widget-connect__button-whatsapp button-slide-out" style="background: white; color: green;" href="https://wa.me/{{$phone}}" data-toggle="tooltip" data-placement="left" title="" target="_blank" data-original-title="WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
        @endif
        <div class="widget-connect__button-activator" style="background-color: #ff0000;">
            <div class="widget-connect__button-activator-icon"></div>
        </div>
    </div>
    @elseif ($phone)
    <a
        href="https://api.whatsapp.com/send?phone={{$phone}}" target="_blank"
        style="position:fixed;width:60px;height:60px;bottom:40px;right:40px;background-color:#25d366;color:#FFF;border-radius:50px;text-align:center;font-size:30px;box-shadow: 2px 2px 3px #999;z-index:100;"
    >
        <i class="fab fa-whatsapp" style="margin-top: 1rem;"></i>
    </a>
    @elseif (strlen($messenger) > 13)
    <a
        href="{{$messenger}}" target="_blank"
        style="position:fixed;width:60px;height:60px;bottom:40px;right:40px;background-color:#0084ff;color:#FFF;border-radius:50px;text-align:center;font-size:30px;box-shadow: 2px 2px 3px #999;z-index:100;"
    >
        <i class="fab fa-facebook-messenger" style="margin-top: 1rem;"></i>
    </a>
    @endif
    <script>
        runWhenJQueryReady(function ($) {
            $(".widget-connect__button-activator-icon")
                .off('click.widgetConnect')
                .on('click.widgetConnect', function () {
                    $(this).toggleClass("active");
                    $(".widget-connect").toggleClass("active");
                    $("a.widget-connect__button").toggleClass("button-slide-out button-slide");
                });
        });
    </script>
    <script>
        document.addEventListener('click', function (event) {
            const zoomThumb = event.target.closest('.xzoom-thumbs a');

            if (zoomThumb) {
                event.preventDefault();
            }
        }, true);
    </script>
    <!-- Scripts -->
    <script>
        // Handle Facebook events
        document.addEventListener('facebookEvent', function(event) {
            // early return condition
            if (event.detail.length === 0) {
                return;
            }

            const { eventName, customData, eventId } = event.detail[0];

            // Track event with fbq
            fbq('track', eventName, customData, eventId);

            // Log for debugging
            console.log('Facebook Event Tracked:', {
                eventName,
                customData,
                eventID: eventId
            });
        });
    </script>
</body>

</html>
