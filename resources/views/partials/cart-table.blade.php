<div class="table-responsive">
    <table class="cart__table cart-table w-100" style="font-size: 14px;">
        <thead class="cart-table__head" style="background: #f8fafc; border-bottom: 1.5px solid #e2e8f0;">
            <tr class="cart-table__row">
                <th class="cart-table__column cart-table__column--image py-3" style="font-weight: 700; color: #475569;">Image</th>
                <th class="cart-table__column cart-table__column--product py-3" style="font-weight: 700; color: #475569;">Product</th>
                <th class="cart-table__column cart-table__column--price py-3" style="font-weight: 700; color: #475569;">Price</th>
                @if (isOninda())
                <th class="cart-table__column cart-table__column--price py-3" style="font-weight: 700; color: #475569;">Sell Price</th>
                @endif
                <th class="cart-table__column cart-table__column--quantity py-3" style="font-weight: 700; color: #475569;">Quantity</th>
                <th class="cart-table__column cart-table__column--total py-3" style="font-weight: 700; color: #475569;">Total</th>
                <th class="cart-table__column cart-table__column--remove py-3"></th>
            </tr>
        </thead>
        <tbody class="cart-table__body">
            @forelse (cart()->content() as $product)
                <tr class="cart-table__row" data-id="{{ $product->id }}" style="border-bottom: 1px solid #f1f5f9;">
                    <td class="cart-table__column cart-table__column--image py-3 px-2">
                        <a href="{{ route('products.show', $product->options->slug) }}" wire:navigate.hover>
                            <img src="{{ asset($product->options->image) }}" style="width: 52px; height: 52px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0;" alt="{{ $product->name }}">
                        </a>
                    </td>
                    <td class="cart-table__column cart-table__column--product py-3">
                        <a href="{{ route('products.show', $product->options->slug) }}"
                            class="cart-table__product-name font-weight-bold text-dark" style="text-decoration: none; font-size: 14px;" wire:navigate.hover>{{ $product->name }}</a>
                    </td>
                    <td class="cart-table__column cart-table__column--price py-3 font-weight-bold text-dark" data-title="Price">TK {{ $product->price }}</td>
                    @if (isOninda())
                    <td class="cart-table__column cart-table__column--price py-3" data-title="Price">
                        <div class="input-group input-group-sm" style="max-width: 120px;">
                            <input type="number" class="form-control form-control-sm text-right font-weight-bold" 
                                x-model="retail['{{$product->id}}'].price" 
                                min="0" @focus="$event.target.select()" />
                            <div class="input-group-append">
                                <span class="input-group-text font-weight-bold">৳</span>
                            </div>
                        </div>
                    </td>
                    @endif
                    <td class="cart-table__column cart-table__column--quantity py-3" data-title="Quantity">
                        <div class="input-number product__quantity d-inline-flex align-items-center" style="border: 1.5px solid #cbd5e1; border-radius: 4px; overflow: hidden; background: #fff;">
                            <input class="form-control input-number__input text-center font-weight-bold p-0" type="number" min="1"
                                value="{{ $product->qty }}" max="{{ $product->max }}" readonly style="width: 38px; height: 32px; border: none; font-size: 14px;" />
                            <div class="input-number__add" wire:click="increaseQuantity('{{ $product->rowId }}')" style="cursor: pointer;"></div>
                            <div class="input-number__sub" wire:click="decreaseQuantity('{{ $product->rowId }}')" style="cursor: pointer;"></div>
                        </div>
                    </td>
                    <td class="cart-table__column cart-table__column--total py-3 font-weight-bold" style="color: var(--brand-dark); font-size: 15px;" data-title="Total">
                        TK {{ $product->price * $product->qty }}
                    </td>
                    <td class="cart-table__column cart-table__column--remove py-3 text-right">
                        <button type="button" class="btn btn-light btn-sm text-danger" style="border-radius: 3px; padding: 6px 10px;"
                            wire:click="remove('{{ $product->rowId }}')" title="Remove item">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr class="bg-light">
                    <td colspan="6" class="py-5 text-center text-muted font-weight-bold">
                        <i class="fas fa-cart-shopping fa-3x mb-3 d-block text-muted"></i>
                        No Items In Cart.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
