@props(['section', 'initialProducts' => null])
@php
    $initialProducts = $initialProducts ?? $section->products();
    $initialProductIds = $initialProducts ? $initialProducts->pluck('id')->values()->all() : [];
@endphp

@push('styles')
<style>
    @keyframes skeleton-pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
    .product-card-skeleton {
        animation: skeleton-pulse 1.5s ease-in-out infinite;
    }

    .infinite-scroll-section .products-skeleton {
        display: grid;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        gap: 1rem;
        grid-template-columns: repeat(var(--skeleton-cols, 5), minmax(0, 1fr));
    }

    .infinite-scroll-section .products-skeleton > * {
        min-width: 0;
    }

    @media (max-width: 767.98px) {
        .infinite-scroll-section .products-skeleton {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>
@endpush

<div class="infinite-scroll-section"
    x-data="infiniteScroll({
        sectionId: {{ $section->id }},
        initialProductIds: {{ json_encode($initialProductIds) }},
        showOption: {{ json_encode([
            'product_grid_button' => setting('show_option')->product_grid_button ?? 'add_to_cart',
            'add_to_cart_icon' => setting('show_option')->add_to_cart_icon ?? '',
            'add_to_cart_text' => setting('show_option')->add_to_cart_text ?? 'Add to Cart',
            'order_now_icon' => setting('show_option')->order_now_icon ?? '',
            'order_now_text' => setting('show_option')->order_now_text ?? 'Order Now',
            'discount_text' => setting('discount_text') ?? '',
            'guest_can_see_price' => (bool) (setting('show_option')->guest_can_see_price ?? false),
        ]) }},
        isOninda: {{ isOninda() ? 'true' : 'false' }},
        appResell: {{ (app()->bound('app.resell') ? app('app.resell') : config('app.resell')) ? 'true' : 'false' }},
        userIsGuest: {{ auth('user')->guest() ? 'true' : 'false' }},
        userIsVerified: {{ auth('user')->check() && auth('user')->user()->is_verified ? 'true' : 'false' }},
        loginUrl: '{{ Route::has('auth.login') ? route('auth.login') : route('user.login') }}'
    })"
    data-section-id="{{ $section->id }}">

    @if ($section->type == 'pure-grid')
        <div class="block block-products-carousel">
            <div class="container">
                @if ($section->title ?? null)
                    <div class="block-header">
                        <h1 class="block-header__title" style="padding: 0.375rem 1rem;">
                            <a href="{{ route('home-sections.products', $section) }}"
                                wire:navigate.hover>{{ $section->title }}</a>
                        </h1>
                        <div class="block-header__divider"></div>
                        <a href="{{ route('products.index', ['filter_section' => $section->id]) }}"
                            class="ml-3 btn btn-sm btn-all" wire:navigate.hover>
                            View All
                        </a>
                    </div>
                @endif
                <div class="products-view__list products-list"
                    data-layout="grid-{{ optional($section->data)->cols ?? 5 }}-full" data-with-features="false">
                    <div class="products-list__body" id="products-container-{{ $section->id }}">
                        @if($initialProducts && $initialProducts->isNotEmpty())
                            @foreach($initialProducts as $product)
                                <div class="products-list__item" data-product-id="{{ $product->id }}">
                                    @include('partials.products.item', ['product' => $product])
                                </div>
                            @endforeach
                        @else
                            <!-- Skeleton placeholders if no initial products -->
                            <div class="products-skeleton" style="--skeleton-cols: {{ optional($section->data)->cols ?? 5 }};">
                                @for($i = 0; $i < (optional($section->data)->cols ?? 5); $i++)
                                    <div class="product-card-skeleton" style="aspect-ratio: 1 / 1.2; background: #f0f0f0; border-radius: 8px;">
                                        <div style="aspect-ratio: 1 / 1; background: #e0e0e0; border-radius: 8px 8px 0 0;"></div>
                                        <div style="padding: 0.75rem;">
                                            <div style="height: 16px; background: #e0e0e0; border-radius: 4px; margin-bottom: 0.5rem;"></div>
                                            <div style="height: 14px; background: #e0e0e0; border-radius: 4px; width: 60%;"></div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Loading trigger -->
    <div class="load-more-trigger" x-show="hasMore" x-ref="loadMoreTrigger" style="min-height: 40px; margin: 20px 0; display: flex; justify-content: center; align-items: center;">
        <div x-show="loading" class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
