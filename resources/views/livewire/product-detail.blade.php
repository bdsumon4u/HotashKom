<div class="product__info">
    <h3 class="mb-2 product__name" data-name="{{ $selectedVar->var_name }}">{{ $product->name }}</h1>
        <div class="pt-2 mb-2 d-flex justify-content-between border-top">
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
                <del class="product-card__old-price" style="color: #dc3545 !important; margin-left: 10px; font-weight: normal;">{!! theMoney($selectedVar->price) !!}</del>
            @endif
        </div>

        @foreach ($attributes as $attribute)
            <div class="mb-1 form-group product__option d-flex align-items-center" style="column-gap: .5rem;">
                <label class="product__option-label">{{ $attribute->name }}:</label>
                @if (strtolower($attribute->name) == 'color')
                    <div class="input-radio-color">
                        <div class="input-radio-color__list" style="display: flex;">
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
                        <div class="input-radio-label__list" style="display: flex;">
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
                <div class="mb-1 form-group product__option">
                    {{-- <label class="product__option-label" for="product-quantity">Quantity</label> --}}
                    <div
                        class="pt-1 product__actions-item d-flex justify-content-between align-items-center">
                        <big>Quantity</big>
                        <div class="input-number product__quantity" style="display: flex; align-items: center; border: 1px solid #ced4da; border-radius: 4px; overflow: hidden;">
                            <div class="input-number__sub" wire:click="decrement" style="background: #f8f9fa; border: none; padding: 8px 12px; cursor: pointer; font-size: 16px; font-weight: bold; color: #6c757d; transition: all 0.2s ease; min-width: 35px; display: flex; align-items: center; justify-content: center; user-select: none; border-right: 1px solid #ced4da;">-</div>
                            <input id="product-quantity" class="input-number__input form-control"
                                wire:model.live="quantity" type="number" min="1" max="{{ $maxQuantity }}"
                                value="1" readonly style="border: none; text-align: center; width: 50px; padding: 8px 4px; font-weight: 500; background: white;">
                            <div class="input-number__add" wire:click="increment" style="background: #f8f9fa; border: none; padding: 8px 12px; cursor: pointer; font-size: 16px; font-weight: bold; color: #6c757d; transition: all 0.2s ease; min-width: 35px; display: flex; align-items: center; justify-content: center; user-select: none; border-left: 1px solid #ced4da;">+</div>
                        </div>
                    </div>
                    <div class="overflow-hidden product__actions">
                        @php($available = !$selectedVar->should_track || $selectedVar->stock_count > 0)
                        @php($show_option = setting('show_option'))
                        <div class="product__buttons @if ($show_option->product_detail_buttons_inline ?? false) d-lg-inline-flex @endif w-100"
                            @if ($show_option->product_detail_buttons_inline ?? false) style="gap: .5rem;" @endif>
                            @if ($show_option->product_detail_order_now ?? false)
                                <div class="product__actions-item product__actions-item--ordernow"
                                    @if ($show_option->product_detail_buttons_inline ?? false) style="flex: 1;" @endif>
                                    <button type="button" wire:click="orderNow"
                                        class="btn btn-success product__ordernow btn-lg btn-block"
                                        {{ $available ? '' : 'disabled' }}
                                        style="background: #28a745; border: 1px solid #28a745; border-radius: 4px; font-weight: 500; padding: 12px 24px; transition: all 0.2s ease;">
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
                                        {{ $available ? '' : 'disabled' }}
                                        style="background: #007bff; border: 1px solid #007bff; border-radius: 4px; font-weight: 500; padding: 12px 24px; transition: all 0.2s ease;">
                                        {!! $show_option->add_to_cart_icon ?? null !!}
                                        <span class="ml-1">{{ $show_option->add_to_cart_text ?? '' }}</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="p-1 mt-2 text-center call-for-order" style="border: 2px dashed #dedede;">
                    <div>এই পণ্য সম্পর্কে প্রশ্ন আছে? অনুগ্রহপূর্বক কল করুন:</div>
                    @foreach (explode(' ', setting('call_for_order')) as $phone)
                        @if ($phone = trim($phone))
                            <a href="tel:{{ $phone }}" class="text-danger">
                                <div class="mt-1 lead">
                                    <i class="mr-2 fa fas fa-phone"></i>
                                    <span>{{ $phone }}</span>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
                @if (($free_delivery->enabled ?? false) && $deliveryText)
                    <div class="mt-2 text-center border font-weight-bold">
                        <p class="mb-1 border-bottom">আজ অর্ডার করলে <br> সারা বাংলাদেশে ডেলিভারি চার্জ <strong
                                class="text-danger">ফ্রি</strong></p>
                        {!! $deliveryText !!}
                    </div>
                @endif
                @if ($product->variations->isEmpty() || $showBrandCategory)
                    <div class="p-3 mt-2 mb-2 border product__footer">
                        <div class="product__tags tags">
                            @if ($product->brand)
                                <p class="mb-0 text-secondary">
                                    Brand: <a href="{{ route('brands.products', $product->brand) }}"
                                        class="text-primary badge badge-light"><big>{{ $product->brand->name }}</big></a>
                                </p>
                            @endif
                            <div class="mt-2">
                                <p class="mr-2 mb-0 text-secondary d-inline-block">Categories:</p>
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
