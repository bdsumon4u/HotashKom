// Storefront components: scroll handlers, add to cart, pagination, Alpine components
runWhenJQueryReady(function($) {
    // Attach dataLayer event listener only once globally
    if (!window.__dataLayerListenerAttached) {
        $(window).on('dataLayer.storefront', function(ev) {
            for (let item of ev.detail) {
                window.dataLayer.push(item);
            }
        });
        window.__dataLayerListenerAttached = true;
    }

    function onScroll() {
        const scrollTop = $(window).scrollTop();

        if (scrollTop > 32) {
            $('.site__header.position-fixed .topbar').hide();
        } else {
            $('.site__header.position-fixed .topbar').show();
        }

        if (scrollTop > 100) {
            $('.departments').each(function() {
                const $departments = $(this);
                const instance = $departments.data('departmentsInstance');

                if ($departments.is('.departments--opened')) {
                    $departments.find('.departments__body').css('overflow', 'hidden');
                    $departments.find('.departments__links-wrapper').css('overflow', 'hidden');

                    if (instance && typeof instance.close === 'function') {
                        instance.close();
                    } else {
                        $departments.removeClass('departments--opened departments--transition');
                        $departments.find('.departments__links-wrapper').css('height', '');
                    }

                    setTimeout(() => {
                        $departments.find('.departments__body').css('overflow', '');
                        $departments.find('.departments__links-wrapper').css('overflow', '');
                    }, 350);
                }

                $departments.removeClass('departments--fixed');
                $departments.find('.departments__body').css('height', '').attr('style', '');
            });
        } else {
            if ($('.departments').data('departments-fixed-by') !== '') {
                $('.departments').addClass('departments--opened departments--fixed');
                $('.departments__body, .departments__links-wrapper').css('overflow', '');
            }
            $('.departments--opened.departments--fixed .departments__body').css('min-height', '458px');
        }
    }

    let scrollHandler = null;
    if (typeof onScroll === 'function') {
        scrollHandler = function(e) {
            try {
                onScroll.call(window, e);
            } catch(err) {
                console.error('Scroll handler error:', err);
            }
        };
        window.removeEventListener('scroll', scrollHandler, { passive: true });
        window.addEventListener('scroll', scrollHandler, { passive: true });
    }
    $(window).off('scroll.siteHeader').on('scroll.siteHeader', onScroll);
    onScroll();
});

