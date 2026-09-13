<div class="product-card bb-modern-card" data-id="{{ $product->id }}"
    data-max="{{ $product->should_track ? $product->stock_count : -1 }}">
    
    @php
        $in_stock = !$product->should_track || $product->stock_count > 0;
        $has_discount = $product->price && $product->selling_price && $product->price > $product->selling_price;
        $discount_percentage = $has_discount ? round((($product->price - $product->selling_price) / $product->price) * 100) : 0;
    @endphp

    <!-- Card Top Badges -->
    <div class="bb-card-badges d-flex justify-content-between align-items-center w-100 position-absolute" style="top: 14px; left: 0; padding: 0 14px; z-index: 5; pointer-events: none;">
        <div>
            @if (!$in_stock)
                <span class="badge badge-danger px-2 py-1 font-weight-bold" style="border-radius: 3px; font-size: 10px; text-transform: uppercase;">Sold Out</span>
            @elseif ($has_discount)
                <span class="badge text-white font-weight-bold" style="background: #ef4444; border-radius: 3px; padding: 3px 6px; font-size: 11px; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);">
                    -{{ $discount_percentage }}%
                </span>
            @endif
        </div>

        <div>
            @if ($is_free_delivery)
                <span class="badge text-dark font-weight-bold d-flex align-items-center gap-1" style="background: #ecfdf5; color: #059669 !important; border: 1px solid #a7f3d0; border-radius: 3px; padding: 3px 6px; font-size: 10px;">
                    <i class="fas fa-truck-fast"></i> Free
                </span>
            @endif
        </div>
    </div>

    <!-- Image Container with soft backdrop and rounded inner frame -->
    <div class="product-card__image bb-card-img-shell" style="aspect-ratio: 1 / 1; overflow: hidden; position: relative; margin: 8px 8px 0; border-radius: 4px; background: #f8fafc;">
        <a href="{{ route('products.show', $product) }}"
            style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; padding: 10px;" wire:navigate.hover>
            @php
                $productImageSrc = optional($product->base_image)->src;
                $productImageAlt = optional($product->base_image)->alt_text ?: $product->name;
            @endphp
            <img
                src="{{ cdn($productImageSrc, 320, 320) }}"
                srcset="
                    {{ cdn($productImageSrc, 320, 320) }} 320w,
                    {{ cdn($productImageSrc, 480, 480) }} 480w
                "
                sizes="
                    (max-width: 575px) 50vw,
                    (max-width: 991px) 33vw,
                    (max-width: 1199px) 25vw,
                    220px
                "
                alt="{{ $productImageAlt }}"
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
            <a href="{{ route('products.show', $product) }}"
                data-name="{{ $product->var_name }}" class="bb-product-title font-weight-bold" style="font-size: 13.5px; line-height: 1.35; color: #1e293b; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-decoration: none; min-height: auto;" wire:navigate.hover>
                {{ $product->name }}
            </a>
        </div>

        @php
            // Use loaded reviews if available to avoid N+1 queries
            $approvedReviews = $product->relationLoaded('reviews') ? $product->reviews : collect();
            if ($product->relationLoaded('reviews')) {
                $totalReviews = $approvedReviews->count();
                $overallRatings = $approvedReviews->flatMap(
                    fn($review) => $review->relationLoaded('ratings')
                        ? $review->ratings->where('key', 'overall')
                        : collect(),
                );
                $averageRating = $overallRatings->count() > 0 ? $overallRatings->avg('value') : 0;
            } else {
                $averageRating = $product->averageRating('overall') ?? 0;
                $totalReviews = $product->totalReviews() ?? 0;
            }
        @endphp
        @if ($averageRating > 0)
            <div class="gap-1 mb-1 d-flex align-items-center" style="font-size: 11px;">
                <div class="d-flex align-items-center text-warning" style="font-size: 10px;">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= floor($averageRating))
                            <i class="fa fa-star"></i>
                        @elseif($i - 0.5 <= $averageRating)
                            <i class="fa fa-star-half-alt"></i>
                        @else
                            <i class="far fa-star text-muted"></i>
                        @endif
                    @endfor
                </div>
                <span class="text-muted ml-1 font-weight-bold">
                    {{ number_format($averageRating, 1) }}
                </span>
            </div>
        @endif

        @php
            $show_option = setting('show_option');
            $guest_can_see_price = (bool) ($show_option->guest_can_see_price ?? false);
            $should_hide_price =
                isOninda() &&
                !$guest_can_see_price &&
                (auth('user')->guest() || (auth('user')->check() && !auth('user')->user()->is_verified));
        @endphp

        <!-- Prices Row Container -->
        <div class="product-card__prices" style="margin-top: 2px !important; margin-bottom: 8px !important; padding: 0 !important; flex: 0 0 auto;">
            @if (isOninda() && (app()->bound('app.resell') ? app('app.resell') : config('app.resell')))
                <div class="product-card__retail-price mb-1 d-flex justify-content-between align-items-center" style="font-size: 12px;">
                    <span style="color: #64748b; font-weight: 500;">Retail:</span>
                    <strong style="color: #0f172a;">{!! theMoney($product->retailPrice()) !!}</strong>
                </div>
                <div class="product-card__wholesale-price d-flex align-items-baseline justify-content-between" style="font-size: 12px;">
                    @if (auth('user')->guest())
                        <span style="color: #64748b; font-weight: 500;">Wholesale:</span>
                        <a href="{{ Route::has('auth.login') ? route('auth.login') : route('user.login') }}" style="color: #2563eb; font-weight: 700; text-decoration: none;">Login</a>
                    @elseif ($should_hide_price)
                        <span class="text-danger font-weight-bold">Verify account</span>
                    @elseif ($product->selling_price == $product->price)
                        <span class="font-weight-bold" style="color: var(--brand-dark); font-size: 15px;">{!! $product->price ? theMoney($product->price) : 'Contact' !!}</span>
                    @else
                        <span class="font-weight-bold" style="color: var(--brand-dark); font-size: 15px;">{!! theMoney($product->selling_price) !!}</span>
                        <span class="text-muted" style="text-decoration: line-through; font-size: 12px;">{!! theMoney($product->price) !!}</span>
                    @endif
                </div>
            @else
                @if ($should_hide_price)
                    <span class="text-danger font-weight-bold" style="font-size: 12px;">
                        {{ auth('user')->guest() ? 'Login to see price' : 'Verify account' }}
                    </span>
                @elseif ($product->selling_price == $product->price)
                    <div class="d-flex align-items-baseline">
                        <span class="product-card__new-price font-weight-bold" style="color: var(--brand-dark); font-size: 16px;">
                            {!! $product->price ? theMoney($product->price) : 'Contact for price' !!}
                        </span>
                    </div>
                @else
                    <div class="d-flex align-items-baseline gap-2">
                        <span class="product-card__new-price font-weight-bold" style="color: var(--brand-dark); font-size: 16px;">
                            {!! theMoney($product->selling_price) !!}
                        </span>
                        <span class="product-card__old-price" style="color: #94a3b8; text-decoration: line-through; font-size: 13px;">
                            {!! theMoney($product->price) !!}
                        </span>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Actions / Buttons Row (Centered) -->
    <div class="product-card__actions" style="padding: 0 10px 10px; width: 100%; display: flex; justify-content: center; align-items: center; margin-top: auto; box-sizing: border-box;">
        @if (!isOninda())
            <div class="product-card__buttons w-100 d-flex justify-content-center align-items-center" style="margin: 0 !important; width: 100% !important;">
                @php
                    $available = !$product->should_track || $product->stock_count > 0;
                    $has_variations = ($product->relationLoaded('variations') ? $product->variations->count() : ($product->variations_count ?? 0)) > 1;
                @endphp
                @if (($show_option->product_grid_button ?? false) == 'add_to_cart')
                    @if ($has_variations)
                        <a href="{{ route('products.show', $product) }}" wire:navigate.hover class="btn bb-btn-card product-card__addtocart w-100 d-flex align-items-center justify-content-center text-center gap-2" style="text-decoration: none; width: 100% !important; margin: 0 auto !important;">
                            <i class="fas fa-cart-plus mr-1" style="font-size: 14px;"></i>
                            <span>{{ $show_option->add_to_cart_text ?? 'Add to Cart' }}</span>
                        </a>
                    @else
                        <button wire:click="addToCart" class="btn bb-btn-card product-card__addtocart w-100 d-flex align-items-center justify-content-center text-center gap-2" type="button"
                            {{ $available ? '' : 'disabled' }} style="width: 100% !important; margin: 0 auto !important;">
                            <i class="fas fa-cart-plus mr-1" style="font-size: 14px;"></i>
                            <span>{{ $show_option->add_to_cart_text ?? 'Add to Cart' }}</span>
                        </button>
                    @endif
                @endif
                @if (($show_option->product_grid_button ?? false) == 'order_now')
                    @if ($has_variations)
                        <a href="{{ route('products.show', $product) }}" wire:navigate.hover class="btn bb-btn-card-order product-card__ordernow w-100 d-flex align-items-center justify-content-center text-center gap-2" style="text-decoration: none; width: 100% !important; margin: 0 auto !important;">
                            <i class="fas fa-bag-shopping mr-1" style="font-size: 14px;"></i>
                            <span>{{ $show_option->order_now_text ?? 'অর্ডার করুন' }}</span>
                        </a>
                    @else
                        <button wire:click="addToCart('kart')" class="btn bb-btn-card-order product-card__ordernow w-100 d-flex align-items-center justify-content-center text-center gap-2" type="button"
                            {{ $available ? '' : 'disabled' }} style="width: 100% !important; margin: 0 auto !important;">
                            <i class="fas fa-bag-shopping mr-1" style="font-size: 14px;"></i>
                            <span>{{ $show_option->order_now_text ?? 'অর্ডার করুন' }}</span>
                        </button>
                    @endif
                @endif
            </div>
        @endif
    </div>
</div>
