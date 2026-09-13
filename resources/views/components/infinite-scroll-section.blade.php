@props(['section'])

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
    x-data="infiniteScroll({{ $section->id }})" data-section-id="{{ $section->id }}">

    @if ($section->type == 'pure-grid')
        <div class="block block-products-carousel">
            <div class="container">
                @if ($section->title ?? null)
                    <div class="block-header">
                        <h2 class="block-header__title" style="padding: 0.375rem 1rem;">
                            <a href="{{ route('home-sections.products', $section) }}"
                                wire:navigate.hover>{{ $section->title }}</a>
                        </h2>
                        <div class="block-header__divider"></div>
                        <a href="{{ route('products.index', ['filter_section' => $section->id]) }}"
                            class="ml-3 btn btn-sm btn-all" wire:navigate.hover>
                            View All
                        </a>
                    </div>
                @endif
                <div class="products-view__list products-list"
                    data-layout="grid-{{ optional($section->data)->cols ?? 5 }}-full" data-with-features="false">
                    <div class="products-list__body" id="products-container-{{ $section->id }}"
                        data-show-option="{{ json_encode([
                            'product_grid_button' => setting('show_option')->product_grid_button ?? 'add_to_cart',
                            'add_to_cart_icon' => setting('show_option')->add_to_cart_icon ?? '',
                            'add_to_cart_text' => setting('show_option')->add_to_cart_text ?? 'Add to Cart',
                            'order_now_icon' => setting('show_option')->order_now_icon ?? '',
                            'order_now_text' => setting('show_option')->order_now_text ?? 'Order Now',
                            'discount_text' => setting('discount_text') ?? '',
                        ]) }}"
                        data-is-oninda="{{ isOninda() ? 'true' : 'false' }}"
                        data-app-resell="{{ (app()->bound('app.resell') ? app('app.resell') : config('app.resell')) ? 'true' : 'false' }}"
                        data-guest-can-see-price="{{ (bool) (setting('show_option')->guest_can_see_price ?? false) ? 'true' : 'false' }}"
                        data-user-guest="{{ auth('user')->guest() ? 'true' : 'false' }}"
                        data-user-verified="{{ auth('user')->check() && auth('user')->user()->is_verified ? 'true' : 'false' }}">
                        <!-- Products will be loaded here by Alpine.js -->
                        <!-- Skeleton placeholders to prevent layout shift -->
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
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Loading trigger -->
    <div class="load-more-trigger" x-show="hasMore" x-ref="loadMoreTrigger" style="margin: 26px 0; text-align: center;">
        <button type="button" @click="loadProducts()" :disabled="loading" x-show="!loading" style="min-width:190px;min-height:47px;padding:10px 22px;border:0;border-radius:8px;background:var(--brand);color:#fff;font-size:14px;font-weight:700;cursor:pointer;">আরও পণ্য দেখুন</button>
        <div x-show="loading" class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
</div>


