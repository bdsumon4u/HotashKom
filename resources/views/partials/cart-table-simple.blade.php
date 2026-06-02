<div class="simple-cart-table-wrapper">
    {{-- Desktop Table View --}}
    <div class="d-none d-md-block table-responsive">
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

    {{-- Mobile Card View --}}
    <div class="d-block d-md-none">
        @forelse (cart()->content() as $product)
            <div class="card mb-3 border shadow-sm" data-id="{{ $product->id }}">
                <div class="card-body p-3">
                    <div class="d-flex position-relative mb-2">
                        <div class="w-100 text-center font-weight-semibold text-secondary px-4" style="font-size: 16px;">
                            {{ $product->name }}
                        </div>
                        <button type="button"
                            class="p-0 btn btn-link text-muted position-absolute"
                            style="right: 0; top: 0;"
                            aria-label="Remove"
                            wire:click="remove('{{ $product->rowId }}')">
                            <span aria-hidden="true" style="font-size: 24px; line-height: 1;">&times;</span>
                        </button>
                    </div>
                    
                    <hr class="mt-2 mb-3">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="font-weight-bold" style="font-size: 16px;">Price:</span>
                        <span class="font-weight-normal" style="font-size: 16px;">{!! theMoney($product->price) !!}</span>
                    </div>

                    @if (isOninda())
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="font-weight-bold" style="font-size: 16px;">Sell Price:</span>
                            <div style="width: 100px;">
                                <input type="number"
                                    class="form-control form-control-sm text-center"
                                    x-model="retail['{{ $product->id }}'].price"
                                    min="0"
                                    @focus="$event.target.select()" />
                            </div>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="font-weight-bold" style="font-size: 16px;">Quantity:</span>
                        <div class="d-inline-flex align-items-center">
                            <button type="button"
                                class="btn btn-sm btn-light border"
                                wire:click="decreaseQuantity('{{ $product->rowId }}')" style="width: 36px; height: 36px; padding: 0; background-color: #e9ecef; font-size: 18px; line-height: 1;">-</button>
                            <input class="mx-0 text-center border-top border-bottom bg-light"
                                type="number"
                                min="1"
                                value="{{ $product->qty }}"
                                style="width: 44px; height: 36px; outline: none; box-shadow: none; border-left: 0; border-right: 0; border-color: #dee2e6 !important;"
                                readonly>
                            <button type="button"
                                class="btn btn-sm btn-light border"
                                wire:click="increaseQuantity('{{ $product->rowId }}')" style="width: 36px; height: 36px; padding: 0; background-color: #e9ecef; font-size: 18px; line-height: 1;">+</button>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="font-weight-bold" style="font-size: 16px;">Total:</span>
                        <span class="font-weight-normal" style="font-size: 16px;">{!! theMoney($product->price * $product->qty) !!}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-3 text-center text-danger font-weight-semibold bg-white rounded border">
                No items in cart.
            </div>
        @endforelse
    </div>
</div>


