<div class="dropcart">
    <div class="dropcart__products-list">
        @forelse($cart as $product)
        <div class="dropcart__product" data-id="{{$product['id']}}">
            <div class="dropcart__product-image">
                <a href="{{route('products.show', $product['slug'])}}">
                    <img src="{{asset($product['image'])}}" alt="">
                </a>
            </div>
            <div class="dropcart__product-info">
                <div class="dropcart__product-name">
                    <a href="{{route('products.show', $product['slug'])}}">{{$product['name']}}</a>
                </div>
                <div class="dropcart__product-meta">
                    <span class="dropcart__product-quantity">{{$product['quantity']}}</span> x <span class="dropcart__product-price">TK {{$product['price']}}</span>
                </div>
            </div>
            <button type="button" class="dropcart__product-remove btn btn-light btn-sm btn-svg-icon" wire:click="remove({{$product['id']}})">
                <svg width="10px" height="10px">
                    <use xlink:href="{{ asset('strokya/images/sprite.svg#cross-10') }}"></use>
                </svg>
            </button>
        </div>
        @empty
        <strong>No Items In Cart.</strong>
        @endforelse
    </div>
    <div class="dropcart__totals">
        <table>
            <tr>
                <th>Subtotal</th>
                <td class="cart-subtotal">{!!  theMoney($subtotal)  !!}</td>
            </tr>
        </table>
    </div>
    <div class="dropcart__buttons">
        <a class="btn btn-primary" href="{{ route('checkout') }}">Checkout</a>
    </div>
</div><!-- .dropcart / end -->