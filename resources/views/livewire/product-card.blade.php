<div class="product-card" data-id="{{ $product->id }}"
    data-max="{{ $product->should_track ? $product->stock_count : -1 }}">
    @php
        $in_stock = !$product->should_track || $product->stock_count > 0;
    @endphp
    <div class="product-card__badges-list">
        @if (!$in_stock)
            <div class="product-card__badge product-card__badge--sale">Sold</div>
        @endif
        @if ($product->price != $product->selling_price)
            @php
                $percent = round(
                    (($product->price - $product->selling_price) * 100) / $product->price,
                    0,
                    PHP_ROUND_HALF_UP,
                );
            @endphp
            <div class="product-card__badge product-card__badge--sale">
                {!! str_replace('[percent]', $percent, setting('discount_text') ?? '<small>Discount:</small> [percent]%') !!}
            </div>
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
        <div class="product-card__prices {{ $product->selling_price == $product->price ? '' : 'has-special' }}">
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
        </div>
        @if (!isOninda())
            <div class="product-card__buttons">
                @php($available = !$product->should_track || $product->stock_count > 0)
                @if (($show_option->product_grid_button ?? false) == 'add_to_cart')
                    <button wire:click="addToCart" class="btn btn-primary product-card__addtocart" type="button"
                        {{ $available ? '' : 'disabled' }}>
                        {!! $show_option->add_to_cart_icon ?? null !!}
                        <span class="ml-1">{{ $show_option->add_to_cart_text ?? '' }}</span>
                    </button>
                @endif
                @if (($show_option->product_grid_button ?? false) == 'order_now')
                    <button wire:click="addToCart('kart')" class="btn btn-primary product-card__ordernow" type="button"
                        {{ $available ? '' : 'disabled' }}>
                        {!! $show_option->order_now_icon ?? null !!}
                        <span class="ml-1">{{ $show_option->order_now_text ?? '' }}</span>
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
