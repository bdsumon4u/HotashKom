<div class="table-responsive">
    <table class="cart__table cart-table">
        <thead class="cart-table__head">
            <tr class="cart-table__row">
                <th class="cart-table__column cart-table__column--image">Image</th>
                <th class="cart-table__column cart-table__column--product">Product</th>
                <th class="cart-table__column cart-table__column--price">Price</th>
                <th class="cart-table__column cart-table__column--quantity">Quantity</th>
                <th class="cart-table__column cart-table__column--total">Total</th>
                <th class="cart-table__column cart-table__column--remove"></th>
            </tr>
        </thead>
        <tbody class="cart-table__body">
            @forelse (cart()->content() as $product)
                <tr class="cart-table__row" data-id="{{ $product->id }}">
                    <td class="cart-table__column cart-table__column--image">
                        <a href="{{ route('products.show', $product->options->slug) }}">
                            <img src="{{ asset($product->options->image) }}" alt=""></a>
                    </td>
                    <td class="cart-table__column cart-table__column--product">
                        <a href="{{ route('products.show', $product->options->slug) }}"
                            class="cart-table__product-name">{{ $product->name }}</a>
                    </td>
                    <td class="cart-table__column cart-table__column--price" data-title="Price">TK {{ $product->price }}
                    </td>
                    <td class="cart-table__column cart-table__column--quantity" data-title="Quantity">
                        <div class="input-number product__quantity">
                            <input class="form-control input-number__input" type="number" min="1"
                                value="{{ $product->qty }}" max="{{ $product->max }}" readonly />
                            <div class="input-number__add" wire:click="increaseQuantity('{{ $product->rowId }}')"></div>
                            <div class="input-number__sub" wire:click="decreaseQuantity('{{ $product->rowId }}')"></div>
                        </div>
                    </td>
                    <td class="cart-table__column cart-table__column--total" data-title="Total">TK
                        {{ $product->price * $product->qty }}</td>
                    <td class="cart-table__column cart-table__column--remove">
                        <button type="button" class="btn btn-light btn-sm btn-svg-icon"
                            wire:click="remove('{{ $product->rowId }}')">
                            <svg width="12px" height="12px">
                                <use xlink:href="{{ asset('strokya/images/sprite.svg#cross-12') }}"></use>
                            </svg>
                        </button>
                    </td>
                </tr>
            @empty
                <tr class="bg-danger">
                    <td colspan="6" class="py-2 text-center">No Items In Cart.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
