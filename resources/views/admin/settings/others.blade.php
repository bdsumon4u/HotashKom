<div class="tab-pane active" id="item-others" role="tabpanel">
    <div class="row">
        <div class="col-md-6">
            <div class="py-2 row borderr">
                <div class="pr-1 col-6">
                    <label for="products_page-add_to_cart_icon">`AddToCart` icon</label>
                    <x-input name="show_option[add_to_cart_icon]" id="show_option-add_to_cart_icon" :value="$show_option->add_to_cart_icon ?? ''" />
                    <x-error field="show_option.add_to_cart_icon" />
                </div>
                <div class="pl-1 col-6">
                    <label for="show_option-add_to_cart_text">`AddToCart` text</label>
                    <x-input name="show_option[add_to_cart_text]" id="show_option-add_to_cart_text" :value="$show_option->add_to_cart_text ?? ''" />
                    <x-error field="show_option.add_to_cart_text" />
                </div>
            </div>
            <div class="py-2 row borderr">
                <div class="pr-1 col-6">
                    <label for="show_option-order_now_icon">`OrderNow` icon</label>
                    <x-input name="show_option[order_now_icon]" id="show_option-order_now_icon" :value="$show_option->order_now_icon ?? ''" />
                    <x-error field="show_option.order_now_icon" />
                </div>
                <div class="pl-1 col-6">
                    <label for="show_option-order_now_text">`OrderNow` text</label>
                    <x-input name="show_option[order_now_text]" id="show_option-order_now_text" :value="$show_option->order_now_text ?? ''" />
                    <x-error field="show_option.order_now_text" />
                </div>
            </div>
        </div>
        <div class="col-md-6 d-flex flex-column justify-content-center">
            <label for="" class="my-1">Product Grid Button</label>
            <div class="ml-2 d-flex">
                <div class="mr-3 radio radio-secondary">
                    <input type="radio" id="product-grid-add-to-cart" name="show_option[product_grid_button]"
                        value="add_to_cart" @if (old('show_option.product_grid_button', $show_option->product_grid_button ?? false) == 'add_to_cart') checked @endif />
                    <label for="product-grid-add-to-cart">`Add To Cart`</label>
                </div>
                <div class="ml-3 radio radio-secondary">
                    <input type="radio" id="product-grid-order-now" name="show_option[product_grid_button]"
                        value="order_now" @if (old('show_option.product_grid_button', $show_option->product_grid_button ?? false) == 'order_now') checked @endif />
                    <label for="product-grid-order-now">`Order Now`</label>
                </div>
            </div>
            <label for="" class="my-1 d-inline-flex align-items-center">
                <span class="mr-1">Product Detail Button(s)</span>
                <span class="px-1 border d-inline-flex">
                    <input type="checkbox" id="product_detail_buttons_inline"
                        name="show_option[product_detail_buttons_inline]"
                        @if (!!old('show_option.product_detail_buttons_inline', $show_option->product_detail_buttons_inline ?? false)) checked @endif />
                    <label for="product_detail_buttons_inline" class="mb-0 ml-1">inline</label>
                </span>
            </label>
            <div class="ml-3 d-flex">
                <div class="mr-3 checkbox checkbox-secondary">
                    <input type="hidden" name="show_option[product_detail_add_to_cart]" value="0">
                    <x-checkbox id="product-detail-add-to-cart" class="d-none"
                        name="show_option[product_detail_add_to_cart]" value="1" :checked="!!old(
                            'show_option.product_detail_add_to_cart',
                            $show_option->product_detail_add_to_cart ?? false,
                        )" />
                    <label for="product-detail-add-to-cart" class="m-0">`Add To Cart`</label>
                </div>
                <div class="ml-3 checkbox checkbox-secondary">
                    <input type="hidden" name="show_option[product_detail_order_now]" value="0">
                    <x-checkbox id="product-detail-order-now" class="d-none"
                        name="show_option[product_detail_order_now]" value="1" :checked="!!old(
                            'show_option.product_detail_order_now',
                            $show_option->product_detail_order_now ?? false,
                        )" />
                    <label for="product-detail-order-now" class="m-0">`Order Now`</label>
                </div>
            </div>
        </div>
        <div class="my-3 border col-md-12"></div>
        <div class="col-md-8">
            <div class="form-group">
                <label for="">Scroll Text</label>
                <x-textarea name="scroll_text" id="scroll_text">{{ $scroll_text ?? null }}</x-textarea>
                <x-error field="scroll_text" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="discount-text">Discount Text</label>
                <x-input name="discount_text" id="discount-text" :value="$discount_text ?? '<small>Discount:</small> [percent]%'" />
                <small>Short Code: <strong>[percent]</strong></small>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label for="" class="mb-0">Products Page</label>
                <div class="py-2 row borderr">
                    <div class="my-1 col-md-6">
                        <label for="products_page-rows">Rows</label>
                        <x-input name="products_page[rows]" id="products_page-rows" :value="$products_page->rows ?? 3" />
                        <x-error field="products_page.rows" />
                    </div>
                    <div class="my-1 col-md-6">
                        <label for="products_page-cols">Cols (4 or 5)</label>
                        <x-input name="products_page[cols]" id="products_page-cols" :value="$products_page->cols ?? 5" />
                        <x-error field="products_page.cols" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label for="" class="mb-0">Related Products</label>
                <div class="py-2 row borderr">
                    <div class="my-1 col-md-6">
                        <label for="related_products-rows">Rows</label>
                        <x-input name="related_products[rows]" id="related_products-rows" :value="$related_products->rows ?? 1" />
                        <x-error field="related_products.rows" />
                    </div>
                    <div class="my-1 col-md-6">
                        <label for="related_products-cols">Cols (4 or 5)</label>
                        <x-input name="related_products[cols]" id="related_products-cols" :value="$related_products->cols ?? 5" />
                        <x-error field="related_products.cols" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="product-sort">Product Sorted By</label>
                @php $sorted = $show_option->product_sort ?? false; @endphp
                <select name="show_option[product_sort]" id="product-sort" class="form-control">
                    <option value="random" @if ($sorted == 'random') selected @endif>Random</option>
                    <option value="updated_at" @if ($sorted == 'updated_at') selected @endif>Last Update</option>
                    <option value="selling_price" @if ($sorted == 'selling_price') selected @endif>Cheap Price
                    </option>
                </select>
            </div>
        </div>
        <div class="col-md-9">
            <div class="flex-wrap mt-2 d-flex" style="row-gap: .25rem; column-gap: .75rem;">
                <div class="checkbox checkbox-secondary">
                    <input type="hidden" name="show_option[topbar_phone]" value="0">
                    <x-checkbox id="show-topbar-phone" name="show_option[topbar_phone]" value="1"
                        :checked="!!($show_option->topbar_phone ?? false)" />
                    <label for="show-topbar-phone" class="my-1">`Topbar Phone`</label>
                </div>
                <div class="checkbox checkbox-secondary">
                    <input type="hidden" name="show_option[track_order]" value="0">
                    <x-checkbox id="show-track-order" name="show_option[track_order]" value="1"
                        :checked="!!($show_option->track_order ?? false)" />
                    <label for="show-track-order" class="my-1">`Track Order`</label>
                </div>
                <div class="checkbox checkbox-secondary">
                    <input type="hidden" name="show_option[customer_login]" value="0">
                    <x-checkbox id="show-customer-login" name="show_option[customer_login]" value="1"
                        :checked="!!($show_option->customer_login ?? false)" />
                    <label for="show-customer-login" class="my-1">`Customer Login`</label>
                </div>
                <div class="checkbox checkbox-secondary">
                    <input type="hidden" name="services[enabled]" value="0">
                    <x-checkbox id="services" name="services[enabled]" value="1" :checked="!!($services->enabled ?? false)" />
                    <label for="services" class="my-1">`Services` Section</label>
                </div>
                <div class="checkbox checkbox-secondary">
                    <input type="hidden" name="show_option[hide_phone_prefix]" value="1">
                    <x-checkbox id="hide-phone-prefix" name="show_option[hide_phone_prefix]" value="0"
                        :checked="!($show_option->hide_phone_prefix ?? false)" />
                    <label for="hide-phone-prefix" class="my-1">Prefix +880</label>
                </div>
                <div class="checkbox checkbox-secondary">
                    <input type="hidden" name="show_option[hide_checkout_note]" value="1">
                    <x-checkbox id="hide-checkout-note" name="show_option[hide_checkout_note]" value="0"
                        :checked="!($show_option->hide_checkout_note ?? false)" />
                    <label for="hide-checkout-note" class="my-1">Checkout Note</label>
                </div>
                <div class="checkbox checkbox-secondary">
                    <input type="hidden" name="show_option[hide_invoice_image]" value="1">
                    <x-checkbox id="hide-invoice-image" name="show_option[hide_invoice_image]" value="0"
                        :checked="!($show_option->hide_invoice_image ?? false)" />
                    <label for="hide-invoice-image" class="my-1">IMAGE on INVOICE</label>
                </div>
                @if (isOninda())
                <div class="checkbox checkbox-secondary">
                    <input type="hidden" name="show_option[resellers_invoice]" value="0">
                    <x-checkbox id="resellers-invoice" name="show_option[resellers_invoice]" value="1"
                        :checked="($show_option->resellers_invoice ?? false)" />
                    <label for="resellers-invoice" class="my-1">Reseller's Invoice</label>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        @foreach (config('services.services', []) as $num => $icon)
            <div class="col-md-6">
                <div class="input-group">
                    <div class="mr-1 input-group-prepend">
                        <span class="input-group-text">
                            <svg width="24px" height="24px">
                                <use xlink:href="{{ asset($icon) }}"></use>
                            </svg>
                        </span>
                    </div>
                    <div class="" style="flex: 1;">
                        <div class="mb-1 form-group">
                            <label for="" class="ml-1">Title</label>
                            <x-input name="services[{{ $num }}][title]" :value="$services->$num->title ?? ''" />
                        </div>
                        <div class="mt-2 mb-0 form-group">
                            <label for="" class="ml-1">Detail</label>
                            <x-input name="services[{{ $num }}][detail]" :value="$services->$num->detail ?? ''" />
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="scripts">Custom Scripts</label>
                <x-textarea name="scripts" id="scripts">{{ $scripts ?? null }}</x-textarea>
                <x-error field="scripts" />
            </div>
        </div>
    </div>
</div>
