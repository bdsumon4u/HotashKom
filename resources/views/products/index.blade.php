@extends('layouts.yellow.master')

@section('title', 'Products')

@section('content')

@include('partials.page-header', [
    'paths' => [
        url('/') => 'Home',
    ],
    'active' => 'Products',
    'page_title' => 'Products'
])

<div class="block">
    <div class="products-view">
        <div class="container">
            <div class="row">
                <!-- Filter Sidebar -->
                <div class="pr-md-1 col-lg-3 col-md-4" x-data="filterSidebar()">
                    <div class="p-3 filter-sidebar">
                        <div class="filter-sidebar__header">
                            <h3 class="filter-sidebar__title">Filters</h3>
                            <button type="button" class="filter-sidebar__toggle d-md-none" @click="mobileOpen = !mobileOpen">
                                <i class="fa" :class="mobileOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>
                        </div>

                        <form method="GET" action="{{
                            ($category = request()->route()->parameter('category'))
                                ? route('categories.products', $category)
                                : (($brand = request()->route()->parameter('brand'))
                                    ? route('brands.products', $brand)
                                    : route('products.index'))
                        }}" id="filter-form"
                              x-show="mobileOpen || isDesktop"
                              x-transition
                              class="filter-sidebar__content"
                              x-init="checkDesktop()">

                            <!-- Preserve search parameter -->
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif

                            <!-- Categories Filter -->
                            @if(!isset($hideCategoryFilter) || !$hideCategoryFilter)
                            <div class="filter-block">
                                <div class="filter-block__header" @click="categoriesOpen = !categoriesOpen">
                                    <h4 class="filter-block__title">Categories</h4>
                                    <i class="fa" :class="categoriesOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                </div>
                                <div class="filter-block__content" x-show="categoriesOpen" x-transition>
                                    @php
                                        $filterCategory = request('filter_category');
                                        $selectedCategories = [];

                                        if ($filterCategory) {
                                            if (is_array($filterCategory)) {
                                                $selectedCategories = array_map('intval', array_filter($filterCategory));
                                            } elseif (is_numeric(str_replace(',', '', $filterCategory))) {
                                                $selectedCategories = array_map('intval', explode(',', $filterCategory));
                                            }
                                        }
                                    @endphp
                                    @foreach($categories ?? [] as $category)
                                        <div class="filter-item">
                                            <label class="filter-checkbox">
                                                <input type="checkbox"
                                                       name="filter_category[]"
                                                       value="{{ $category->id }}"
                                                       @if(in_array((int)$category->id, $selectedCategories)) checked @endif
                                                       @change="updateFilter()">
                                                <span class="filter-checkbox__label">{{ $category->name }}</span>
                                                <span class="filter-checkbox__count">({{ $category->products()->whereIsActive(1)->whereNull('parent_id')->count() }})</span>
                                            </label>
                                            @if($category->childrens->isNotEmpty())
                                                <div class="ml-3 filter-item__children">
                                                    @foreach($category->childrens as $child)
                                                        <label class="filter-checkbox">
                                                            <input type="checkbox"
                                                                   name="filter_category[]"
                                                                   value="{{ $child->id }}"
                                                                   @if(in_array((int)$child->id, $selectedCategories)) checked @endif
                                                                   @change="updateFilter()">
                                                            <span class="filter-checkbox__label">{{ $child->name }}</span>
                                                            <span class="filter-checkbox__count">({{ $child->products()->whereIsActive(1)->whereNull('parent_id')->count() }})</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Attributes Filter -->
                            @php
                                $filterOption = request('filter_option');
                                $selectedOptions = [];

                                if ($filterOption) {
                                    if (is_array($filterOption)) {
                                        $selectedOptions = array_map('intval', array_filter($filterOption));
                                    } else {
                                        $selectedOptions = array_map('intval', explode(',', $filterOption));
                                    }
                                }
                            @endphp
                            @foreach($attributes ?? [] as $attribute)
                                <div class="filter-block">
                                    <div class="filter-block__header" @click="attributesOpen['{{ $attribute->id }}'] = !attributesOpen['{{ $attribute->id }}']">
                                        <h4 class="filter-block__title">{{ $attribute->name }}</h4>
                                        <i class="fa" :class="attributesOpen['{{ $attribute->id }}'] ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                    </div>
                                    <div class="filter-block__content" x-show="attributesOpen['{{ $attribute->id }}']" x-transition>
                                        @foreach($attribute->options as $option)
                                            <label class="filter-checkbox">
                                                <input type="checkbox"
                                                       name="filter_option[]"
                                                       value="{{ $option->id }}"
                                                       @if(in_array((int)$option->id, $selectedOptions)) checked @endif
                                                       @change="updateFilter()">
                                                <span class="filter-checkbox__label">{{ $option->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                            <!-- Filter Actions -->
                            <div class="filter-actions">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{
                                    ($category = request()->route()->parameter('category'))
                                        ? route('categories.products', $category)
                                        : (($brand = request()->route()->parameter('brand'))
                                            ? route('brands.products', $brand)
                                            : route('products.index', request()->only('search')))
                                }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Products Content -->
                <div class="pl-md-1 col-lg-9 col-md-8">
                    <div class="products-view__options">
                        <div class="view-options">
                            <div class="view-options__legend" 
                                 @if(config('app.infinite_scroll_section', false)) 
                                 x-data="productCountDisplay({{ $products->total() }}, {{ $products->count() }})"
                                 x-text="getDisplayText()"
                                 id="product-count-display"
                                 @else
                                 @if(request('search'))
                                 Found {{ $products->total() }} result(s) for "{{ request('search', 'NULL') }}"
                                 @elseif($category = request()->route()->parameter('category'))
                                 Showing from "{{ $category->name }}" category.
                                 @elseif($brand = request()->route()->parameter('brand'))
                                 Showing from "{{ $brand->name }}" brand.
                                 @else
                                 Showing {{ $products->count() }} of {{ $products->total() }} products
                                 @endif
                                 @endif
                            </div>
                            <div class="view-options__divider"></div>
                        </div>
                    </div>

                    @if(config('app.infinite_scroll_section', false))
                        <div class="products-view__list products-list" 
                             data-layout="grid-4-full" 
                             data-with-features="false" 
                             x-data="shopInfiniteScroll" 
                             x-init="init()">
                            <div class="products-list__body"
                                 id="products-container-shop"
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
                                @foreach($products as $product)
                                    <div class="products-list__item">
                                        <livewire:product-card :product="$product" :key="$product->id" />
                                    </div>
                                @endforeach
                            </div>

                            <!-- Loading trigger -->
                            <div class="load-more-trigger"
                                 x-show="hasMore"
                                 x-ref="loadMoreTrigger"
                                 style="height: 20px; margin: 20px 0;">
                                <div x-show="loading" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        @include('partials.products.pure-grid', [
                            'title' => null,
                            'cols' => 4,
                        ])

                        <div class="pt-0 products-view__pagination">
                            {!! $products->appends(request()->query())->links() !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.filter-sidebar {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    position: sticky;
    top: 20px;
    max-height: calc(100vh - 40px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.filter-sidebar__content {
    overflow-y: auto;
    overflow-x: hidden;
    flex: 1;
    padding-right: 0.5rem;
    margin-right: -0.5rem;
}

.filter-sidebar__content::-webkit-scrollbar {
    width: 6px;
}

.filter-sidebar__content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.filter-sidebar__content::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.filter-sidebar__content::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.filter-sidebar__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}

.filter-sidebar__title {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
}

.filter-sidebar__toggle {
    background: none;
    border: none;
    font-size: 1.2rem;
    color: #6c757d;
}

.filter-block {
    border-bottom: 1px solid #e9ecef;
}

.filter-block:last-child {
    border-bottom: none;
}

.filter-block__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    padding: 0.5rem 0;
}

.filter-block__title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
}

.filter-block__content {
    margin-top: 0.5rem;
}

.filter-item {
    margin-bottom: 0.75rem;
}

.filter-item__children {
    margin-top: 0.5rem;
}

.filter-checkbox {
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 0;
    user-select: none;
}

.filter-checkbox input[type="checkbox"] {
    margin-right: 0.5rem;
    cursor: pointer;
}

.filter-checkbox__label {
    flex: 1;
    color: #333;
}

.filter-checkbox__count {
    color: #6c757d;
    font-size: 0.9rem;
}

.filter-actions {
    margin-top: 1.5rem;
    display: flex;
    gap: 0.5rem;
}

.filter-actions .btn {
    flex: 1;
}

@media (max-width: 767px) {
    .filter-sidebar {
        position: relative;
        top: 0;
        margin-bottom: 1rem;
        max-height: none;
    }

    .filter-sidebar__content {
        max-height: 70vh;
    }
}
</style>
@endpush

@push('scripts')
<script>
function filterSidebar() {
    return {
        mobileOpen: false,
        isDesktop: window.innerWidth >= 768,
        categoriesOpen: true,
        attributesOpen: {},

        init() {
            // Initialize attributes open state
            @foreach($attributes ?? [] as $attribute)
                this.attributesOpen['{{ $attribute->id }}'] = true;
            @endforeach

            // Handle window resize
            window.addEventListener('resize', () => {
                this.checkDesktop();
            });
        },

        checkDesktop() {
            this.isDesktop = window.innerWidth >= 768;
            if (this.isDesktop) {
                this.mobileOpen = true;
            }
        },

        updateFilter() {
            // Auto-submit on filter change (optional - remove if you want manual filter button)
            // this.$el.closest('form').submit();
        }
    }
}

@if(config('app.infinite_scroll_section', false))
document.addEventListener('alpine:init', () => {
    // Product count display component
    Alpine.data('productCountDisplay', (totalProducts, initialCount) => ({
        totalProducts: totalProducts,
        loadedProducts: initialCount,
        
        updateCount(count) {
            this.loadedProducts = count;
        },
        
        getDisplayText() {
            if (this.loadedProducts >= this.totalProducts) {
                return `Showing all ${this.totalProducts} products`;
            }
            return `Showing ${this.loadedProducts} of ${this.totalProducts} products`;
        }
    }));

    Alpine.data('shopInfiniteScroll', () => ({
        currentPage: {{ $products->currentPage() }},
        hasMore: {{ $products->hasMorePages() ? 'true' : 'false' }},
        loading: false,
        perPage: {{ $per_page ?? 20 }},
        loadedProductIds: new Set(),
        totalProducts: {{ $products->total() }},
        observer: null,

        init() {
            // Mark initial products as loaded
            @foreach($products as $product)
                this.loadedProductIds.add({{ $product->id }});
            @endforeach

            // Update initial count
            this.$nextTick(() => {
                this.updateProductCount();
                this.setupIntersectionObserver();
            });
        },

        async loadProducts() {
            // Double-check hasMore before proceeding
            if (!this.hasMore) {
                console.log('Skipping load - hasMore is false, disconnecting observer');
                this.disconnectObserver();
                return;
            }
            
            if (this.loading) {
                console.log('Skipping load - already loading');
                return;
            }

            console.log('Loading products - page:', this.currentPage + 1);
            this.loading = true;

            try {
                // Build query string with current filters
                const params = new URLSearchParams(window.location.search);
                params.set('page', this.currentPage + 1);
                params.set('per_page', this.perPage);

                const url = `/api/shop/products?${params.toString()}`;
                console.log('Fetching:', url);

                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                console.log('Response status:', response.status);

                if (response.ok) {
                    const data = await response.json();
                    console.log('Received data:', data);
                    console.log('Pagination info:', {
                        current_page: data.pagination?.current_page,
                        last_page: data.pagination?.last_page,
                        total: data.pagination?.total,
                        has_more: data.pagination?.has_more,
                        data_count: data.data?.length
                    });

                    if (data.data && Array.isArray(data.data) && data.data.length > 0) {
                        // Update hasMore based on current_page vs last_page, not just has_more flag
                        const currentPage = data.pagination?.current_page || (this.currentPage + 1);
                        const lastPage = data.pagination?.last_page || 1;
                        const hasMorePages = currentPage < lastPage;
                        
                        this.currentPage = currentPage;
                        
                        console.log('Received', data.data.length, 'products. Current page:', this.currentPage, 'Last page:', lastPage);
                        
                        // Track how many products we had before appending
                        const productsBefore = this.loadedProductIds.size;
                        
                        // Append products (this will skip duplicates)
                        this.appendProducts(data.data);
                        
                        // Check how many new products were actually added
                        const productsAfter = this.loadedProductIds.size;
                        const newProductsAdded = productsAfter - productsBefore;
                        
                        console.log('Added', newProductsAdded, 'new products. Total loaded:', productsAfter);
                        
                        // If we're on the last page OR no new products were added, stop loading
                        if (!hasMorePages) {
                            console.log('Reached last page - disconnecting observer');
                            this.hasMore = false;
                            this.disconnectObserver();
                        } else if (newProductsAdded === 0 && data.data.length > 0) {
                            // If we got products from API but they were all duplicates
                            // Check if we've loaded all or most of the products
                            const totalProducts = data.pagination?.total || this.totalProducts || 0;
                            
                            if (productsAfter >= totalProducts) {
                                console.log('All products loaded (reached total) - disconnecting observer');
                                this.hasMore = false;
                                this.disconnectObserver();
                            } else if (productsAfter >= totalProducts * 0.95) {
                                // If we've loaded 95% or more, likely we have all unique products
                                console.log('Most products loaded (95%+) - disconnecting observer');
                                this.hasMore = false;
                                this.disconnectObserver();
                            } else {
                                // Still have more pages, continue but log warning
                                console.warn('Got duplicates but still have more pages. Loaded:', productsAfter, 'of', totalProducts);
                                this.hasMore = hasMorePages;
                            }
                        } else {
                            // Normal case: we have more pages and got new products
                            this.hasMore = hasMorePages;
                        }
                        
                        // Final check: if we've loaded all products, stop
                        const totalProducts = data.pagination?.total || this.totalProducts || 0;
                        if (productsAfter >= totalProducts) {
                            console.log('All products loaded - disconnecting observer');
                            this.hasMore = false;
                            this.disconnectObserver();
                        }
                    } else {
                        console.log('No more products to load - empty data array');
                        this.hasMore = false;
                        this.currentPage = data.pagination?.current_page || this.currentPage;
                        this.disconnectObserver();
                    }
                } else {
                    console.error('API Error:', response.status, response.statusText);
                    const errorText = await response.text();
                    console.error('Error response:', errorText);
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

        appendProducts(products) {
            const container = document.querySelector('#products-container-shop');
            if (!container) return;

            let addedCount = 0;
            products.forEach((product, index) => {
                const productId = product.id || index;

                if (this.loadedProductIds.has(productId)) {
                    console.log('Skipping duplicate product ID:', productId);
                    return;
                }

                this.loadedProductIds.add(productId);
                const element = this.createProductElement(product, index);
                container.appendChild(element);
                this.attachNavigationHandlers(element);
                addedCount++;
            });

            console.log('Added', addedCount, 'new products. Total loaded:', this.loadedProductIds.size);

            // Update product count display
            this.updateProductCount();

            // Re-observe the trigger element after adding products (in case it moved)
            // Only if we still have more products to load
            if (this.hasMore && this.observer) {
                this.$nextTick(() => {
                    // Double-check hasMore again after $nextTick
                    if (!this.hasMore) {
                        this.disconnectObserver();
                        return;
                    }
                    
                    const trigger = this.$refs.loadMoreTrigger || this.$el.querySelector('.load-more-trigger');
                    if (trigger) {
                        // Unobserve first, then observe again to reset the observer
                        this.observer.unobserve(trigger);
                        this.observer.observe(trigger);
                    }
                });
            } else if (!this.hasMore) {
                // Make sure observer is disconnected if no more products
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

            // Get button configuration from PHP (passed via data attributes)
            const showOption = this.getShowOption();
            const isOninda = this.getIsOninda();
            const guestCanSeePrice = this.getGuestCanSeePrice();

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

            // Generate price HTML - format to match theMoney() function: "TK <span>amount</span>"
            const formatPrice = (price) => {
                return `TK&nbsp;<span>${parseFloat(price).toLocaleString('en-US')}</span>`;
            };

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

        getShowOption() {
            const container = document.querySelector('#products-container-shop');
            if (container && container.dataset.showOption) {
                return JSON.parse(container.dataset.showOption);
            }
            return {
                product_grid_button: 'add_to_cart',
                add_to_cart_icon: '',
                add_to_cart_text: 'Add to Cart',
                order_now_icon: '',
                order_now_text: 'Order Now'
            };
        },

        getIsOninda() {
            const container = document.querySelector('#products-container-shop');
            return container && container.dataset.isOninda === 'true';
        },

        getGuestCanSeePrice() {
            const container = document.querySelector('#products-container-shop');
            return container && container.dataset.guestCanSeePrice === 'true';
        },

        setupIntersectionObserver() {
            console.log('Setting up Intersection Observer');
            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    // Double-check hasMore before loading
                    if (!this.hasMore) {
                        console.log('Observer triggered but hasMore is false - disconnecting');
                        this.disconnectObserver();
                        return;
                    }
                    
                    console.log('Intersection entry:', {
                        isIntersecting: entry.isIntersecting,
                        loading: this.loading,
                        hasMore: this.hasMore,
                        currentPage: this.currentPage,
                        intersectionRatio: entry.intersectionRatio
                    });
                    
                    if (entry.isIntersecting && !this.loading && this.hasMore) {
                        console.log('Triggering loadProducts');
                        this.loadProducts();
                    } else if (entry.isIntersecting && !this.hasMore) {
                        console.log('Not loading - hasMore is false');
                        this.disconnectObserver();
                    } else if (entry.isIntersecting && this.loading) {
                        console.log('Not loading - already loading');
                    }
                });
            }, {
                root: null,
                rootMargin: '200px', // Increased from 100px to trigger earlier
                threshold: 0.01 // Lowered threshold to trigger more reliably
            });

            this.$nextTick(() => {
                // Try to get the trigger using $refs first, then fallback to querySelector
                const trigger = this.$refs.loadMoreTrigger || this.$el.querySelector('.load-more-trigger');
                console.log('Load more trigger element:', trigger);
                if (trigger) {
                    this.observer.observe(trigger);
                    console.log('Observer attached to trigger');
                } else {
                    console.error('Load more trigger not found!');
                    // Try again after a short delay in case DOM isn't ready
                    setTimeout(() => {
                        const retryTrigger = this.$refs.loadMoreTrigger || this.$el.querySelector('.load-more-trigger');
                        if (retryTrigger) {
                            this.observer.observe(retryTrigger);
                            console.log('Observer attached to trigger (retry)');
                        } else {
                            console.error('Load more trigger still not found after retry!');
                        }
                    }, 500);
                }
            });
        },

        disconnectObserver() {
            if (this.observer) {
                // Unobserve all observed elements first
                const trigger = this.$refs.loadMoreTrigger || this.$el.querySelector('.load-more-trigger');
                if (trigger) {
                    try {
                        this.observer.unobserve(trigger);
                    } catch (e) {
                        console.log('Error unobserving trigger:', e);
                    }
                }
                // Then disconnect
                this.observer.disconnect();
                this.observer = null;
                console.log('Observer disconnected');
            }
            // Ensure hasMore is false to prevent any further loading
            this.hasMore = false;
        }
    }));
});
@endif

// Add wire:navigate.hover to pagination links
function addPaginationSPA() {
    const paginationLinks = document.querySelectorAll('.pagination a');
    paginationLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && !link.getAttribute('wire:navigate.hover') && !link.getAttribute('wire:navigate')) {
            link.setAttribute('wire:navigate.hover', '');
        }
    });
}

document.addEventListener('DOMContentLoaded', addPaginationSPA);
document.addEventListener('livewire:navigated', addPaginationSPA);
</script>
@endpush
@endsection