<script>
    function infiniteScroll(sectionId) {
        return {
            sectionId: sectionId,
            currentPage: 1,
            hasMore: true,
            loading: false,
            perPage: {{ strcasecmp(trim($section->title ?? ''), 'All Products') === 0 ? 100 : 20 }},
            loadedProductIds: new Set(),
            loadedProductSlugs: new Set(),
            requestedPages: new Set(),
            initialized: false,
            observer: null,

            init() {
                if (this.initialized) return;

                this.initialized = true;
                this.restoreExistingState();

                setTimeout(() => {
                    if (
                        this.hasMore &&
                        this.loadedProductIds.size === 0 &&
                        this.loadedProductSlugs.size === 0
                    ) {
                        this.loadProducts();
                    }

                    // Manual load enabled: products load only after button click.
                }, 100);
            },

            getContainer() {
                return document.querySelector(
                    `#products-container-${this.sectionId}`
                );
            },

            restoreExistingState() {
                const container = this.getContainer();
                if (!container) return;

                const savedNextPage = Number(container.dataset.nextPage || 1);

                if (
                    Number.isInteger(savedNextPage) &&
                    savedNextPage > 0
                ) {
                    this.currentPage = savedNextPage;
                }

                if (container.dataset.hasMore === 'false') {
                    this.hasMore = false;
                }

                container
                    .querySelectorAll('[data-product-id]')
                    .forEach((element) => {
                        const id = element.dataset.productId;

                        if (id) {
                            this.loadedProductIds.add(String(id));
                        }
                    });

                container
                    .querySelectorAll('a[href*="/products/"]')
                    .forEach((link) => {
                        try {
                            const url = new URL(
                                link.getAttribute('href'),
                                window.location.origin
                            );

                            const marker = '/products/';
                            const position = url.pathname.indexOf(marker);

                            if (position === -1) return;

                            const slug = decodeURIComponent(
                                url.pathname
                                    .slice(position + marker.length)
                                    .split('/')[0]
                            );

                            if (slug) {
                                this.loadedProductSlugs.add(slug);
                            }
                        } catch (error) {
                            // Ignore malformed links.
                        }
                    });
            },

            persistState() {
                const container = this.getContainer();
                if (!container) return;

                container.dataset.nextPage = String(this.currentPage);
                container.dataset.hasMore = this.hasMore ? 'true' : 'false';
            },

            async loadProducts() {
                if (this.loading || !this.hasMore) return;

                const requestedPage = Number(this.currentPage);

                if (
                    !Number.isInteger(requestedPage) ||
                    requestedPage < 1 ||
                    this.requestedPages.has(requestedPage)
                ) {
                    return;
                }

                this.requestedPages.add(requestedPage);
                this.loading = true;

                try {
                    const response = await fetch(
                        `/api/sections/${this.sectionId}/products?page=${requestedPage}&per_page=${this.perPage}`
                    );

                    if (response.ok) {
                        const data = await response.json();

                        if (data.data && Array.isArray(data.data)) {
                            const responsePage = Number(
                                data.pagination?.current_page || requestedPage
                            );

                            this.hasMore = Boolean(
                                data.pagination?.has_more
                            );

                            this.currentPage = responsePage + 1;
                            this.persistState();
                            this.appendProducts(data.data);

                            if (!this.hasMore) {
                                this.disconnectObserver();
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
                    this.hasMore = false;
                    this.disconnectObserver();
                }

                this.loading = false;
            },

            appendProducts(products) {
                const container = document.querySelector(`#products-container-${this.sectionId}`);
                if (!container) return;

                // Remove skeleton on first product load
                const skeleton = container.querySelector('.products-skeleton');
                if (skeleton && products.length > 0) {
                    skeleton.remove();
                }

                if (
                    skeleton &&
                    products.length === 0 &&
                    this.loadedProductIds.size === 0 &&
                    this.hasMore === false
                ) {
                    skeleton.remove();
                }

                products.forEach((product, index) => {
                    const productId = product.id == null
                        ? ''
                        : String(product.id);

                    const productSlug = product.slug == null
                        ? ''
                        : String(product.slug);

                    if (
                        (productId &&
                            this.loadedProductIds.has(productId)) ||
                        (productSlug &&
                            this.loadedProductSlugs.has(productSlug))
                    ) {
                        return;
                    }

                    if (productId) {
                        this.loadedProductIds.add(productId);
                    }

                    if (productSlug) {
                        this.loadedProductSlugs.add(productSlug);
                    }

                    const element = this.createProductElement(product, index);

                    if (productId) {
                        element.dataset.productId = productId;
                    }

                    if (productSlug) {
                        element.dataset.productSlug = productSlug;
                    }

                    container.appendChild(element);
                });
            },

            createProductElement(product, index) {
                const div = document.createElement('div');
                div.className = 'products-list__item';
                div.innerHTML = this.getProductHTML(product, index);
                return div;
            },

            getProductHTML(product, index) {
                const productId = product.id || index;
                const productName = product.name || 'Product';
                const productSlug = product.slug || productId;
                const productPrice = Number(product.price) || 0;
                const productSellingPrice = Number(product.selling_price) || productPrice;
                const productImage320 =
                    product.base_image_url_320 ||
                    product.base_image_url ||
                    '/images/placeholder.jpg';

                const productImage480 =
                    product.base_image_url_480 ||
                    productImage320;

                const productImageAlt = String(
                    product.base_image_alt || productName
                )
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;');
                const productUrl = `/products/${encodeURIComponent(productSlug)}`;
                const inStock = !product.should_track || (product.stock_count || 0) > 0;
                const hasDiscount = productPrice > 0 && productSellingPrice > 0 && productPrice > productSellingPrice;
                const discountPercent = hasDiscount ? Math.round(((productPrice - productSellingPrice) * 100) / productPrice) : 0;

                // Get button configuration from PHP (passed via data attributes)
                const showOption = this.getShowOption();

                let discountText = '';
                if (hasDiscount && productPrice > 0) {
                    const template = (showOption.discount_text || '').toString();
                    discountText = template.replace('[percent]', discountPercent);
                    if (!discountText.trim()) {
                        discountText = '';
                    }
                }
                const isOninda = this.getIsOninda();
                const guestCanSeePrice = this.getGuestCanSeePrice();

                const hasVariations = (product.variations_count !== undefined ? product.variations_count : (product.variations ? product.variations.length : 0)) > 1;

                // Generate buttons HTML
                let buttonsHTML = '';
                if (!isOninda) {
                    const available = inStock;
                    const disabledAttr = available ? '' : 'disabled';
                    const buttonType = showOption.product_grid_button || 'add_to_cart';

                    if (buttonType === 'add_to_cart') {
                        const text = showOption.add_to_cart_text || 'Add to Cart';
                        if (hasVariations) {
                            buttonsHTML = `
                                <a href="${productUrl}" wire:navigate.hover class="btn bb-btn-card product-card__addtocart w-100 d-flex align-items-center justify-content-center text-center gap-2" style="text-decoration: none; width: 100% !important; margin: 0 auto !important;">
                                    <i class="fas fa-cart-plus mr-1" style="font-size: 14px;"></i>
                                    <span>${text}</span>
                                </a>
                            `;
                        } else {
                            buttonsHTML = `
                                <button class="btn bb-btn-card product-card__addtocart w-100 d-flex align-items-center justify-content-center text-center gap-2" type="button" ${disabledAttr}
                                        data-product-id="${productId}" data-action="add" onclick="handleAddToCart(this)" style="width: 100% !important; margin: 0 auto !important;">
                                    <i class="fas fa-cart-plus mr-1" style="font-size: 14px;"></i>
                                    <span>${text}</span>
                                </button>
                            `;
                        }
                    } else if (buttonType === 'order_now') {
                        const text = showOption.order_now_text || 'অর্ডার করুন';
                        if (hasVariations) {
                            buttonsHTML = `
                                <a href="${productUrl}" wire:navigate.hover class="btn bb-btn-card-order product-card__ordernow w-100 d-flex align-items-center justify-content-center text-center gap-2" style="text-decoration: none; width: 100% !important; margin: 0 auto !important;">
                                    <i class="fas fa-bag-shopping mr-1" style="font-size: 14px;"></i>
                                    <span>${text}</span>
                                </a>
                            `;
                        } else {
                            buttonsHTML = `
                                <button class="btn bb-btn-card-order product-card__ordernow w-100 d-flex align-items-center justify-content-center text-center gap-2" type="button" ${disabledAttr}
                                        data-product-id="${productId}" data-action="kart" onclick="handleAddToCart(this)" style="width: 100% !important; margin: 0 auto !important;">
                                    <i class="fas fa-bag-shopping mr-1" style="font-size: 14px;"></i>
                                    <span>${text}</span>
                                </button>
                            `;
                        }
                    }
                }

                const userIsGuest = this.getUserIsGuest();
                const userIsVerified = this.getUserIsVerified();
                const shouldHidePrice = isOninda && !guestCanSeePrice && (userIsGuest || !userIsVerified);
                const appResell = this.getAppResell();

                const theMoney = (price) => `TK&nbsp;<span>${parseFloat(price || 0).toLocaleString('en-US')}</span>`;

                let priceHTML = '';
                if (isOninda && appResell) {
                    const retailPrice = product.retail_price || Math.round(productSellingPrice * 1.4);
                    const retailPriceHTML = `<div class="product-card__retail-price mb-1 d-flex justify-content-between align-items-center" style="font-size: 12px;">
                        <span style="color: #64748b; font-weight: 500;">Retail:</span>
                        <strong style="color: #0f172a;">${theMoney(retailPrice)}</strong>
                    </div>`;
                    let wholesalePriceHTML = '';

                    if (userIsGuest) {
                        wholesalePriceHTML = `<div class="product-card__wholesale-price d-flex align-items-baseline justify-content-between" style="font-size: 12px;">
                            <span style="color: #64748b; font-weight: 500;">Wholesale:</span>
                            <a href="{{ Route::has('auth.login') ? route('auth.login') : route('user.login') }}" style="color: #2563eb; font-weight: 700; text-decoration: none;">Login</a>
                        </div>`;
                    } else if (shouldHidePrice) {
                        wholesalePriceHTML = `<div class="product-card__wholesale-price">
                            <span class="text-danger font-weight-bold" style="font-size: 12px;">Verify account</span>
                        </div>`;
                    } else if (hasDiscount) {
                        wholesalePriceHTML = `<div class="product-card__wholesale-price d-flex align-items-baseline justify-content-between" style="font-size: 12px;">
                            <span class="font-weight-bold" style="color: var(--brand-dark); font-size: 15px;">${theMoney(productSellingPrice)}</span>
                            <span class="text-muted" style="text-decoration: line-through; font-size: 12px;">${theMoney(productPrice)}</span>
                        </div>`;
                    } else {
                        wholesalePriceHTML = `<div class="product-card__wholesale-price">
                            <span class="font-weight-bold" style="color: var(--brand-dark); font-size: 15px;">${theMoney(productPrice)}</span>
                        </div>`;
                    }

                    priceHTML = `${retailPriceHTML}${wholesalePriceHTML}`;
                } else {
                    if (shouldHidePrice) {
                        priceHTML = `<span class="text-danger font-weight-bold" style="font-size: 12px;">${
                            userIsGuest ? 'Login to see price' : 'Verify account'
                        }</span>`;
                    } else if (hasDiscount) {
                        priceHTML = `
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="product-card__new-price font-weight-bold" style="color: var(--brand-dark); font-size: 16px;">
                                    ${theMoney(productSellingPrice)}
                                </span>
                                <span class="product-card__old-price" style="color: #94a3b8; text-decoration: line-through; font-size: 13px;">
                                    ${theMoney(productPrice)}
                                </span>
                            </div>
                        `;
                    } else {
                        priceHTML = `
                            <div class="d-flex align-items-baseline">
                                <span class="product-card__new-price font-weight-bold" style="color: var(--brand-dark); font-size: 16px;">
                                    ${productPrice ? theMoney(productPrice) : 'Contact for price'}
                                </span>
                            </div>
                        `;
                    }
                }

                return `
                    <div class="product-card bb-modern-card" data-id="${productId}" data-max="${product.should_track ? (product.stock_count || 0) : -1}">
                        <!-- Card Top Badges -->
                        <div class="bb-card-badges d-flex justify-content-between align-items-center w-100 position-absolute" style="top: 14px; left: 0; padding: 0 14px; z-index: 5; pointer-events: none;">
                            <div>
                                ${!inStock ? 
                                    '<span class="badge badge-danger px-2 py-1 font-weight-bold" style="border-radius: 3px; font-size: 10px; text-transform: uppercase;">Sold Out</span>' : 
                                    (hasDiscount && discountPercent > 0 ? `<span class="badge text-white font-weight-bold" style="background: #ef4444; border-radius: 3px; padding: 3px 6px; font-size: 11px; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);">${discountText || ('-' + discountPercent + '%')}</span>` : '')
                                }
                            </div>
                            <div>
                                ${product.free_delivery ? '<span class="badge text-dark font-weight-bold d-flex align-items-center gap-1" style="background: #ecfdf5; color: #059669 !important; border: 1px solid #a7f3d0; border-radius: 3px; padding: 3px 6px; font-size: 10px;"><i class="fas fa-truck-fast"></i> Free</span>' : ''}
                            </div>
                        </div>

                        <!-- Image Container with soft backdrop and rounded inner frame -->
                        <div class="product-card__image bb-card-img-shell" style="aspect-ratio: 1 / 1; overflow: hidden; position: relative; margin: 8px 8px 0; border-radius: 4px; background: #f8fafc;">
                            <a href="${productUrl}" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; padding: 10px;" wire:navigate.hover>
                                <img
                                    src="${productImage320}"
                                    srcset="${productImage320} 320w, ${productImage480} 480w"
                                    sizes="(max-width: 575px) 50vw, (max-width: 991px) 33vw, (max-width: 1199px) 25vw, 220px"
                                    alt="${productImageAlt}"
                                    width="480"
                                    height="480"
                                    loading="lazy"
                                    decoding="async"
                                    class="bb-product-img"
                                    style="max-width: 100%; max-height: 100%; object-fit: contain; transition: transform 0.3s ease;"
                                >
                            </a>
                        </div>

                        <!-- Product Details -->
                        <div class="product-card__info" style="display: flex; flex-direction: column; padding: 8px 10px 0; flex: 0 0 auto;">
                            <!-- Product Name -->
                            <div class="product-card__name" style="flex: 0 0 auto; min-height: auto; margin-bottom: 2px !important;">
                                <a href="${productUrl}" data-name="${product.var_name || productName}" class="bb-product-title font-weight-bold" style="font-size: 13.5px; line-height: 1.35; color: #1e293b; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-decoration: none; min-height: auto;" wire:navigate.hover>
                                    ${productName}
                                </a>
                            </div>

                            ${this.getRatingHTML ? this.getRatingHTML(product) : ''}

                            <!-- Prices Row Container (Dedicated Line 1) -->
                            <div class="product-card__prices" style="margin-top: 2px !important; margin-bottom: 8px !important; padding: 0 !important; flex: 0 0 auto;">
                                ${priceHTML}
                            </div>
                        </div>

                        <!-- Actions / Buttons Row (Dedicated Line 2: Full width centered button) -->
                        <div class="product-card__actions" style="padding: 0 10px 10px; width: 100%; display: flex; justify-content: center; align-items: center; margin-top: auto; box-sizing: border-box;">
                            ${!isOninda ? `
                                <div class="product-card__buttons w-100 d-flex justify-content-center align-items-center" style="margin: 0 !important; width: 100% !important;">
                                    ${buttonsHTML}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            },

            getShowOption() {
                // Get show option from the component's data attributes
                const container = document.querySelector(`#products-container-${this.sectionId}`);
                if (container && container.dataset.showOption) {
                    try {
                        return JSON.parse(container.dataset.showOption);
                    } catch (e) {
                        console.error('Error parsing showOption:', e);
                    }
                }
                // Fallback to default values
                return {
                    product_grid_button: 'add_to_cart',
                    add_to_cart_icon: '',
                    add_to_cart_text: 'Add to Cart',
                    order_now_icon: '',
                    order_now_text: 'Order Now'
                };
            },

            getIsOninda() {
                // Get isOninda value from the component's data attributes
                const container = document.querySelector(`#products-container-${this.sectionId}`);
                return container && container.dataset.isOninda === 'true';
            },

            getAppResell() {
                // Get appResell value from the component's data attributes
                const container = document.querySelector(`#products-container-${this.sectionId}`);
                return container && container.dataset.appResell === 'true';
            },

            getGuestCanSeePrice() {
                // Get guest_can_see_price value from the component's data attributes
                const container = document.querySelector(`#products-container-${this.sectionId}`);
                return container && container.dataset.guestCanSeePrice === 'true';
            },

            getUserIsGuest() {
                const container = document.querySelector(`#products-container-${this.sectionId}`);
                return container && container.dataset.userGuest === 'true';
            },

            getUserIsVerified() {
                const container = document.querySelector(`#products-container-${this.sectionId}`);
                return container && container.dataset.userVerified === 'true';
            },

            getRatingHTML(product) {
                const averageRating = product.average_rating || 0;
                const totalReviews = product.total_reviews || 0;

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
                // Prevent multiple observers after browser Back or Alpine re-init.
                this.disconnectObserver();

                if (!this.hasMore) return;

                this.observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !this.loading && this.hasMore) {
                            this.loadProducts();
                        }
                    });
                }, {
                    root: null,
                    rootMargin: '100px',
                    threshold: 0.1
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
        }
    }

    // Global handleAddToCart moved to master layout

    // Legacy function for backward compatibility
    window.addToCart = function(productId, action = 'add') {
        // Create a temporary button element to use the new handler
        const tempButton = document.createElement('button');
        tempButton.setAttribute('data-product-id', productId);
        tempButton.setAttribute('data-action', action);
        window.handleAddToCart(tempButton);
    };
</script>
