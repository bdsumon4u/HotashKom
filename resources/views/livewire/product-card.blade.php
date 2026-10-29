@if (isGbazTheme())
    @php
        $show_option = setting('show_option');
        $hasDiscount = $product->price != $product->selling_price && $product->price > $product->selling_price;
        $percent = $hasDiscount && $product->price > 0
            ? round((($product->price - $product->selling_price) * 100) / $product->price, 0, PHP_ROUND_HALF_UP)
            : 0;
        $guest_can_see_price = (bool) ($show_option->guest_can_see_price ?? false);
        $should_hide_price =
            isOninda() &&
            ! $guest_can_see_price &&
            (auth('user')->guest() || (auth('user')->check() && ! auth('user')->user()->is_verified));
        $addToCartIcon = ! empty(trim($show_option->add_to_cart_icon ?? '')) ? $show_option->add_to_cart_icon : '<i class="fas fa-shopping-cart"></i>';
        $addToCartText = ! empty(trim($show_option->add_to_cart_text ?? '')) ? $show_option->add_to_cart_text : 'Add To Cart';
        $orderNowIcon = ! empty(trim($show_option->order_now_icon ?? '')) ? $show_option->order_now_icon : '<i class="fas fa-shopping-cart"></i>';
        $orderNowText = ! empty(trim($show_option->order_now_text ?? '')) ? $show_option->order_now_text : 'Add To Cart';
    @endphp
    <div class="product-card" data-id="{{ $product->id }}" data-max="-1">
        <div class="product-card__badges-list">
            @if ($hasDiscount && $percent > 0)
                <span class="gb-badge-offer">Offered Items</span>
                <span class="gb-badge-save">Save {{ $percent }}%</span>
            @elseif ($is_free_delivery)
                <span class="gb-badge-offer">Free Delivery</span>
            @endif
        </div>
        <div class="product-card__image">
            <a href="{{ route('products.show', $product) }}" wire:navigate.hover style="display: block; width: 100%; height: 100%;">
                <img src="{{ cdn(optional($product->base_image)->src) }}" alt="{{ $product->name }}" loading="lazy">
            </a>
        </div>
        <div class="product-card__info">
            <div class="product-card__name">
                <a href="{{ route('products.show', $product) }}" wire:navigate.hover
                    data-name="{{ $product->var_name }}">{{ $product->name }}</a>
            </div>
        </div>
        <div class="product-card__actions">
            <div class="product-card__prices {{ $hasDiscount ? 'has-special' : '' }}">
                @if (isOninda() && (app()->bound('app.resell') ? app('app.resell') : config('app.resell')))
                    <div class="product-card__retail-price" style="margin-bottom: 2px;">
                        <span style="color: #6b7280; font-weight: 500;">Retail price:</span>
                        <span style="font-weight: 700; color: #111827;">৳{{ number_format((float) $product->retailPrice()) }}</span>
                    </div>
                    <div class="product-card__wholesale-price">
                        @if (auth('user')->guest())
                            <span style="color: #6b7280; font-weight: 500;">Wholesale price:</span>
                            <a href="{{ Route::has('auth.login') ? route('auth.login') : route('user.login') }}" style="color: #2563eb; font-weight: 700; text-decoration: none; border-bottom: 1px dashed #2563eb; padding-bottom: 1px;">Login</a>
                        @elseif ($should_hide_price)
                            <span class="product-card__new-price text-danger" style="font-weight: 700; font-size: 12px;">
                                Verify account to see price
                            </span>
                        @elseif (! $hasDiscount)
                            <span class="product-card__new-price">{{ $product->price ? '৳' . number_format((float) $product->price) : 'Contact for price' }}</span>
                        @else
                            <span class="product-card__new-price">৳{{ number_format((float) $product->selling_price) }}</span>
                            <span class="product-card__old-price">৳{{ number_format((float) $product->price) }}</span>
                        @endif
                    </div>
                @else
                    @if ($should_hide_price)
                        <span class="product-card__new-price text-danger">
                            {{ auth('user')->guest() ? 'Login to see price' : 'Verify account to see price' }}
                        </span>
                    @elseif (! $hasDiscount)
                        <span class="product-card__new-price">{{ $product->price ? '৳' . number_format((float) $product->price) : 'Contact for price' }}</span>
                    @else
                        <span class="product-card__new-price">৳{{ number_format((float) $product->selling_price) }}</span>
                        <span class="product-card__old-price">৳{{ number_format((float) $product->price) }}</span>
                    @endif
                @endif
            </div>
            @if (!isOninda())
                <div class="product-card__buttons">
                    @php($available = true)
                    @php($has_variations = ($product->relationLoaded('variations') ? $product->variations->count() : ($product->variations_count ?? 0)) > 1)
                    @if (($show_option->product_grid_button ?? 'add_to_cart') == 'add_to_cart')
                        @if ($has_variations)
                            <a href="{{ route('products.show', $product) }}" wire:navigate.hover class="btn product-card__addtocart" style="text-decoration: none;">
                                {!! $addToCartIcon !!}
                                <span>{{ $addToCartText }}</span>
                            </a>
                        @else
                            <button wire:click="addToCart" class="btn product-card__addtocart" type="button"
                                {{ $available ? '' : 'disabled' }}>
                                {!! $addToCartIcon !!}
                                <span>{{ $addToCartText }}</span>
                            </button>
                        @endif
                    @elseif (($show_option->product_grid_button ?? false) == 'order_now')
                        @if ($has_variations)
                            <a href="{{ route('products.show', $product) }}" wire:navigate.hover class="btn product-card__ordernow" style="text-decoration: none;">
                                {!! $orderNowIcon !!}
                                <span>{{ $orderNowText }}</span>
                            </a>
                        @else
                            <button wire:click="addToCart('kart')" class="btn product-card__ordernow" type="button"
                                {{ $available ? '' : 'disabled' }}>
                                {!! $orderNowIcon !!}
                                <span>{{ $orderNowText }}</span>
                            </button>
                        @endif
                    @endif
                </div>
            @endif
        </div>
    </div>
