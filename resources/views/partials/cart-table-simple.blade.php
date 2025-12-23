<div class="simple-cart-table-wrapper table-responsive">
    <table class="table mb-0 simple-cart-table">
        <thead>
            <tr>
                <th class="text-left align-middle">Product</th>
                <th class="text-center align-middle">Buy Price</th>
                @if (isOninda())
                    <th class="text-center align-middle">Sell Price</th>
                @endif
                <th class="text-center align-middle">Quantity</th>
                <th class="text-right align-middle">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse (cart()->content() as $product)
                <tr data-id="{{ $product->id }}">
                    <td class="align-middle">
                        <div class="d-flex align-items-center">
                            <button type="button"
                                class="p-0 mr-3 btn btn-link text-danger simple-cart-remove"
                                aria-label="Remove"
                                wire:click="remove('{{ $product->rowId }}')">
                                <i class="fa fa-trash"></i>
                            </button>
                            <div class="mr-3 simple-cart-thumb">
                                <img src="{{ asset($product->options->image) }}" alt="{{ $product->name }}">
                            </div>
                            <div class="simple-cart-product">
                                <div class="font-weight-semibold">
                                    {{ $product->name }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center align-middle">
                        {!! theMoney($product->price) !!}
                    </td>
                    @if (isOninda())
                        <td class="text-center align-middle">
                            <div class="d-inline-flex align-items-center">
                                <input type="number"
                                    class="form-control form-control-sm text-center"
                                    x-model="retail['{{ $product->id }}'].price"
                                    min="0"
                                    @focus="$event.target.select()" />
                            </div>
                        </td>
                    @endif
                    <td class="text-center align-middle">
                        <div class="d-inline-flex align-items-center simple-qty-control">
                            <button type="button"
                                class="btn btn-sm simple-qty-btn simple-qty-btn--minus"
                                wire:click="decreaseQuantity('{{ $product->rowId }}')">-</button>
                            <input class="mx-1 text-center simple-qty-input"
                                type="number"
                                min="1"
                                value="{{ $product->qty }}"
                                readonly>
                            <button type="button"
                                class="btn btn-sm simple-qty-btn simple-qty-btn--plus"
                                wire:click="increaseQuantity('{{ $product->rowId }}')">+</button>
                        </div>
                    </td>
                    <td class="text-right align-middle">
                        {!! theMoney($product->price * $product->qty) !!}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="py-3 text-center text-danger font-weight-semibold">
                        No items in cart.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


