@props(['product', 'show_option' => null, 'free_delivery' => null])
@php
    $show_option = $show_option ?? setting('show_option');
    $free_delivery = $free_delivery ?? setting('free_delivery');
    $is_free_delivery = false;
    if ($free_delivery->enabled ?? false) {
        $is_free_delivery = ($free_delivery->for_all ?? false) || array_key_exists($product->id, (array) ($free_delivery->products ?? []));
    }
    $imageSrc = $product->thumbnail->first()?->src ?? optional($product->base_image)->src ?? null;
    $hasDiscount = $product->price != $product->selling_price;
    $percent = $hasDiscount && $product->price > 0 ? round((($product->price - $product->selling_price) * 100) / $product->price) : 0;
    $discountText = $percent > 0 ? str_replace('[percent]', $percent, setting('discount_text') ?? '') : '';
    $hasVariations = ($product->relationLoaded('variations') ? $product->variations->count() : ($product->variations_count ?? 0)) > 1;
    $productUrl = route('products.show', $product);
    $isOninda = isOninda();
    $guestCanSeePrice = (bool) ($show_option->guest_can_see_price ?? false);
    $shouldHidePrice = $isOninda && ! $guestCanSeePrice && (auth('user')->guest() || (auth('user')->check() && ! auth('user')->user()->is_verified));
    $buttonType = $show_option->product_grid_button ?? 'add_to_cart';

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
        $averageRating = 0;
        $totalReviews = 0;
    }
@endphp

<div class="product-card" data-id="{{ $product->id }}" data-max="-1">
    @if ($is_free_delivery)
        <div class="product-card__ribbon">
            <span class="badge badge--free-delivery">Free Delivery</span>
        </div>
    @endif
    <div class="product-card__badges-list">
        @if ($hasDiscount && ! empty(trim($discountText)))
            <div class="product-card__badge product-card__badge--sale">
                {!! $discountText !!}
            </div>
        @endif
    </div>
    <div class="product-card__image" style="aspect-ratio: 1 / 1; overflow: hidden;">
        <a href="{{ $productUrl }}" wire:navigate.hover style="display: block; width: 100%; height: 100%;">
            <img src="{{ cdn($imageSrc) }}" alt="{{ $product->name }}" loading="lazy"
                style="width: 100%; height: 100%; object-fit: cover;">
        </a>
    </div>
    <div class="product-card__info">
        <div class="product-card__name">
            <a href="{{ $productUrl }}" wire:navigate.hover data-name="{{ $product->var_name ?? $product->name }}">{{ $product->name }}</a>
        </div>
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
            @if (! $product->should_track)
                <span class="text-success">In Stock</span>
            @else
                <span class="text-{{ $product->stock_count ? 'success' : 'danger' }}">{{ $product->stock_count }} In Stock</span>
            @endif
        </div>
        <div class="product-card__prices {{ $product->selling_price == $product->price ? '' : 'has-special' }}" style="font-size: 13px; font-weight: normal; line-height: 1.5; margin-top: 4px;">
            @if ($isOninda && (app()->bound('app.resell') ? app('app.resell') : config('app.resell')))
                <div class="product-card__retail-price" style="margin-bottom: 2px;">
                    <span style="color: #6b7280; font-weight: 500;">Retail price:</span>
                    <span style="font-weight: 700; color: #111827;">{!! theMoney($product->retailPrice()) !!}</span>
                </div>
                <div class="product-card__wholesale-price">
                    @if (auth('user')->guest())
                        <span style="color: #6b7280; font-weight: 500;">Wholesale price:</span>
                        <a href="{{ Route::has('auth.login') ? route('auth.login') : route('user.login') }}" style="color: #2563eb; font-weight: 700; text-decoration: none; border-bottom: 1px dashed #2563eb; padding-bottom: 1px;">Login</a>
                    @elseif ($shouldHidePrice)
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
                @if ($shouldHidePrice)
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
        @if (! $isOninda)
            <div class="product-card__buttons">
                @if ($buttonType == 'add_to_cart')
                    @if ($hasVariations)
                        <a href="{{ $productUrl }}" wire:navigate.hover class="btn btn-primary product-card__addtocart" style="text-decoration: none;">
                            {!! $show_option->add_to_cart_icon ?? null !!}
                            <span class="ml-1">{{ $show_option->add_to_cart_text ?? 'Add to Cart' }}</span>
                        </a>
                    @else
                        <button class="btn btn-primary product-card__addtocart" type="button"
                            data-product-id="{{ $product->id }}" data-action="add" onclick="handleAddToCart(this)">
                            {!! $show_option->add_to_cart_icon ?? null !!}
                            <span class="ml-1">{{ $show_option->add_to_cart_text ?? 'Add to Cart' }}</span>
                        </button>
                    @endif
                @elseif ($buttonType == 'order_now')
                    @if ($hasVariations)
                        <a href="{{ $productUrl }}" wire:navigate.hover class="btn btn-primary product-card__ordernow order-now-drift" style="text-decoration: none;">
                            {!! $show_option->order_now_icon ?? null !!}
                            <span class="ml-1">{{ $show_option->order_now_text ?? 'Order Now' }}</span>
                        </a>
                    @else
                        <button class="btn btn-primary product-card__ordernow order-now-drift" type="button"
                            data-product-id="{{ $product->id }}" data-action="kart" onclick="handleAddToCart(this)">
                            {!! $show_option->order_now_icon ?? null !!}
                            <span class="ml-1">{{ $show_option->order_now_text ?? 'Order Now' }}</span>
                        </button>
                    @endif
                @endif
            </div>
        @endif
    </div>
</div>
