<div class="dropcart">
    <div class="dropcart__products-list">
        @forelse(cart()->content() as $product)
            <div class="dropcart__product" data-id="{{ $product->id }}">
                <div class="dropcart__product-image">
                    <a href="{{ route('products.show', $product->options->slug) }}" wire:navigate.hover>
                        <img src="{{ asset($product->options->image) }}" alt="">
                    </a>
                </div>
                <div class="dropcart__product-info">
                    <div class="dropcart__product-name">
                        <a href="{{ route('products.show', $product->options->slug) }}" wire:navigate.hover>{{ $product->name }}</a>
                    </div>
                    <div class="dropcart__product-meta">
                        <span class="dropcart__product-quantity">{{ $product->qty }}</span> x <span
                            class="dropcart__product-price">TK {{ $product->price }}</span>
                    </div>
                </div>
                <button type="button" class="dropcart__product-remove btn btn-light btn-sm btn-svg-icon"
                    wire:click="remove('{{ $product->rowId }}')">
                    <svg width="10px" height="10px" viewBox="0 0 10 10"><path d="M8.8 8.8c-.4.4-1 .4-1.4 0L5 6.4 2.6 8.8c-.4.4-1 .4-1.4 0-.4-.4-.4-1 0-1.4L3.6 5 1.2 2.6c-.4-.4-.4-1 0-1.4.4-.4 1-.4 1.4 0L5 3.6l2.4-2.4c.4-.4 1-.4 1.4 0 .4.4.4 1 0 1.4L6.4 5l2.4 2.4c.4.4.4 1 0 1.4z"/></svg>
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
                <td class="cart-subtotal">{!! theMoney(cart()->subTotal()) !!}</td>
            </tr>
        </table>
    </div>
    <div class="dropcart__buttons">
        <a class="btn btn-outline-primary btn-sm d-none" href="{{ route('reseller.checkout') }}" wire:navigate.hover>View Cart</a>
        <a class="btn btn-primary" href="{{ auth('user')->check() ? route('reseller.checkout') : route('checkout') }}" wire:navigate.hover>Checkout</a>
    </div>
</div><!-- .dropcart / end -->
