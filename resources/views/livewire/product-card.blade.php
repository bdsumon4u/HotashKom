<div class="product-card-enhanced" data-id="{{ $product->id }}"
    data-max="{{ $product->should_track ? $product->stock_count : -1 }}">
    @php($in_stock = !$product->should_track || $product->stock_count > 0)

    <div class="product-image-container">
        <a href="{{ route('products.show', $product) }}">
            <img src="{{ asset(optional($product->base_image)->src) }}"
                 alt="{{ $product->name }}"
                 loading="lazy">
        </a>

        @if ($product->price != $product->selling_price)
            @php($percent = round((($product->price - $product->selling_price) * 100) / $product->price, 0, PHP_ROUND_HALF_UP))
            <div class="product-badge-sale">
                {{$percent}}% OFF
            </div>
        @endif
    </div>

    <div class="product-info-enhanced">
        <h3 class="product-title-enhanced">
            <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
        </h3>

        <div class="product-price-enhanced {{ $product->selling_price == $product->price ? '' : 'has-special' }}">
            @if ($product->selling_price == $product->price)
                {!! theMoney($product->price) !!}
            @else
                <span class="product-price-new">{!! theMoney($product->selling_price) !!}</span>
                <span class="product-price-old">{!! theMoney($product->price) !!}</span>
            @endif
        </div>

        <div class="product-actions-enhanced">
            @php($available = !$product->should_track || $product->stock_count > 0)
            @php($show_option = setting('show_option'))

            {{-- Always show Add to Cart button (small square) --}}
            <button wire:click="addToCart" class="cart-btn" title="Add to Cart" {{ $available ? '' : 'disabled' }}>
                <i class="fa fa-shopping-cart"></i>
            </button>

            {{-- Always show Buy Now button (large rectangular) --}}
            <button wire:click="orderNow" class="buy-now-btn" {{ $available ? '' : 'disabled' }}>
                Buy Now <i class="fa fa-arrow-right"></i>
            </button>
        </div>
    </div>
</div>
