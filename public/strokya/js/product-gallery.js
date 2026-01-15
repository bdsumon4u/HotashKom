// Product gallery and carousel functionality
(function() {
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
                        const response = await fetch(
                            `/api/products/${encodeURIComponent(this.productId)}/related.json`
                        );

                        if (response.ok) {
                            const products = await response.json();
                            this.renderProducts(products, container);
                            this.loaded = true;
                            this.observer?.disconnect();
                        } else {
                            container.innerHTML =
                                '<div class="py-5 text-center text-muted">Unable to load related products.</div>';
                        }
                    } catch (error) {
                        console.error('Error loading related products:', error);
                        container.innerHTML =
                            '<div class="py-5 text-center text-muted">Unable to load related products.</div>';
                    }

                    this.loading = false;
                },

                renderProducts(products, container) {
                    if (!products || products.length === 0) {
                        container.innerHTML =
                            '<div class="py-5 text-center text-muted">No related products found.</div>';
                        return;
                    }

                    container.innerHTML = '';

                    products.forEach((product) => {
                        const productElement = this.createProductElement(product);
                        container.appendChild(productElement);
                    });
                },

                createProductElement(product) {
                    const div = document.createElement('div');
                    div.className = 'products-list__item';
                    div.innerHTML = this.getProductHTML(product);
                    return div;
                },

                getProductHTML(product) {
                    const productId = product.id;
                    const productName = product.name || 'Product';
                    const productSlug = product.slug || productId;
                    const productPrice = product.compareAtPrice || product.price || 0;
                    const productSellingPrice = product.price || productPrice;
                    const productImage = product.base_image_url || (product.images && product.images.length > 0 ?
                        `/storage/${product.images[0]}` :
                        '/images/placeholder.jpg');
                    const productUrl = `/products/${encodeURIComponent(productSlug)}`;
                    const inStock = product.availability !== 'Out of Stock' && (product.availability === 'In Stock' || (product.availability && parseInt(product.availability) > 0));
                    const stockCount = typeof product.availability === 'number' ? product.availability : (product.availability === 'In Stock' ? null : 0);
                    const shouldTrack = typeof product.availability === 'number' || product.availability !== 'In Stock';
                    const hasDiscount = productPrice !== productSellingPrice && productPrice > 0;
                    const discountPercent = hasDiscount ? Math.round(((productPrice - productSellingPrice) * 100) / productPrice) : 0;

                    const showOption = JSON.parse(this.$el.dataset.showOption || '{}');
                    const isOninda = this.$el.dataset.isOninda === 'true';
                    const guestCanSeePrice = this.$el.dataset.guestCanSeePrice === 'true';
                    const userIsGuest = this.$el.dataset.userGuest === 'true';
                    const userIsVerified = this.$el.dataset.userVerified === 'true';
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
                        priceHTML =
                            `<span class="product-card__new-price">${formatPrice(productSellingPrice)}</span><span class="product-card__old-price">${formatPrice(productPrice)}</span>`;
                    } else {
                        priceHTML = formatPrice(productSellingPrice);
                    }

                    let discountText = '';
                    if (hasDiscount) {
                        const template = (showOption.discount_text || '').toString();
                        discountText = template.replace('[percent]', discountPercent);
                        if (!discountText.trim()) {
                            discountText = '';
                        }
                    }

                    const getRatingHTML = (product) => {
                        const averageRating = product.average_rating || product.rating || 0;
                        const totalReviews = product.total_reviews || product.reviews || 0;

                        if (averageRating <= 0) {
                            return '';
                        }

                        let starsHTML = '';
                        for (let i = 1; i <= 5; i++) {
                            if (i <= Math.floor(averageRating)) {
                                starsHTML +=
                                    '<i class="fa fa-star text-warning" style="font-size: 0.75rem;"></i>';
                            } else if (i - 0.5 <= averageRating) {
                                starsHTML +=
                                    '<i class="fa fa-star-half-alt text-warning" style="font-size: 0.75rem;"></i>';
                            } else {
                                starsHTML +=
                                    '<i class="far fa-star text-muted" style="font-size: 0.75rem;"></i>';
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
                    };

                    return `
                        <div class="product-card" data-id="${productId}" data-max="${shouldTrack ? (stockCount || 0) : -1}">
                            <div class="product-card__badges-list">
                                ${!inStock ? '<div class="product-card__badge product-card__badge--sale">Sold</div>' : ''}
                                ${discountText ? `<div class="product-card__badge product-card__badge--sale">${discountText}</div>` : ''}
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
                                ${getRatingHTML(product)}
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
            document.addEventListener('alpine:init', initComponent, {
                once: true
            });
        }
    }

    function initializeProductShowScripts() {
        if (!document.querySelector('.xzoom-container')) {
            if (window.__productShowCleanup) {
                window.__productShowCleanup();
                window.__productShowCleanup = null;
            }
            return;
        }

        function waitForDeps(callback, retries = 0) {
            const $ = window.jQuery || window.$;
            if (!$ || typeof $ !== 'function') {
                return retries < 100 ? setTimeout(() => waitForDeps(callback, retries + 1), 50) : null;
            }
            if (typeof $.fn.xzoom === 'function') {
                callback($);
            } else {
                retries < 100 ? setTimeout(() => waitForDeps(callback, retries + 1), 100) : callback($);
            }
        }

        waitForDeps(($) => {
            if (window.__productShowCleanup) {
                window.__productShowCleanup();
                window.__productShowCleanup = null;
            }

            const namespace = '.productShow';
            const $galleryLinks = $('.xzoom-thumbs a');
            if (!$galleryLinks.length) {
                return;
            }

            $('.xzoom, .xzoom-gallery').each(function() {
                const xzoom = $(this).data('xzoom');
                if (xzoom?.destroy) {
                    try {
                        xzoom.destroy();
                    } catch (e) {}
                }
            });

            if (typeof $.fn.xzoom === 'function') {
                $('.xzoom, .xzoom-gallery').xzoom({
                    zoomWidth: 400,
                    title: true,
                    tint: '#333',
                    Xoffset: 15,
                });
            }

            setTimeout(() => {
                const $mainXzoom = $('.xzoom');
                const $leftBtn = $('.zoom-control.left');
                const $rightBtn = $('.zoom-control.right');
                const linksCount = $galleryLinks.length;
                const lastG = linksCount - 1;

                if (linksCount <= 1) {
                    return;
                }

                let autoNavigationTimer = null;

                // Get current slide index from DOM
                const getCurrentSlideIndex = () => {
                    if (!$mainXzoom.length) return 0;
                    const currentSrc = $mainXzoom.attr('src') || $mainXzoom.attr('xoriginal');
                    for (let i = 0; i < $galleryLinks.length; i++) {
                        const $link = $galleryLinks.eq(i);
                        const $img = $link.find('img');
                        if ($link.attr('href') === currentSrc || $img.attr('src') === currentSrc ||
                            $img.hasClass('xactive') || $link.hasClass('xactive')) {
                            return i;
                        }
                    }
                    return 0;
                };

                // Helper to detect when the user is typing (e.g. in the search box)
                const isUserTyping = () => {
                    const active = document.activeElement;
                    if (!active) {
                        return false;
                    }

                    const tag = active.tagName;
                    if (tag === 'INPUT' || tag === 'TEXTAREA') {
                        return true;
                    }

                    if (active.isContentEditable) {
                        return true;
                    }

                    return false;
                };

                // Change slide, but skip when the user is typing to avoid closing the keyboard/search box
                const changeSlide = (targetIndex) => {
                    if (isUserTyping()) {
                        return;
                    }

                    const $targetLink = $galleryLinks.eq(targetIndex);
                    if (!$targetLink.length) {
                        return;
                    }

                    // Use the original xzoom click behaviour
                    $targetLink[0].click();
                };

                // Navigate to next slide
                const goToNextSlide = () => {
                    const currentIndex = getCurrentSlideIndex();
                    const nextIndex = currentIndex >= lastG ? 0 : currentIndex + 1;
                    changeSlide(nextIndex);
                };

                // Start auto-navigation timer
                const startAutoNavigation = () => {
                    clearTimeout(autoNavigationTimer);
                    autoNavigationTimer = setTimeout(goToNextSlide, 3000);
                };

                // Reset auto-navigation (used for manual clicks)
                const resetAutoNavigation = () => {
                    startAutoNavigation();
                };

                // Arrow button handlers
                const handleArrowClick = (direction, e) => {
                    if (e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    const currentIndex = getCurrentSlideIndex();
                    const targetIndex = direction === 'next'
                        ? (currentIndex >= lastG ? 0 : currentIndex + 1)
                        : (currentIndex <= 0 ? lastG : currentIndex - 1);
                    changeSlide(targetIndex);
                    resetAutoNavigation();
                };

                if ($leftBtn.length) {
                    $leftBtn.off('click touchend' + namespace)
                        .on('click touchend' + namespace, (e) => handleArrowClick('prev', e));
                }

                if ($rightBtn.length) {
                    $rightBtn.off('click touchend' + namespace)
                        .on('click touchend' + namespace, (e) => handleArrowClick('next', e));
                }

                // Gallery link clicks (manual navigation)
                $galleryLinks.off('click' + namespace).on('click' + namespace, function() {
                    // Reset timer on any manual click
                    resetAutoNavigation();
                });

                // Start auto-navigation after initialization
                setTimeout(startAutoNavigation, 500);

                window.__handleVariantChange = function(event) {
                    const variantId = event.variantId;
                    const $variantImage = $('.variant-image').filter(function() {
                        const ids = $(this).data('variant-ids');
                        return Array.isArray(ids) && ids.includes(variantId);
                    }).first();

                    if ($variantImage.length) {
                        const $link = $variantImage.closest('a');
                        if ($link.length) {
                            // Find the index of this link in the gallery
                            const targetIndex = $galleryLinks.index($link);
                            if (targetIndex >= 0) {
                                setTimeout(() => {
                                    changeSlide(targetIndex);
                                }, 100);
                                resetAutoNavigation();
                            }
                        }
                    }
                };

                if (!window.__variantChangeListenerRegistered) {
                    window.__variantChangeListenerRegistered = true;
                    const registerVariantListener = () => {
                        Livewire.on('variantChanged', (event) => {
                            window.__handleVariantChange?.(event);
                        });
                    };
                    window.Livewire ? registerVariantListener()
                        : document.addEventListener('livewire:load', registerVariantListener, { once: true });
                }

                window.__productShowCleanup = function() {
                    clearTimeout(autoNavigationTimer);
                    if ($leftBtn.length) {
                        $leftBtn.off('click touchend' + namespace);
                    }
                    if ($rightBtn.length) {
                        $rightBtn.off('click touchend' + namespace);
                    }
                    $galleryLinks.off('click' + namespace);
                };
            }, 200);
        });
    }

    function runInitializers() {
        registerLazyRelatedProductsComponent();
        requestAnimationFrame(initializeProductShowScripts);
    }

    registerLazyRelatedProductsComponent();

    document.addEventListener('DOMContentLoaded', runInitializers);
    document.addEventListener('livewire:navigate', runInitializers);

    if (document.readyState !== 'loading') {
        runInitializers();
    }

    // Prevent default on xzoom thumb clicks
    document.addEventListener('click', function(event) {
        const zoomThumb = event.target.closest('.xzoom-thumbs a');
        if (zoomThumb) {
            event.preventDefault();
        }
    }, true);
})();

