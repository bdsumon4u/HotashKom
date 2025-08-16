<div class="block block-products-carousel">
    <div class="container">
        @if($title ?? null)
            <div class="section-title">
                <h2>{{ $title }}</h2>
            </div>
        @endif

        <div class="products-grid-modern" data-cols="{{ $cols ?? 3 }}">
            @foreach($products as $product)
                <div class="product-card-enhanced">
                    <div class="product-image-container">
                        <a href="{{route('products.show', $product)}}">
                            <img src="{{asset($product->base_image->src)}}"
                                 alt="{{$product->name}}"
                                 loading="lazy">
                        </a>

                        @if($product->price != $product->selling_price)
                            @php($percent = round((($product->price - $product->selling_price) * 100) / $product->price, 0, PHP_ROUND_HALF_UP))
                            <div class="product-badge-sale">
                                {{$percent}}% OFF
                            </div>
                        @endif
                    </div>

                    <div class="product-info-enhanced">
                        <h3 class="product-title-enhanced">
                            <a href="{{route('products.show', $product)}}">{{$product->name}}</a>
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
                            <button class="cart-btn" title="Add to Cart">
                                <i class="fa fa-shopping-cart"></i>
                            </button>
                            <a href="{{route('products.show', $product)}}" class="buy-now-btn">
                                Buy Now <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