</div>

<script>
    if (typeof window.infiniteScroll !== 'function') {
        window.infiniteScroll = function(config) {
            const initialIds = Array.isArray(config.initialProductIds) ? config.initialProductIds.map(Number) : [];
            const perPage = 20;
            const initialCount = initialIds.length;
            const initialPage = initialCount > 0 ? Math.floor(initialCount / perPage) + 1 : 1;

            return {
                sectionId: config.sectionId,
                showOption: config.showOption || {},
                isOninda: Boolean(config.isOninda),
                appResell: Boolean(config.appResell),
                userIsGuest: Boolean(config.userIsGuest),
                userIsVerified: Boolean(config.userIsVerified),
                loginUrl: config.loginUrl || '/login',
                currentPage: initialPage,
                hasMore: true,
                loading: false,
                perPage: perPage,
                loadedProductIds: new Set(initialIds),
                observer: null,

                init() {
                    if (this.loadedProductIds.size === 0) {
                        this.loadProducts();
                    }
                    this.setupIntersectionObserver();
                },

                async loadProducts() {
                    if (this.loading || !this.hasMore) return;

                    this.loading = true;

                    try {
                        const response = await fetch(
                            `/api/sections/${this.sectionId}/products?page=${this.currentPage}&per_page=${this.perPage}`
                        );

                        if (response.ok) {
                            const data = await response.json();

                            if (data.data && Array.isArray(data.data)) {
                                this.hasMore = Boolean(data.pagination?.has_more);
                                this.currentPage++;
                                const addedCount = this.appendProducts(data.data);

                                if (!this.hasMore) {
                                    this.disconnectObserver();
                                } else if (addedCount === 0) {
                                    this.loading = false;
                                    return this.loadProducts();
                                }
                            } else if (Array.isArray(data)) {
                                this.hasMore = false;
                                this.appendProducts(data);
                                this.disconnectObserver();
                            } else {
                                this.hasMore = false;
                                this.disconnectObserver();
                            }
                        } else {
                            this.hasMore = false;
                            this.disconnectObserver();
                        }
                    } catch (error) {
                        console.error('Error loading products for section ' + this.sectionId, error);
                        this.hasMore = false;
                        this.disconnectObserver();
                    } finally {
                        this.loading = false;
                    }
                },

                appendProducts(products) {
                    const container = document.querySelector(`#products-container-${this.sectionId}`);
                    if (!container) return 0;

                    const skeleton = container.querySelector('.products-skeleton');
                    if (skeleton) {
                        skeleton.remove();
                    }

                    let addedCount = 0;
                    products.forEach((product, index) => {
                        const productId = Number(product.id || index);

                        if (this.loadedProductIds.has(productId)) return;

                        this.loadedProductIds.add(productId);
                        const element = this.createProductElement(product, index);
                        container.appendChild(element);
                        addedCount++;
                    });

                    return addedCount;
                },

                createProductElement(product, index) {
                    const div = document.createElement('div');
                    div.className = 'products-list__item';
                    div.setAttribute('data-product-id', product.id);
                    div.innerHTML = this.getProductHTML(product, index);
                    return div;
                },

                formatMoney(amount) {
                    const num = Number(amount || 0);
                    return 'TK&nbsp;<span>' + num.toLocaleString() + '</span>';
                },

                getProductHTML(product, index) {
                    const productId = product.id || index;
                    const productName = product.name || 'Product';
                    const productSlug = product.slug || productId;
                    const productPrice = Number(product.price || 0);
                    const productSellingPrice = Number(product.selling_price || productPrice);
                    const productImage = product.base_image_url || '/images/placeholder.jpg';
                    const productUrl = `/products/${encodeURIComponent(productSlug)}`;
                    const hasDiscount = productPrice !== productSellingPrice;

                    let discountText = '';
                    if (hasDiscount && productPrice > 0) {
                        const discountPercent = Math.round(((productPrice - productSellingPrice) * 100) / productPrice);
                        const template = (this.showOption.discount_text || '').toString();
                        discountText = template.replace('[percent]', discountPercent);
                        if (!discountText.trim()) {
                            discountText = '';
                        }
                    }

                    const hasVariations = (product.variations_count !== undefined ? product.variations_count : (product.variations ? product.variations.length : 0)) > 1;

                    let buttonsHTML = '';
                    if (!this.isOninda) {
                        const buttonType = this.showOption.product_grid_button || 'add_to_cart';

                        if (buttonType === 'add_to_cart') {
                            if (hasVariations) {
                                buttonsHTML = `
                                    <div class="product-card__buttons">
                                        <a class="btn btn-primary product-card__addtocart" href="${productUrl}" wire:navigate.hover style="text-decoration: none;">
                                            ${this.showOption.add_to_cart_icon || ''}
                                            <span class="ml-1">${this.showOption.add_to_cart_text || 'Add to Cart'}</span>
                                        </a>
                                    </div>
                                `;
                            } else {
                                buttonsHTML = `
                                    <div class="product-card__buttons">
                                        <button class="btn btn-primary product-card__addtocart" type="button"
                                                data-product-id="${productId}" data-action="add" onclick="handleAddToCart(this)">
                                            ${this.showOption.add_to_cart_icon || ''}
                                            <span class="ml-1">${this.showOption.add_to_cart_text || 'Add to Cart'}</span>
                                        </button>
                                    </div>
                                `;
                            }
                        } else if (buttonType === 'order_now') {
                            if (hasVariations) {
                                buttonsHTML = `
                                    <div class="product-card__buttons">
                                        <a class="btn btn-primary product-card__ordernow order-now-drift" href="${productUrl}" wire:navigate.hover style="text-decoration: none;">
                                            ${this.showOption.order_now_icon || ''}
                                            <span class="ml-1">${this.showOption.order_now_text || 'Order Now'}</span>
                                        </a>
                                    </div>
                                `;
                            } else {
                                buttonsHTML = `
                                    <div class="product-card__buttons">
                                        <button class="btn btn-primary product-card__ordernow order-now-drift" type="button"
                                                data-product-id="${productId}" data-action="kart" onclick="handleAddToCart(this)">
                                            ${this.showOption.order_now_icon || ''}
                                            <span class="ml-1">${this.showOption.order_now_text || 'Order Now'}</span>
                                        </button>
                                    </div>
                                `;
                            }
                        }
                    }

                    const shouldHidePrice = this.isOninda && !this.showOption.guest_can_see_price && (this.userIsGuest || !this.userIsVerified);

                    let priceHTML = '';
                    if (this.isOninda && this.appResell) {
                        const retailPrice = product.retail_price || Math.round(productSellingPrice * 1.4);
                        const retailPriceHTML = `<div class="product-card__retail-price" style="margin-bottom: 2px;">
                            <span style="color: #6b7280; font-weight: 500;">Retail price:</span>
                            <span style="font-weight: 700; color: #111827;">${this.formatMoney(retailPrice)}</span>
                        </div>`;
                        let wholesalePriceHTML = '';

                        if (this.userIsGuest) {
                            wholesalePriceHTML = `<div class="product-card__wholesale-price">
                                <span style="color: #6b7280; font-weight: 500;">Wholesale price:</span>
                                <a href="${this.loginUrl}" style="color: #2563eb; font-weight: 700; text-decoration: none; border-bottom: 1px dashed #2563eb; padding-bottom: 1px;">Login</a>
                            </div>`;
                        } else if (shouldHidePrice) {
                            wholesalePriceHTML = `<div class="product-card__wholesale-price">
                                <span class="product-card__new-price text-danger" style="font-weight: 700; font-size: 12px;">Verify account to see price</span>
                            </div>`;
                        } else if (hasDiscount) {
                            wholesalePriceHTML = `<div class="product-card__wholesale-price">
                                <span class="product-card__new-price" style="font-weight: 700;">${this.formatMoney(productSellingPrice)}</span>
                                <span class="product-card__old-price" style="margin-left: 4px;">${this.formatMoney(productPrice)}</span>
                            </div>`;
                        } else {
                            wholesalePriceHTML = `<div class="product-card__wholesale-price">
                                <span style="font-weight: 700; color: #111827;">${this.formatMoney(productPrice)}</span>
                            </div>`;
                        }

                        priceHTML = `${retailPriceHTML}${wholesalePriceHTML}`;
                    } else {
                        if (shouldHidePrice) {
                            priceHTML = `<span class="product-card__new-price text-danger">${
                                this.userIsGuest ? 'Login to see price' : 'Verify account to see price'
                            }</span>`;
                        } else if (hasDiscount) {
                            priceHTML =
                                `<span class="product-card__new-price">${this.formatMoney(productSellingPrice)}</span><span class="product-card__old-price">${this.formatMoney(productPrice)}</span>`;
                        } else {
                            priceHTML = `${this.formatMoney(productPrice)}`;
                        }
                    }

                    return `
                        <div class="product-card" data-id="${productId}" data-max="-1">
                            ${product.free_delivery ? '<div class="product-card__ribbon"><span class="badge badge--free-delivery">Free Delivery</span></div>' : ''}
                            <div class="product-card__badges-list">
                                ${discountText ? `<div class="product-card__badge product-card__badge--sale">${discountText}</div>` : ''}
                            </div>
                            <div class="product-card__image" style="aspect-ratio: 1 / 1; overflow: hidden;">
                                <a href="${productUrl}" class="product-link" wire:navigate.hover style="display: block; width: 100%; height: 100%;">
                                    <img src="${productImage}" alt="${productName}" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                                </a>
                            </div>
                            <div class="product-card__info">
                                <div class="product-card__name">
                                    <a href="${productUrl}" class="product-link" wire:navigate.hover data-name="${product.var_name || productName}">${productName}</a>
                                </div>
                                ${this.getRatingHTML(product)}
                            </div>
                            <div class="product-card__actions">
                                <div class="product-card__availability">Availability:
                                    ${!product.should_track ?
                                        '<span class="text-success">In Stock</span>' :
                                        `<span class="text-${(product.stock_count || 0) ? 'success' : 'danger'}">${product.stock_count || 0} In Stock</span>`
                                    }
                                </div>
                                <div class="product-card__prices ${hasDiscount ? 'has-special' : ''}" style="font-size: 13px; font-weight: normal; line-height: 1.5; margin-top: 4px;">
                                    ${priceHTML}
                                </div>
                                ${buttonsHTML}
                            </div>
                        </div>
                    `;
                },

                getRatingHTML(product) {
                    const averageRating = Number(product.average_rating || 0);
                    const totalReviews = Number(product.total_reviews || 0);

                    if (averageRating <= 0) {
                        return '';
                    }

                    let starsHTML = '';
                    for (let i = 1; i <= 5; i++) {
                        if (i <= Math.floor(averageRating)) {
                            starsHTML += '<i class="fa fa-star text-warning" style="font-size: 0.75rem;"></i>';
                        } else if (i - 0.5 <= averageRating) {
                            starsHTML += '<i class="fa fa-star-half-alt text-warning" style="font-size: 0.75rem;"></i>';
                        } else {
                            starsHTML += '<i class="far fa-star text-muted" style="font-size: 0.75rem;"></i>';
                        }
                    }

                    const reviewText = totalReviews === 1 ? 'review' : 'reviews';

                    return `
                        <div class="gap-2 d-flex align-items-center" style="font-size: 0.875rem;">
                            <div class="d-flex align-items-center" style="margin-top: -1px;">
                                ${starsHTML}
                            </div>
                            <span class="text-muted small" style="margin-top: 1px;">
                                <strong>${averageRating.toFixed(1)}</strong>
                                (${totalReviews} ${reviewText})
                            </span>
                        </div>
                    `;
                },

                setupIntersectionObserver() {
                    if (this.observer) {
                        this.observer.disconnect();
                    }

                    this.observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting && !this.loading && this.hasMore) {
                                this.loadProducts();
                            }
                        });
                    }, {
                        root: null,
                        rootMargin: '350px',
                        threshold: 0.01
                    });

                    this.$nextTick(() => {
                        const trigger = this.$refs.loadMoreTrigger;
                        if (trigger) {
                            this.observer.observe(trigger);
                        }
                    });
                },

                disconnectObserver() {
                    if (this.observer) {
                        this.observer.disconnect();
                        this.observer = null;
                    }
                }
            };
        };
    }
</script>
