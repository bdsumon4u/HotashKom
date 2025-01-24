<section
    class="elementor-section elementor-top-section elementor-element elementor-element-05fe02b elementor-element-c559378 elementor-element-ab204be elementor-section-boxed elementor-section-height-default"
    data-id="c559378" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
    <div class="elementor-container elementor-column-gap-no">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-82720a5 elementor-element-82536c1 elementor-element-9a6b06a elementor-element-4bea656d"
            data-id="9a6b06a" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-9a9501a elementor-element-72090b7 elementor-element-68acae2 elementor-element-37676298 elementor-element-4e1d8f5a elementor-widget elementor-widget-heading"
                    data-id="72090b7" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <h2 class="elementor-heading-title elementor-size-default">অর্ডার করতে নিচের ফর্মটি
                            পূরণ করুন</h2>
                    </div>
                </div>
                @if ($layout == 'five')
                    <div class="elementor-element elementor-element-31ee8a7 elementor-headline--style-highlight elementor-widget elementor-widget-animated-headline"
                        data-id="31ee8a7" data-element_type="widget"
                        data-settings="{&quot;marker&quot;:&quot;underline_zigzag&quot;,&quot;highlighted_text&quot;:&quot;01819000000&quot;,&quot;headline_style&quot;:&quot;highlight&quot;,&quot;loop&quot;:&quot;yes&quot;,&quot;highlight_animation_duration&quot;:1200,&quot;highlight_iteration_delay&quot;:8000}"
                        data-widget_type="animated-headline.default">
                        <div class="elementor-widget-container">
                            <a href="tel:{{ setting('company')->phone }}">

                                <h3 class="elementor-headline e-animated e-hide-highlight">
                                    <span class="elementor-headline-plain-text elementor-headline-text-wrapper">ফোনে
                                        অর্ডার
                                        করুন: </span>
                                    <span class="elementor-headline-dynamic-wrapper elementor-headline-text-wrapper">
                                        <span
                                            class="elementor-headline-dynamic-text elementor-headline-text-active">{{ setting('company')->phone }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150"
                                            preserveAspectRatio="none">
                                            <path
                                                d="M9.3,127.3c49.3-3,150.7-7.6,199.7-7.4c121.9,0.4,189.9,0.4,282.3,7.2C380.1,129.6,181.2,130.6,70,139 c82.6-2.9,254.2-1,335.9,1.3c-56,1.4-137.2-0.3-197.1,9">
                                            </path>
                                        </svg></span>
                                </h3>
                            </a>
                        </div>
                    </div>
                @endif
                <div class="elementor-element elementor-element-7d864ca elementor-element-50bfeb0b elementor-widget elementor-widget-checkout-form"
                    data-id="7d864ca" data-element_type="widget" id="order"
                    data-widget_type="checkout-form.default">
                    <div class="elementor-widget-container">
                        <div class = "wcf-el-checkout-form cartflows-elementor__checkout-form">
                            <div id="wcf-embed-checkout-form"
                                class="wcf-embed-checkout-form wcf-embed-checkout-form-two-column wcf-field-default">
                                <!-- CHECKOUT SHORTCODE -->

                                <div class="woocommerce">
                                    <div class="woocommerce-notices-wrapper"></div>
                                    <div class="woocommerce-notices-wrapper"></div>
                                    <form wire:submit="checkout" name="checkout" method="post"
                                        class="checkout woocommerce-checkout" enctype="multipart/form-data">

                                        @if(session()->has('error') || $errors->any())
                                        <div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">
                                            <ul class="woocommerce-error" role="alert">
                                                @if(session()->has('error'))
                                                <li data-id="billing_first_name">
                                                    {{ session('error') }}
                                                </li>
                                                @endif
                                                @foreach($errors->all() as $error)
                                                <li data-id="billing_address_1">
                                                    {{ $error }}
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif

                                        <div class="wcf-col2-set col2-set" id="customer_details">
                                            <div class="wcf-col-1 col-1">
                                                <wc-order-attribution-inputs></wc-order-attribution-inputs>
                                                <div class="woocommerce-billing-fields">

                                                    <h3 id="billing_fields_heading">Billing details</h3>



                                                    <div class="woocommerce-billing-fields__field-wrapper">
                                                        <p class="form-row form-row-first wcf-column-100 validate-required"
                                                            id="billing_first_name_field" data-priority="10"><label
                                                                for="billing_first_name" class="">আপনার
                                                                নাম&nbsp;<abbr class="required"
                                                                    title="required">*</abbr></label><span
                                                                class="woocommerce-input-wrapper"><input type="text"
                                                                    wire:model="name" class="input-text "
                                                                    name="billing_first_name" id="billing_first_name"
                                                                    placeholder="" value="" aria-required="true"
                                                                    autocomplete="given-name" />
                                                                <span
                                                                    class="wcf-field-required-error">{{ $errors->first('name') }}</span>
                                                            </span></p>
                                                        <p class="form-row form-row-wide address-field wcf-column-100 validate-required"
                                                            id="billing_address_1_field" data-priority="50"><label
                                                                for="billing_address_1" class="">আপনার সম্পূর্ণ
                                                                ঠিকানা&nbsp;<abbr class="required"
                                                                    title="required">*</abbr></label><span
                                                                class="woocommerce-input-wrapper"><input type="text"
                                                                    wire:model="address" class="input-text "
                                                                    name="billing_address_1" id="billing_address_1"
                                                                    placeholder="House number and street name"
                                                                    value="" aria-required="true"
                                                                    autocomplete="address-line1" />
                                                                <span
                                                                    class="wcf-field-required-error">{{ $errors->first('address') }}</span>
                                                            </span>
                                                        </p>
                                                        <p class="form-row form-row-wide wcf-column-100 validate-required validate-phone"
                                                            id="billing_phone_field" data-priority="100">
                                                            <label for="billing_phone" class="">আপনার ফোন
                                                                নাম্বার&nbsp;<abbr class="required"
                                                                    title="required">*</abbr></label><span
                                                                class="woocommerce-input-wrapper"><input
                                                                    type="tel" wire:model="phone"
                                                                    class="input-text " name="billing_phone"
                                                                    id="billing_phone" placeholder=""
                                                                    value="{{ setting('show_option')->hide_phone_prefix ?? false ? '' : '+880' }}"
                                                                    aria-required="true" autocomplete="tel" />
                                                                <span
                                                                    class="wcf-field-required-error">{{ $errors->first('phone') }}</span>
                                                            </span>
                                                        </p>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>


                                        <div style="margin-top: 0"
                                            class="wcf-product-option-wrap wcf-yp-skin-cards wcf-product-option-after-customer">
                                            <h3 id="your_products_heading" style="margin-bottom: .25rem;"> Your
                                                Products </h3>
                                            <div class="wcf-qty-options">
                                                @foreach ($product->variations->isEmpty() ? [$product] : $product->variations as $product)
                                                    <div class="wcf-qty-row wcf-qty-row-452 "
                                                        data-options="{&quot;product_id&quot;:440,&quot;variation_id&quot;:452,&quot;type&quot;:&quot;variation&quot;,&quot;unique_id&quot;:&quot;zwr6yipq&quot;,&quot;mode&quot;:&quot;quantity&quot;,&quot;highlight_text&quot;:&quot;&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;default_quantity&quot;:1,&quot;original_price&quot;:&quot;200&quot;,&quot;discounted_price&quot;:&quot;&quot;,&quot;total_discounted_price&quot;:&quot;&quot;,&quot;currency&quot;:&quot;&amp;#2547;&amp;nbsp;&quot;,&quot;cart_item_key&quot;:&quot;4606109fe00ffd19b2a98941e90aaaa8&quot;,&quot;save_value&quot;:&quot;&quot;,&quot;save_percent&quot;:&quot;&quot;,&quot;sign_up_fee&quot;:0,&quot;subscription_price&quot;:&quot;200&quot;,&quot;trial_period_string&quot;:&quot;&quot;}">
                                                        <div class="wcf-item">
                                                            <div class="wcf-item-selector wcf-item-multiple-sel">
                                                                <input class="wcf-multiple-sel" type="checkbox"
                                                                    @if (isset($cart[$product->id])) wire:click="remove({{ $product->id }})"
                                                                @else
                                                                wire:click="increaseQuantity({{ $product->id }})" @endif
                                                                    name="wcf-multiple-sel"
                                                                    value="{{ $product->id }}"
                                                                    @checked(isset($cart[$product->id]))>
                                                            </div>

                                                            <div class="wcf-item-image" style=""><img
                                                                    fetchpriority="high" decoding="async"
                                                                    width="300" height="300"
                                                                    src="{{ $product->base_image->src }}"
                                                                    class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail"
                                                                    alt="" /></div>
                                                            <div class="wcf-item-content-options">
                                                                <div class="wcf-item-wrap">
                                                                    <span
                                                                        class="wcf-display-title">{{ $product->name }}</span><span
                                                                        class="wcf-display-title-quantity">
                                                                        <div class="wcf-display-attributes"><span
                                                                                class="wcf-att-inner">Price: Tk
                                                                                {{ $prc = $cart[$product->id]['price'] ?? $product->selling_price }}</span>
                                                                        </div>
                                                                </div>

                                                                <div class="wcf-qty ">
                                                                    <div class="wcf-qty-selection-wrap">
                                                                        <span
                                                                            class="wcf-qty-selection-btn wcf-qty-decrement wcf-qty-change-icon"
                                                                            title=""
                                                                            wire:click="decreaseQuantity({{ $product->id }})">&minus;</span>
                                                                        <input autocomplete="off" type="number"
                                                                            value="{{ $qty = $cart[$product->id]['quantity'] ?? 0 }}"
                                                                            step="1" name="wcf_qty_selection"
                                                                            class="wcf-qty-selection"
                                                                            data-sale-limit="false" title="">
                                                                        <span
                                                                            class="wcf-qty-selection-btn wcf-qty-increment wcf-qty-change-icon"
                                                                            title=""
                                                                            wire:click="increaseQuantity({{ $product->id }})">&plus;</span>
                                                                    </div>
                                                                </div>
                                                                <div class="wcf-price">
                                                                    <div class="wcf-display-price wcf-field-label">
                                                                        <span
                                                                            class="woocommerce-Price-amount amount"><span
                                                                                class="woocommerce-Price-currencySymbol">&#2547;&nbsp;</span>&nbsp;{{ $qty * $prc }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>


                                        <div class='wcf-order-wrap'>



                                            <h3 id="order_review_heading">Your order</h3>


                                            <div id="order_review" class="woocommerce-checkout-review-order">
                                                <table class="shop_table woocommerce-checkout-review-order-table"
                                                    data-update-time="1737164735">
                                                    <thead>
                                                        <tr>
                                                            <th class="product-name">Product</th>
                                                            <th class="product-total">Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($cart as $item)
                                                            <tr class="cart_item">
                                                                <td class="product-name">{{ $item['name'] }}&nbsp;
                                                                    <strong
                                                                        class="product-quantity">&times;&nbsp;{{ $item['quantity'] }}</strong>
                                                                </td>
                                                                <td class="product-total">
                                                                    <span class="woocommerce-Price-amount amount"><bdi><span
                                                                                class="woocommerce-Price-currencySymbol">&#2547;&nbsp;</span>&nbsp;{{ $item['price'] * $item['quantity'] }}</bdi></span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>

                                                        <tr class="cart-subtotal">
                                                            <th>Subtotal</th>
                                                            <td><span class="woocommerce-Price-amount amount"><bdi><span
                                                                            class="woocommerce-Price-currencySymbol">&#2547;&nbsp;</span>&nbsp;{{ $subtotal }}</bdi></span>
                                                            </td>
                                                        </tr>




                                                        <tr class="woocommerce-shipping-totals shipping">
                                                            <th>Shipping</th>
                                                            <td data-title="Shipping">
                                                                <ul id="shipping_method"
                                                                    class="woocommerce-shipping-methods">
                                                                    <li style="white-space: nowrap">
                                                                        <input type="radio"
                                                                            wire:model.live="shipping"
                                                                            name="shipping_method[0]" data-index="0"
                                                                            id="shipping_method_0_flat_rate1"
                                                                            value="Inside Dhaka"
                                                                            class="shipping_method"
                                                                            checked='checked' /><label
                                                                            for="shipping_method_0_flat_rate1">Inside
                                                                            Dhaka <strong
                                                                                class="woocommerce-Price-amount amount"><bdi>
                                                                                    @if (!(setting('show_option')->productwise_delivery_charge ?? false))
                                                                                        <strong
                                                                                            class="woocommerce-Price-currencySymbol">&#2547;</strong>
                                                                                        {{ $isFreeDelivery ? 'FREE' : setting('delivery_charge')->inside_dhaka }}
                                                                                    @endif
                                                                                </bdi>
                                                                            </strong></label>
                                                                    </li>
                                                                    <li style="white-space: nowrap">
                                                                        <input type="radio"
                                                                            wire:model.live="shipping"
                                                                            name="shipping_method[0]" data-index="0"
                                                                            id="shipping_method_0_flat_rate2"
                                                                            value="Outside Dhaka"
                                                                            class="shipping_method" /><label
                                                                            for="shipping_method_0_flat_rate2">Outside
                                                                            Dhaka <strong
                                                                                class="woocommerce-Price-amount amount"><bdi>
                                                                                    @if (!(setting('show_option')->productwise_delivery_charge ?? false))
                                                                                        <strong
                                                                                            class="woocommerce-Price-currencySymbol">&#2547;</strong>
                                                                                        {{ $isFreeDelivery ? 'FREE' : setting('delivery_charge')->outside_dhaka }}
                                                                                    @endif
                                                                                </bdi></strong></label>
                                                                    </li>
                                                                </ul>


                                                            </td>
                                                        </tr>






                                                        <tr class="order-total">
                                                            <th>Total</th>
                                                            <td><strong><span
                                                                        class="woocommerce-Price-amount amount"><bdi><span
                                                                                class="woocommerce-Price-currencySymbol">&#2547;&nbsp;</span>&nbsp;{{ $total }}</bdi></span></strong>
                                                            </td>
                                                        </tr>


                                                    </tfoot>
                                                </table>
                                                <div id="payment" class="woocommerce-checkout-payment">
                                                    <ul class="wc_payment_methods payment_methods methods">
                                                        <li class="wc_payment_method payment_method_cod">
                                                            <input id="payment_method_cod" type="radio"
                                                                class="input-radio" name="payment_method"
                                                                value="cod" checked='checked'
                                                                data-order_button_text="" />

                                                            <label for="payment_method_cod">
                                                                Cash on delivery </label>
                                                            <div class="payment_box payment_method_cod">
                                                                <p>Pay with cash upon delivery.</p>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                    <div class="form-row place-order">
                                                        <noscript>
                                                            Since your browser does not support JavaScript,
                                                            or it is disabled, please ensure you click the
                                                            <em>Update Totals</em> button before placing
                                                            your order. You may be charged more than the
                                                            amount stated above if you fail to do so.
                                                            <br /><button type="submit" class="button alt"
                                                                name="woocommerce_checkout_update_totals"
                                                                value="Update totals">Update
                                                                totals</button>
                                                        </noscript>

                                                        <div class="woocommerce-terms-and-conditions-wrapper">
                                                            <div class="woocommerce-privacy-policy-text">
                                                                <p>Your personal data will be used to
                                                                    process your order, support your
                                                                    experience throughout this website, and
                                                                    for other purposes described in our <a
                                                                        href="https://demo.orioit.com/?page_id=3"
                                                                        class="woocommerce-privacy-policy-link"
                                                                        target="_blank">privacy policy</a>.
                                                                </p>
                                                            </div>
                                                        </div>


                                                        <button type="submit" class="button alt"
                                                            wire:loading.attr="disabled"
                                                            name="woocommerce_checkout_place_order" id="place_order"
                                                            value="Place Order&nbsp;&nbsp;&#2547;&nbsp;&nbsp;250.00"
                                                            data-value="Place Order&nbsp;&nbsp;&#2547;&nbsp;&nbsp;250.00">Place
                                                            Order&nbsp;&nbsp;&#2547;&nbsp;&nbsp;{{ $total }}</button>

                                                        <input type="hidden" id="woocommerce-process-checkout-nonce"
                                                            name="woocommerce-process-checkout-nonce"
                                                            value="b8a5c02791" /><input type="hidden"
                                                            name="_wp_http_referer" value="/step/red-rice/" />
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </form>

                                </div>
                                <!-- END CHECKOUT SHORTCODE -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
