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
        @php $show_option = setting('show_option') @endphp
        @php $guest_can_see_price = (bool)($show_option->guest_can_see_price ?? false) @endphp
        <div
            class="product__prices mb-1 {{ ($selling = $selectedVar->getPrice($quantity)) == $selectedVar->price ? '' : 'has-special' }}">
            Price:
            @if(isOninda() && auth('user')->guest() && !$guest_can_see_price)
                <span class="product-card__new-price text-danger">Login to see price</span>
            @elseif(isOninda() && !auth('user')->user()->is_verified && !$guest_can_see_price)
                <span class="product-card__new-price text-danger">Verify account to see price</span>
            @elseif ($selling == $selectedVar->price)
                {!! theMoney($selectedVar->price) !!}
            @else
                <span class="product-card__new-price">{!! theMoney($selling) !!}</span>
                <span class="product-card__old-price">{!! theMoney($selectedVar->price) !!}</span>
            @endif
        </div>

        @foreach ($attributes as $attribute)
            <div class="mb-1 form-group product__option d-flex align-items-center" style="column-gap: .5rem;">
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
                                    <span class="p-1 @if($options[$attribute->id] == $option->id) border-primary @endif">{{ $option->name }}</span>
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
                        class="pt-1 product__actions-item d-flex justify-content-between align-items-center border-top">
                        <big>Quantity</big>
                        <div class="input-number product__quantity">
                            <input id="product-quantity" class="input-number__input form-control"
                                wire:model.live="quantity" type="number" min="1" max="{{ $maxQuantity }}"
                                value="1" readonly style="border: 2px solid">
                            <div class="input-number__add" wire:click="increment"></div>
                            <div class="input-number__sub" wire:click="decrement"></div>
                        </div>
                    </div>
                    @if(isOninda())
                    <div class="pt-1 product__actions-item d-flex justify-content-between align-items-center border-top">
                        <div class="mr-2 font-weight-bold text-danger" style="white-space:nowrap;">Retail Price</div>
                        <div class="input-group input-group-sm">
                            <input type="number" class="form-control form-control-sm" wire:model="retailPrice" min="0" @focus="$event.target.select()" required>
                            <div class="input-group-append">
                                <span class="input-group-text">৳</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-1 small text-muted">
                        <i class="fa fa-info-circle"></i> Suggested retail price:
                        <strong>৳{{ number_format($selectedVar->selling_price * 1.3, 2) }} - ৳{{ number_format($selectedVar->selling_price * 1.5, 2) }}</strong>
                    </div>
                    @endif
                    <div class="overflow-hidden product__actions">
                        @php $available = !$selectedVar->should_track || $selectedVar->stock_count > 0 @endphp
                        <div class="product__buttons @if ($show_option->product_detail_buttons_inline ?? false) d-lg-inline-flex @endif w-100"
                            @if ($show_option->product_detail_buttons_inline ?? false) style="gap: .5rem;" @endif>
                            @if ($show_option->product_detail_order_now ?? false)
                                <div class="product__actions-item product__actions-item--ordernow"
                                    @if ($show_option->product_detail_buttons_inline ?? false) style="flex: 1;" @endif>
                                    <button type="button" wire:click="addToCart('kart')"
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
                @php
                    $company = setting('company');
                    $phone = preg_replace('/[^\d]/', '', $company->whatsapp ?? '');
                    $phone = strlen($phone) == 11 ? '88' . $phone : $phone;
                    $messenger = $company->messenger ?? '';
                @endphp
                <div class="gap-2 mt-2 d-flex justify-content-center">
                    @if(strlen($messenger) > 13)
                    <a href="{{$messenger}}" target="_blank" rel="noopener"
                        class="btn btn-outline-primary d-flex align-items-center" style="min-width: 140px;">
                        <i class="mr-2 fab fa-facebook-messenger"></i> Messenger
                    </a>
                    @endif
                    <a href="https://api.whatsapp.com/send?phone={{ $phone }}&text=Hello+%0D%0AI+am+interested+in+ordering+%22{{ $product->name }}%22.%0D%0A%0D%0A{{ url()->current() }}"
                        target="_blank" rel="noopener"
                        class="btn btn-outline-success d-flex align-items-center" style="min-width: 140px;">
                        <i class="mr-2 fab fa-whatsapp"></i> WhatsApp
                    </a>
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
