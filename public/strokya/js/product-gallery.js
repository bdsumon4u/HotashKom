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

                    const theMoney = (price) => `TK&nbsp;<span>${parseFloat(price || 0).toLocaleString('en-US')}</span>`;

                    let buttonsHTML = '';
                    if (!isOninda) {
                        const available = inStock;
                        const disabledAttr = available ? '' : 'disabled';

                        if (showOption.product_grid_button === 'add_to_cart') {
                            const text = showOption.add_to_cart_text || 'Add to Cart';
                            buttonsHTML = `
                                <button class="btn bb-btn-card product-card__addtocart w-100 d-flex align-items-center justify-content-center text-center gap-2" type="button" ${disabledAttr}
                                        data-product-id="${productId}" data-action="add" onclick="handleAddToCart(this)" style="width: 100% !important; margin: 0 auto !important;">
                                    <i class="fas fa-cart-plus mr-1" style="font-size: 14px;"></i>
                                    <span>${text}</span>
                                </button>
                            `;
                        } else if (showOption.product_grid_button === 'order_now') {
                            const text = showOption.order_now_text || 'অর্ডার করুন';
                            buttonsHTML = `
                                <button class="btn bb-btn-card-order product-card__ordernow w-100 d-flex align-items-center justify-content-center text-center gap-2" type="button" ${disabledAttr}
                                        data-product-id="${productId}" data-action="kart" onclick="handleAddToCart(this)" style="width: 100% !important; margin: 0 auto !important;">
                                    <i class="fas fa-bag-shopping mr-1" style="font-size: 14px;"></i>
                                    <span>${text}</span>
                                </button>
                            `;
                        }
                    }

                    let priceHTML = '';
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

                    const getRatingHTML = (product) => {
                        const averageRating = product.average_rating || product.rating || 0;
                        const totalReviews = product.total_reviews || product.reviews || 0;

                        if (averageRating <= 0) {
                            return '';
                        }

                        let starsHTML = '';
                        for (let i = 1; i <= 5; i++) {
                            if (i <= Math.floor(averageRating)) {
                                starsHTML += '<i class="fa fa-star"></i>';
                            } else if (i - 0.5 <= averageRating) {
                                starsHTML += '<i class="fa fa-star-half-alt"></i>';
                            } else {
                                starsHTML += '<i class="far fa-star text-muted"></i>';
                            }
                        }

                        return `
                            <div class="gap-1 mb-1 d-flex align-items-center" style="font-size: 11px;">
                                <div class="d-flex align-items-center text-warning" style="font-size: 10px;">
                                    ${starsHTML}
                                </div>
                                <span class="text-muted ml-1 font-weight-bold">
                                    ${averageRating.toFixed(1)}
                                </span>
                            </div>
                        `;
                    };

                    return `
                        <div class="product-card bb-modern-card" data-id="${productId}" data-max="${shouldTrack ? (stockCount || 0) : -1}">
                            <div class="bb-card-badges d-flex justify-content-between align-items-center w-100 position-absolute" style="top: 14px; left: 0; padding: 0 14px; z-index: 5; pointer-events: none;">
                                <div>
                                    ${!inStock ? 
                                        '<span class="badge badge-danger px-2 py-1 font-weight-bold" style="border-radius: 3px; font-size: 10px; text-transform: uppercase;">Sold Out</span>' : 
                                        (hasDiscount && discountPercent > 0 ? `<span class="badge text-white font-weight-bold" style="background: #ef4444; border-radius: 3px; padding: 3px 6px; font-size: 11px; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);">-${discountPercent}%</span>` : '')
                                    }
                                </div>
                                <div>
                                    ${product.free_delivery ? '<span class="badge text-dark font-weight-bold d-flex align-items-center gap-1" style="background: #ecfdf5; color: #059669 !important; border: 1px solid #a7f3d0; border-radius: 3px; padding: 3px 6px; font-size: 10px;"><i class="fas fa-truck-fast"></i> Free</span>' : ''}
                                </div>
                            </div>
                            <div class="product-card__image bb-card-img-shell" style="aspect-ratio: 1 / 1; overflow: hidden; position: relative; margin: 8px 8px 0; border-radius: 4px; background: #f8fafc;">
                                <a href="${productUrl}" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; padding: 10px;" wire:navigate.hover>
                                    <img src="${productImage}" alt="${productName}" class="bb-product-img" style="max-width: 100%; max-height: 100%; object-fit: contain; transition: transform 0.3s ease;" loading="lazy">
                                </a>
                            </div>
                            <div class="product-card__info" style="display: flex; flex-direction: column; padding: 8px 10px 0; flex: 0 0 auto;">
                                <div class="product-card__name" style="flex: 0 0 auto; min-height: auto; margin-bottom: 2px !important;">
                                    <a href="${productUrl}" class="bb-product-title font-weight-bold" style="font-size: 13.5px; line-height: 1.35; color: #1e293b; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-decoration: none; min-height: auto;" wire:navigate.hover data-name="${product.var_name || productName}">${productName}</a>
                                </div>
                                ${getRatingHTML(product)}
                                <div class="product-card__prices" style="margin-top: 2px !important; margin-bottom: 8px !important; padding: 0 !important; flex: 0 0 auto;">
                                    ${priceHTML}
                                </div>
                            </div>
                            <div class="product-card__actions" style="padding: 0 10px 10px; width: 100%; display: flex; justify-content: center; align-items: center; margin-top: auto; box-sizing: border-box;">
                                ${!isOninda ? `
                                    <div class="product-card__buttons w-100 d-flex justify-content-center align-items-center" style="margin: 0 !important; width: 100% !important;">
                                        ${buttonsHTML}
                                    </div>
                                ` : ''}
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

