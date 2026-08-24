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
                const productPrice = product.price || 0;
                const productSellingPrice = product.selling_price || productPrice;
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
                const hasDiscount = productPrice !== productSellingPrice;

                // Get button configuration from PHP (passed via data attributes)
                const showOption = this.getShowOption();

                let discountText = '';
                if (hasDiscount && productPrice > 0) {
                    const discountPercent = Math.round(((productPrice - productSellingPrice) * 100) / productPrice);
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
                        if (hasVariations) {
                            buttonsHTML = `
                                     <div class="product-card__buttons">
                                         <a class="btn btn-primary product-card__addtocart" href="${productUrl}" wire:navigate.hover style="text-decoration: none;">
                                             ${showOption.add_to_cart_icon || ''}
                                             <span class="ml-1">${showOption.add_to_cart_text || 'Add to Cart'}</span>
                                         </a>
                                     </div>
                                 `;
                        } else {
                            buttonsHTML = `
                                     <div class="product-card__buttons">
                                         <button class="btn btn-primary product-card__addtocart" type="button" ${disabledAttr}
                                                 data-product-id="${productId}" data-action="add" onclick="handleAddToCart(this)">
                                             ${showOption.add_to_cart_icon || ''}
                                             <span class="ml-1">${showOption.add_to_cart_text || 'Add to Cart'}</span>
                                         </button>
                                     </div>
                                 `;
                        }
                    } else if (buttonType === 'order_now') {
                        if (hasVariations) {
                            buttonsHTML = `
                                     <div class="product-card__buttons">
                                         <a class="btn btn-primary product-card__ordernow order-now-drift" href="${productUrl}" wire:navigate.hover style="text-decoration: none;">
                                             ${showOption.order_now_icon || ''}
                                             <span class="ml-1">${showOption.order_now_text || 'Order Now'}</span>
                                         </a>
                                     </div>
                                 `;
                        } else {
                            buttonsHTML = `
                                     <div class="product-card__buttons">
                                         <button class="btn btn-primary product-card__ordernow order-now-drift" type="button" ${disabledAttr}
                                                 data-product-id="${productId}" data-action="kart" onclick="handleAddToCart(this)">
                                             ${showOption.order_now_icon || ''}
                                             <span class="ml-1">${showOption.order_now_text || 'Order Now'}</span>
                                         </button>
                                     </div>
                                 `;
                        }
                    }
                }

                const userIsGuest = this.getUserIsGuest();
                const userIsVerified = this.getUserIsVerified();
                const shouldHidePrice = isOninda && !guestCanSeePrice && (userIsGuest || !userIsVerified);
                const appResell = this.getAppResell();

                let priceHTML = '';
                if (isOninda && appResell) {
                    const retailPrice = product.retail_price || Math.round(productSellingPrice * 1.4);
                    const retailPriceHTML = `<div class="product-card__retail-price" style="margin-bottom: 2px;">
                        <span style="color: #6b7280; font-weight: 500;">Retail price:</span>
                        <span style="font-weight: 700; color: #111827;">Tk. ${retailPrice}</span>
                    </div>`;
                    let wholesalePriceHTML = '';

                    if (userIsGuest) {
                        wholesalePriceHTML = `<div class="product-card__wholesale-price">
                            <span style="color: #6b7280; font-weight: 500;">Wholesale price:</span>
                            <a href="{{ Route::has('auth.login') ? route('auth.login') : route('user.login') }}" style="color: #2563eb; font-weight: 700; text-decoration: none; border-bottom: 1px dashed #2563eb; padding-bottom: 1px;">Login</a>
                        </div>`;
                    } else if (shouldHidePrice) {
                        wholesalePriceHTML = `<div class="product-card__wholesale-price">
                            <span class="product-card__new-price text-danger" style="font-weight: 700; font-size: 12px;">Verify account to see price</span>
                        </div>`;
                    } else if (hasDiscount) {
                        wholesalePriceHTML = `<div class="product-card__wholesale-price">
                            <span class="product-card__new-price" style="font-weight: 700;">Tk. ${productSellingPrice}</span>
                            <span class="product-card__old-price" style="margin-left: 4px;">Tk. ${productPrice}</span>
                        </div>`;
                    } else {
                        wholesalePriceHTML = `<div class="product-card__wholesale-price">
                            <span style="font-weight: 700; color: #111827;">Tk. ${productPrice}</span>
                        </div>`;
                    }

                    priceHTML = `${retailPriceHTML}${wholesalePriceHTML}`;
                } else {
                    if (shouldHidePrice) {
                        priceHTML = `<span class="product-card__new-price text-danger">${
                                 userIsGuest ? 'Login to see price' : 'Verify account to see price'
                             }</span>`;
                    } else if (hasDiscount) {
                        priceHTML =
                            `<span class="product-card__new-price">Tk. ${productSellingPrice}</span><span class="product-card__old-price">Tk. ${productPrice}</span>`;
                    } else {
                        priceHTML = `Tk. ${productPrice}`;
                    }
                }

                return `
                         <div class="product-card" data-id="${productId}" data-max="${product.should_track ? (product.stock_count || 0) : -1}">
                             ${product.free_delivery ? '<div class="product-card__ribbon"><span class="badge badge--free-delivery">Free Delivery</span></div>' : ''}
                             <div class="product-card__badges-list">
                                ${!inStock ? '<div class="product-card__badge product-card__badge--sale">Sold</div>' : ''}
                             </div>
                             <div class="product-card__image" style="aspect-ratio: 1 / 1; overflow: hidden;">
                                 <a href="${productUrl}" class="product-link" wire:navigate.hover style="display: block; width: 100%; height: 100%;">
                                     <img
    src="${productImage320}"
    srcset="${productImage320} 320w, ${productImage480} 480w"
    sizes="(max-width: 575px) 50vw, (max-width: 991px) 33vw, (max-width: 1199px) 25vw, 220px"
    alt="${productImageAlt}"
    width="480"
    height="480"
    loading="lazy"
    decoding="async"
    style="width: 100%; height: 100%; object-fit: cover;"
>
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
                                  <div class="product-card__prices ${hasDiscount ? 'has-special' : ''}" ${isOninda && appResell ? 'style="font-size: 13px; font-weight: normal; line-height: 1.5; margin-top: 4px;"' : ''}>
                                     ${priceHTML}
                                  </div>
                                 ${buttonsHTML}
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