@else
    <div class="product-card" data-id="{{ $product->id }}"
        data-max="-1">
        @if ($is_free_delivery)
            <div class="product-card__ribbon">
                <span class="badge badge--free-delivery">Free Delivery</span>
            </div>
        @endif
        @php
            $in_stock = true;
        @endphp
        <div class="product-card__badges-list">
            @if ($product->price != $product->selling_price)
                @php
                    $percent = round(
                        (($product->price - $product->selling_price) * 100) / $product->price,
                        0,
                        PHP_ROUND_HALF_UP,
                    );
                    $discountText = str_replace('[percent]', $percent, setting('discount_text') ?? '');
                @endphp
                @if (! empty(trim($discountText)))
                    <div class="product-card__badge product-card__badge--sale">
                        {!! $discountText !!}
                    </div>
                @endif
            @endif
        </div>
        <div class="product-card__image" style="aspect-ratio: 1 / 1; overflow: hidden;">
            <a href="{{ route('products.show', $product) }}" wire:navigate.hover style="display: block; width: 100%; height: 100%;">
                <img src="{{ cdn(optional($product->base_image)->src) }}" alt="Base Image" loading="lazy"
                    style="width: 100%; height: 100%; object-fit: cover;">
            </a>
        </div>
        <div class="product-card__info">
            <div class="product-card__name">
                <a href="{{ route('products.show', $product) }}" wire:navigate.hover
                    data-name="{{ $product->var_name }}">{{ $product->name }}</a>
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
                <div class="gap-2 d-flex align-items-center" style="font-size: 0.875rem;">
                    <div class="d-flex align-items-center" style="margin-top: -1px;">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= floor($averageRating))
                                <i class="fa fa-star text-warning" style="font-size: 0.75rem;"></i>
                            @elseif($i - 0.5 <= $averageRating)
                                <i class="fa fa-star-half-alt text-warning" style="font-size: 0.75rem;"></i>
                            @else
                                <i class="far fa-star text-muted" style="font-size: 0.75rem;"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="text-muted small" style="margin-top: 1px;">
                        <strong>{{ number_format($averageRating, 1) }}</strong>
                        ({{ $totalReviews }} {{ Str::plural('review', $totalReviews) }})
                    </span>
                </div>
            @endif
        </div>
        <div class="product-card__actions">
            <div class="product-card__availability">Availability:
                @if (!$product->should_track)
                    <span class="text-success">In Stock</span>
                @else
                    <span class="text-{{ $product->stock_count ? 'success' : 'danger' }}">{{ $product->stock_count }} In
                        Stock</span>
                @endif
            </div>
            @php
                $show_option = setting('show_option');
                $guest_can_see_price = (bool) ($show_option->guest_can_see_price ?? false);
                $should_hide_price =
                    isOninda() &&
                    !$guest_can_see_price &&
                    (auth('user')->guest() || (auth('user')->check() && !auth('user')->user()->is_verified));
            @endphp
            <div class="product-card__prices {{ $product->selling_price == $product->price ? '' : 'has-special' }}" style="font-size: 13px; font-weight: normal; line-height: 1.5; margin-top: 4px;">
                @if (isOninda() && (app()->bound('app.resell') ? app('app.resell') : config('app.resell')))
                    <div class="product-card__retail-price" style="margin-bottom: 2px;">
                        <span style="color: #6b7280; font-weight: 500;">Retail price:</span>
                        <span style="font-weight: 700; color: #111827;">{!! theMoney($product->retailPrice()) !!}</span>
                    </div>
                    <div class="product-card__wholesale-price">
                        @if (auth('user')->guest())
                            <span style="color: #6b7280; font-weight: 500;">Wholesale price:</span>
                            <a href="{{ Route::has('auth.login') ? route('auth.login') : route('user.login') }}" style="color: #2563eb; font-weight: 700; text-decoration: none; border-bottom: 1px dashed #2563eb; padding-bottom: 1px;">Login</a>
                        @elseif ($should_hide_price)
                            <span class="product-card__new-price text-danger" style="font-weight: 700; font-size: 12px;">
                                Verify account to see price
                            </span>
                        @elseif ($product->selling_price == $product->price)
                            <span style="font-weight: 700; color: #111827;">{!! $product->price ? theMoney($product->price) : 'Contact for price' !!}</span>
                        @else
                            <span class="product-card__new-price" style="font-weight: 700;">{!! theMoney($product->selling_price) !!}</span>
                            <span class="product-card__old-price" style="margin-left: 4px;">{!! theMoney($product->price) !!}</span>
                        @endif
                    </div>
                @else
                    @if ($should_hide_price)
                        <span class="product-card__new-price text-danger">
                            {{ auth('user')->guest() ? 'Login to see price' : 'Verify account to see price' }}
                        </span>
                    @elseif ($product->selling_price == $product->price)
                        {!! $product->price ? theMoney($product->price) : 'Contact for price' !!}
                    @else
                        <span class="product-card__new-price">{!! theMoney($product->selling_price) !!}</span>
                        <span class="product-card__old-price">{!! theMoney($product->price) !!}</span>
                    @endif
                @endif
            </div>
            @if (!isOninda())
                <div class="product-card__buttons">
                    @php($available = true)
                    @php($has_variations = ($product->relationLoaded('variations') ? $product->variations->count() : ($product->variations_count ?? 0)) > 1)
                    @if (($show_option->product_grid_button ?? false) == 'add_to_cart')
                        @if ($has_variations)
                            <a href="{{ route('products.show', $product) }}" wire:navigate.hover class="btn btn-primary product-card__addtocart" style="text-decoration: none;">
                                {!! $show_option->add_to_cart_icon ?? null !!}
                                <span class="ml-1">{{ $show_option->add_to_cart_text ?? '' }}</span>
                            </a>
                        @else
                            <button wire:click="addToCart" class="btn btn-primary product-card__addtocart" type="button"
                                {{ $available ? '' : 'disabled' }}>
                                {!! $show_option->add_to_cart_icon ?? null !!}
                                <span class="ml-1">{{ $show_option->add_to_cart_text ?? '' }}</span>
                            </button>
                        @endif
                    @endif
                    @if (($show_option->product_grid_button ?? false) == 'order_now')
                        @if ($has_variations)
                            <a href="{{ route('products.show', $product) }}" wire:navigate.hover class="btn btn-primary product-card__ordernow" style="text-decoration: none;">
                                {!! $show_option->order_now_icon ?? null !!}
                                <span class="ml-1">{{ $show_option->order_now_text ?? '' }}</span>
                            </a>
                        @else
                            <button wire:click="addToCart('kart')" class="btn btn-primary product-card__ordernow" type="button"
                                {{ $available ? '' : 'disabled' }}>
                                {!! $show_option->order_now_icon ?? null !!}
                                <span class="ml-1">{{ $show_option->order_now_text ?? '' }}</span>
                            </button>
                        @endif
                    @endif
                </div>
            @endif
        </div>
    </div>
@endif