// Livewire navigation error handling
(function() {
    if (window.Livewire) {
        document.addEventListener('livewire:navigate-error', function(e) {
            const error = e.detail?.error || e.error;
            const targetUrl = e.detail?.url || window.location.href;

            if (error) {
                const status = error.status || error.response?.status || 0;

                if (status >= 500 || status === 0 || status === 520 || status === 522) {
                    console.warn('Server error during SPA navigation, falling back to full page reload:', status, targetUrl);

                    const activeLink = document.querySelector('a[wire\\:navigate][href="' + targetUrl + '"]');
                    if (activeLink) {
                        activeLink.removeAttribute('wire:navigate');
                    }

                    setTimeout(() => {
                        window.location.href = targetUrl;
                    }, 500);
                }
            }
        });

        let navigationStartTime = null;
        let navigationTimeout = null;

        document.addEventListener('livewire:navigate', function(e) {
            navigationStartTime = Date.now();
            const targetUrl = e.detail?.url || window.location.href;

            if (navigationTimeout) {
                clearTimeout(navigationTimeout);
            }

            navigationTimeout = setTimeout(() => {
                if (navigationStartTime && (Date.now() - navigationStartTime) > 900) {
                    console.warn('Navigation timeout detected (1s), reloading page');
                    window.location.href = targetUrl;
                }
            }, 1000);
        });

        document.addEventListener('livewire:navigated', function() {
            navigationStartTime = null;
            if (navigationTimeout) {
                clearTimeout(navigationTimeout);
                navigationTimeout = null;
            }
        });

        document.addEventListener('livewire:navigate-error', function() {
            navigationStartTime = null;
            if (navigationTimeout) {
                clearTimeout(navigationTimeout);
                navigationTimeout = null;
            }
        });
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.departments__button')) {
            setTimeout(() => {
                $('.departments').removeClass('departments--transition');
                if (!$('.departments').hasClass('departments--fixed')) {
                    $('.departments__links-wrapper').css('height', '');
                }
            }, 350);
        }
    });

    if (window.__storefrontComponentsRegistered) {
        return;
    }

    window.__storefrontComponentsRegistered = true;

    window.handleAddToCart = function(button) {
        const productId = button.getAttribute('data-product-id');
        const action = button.getAttribute('data-action') || 'add';

        button.disabled = true;
        const originalText = button.innerHTML;
        button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

        fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1,
                    instance: action === 'kart' ? 'kart' : 'default'
                })
            })
            .then(response => response.json())
            .then(data => {
                button.disabled = false;
                button.innerHTML = originalText;

                if (data.success) {
                    const cartCountElement = document.querySelector('.cart-count');
                    if (cartCountElement && data.cart_count) {
                        cartCountElement.textContent = data.cart_count;
                    }

                    if (window.Livewire) {
                        window.Livewire.dispatch('cartUpdated');
                        window.Livewire.dispatch('notify', {
                            message: 'Product added to cart'
                        });
                    } else if (window.$) {
                        window.$.notify('Product added to cart');
                    } else {
                        alert('Product added to cart');
                    }

                    if (action === 'kart' || data.redirect_url) {
                        setTimeout(() => {
                            window.location.href = data.redirect_url || '/checkout';
                        }, 500);
                    }
                } else {
                    console.error('Failed to add product to cart:', data.message);
                    const errorMessage = 'Failed to add product to cart: ' + (data.message || 'Unknown error');
                    if (window.Livewire) {
                        window.Livewire.dispatch('notify', {
                            message: errorMessage,
                            type: 'error'
                        });
                    } else if (window.$) {
                        window.$.notify(errorMessage, {
                            type: 'error'
                        });
                    } else {
                        alert(errorMessage);
                    }
                }
            })
            .catch(error => {
                button.disabled = false;
                button.innerHTML = originalText;
                console.error('Error adding to cart:', error);
                alert('Something went wrong. Please try again.');
            });
    };

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

                this.$watch('retail', (value) => sync('retail', value), {
                    deep: true
                });
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
                return 'TK ' + (parseFloat(price) || 0).toLocaleString('en-US', {
                    maximumFractionDigits: 0
                });
            },
        }));

        // shopInfiniteScroll - truncated for brevity, full version in original file
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
                if (!container) return;
                const ids = container.dataset.initialProducts;
                if (!ids) return;
                try {
                    JSON.parse(ids).forEach(id => this.loadedProductIds.add(Number(id)));
                } catch (error) {
                    console.error('Failed to parse initial product IDs', error);
                }
            },

            async loadProducts() {
                if (!this.hasMore || this.loading) {
                    if (!this.hasMore) this.disconnectObserver();
                    return;
                }

                this.loading = true;
                try {
                    const params = new URLSearchParams(window.location.search);
                    params.set('page', this.currentPage + 1);
                    params.set('per_page', this.perPage);
                    const shuffleSeed = this.getShuffleSeed();
                    if (shuffleSeed) params.set('shuffle', shuffleSeed);

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
                if (!container) return;

                products.forEach((product, index) => {
                    const productId = product.id || index;
                    if (this.loadedProductIds.has(productId)) return;

                    this.loadedProductIds.add(productId);
                    const element = this.createProductElement(product, index);
                    container.appendChild(element);
                });

                this.updateProductCount();

                if (this.hasMore && this.observer) {
                    this.$nextTick(() => {
                        if (!this.observer) return;
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

            getProductHTML(product, index) {
                // This is a large function - keeping it simplified here
                // Full implementation should match the original
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
                const userIsGuest = this.getUserIsGuest();
                const userIsVerified = this.getUserIsVerified();
                const shouldHidePrice = isOninda && !guestCanSeePrice && (userIsGuest || !userIsVerified);

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
                if (shouldHidePrice) {
                    priceHTML = `<span class="product-card__new-price text-danger">${
                            userIsGuest ? 'Login to see price' : 'Verify account to see price'
                        }</span>`;
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
                        <div class="product-card__image" style="aspect-ratio: 1 / 1; overflow: hidden;">
                            <a href="${productUrl}" class="product-link" wire:navigate.hover style="display: block; width: 100%; height: 100%;">
                                <img src="${productImage}" alt="Base Image" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                            </a>
                        </div>
                        <div class="product-card__info">
                            <div class="product-card__name">
                                <a href="${productUrl}" class="product-link" wire:navigate.hover data-name="${product.var_name || productName}">${productName}</a>
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

            getUserIsGuest() {
                const container = this.getContainer();
                return container && container.dataset.userGuest === 'true';
            },

            getUserIsVerified() {
                const container = this.getContainer();
                return container && container.dataset.userVerified === 'true';
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

