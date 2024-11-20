<div class="product__info">
    <h3 class="product__name mb-2" data-name="{{ $selectedVar->var_name }}">{{ $product->name }}</h1>
        <div class="d-flex justify-content-between border-top pt-2 mb-2">
            <div>Product Code: <strong>{{ $selectedVar->sku }}</strong></div>
            <div>Availability:
                <strong>
                    @if (!$selectedVar->should_track)
                        <span class="text-success">In Stock</span>
                    @else
                        <span
                            class="text-{{ $selectedVar->stock_count ? 'success' : 'danger' }}">{{ $selectedVar->stock_count }}
                            In Stock</span>
                    @endif
                </strong>
            </div>
        </div>
        <div
            class="product__prices mb-1 {{ ($selling = $selectedVar->getPrice($quantity)) == $selectedVar->price ? '' : 'has-special' }}">
            Price:
            @if ($selling == $selectedVar->price)
                {!! theMoney($selectedVar->price) !!}
            @else
                <span class="product-card__new-price">{!! theMoney($selling) !!}</span>
                <span class="product-card__old-price">{!! theMoney($selectedVar->price) !!}</span>
            @endif
        </div>

        @foreach ($attributes as $attribute)
            <div class="form-group product__option mb-1 d-flex align-items-center" style="column-gap: .5rem;">
                <label class="product__option-label">{{ $attribute->name }}:</label>
                @if (strtolower($attribute->name) == 'color')
                    <div class="input-radio-color">
                        <div class="input-radio-color__list">
                            @foreach ($optionGroup[$attribute->id] as $option)
                                <label
                                    class="input-radio-color__item @if (strtolower($option->name) == 'white') input-radio-color__item--white @endif"
                                    style="color: {{ $option->value }};" data-toggle="tooltip" title=""
                                    data-original-title="{{ $option->name }}">
                                    <input type="radio" wire:model.live="options.{{ $attribute->id }}"
                                        name="options[{{ $attribute->id }}]" value="{{ $option->id }}"
                                        class="option-picker">
                                    <span></span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="input-radio-label">
                        <div class="input-radio-label__list">
                            @foreach ($optionGroup[$attribute->id] as $option)
                                <label>
                                    <input type="radio" wire:model.live="options.{{ $attribute->id }}"
                                        name="options[{{ $attribute->id }}]" value="{{ $option->id }}"
                                        class="option-picker">
                                    <span>{{ $option->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
        <!-- .product__sidebar -->
        <div class="product__sidebar">
            <!-- .product__options -->
            <form class="product__options">
                <div class="form-group product__option mb-1">
                    {{-- <label class="product__option-label" for="product-quantity">Quantity</label> --}}
                    <div
                        class="product__actions-item d-flex justify-content-between align-items-center border-top pt-1">
                        <big>Quantity</big>
                        <div class="input-number product__quantity">
                            <input id="product-quantity" class="input-number__input form-control"
                                wire:model.live="quantity" type="number" min="1" max="{{ $maxQuantity }}"
                                value="1" readonly style="border: 2px solid">
                            <div class="input-number__add" wire:click="increment"></div>
                            <div class="input-number__sub" wire:click="decrement"></div>
                        </div>
                    </div>
                    <div class="product__actions overflow-hidden">
                        @php($available = !$selectedVar->should_track || $selectedVar->stock_count > 0)
                        @php($show_option = setting('show_option'))
                        <div class="product__buttons @if ($show_option->product_detail_buttons_inline ?? false) d-lg-inline-flex @endif w-100"
                            @if ($show_option->product_detail_buttons_inline ?? false) style="gap: .5rem;" @endif>
                            @if ($show_option->product_detail_order_now ?? false)
                                <div class="product__actions-item product__actions-item--ordernow"
                                    @if ($show_option->product_detail_buttons_inline ?? false) style="flex: 1;" @endif>
                                    <button type="button" wire:click="orderNow"
                                        class="btn btn-primary product__ordernow btn-lg btn-block"
                                        {{ $available ? '' : 'disabled' }}>
                                        {!! $show_option->order_now_icon ?? null !!}
                                        <span class="ml-1">{{ $show_option->order_now_text ?? '' }}</span>
                                    </button>
                                </div>
                            @endif
                            @if ($show_option->product_detail_add_to_cart ?? false)
                                <div class="product__actions-item product__actions-item--addtocart"
                                    @if ($show_option->product_detail_buttons_inline ?? false) style="flex: 1;" @endif>
                                    <button type="button" wire:click="addToCart"
                                        class="btn btn-primary product__addtocart btn-lg btn-block"
                                        {{ $available ? '' : 'disabled' }}>
                                        {!! $show_option->add_to_cart_icon ?? null !!}
                                        <span class="ml-1">{{ $show_option->add_to_cart_text ?? '' }}</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="call-for-order p-1 text-center mt-2" style="border: 2px dashed #dedede;">
                    <div>এই পণ্য সম্পর্কে প্রশ্ন আছে? অনুগ্রহপূর্বক কল করুন:</div>
                    @foreach (explode(' ', setting('call_for_order')) as $phone)
                        @if ($phone = trim($phone))
                            <a href="tel:{{ $phone }}" class="text-danger">
                                <div class="lead mt-1">
                                    <i class="fa fas fa-phone mr-2"></i>
                                    <span>{{ $phone }}</span>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
                @if (($free_delivery->enabled ?? false) && $deliveryText)
                    <div class="text-center font-weight-bold border mt-2">
                        <p class="border-bottom mb-1">আজ অর্ডার করলে <br> সারা বাংলাদেশে ডেলিভারি চার্জ <strong
                                class="text-danger">ফ্রি</strong></p>
                        {!! $deliveryText !!}
                    </div>
                @endif
                @if ($product->variations->isEmpty() || $showBrandCategory)
                    <div class="product__footer mt-2 mb-2 border p-3">
                        <div class="product__tags tags">
                            @if ($product->brand)
                                <p class="text-secondary mb-0">
                                    Brand: <a href="{{ route('brands.products', $product->brand) }}"
                                        class="text-primary badge badge-light"><big>{{ $product->brand->name }}</big></a>
                                </p>
                            @endif
                            <div class="mt-2">
                                <p class="text-secondary mb-0 d-inline-block mr-2">Categories:</p>
                                @foreach ($product->categories as $category)
                                    <a href="{{ route('categories.products', $category) }}"
                                        class="badge badge-primary">{{ $category->name }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th colspan="2" class="text-center">Delivery Charge</th>
                        </tr>
                        <tr>
                            <th width="50%">Inside Dhaka</th>
                            <th width="50%">Outside Dhaka</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{!! theMoney($selectedVar->shipping_inside) !!}</td>
                            <td>{!! theMoney($selectedVar->shipping_outside) !!}</td>
                        </tr>
                    </tbody>
                </table>
                @if ($selectedVar->wholesale['quantity'])
                    <div class="mt-3">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="2" class="text-center">Wholesale Price</th>
                                </tr>
                                <tr>
                                    <th width="50%">Min. Quantity</th>
                                    <th width="50%">Unit Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($selectedVar->wholesale['price'] as $price)
                                    <tr>
                                        <td>{{ $selectedVar->wholesale['quantity'][$loop->index] }}</td>
                                        <td>{!! theMoney($price) !!}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </form><!-- .product__options / end -->
        </div><!-- .product__end -->
</div>
