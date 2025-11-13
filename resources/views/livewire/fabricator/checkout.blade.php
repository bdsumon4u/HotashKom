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

                                        @if (session()->has('error') || $errors->any())
                                            <div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">
                                                <ul class="woocommerce-error" role="alert">
                                                    @if (session()->has('error'))
                                                        <li data-id="billing_first_name">
                                                            {{ session('error') }}
                                                        </li>
                                                    @endif
                                                    @foreach ($errors->all() as $error)
                                                        <li data-id="billing_address_1">
                                                            {{ $error }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        @php
                                            // Group variations by color
                                            $colorOptions = collect();
                                            if ($product->variations->isNotEmpty()) {
                                                $colorOptions = $product->variations->flatMap(function ($variation) {
                                                    return $variation->options->filter(function ($option) {
                                                        return strtolower($option->attribute->name) === 'color';
                                                    });
                                                })->unique('id')->values();
                                            }

                                            // Determine the initially selected product/variation
                                            // Priority:
                                            // 1. Check if any variation of this product is in cart
                                            // 2. If $product is a variation (has parent_id), use it
                                            // 3. Otherwise, use the first variation or the product itself
                                            $selectedProduct = null;

                                            // First, check cart for any variation of this product
                                            $cartVariation = cart()->content()->first(function($item) use ($product) {
                                                $itemProduct = \App\Models\Product::find($item->id);
                                                if (!$itemProduct) return false;

                                                // Check if this cart item is a variation of our product
                                                $parentId = $product->parent_id ?? $product->id;
                                                return $itemProduct->id == $parentId || $itemProduct->parent_id == $parentId;
                                            });

                                            if ($cartVariation) {
                                                // Use the variation that's in the cart
                                                $selectedProduct = \App\Models\Product::with('options')->find($cartVariation->id);
                                            } elseif ($product->parent_id) {
                                                // Product is a variation itself
                                                $selectedProduct = $product;
                                            } elseif ($product->variations->isNotEmpty()) {
                                                // Use the first variation
                                                $selectedProduct = $product->variations->first();
                                            } else {
                                                // Product has no variations
                                                $selectedProduct = $product;
                                            }

                                            // Get the option IDs of the selected product for pre-selection
                                            $selectedOptionIds = $selectedProduct->options->pluck('id')->toArray();
                                        @endphp

                                        <div style="margin: 0; padding: 0; width: 100%;"
                                            class="wcf-product-option-wrap wcf-yp-skin-cards wcf-product-option-after-customer">
                                            <h3 id="your_products_heading" style="margin-bottom: .25rem;"> Your
                                                Products </h3>
                                            <div class="wcf-qty-options" style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                                                @if($colorOptions->isEmpty())
                                                    {{-- No variations, show the main product --}}
                                                    @php
                                                        $row = cart()->content()->first(fn ($item) => $item->id == $product->id);
                                                    @endphp
                                                    <div class="wcf-qty-row wcf-qty-row-{{ $product->id }}">
                                                        <div class="wcf-item">
                                                            <div class="wcf-item-selector wcf-item-multiple-sel">
                                                                <input class="wcf-multiple-sel" type="checkbox"
                                                                @if ($row)
                                                                    wire:click="remove('{{ $row->rowId }}')"
                                                                    checked
                                                                @else
                                                                    wire:click="increaseQuantity({{ $product->id }})"
                                                                @endif
                                                                name="wcf-multiple-sel"
                                                                value="{{ $product->id }}">
                                                            </div>

                                                            <div class="wcf-item-image" style=""><img fetchpriority="high" decoding="async" width="300" height="300"
                                                                    src="{{ asset($product->base_image->src) }}"
                                                                    class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" /></div>
                                                            <div class="wcf-item-content-options">
                                                                <div class="wcf-item-wrap">
                                                                    <span class="wcf-display-title">{{ $product->name }}</span><span
                                                                        class="wcf-display-title-quantity">
                                                                        <div class="wcf-display-attributes"><span class="wcf-att-inner">Price: Tk
                                                                                {{ $prc = $row->price ?? $product->selling_price }}</span>
                                                                        </div>
                                                                </div>

                                                                <div class="wcf-qty">
                                                                    <div class="wcf-qty-selection-wrap">
                                                                        <span class="wcf-qty-selection-btn wcf-qty-decrement wcf-qty-change-icon" title=""
                                                                            wire:click="decreaseQuantity({{ $product->id }})">&minus;</span>
                                                                        <input autocomplete="off" type="number" value="{{ $qty = $row->qty ?? 0 }}" step="1"
                                                                            name="wcf_qty_selection" class="wcf-qty-selection" data-sale-limit="false" title="">
                                                                        <span class="wcf-qty-selection-btn wcf-qty-increment wcf-qty-change-icon" title=""
                                                                            wire:click="increaseQuantity({{ $product->id }})">&plus;</span>
                                                                    </div>
                                                                </div>
                                                                <div class="wcf-price">
                                                                    <div class="wcf-display-price wcf-field-label">
                                                                        <span class="woocommerce-Price-amount amount"><span
                                                                                class="woocommerce-Price-currencySymbol">&#2547;&nbsp;</span>&nbsp;{{ $qty * $prc
                                                                            }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    {{-- Show color options only --}}
                                                    @foreach ($colorOptions as $colorOption)
                                                        @php
                                                            // Find any variation with this color to get image and price
                                                            $sampleVariation = $product->variations->first(function($v) use ($colorOption) {
                                                                return $v->options->contains('id', $colorOption->id);
                                                            });

                                                            // Check if any variation with this color is in cart
                                                            $cartRow = cart()->content()->first(function($item) use ($product, $colorOption) {
                                                                $itemProduct = \App\Models\Product::find($item->id);
                                                                return $itemProduct &&
                                                                       $itemProduct->parent_id == $product->id &&
                                                                       $itemProduct->options->contains('id', $colorOption->id);
                                                            });

                                                            // Check if any product variation is in cart
                                                            $anyProductInCart = cart()->content()->first(function($item) use ($product) {
                                                                $itemProduct = \App\Models\Product::find($item->id);
                                                                return $itemProduct && $itemProduct->parent_id == $product->id;
                                                            });

                                                            $displayImage = $sampleVariation->base_image ?? $product->base_image;
                                                            $displayPrice = $cartRow ? $cartRow->price : $sampleVariation->selling_price;
                                                            $displayQty = $cartRow ? $cartRow->qty : 0;

                                                            // Check if this color should be selected based on $selectedProduct
                                                            $shouldCheck = !$anyProductInCart && in_array($colorOption->id, $selectedOptionIds);
                                                        @endphp
                                                        <div class="wcf-qty-row wcf-qty-row-color-{{ $colorOption->id }}">
                                                            <div class="wcf-item">
                                                                <div class="wcf-item-selector wcf-item-multiple-sel">
                                                                    <input class="wcf-multiple-sel color-checkbox" type="checkbox"
                                                                        data-color-id="{{ $colorOption->id }}"
                                                                        @if($cartRow || $shouldCheck) checked @endif
                                                                        name="wcf-multiple-sel"
                                                                        value="{{ $colorOption->id }}">
                                                                </div>

                                                                <div class="wcf-item-image" style=""><img fetchpriority="high" decoding="async" width="300" height="300"
                                                                        src="{{ asset($displayImage->src) }}"
                                                                        class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" /></div>
                                                                <div class="wcf-item-content-options">
                                                                    <div class="wcf-item-wrap">
                                                                        <span class="wcf-display-title">{{ $product->name }} - {{ $colorOption->name }}</span><span
                                                                            class="wcf-display-title-quantity">
                                                                            <div class="wcf-display-attributes"><span class="wcf-att-inner">Price: Tk
                                                                                    <span class="color-price-{{ $colorOption->id }}">{{ $displayPrice }}</span></span>
                                                                            </div>
                                                                    </div>

                                                                    <div class="wcf-qty">
                                                                        <div class="wcf-qty-selection-wrap">
                                                                            <span class="wcf-qty-selection-btn wcf-qty-decrement wcf-qty-change-icon color-qty-decrement"
                                                                                data-color-id="{{ $colorOption->id }}" title="">&minus;</span>
                                                                            <input autocomplete="off" type="number" value="{{ $displayQty }}" step="1"
                                                                                name="wcf_qty_selection" class="wcf-qty-selection color-qty-input-{{ $colorOption->id }}"
                                                                                data-sale-limit="false" title="" readonly>
                                                                            <span class="wcf-qty-selection-btn wcf-qty-increment wcf-qty-change-icon color-qty-increment"
                                                                                data-color-id="{{ $colorOption->id }}" title="">&plus;</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="wcf-price">
                                                                        <div class="wcf-display-price wcf-field-label">
                                                                            <span class="woocommerce-Price-amount amount"><span
                                                                                    class="woocommerce-Price-currencySymbol">&#2547;&nbsp;</span>&nbsp;<span class="color-total-{{ $colorOption->id }}">{{ $displayQty * $displayPrice }}</span></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>

                                        <style>
                                            @media (min-width: 768px) {
                                                .wcf-qty-options {
                                                    grid-template-columns: repeat(2, 1fr) !important;
                                                }
                                            }

                                            /* Make color cards clickable */
                                            .wcf-qty-row[class*="wcf-qty-row-color-"] {
                                                cursor: pointer;
                                                transition: all 0.2s ease;
                                            }

                                            .wcf-qty-row[class*="wcf-qty-row-color-"]:hover {
                                                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                                                transform: translateY(-2px);
                                            }

                                            /* Keep buttons with their own cursor */
                                            .wcf-qty-selection-btn {
                                                cursor: pointer !important;
                                            }
                                        </style>

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
                                                                    wire:model="name" class="input-text"
                                                                    name="billing_first_name" id="billing_first_name"
                                                                    placeholder="" value="" aria-required="true"
                                                                    autocomplete="given-name" />
                                                                <span
                                                                    class="wcf-field-required-error">{{ $errors->first('name') }}</span>
                                                            </span>
                                                        </p>
                                                        <div class="form-row form-row-wide address-field wcf-column-100 validate-required"
                                                            id="billing_address_1_field" data-priority="50"><label
                                                                for="billing_address_1" class="">Shipping&nbsp;<abbr class="required"
                                                                    title="required">*</abbr></label><div
                                                                class="woocommerce-input-wrapper">


                                                                <ul id="shipping_method"
                                                                    style="border: 1px solid var( --wcf-field-border-color ); display: flex; column-gap: 1rem; padding: .5rem;"
                                                                    class="woocommerce-shipping-methods">
                                                                    <li style="white-space: nowrap; margin: 0;">
                                                                        <input type="radio"
                                                                            wire:model.live="shipping"
                                                                            name="shipping_method[0]" data-index="0"
                                                                            id="shipping_method_0_flat_rate1"
                                                                            value="Inside Dhaka"
                                                                            class="shipping_method"
                                                                            checked='checked' /><label
                                                                            for="shipping_method_0_flat_rate1">ঢাকা শহর <strong
                                                                                class="woocommerce-Price-amount amount"><bdi>
                                                                                    @if (!(setting('show_option')->productwise_delivery_charge ?? false))
                                                                                        <strong
                                                                                            class="woocommerce-Price-currencySymbol">&#2547;</strong>
                                                                                        {{ $isFreeDelivery ? 'FREE' : setting('delivery_charge')->inside_dhaka }}
                                                                                    @endif
                                                                                </bdi>
                                                                            </strong></label>
                                                                    </li>
                                                                    <li style="white-space: nowrap; margin: 0;">
                                                                        <input type="radio"
                                                                            wire:model.live="shipping"
                                                                            name="shipping_method[0]" data-index="0"
                                                                            id="shipping_method_0_flat_rate2"
                                                                            value="Outside Dhaka"
                                                                            class="shipping_method" /><label
                                                                            for="shipping_method_0_flat_rate2">ঢাকার বাইরে <strong
                                                                                class="woocommerce-Price-amount amount"><bdi>
                                                                                    @if (!(setting('show_option')->productwise_delivery_charge ?? false))
                                                                                        <strong
                                                                                            class="woocommerce-Price-currencySymbol">&#2547;</strong>
                                                                                        {{ $isFreeDelivery ? 'FREE' : setting('delivery_charge')->outside_dhaka }}
                                                                                    @endif
                                                                                </bdi></strong></label>
                                                                    </li>
                                                                </ul>

                                                                <div
                                                                    class="wcf-field-required-error">{{ $errors->first('address') }}</div>
                                                            </div>
                                                        </div>
                                                        <p class="form-row form-row-wide address-field wcf-column-100 validate-required"
                                                            id="billing_address_1_field" data-priority="50"><label
                                                                for="billing_address_1" class="">আপনার সম্পূর্ণ
                                                                ঠিকানা&nbsp;<abbr class="required"
                                                                    title="required">*</abbr></label><span
                                                                class="woocommerce-input-wrapper"><input type="text"
                                                                    wire:model="address" class="input-text"
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
                                                                    class="input-text" name="billing_phone"
                                                                    id="billing_phone" placeholder=""
                                                                    value="{{ setting('show_option')->hide_phone_prefix ?? false ? '' : '+880' }}"
                                                                    aria-required="true" autocomplete="tel" />
                                                                <span
                                                                    class="wcf-field-required-error">{{ $errors->first('phone') }}</span>
                                                            </span>
                                                        </p>

                                                        @if($colorOptions->isNotEmpty())
                                                            @php
                                                                // Get all non-color attributes from variations
                                                                $otherAttributes = $product->variations
                                                                    ->flatMap(fn($v) => $v->options)
                                                                    ->filter(fn($option) => strtolower($option->attribute->name) !== 'color')
                                                                    ->unique('id')
                                                                    ->groupBy('attribute_id');

                                                                $attributes = \App\Models\Attribute::find($otherAttributes->keys());
                                                            @endphp

                                                            @foreach($attributes as $attribute)
                                                                <div class="form-row form-row-wide address-field wcf-column-100 validate-required"
                                                                    id="attribute_{{ $attribute->id }}_field" data-priority="105">
                                                                    <label for="attribute_{{ $attribute->id }}" class="">{{ $attribute->name }}&nbsp;<abbr class="required"
                                                                        title="required">*</abbr></label>
                                                                    <div class="woocommerce-input-wrapper">
                                                                        <ul id="attribute_{{ $attribute->id }}"
                                                                            style="border: 1px solid var( --wcf-field-border-color ); display: flex; column-gap: 1rem; padding: .5rem; list-style: none; margin: 0;"
                                                                            class="woocommerce-shipping-methods">
                                                                            @foreach($otherAttributes[$attribute->id] as $option)
                                                                                <li style="white-space: nowrap; margin: 0; display: flex; align-items: center;">
                                                                                    <input type="radio"
                                                                                        name="attribute_{{ $attribute->id }}"
                                                                                        data-attribute-id="{{ $attribute->id }}"
                                                                                        id="attribute_option_{{ $option->id }}"
                                                                                        value="{{ $option->id }}"
                                                                                        class="attribute-option-radio"
                                                                                        style="margin: 3px .4375em 0 0;"
                                                                                        @if(in_array($option->id, $selectedOptionIds)) checked='checked' @endif />
                                                                                    <label for="attribute_option_{{ $option->id }}" style="margin: 0;">{{ $option->name }}</label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>

                                                </div>

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
                                                        @foreach (cart()->content() as $item)
                                                            <tr class="cart_item">
                                                                <td class="product-name">{{ $item->name }}&nbsp;
                                                                    <strong
                                                                        class="product-quantity">&times;&nbsp;{{ $item->qty }}</strong>
                                                                </td>
                                                                <td class="product-total">
                                                                    <span class="woocommerce-Price-amount amount"><bdi><span
                                                                                class="woocommerce-Price-currencySymbol">&#2547;&nbsp;</span>&nbsp;{{ $item->qty * $item->price }}</bdi></span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>

                                                        <tr class="cart-subtotal" style="display: none;">
                                                            <th>Subtotal</th>
                                                            <td><span class="woocommerce-Price-amount amount"><bdi><span
                                                                            class="woocommerce-Price-currencySymbol">&#2547;&nbsp;</span>&nbsp;{{ cart()->subtotal() }}</bdi></span>
                                                            </td>
                                                        </tr>




                                                        <tr class="woocommerce-shipping-totals shipping">
                                                            <th>Shipping</th>
                                                            <td><span class="woocommerce-Price-amount amount"><bdi><span
                                                                            class="woocommerce-Price-currencySymbol">&#2547;&nbsp;</span>&nbsp;{{ cart()->getCost('deliveryFee') }}</bdi></span>
                                                            </td>
                                                        </tr>






                                                        <tr class="order-total">
                                                            <th>Total</th>
                                                            <td><strong><span
                                                                        class="woocommerce-Price-amount amount"><bdi><span
                                                                                class="woocommerce-Price-currencySymbol">&#2547;&nbsp;</span>&nbsp;{{ cart()->total() }}</bdi></span></strong>
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
                                                                        href="{{ url('/privacy-policy') }}"
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
                                                            Order&nbsp;&nbsp;&#2547;&nbsp;&nbsp;{{ cart()->total() }}</button>

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
    @php
        $variationsData = $product->variations->map(function($v) {
            return [
                'id' => $v->id,
                'options' => $v->options->pluck('id')->toArray(),
                'selling_price' => $v->selling_price,
            ];
        });
    @endphp

    <script>
        // Store variation data for finding correct variation ID
        const productVariations = @json($variationsData);

        // Store selected color and attributes
        let selectedColorId = null;
        const selectedAttributes = {};
        let currentCartVariationId = null; // Track the variation currently in cart

        // Handle color checkbox changes (work as radio buttons)
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('color-checkbox')) {
                const colorId = e.target.dataset.colorId;
                const isChecked = e.target.checked;

                if (isChecked) {
                    // Uncheck all other color checkboxes
                    document.querySelectorAll('.color-checkbox').forEach(cb => {
                        if (cb !== e.target) {
                            cb.checked = false;
                        }
                    });

                    selectedColorId = parseInt(colorId);
                    updateVariationSelection();
                } else {
                    selectedColorId = null;
                    updateVariationSelection();
                }
            }

            // Handle attribute selection changes
            if (e.target.classList.contains('attribute-option-radio')) {
                const attributeId = e.target.dataset.attributeId;
                const optionId = parseInt(e.target.value);

                selectedAttributes[attributeId] = optionId;
                updateVariationSelection();
            }
        });

        // Handle quantity increment
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('color-qty-increment')) {
                const colorId = parseInt(e.target.dataset.colorId);

                // Make sure this color is selected
                if (selectedColorId !== colorId) {
                    const colorCheckbox = document.querySelector(`.color-checkbox[data-color-id="${colorId}"]`);
                    if (colorCheckbox) {
                        colorCheckbox.click(); // This will trigger color selection
                    }
                    selectedColorId = colorId;
                }

                const variationId = getVariationId();
                if (variationId) {
                    @this.call('increaseQuantity', variationId);
                }
            }

            // Handle quantity decrement
            if (e.target.classList.contains('color-qty-decrement')) {
                const colorId = parseInt(e.target.dataset.colorId);
                const qtyInput = document.querySelector(`.color-qty-input-${colorId}`);
                const currentQty = parseInt(qtyInput ? qtyInput.value : 0) || 0;

                if (currentQty > 0) {
                    // Make sure this color is selected
                    if (selectedColorId !== colorId) {
                        selectedColorId = colorId;
                    }

                    const variationId = getVariationId();
                    if (variationId) {
                        @this.call('decreaseQuantity', variationId);
                    }
                }
            }
        });

        function updateVariationSelection() {
            if (!selectedColorId) return;

            // Build array of selected option IDs (color + other attributes)
            const selectedOptions = [selectedColorId, ...Object.values(selectedAttributes)];

            // Find matching variation
            const variation = productVariations.find(v => {
                return selectedOptions.every(opt => v.options.includes(opt)) &&
                       selectedOptions.length === v.options.length;
            });

            if (variation) {
                // Get current quantity for this color
                const qtyInput = document.querySelector(`.color-qty-input-${selectedColorId}`);
                const currentQty = parseInt(qtyInput ? qtyInput.value : 0) || 0;

                // Update price display for this color
                const priceElement = document.querySelector(`.color-price-${selectedColorId}`);
                if (priceElement) {
                    priceElement.textContent = variation.selling_price;
                }

                // Update total display
                const totalElement = document.querySelector(`.color-total-${selectedColorId}`);
                if (totalElement) {
                    totalElement.textContent = currentQty * variation.selling_price;
                }

                // Check if this variation is already in cart or if we need to swap
                const colorCheckbox = document.querySelector(`.color-checkbox[data-color-id="${selectedColorId}"]`);
                if (colorCheckbox && colorCheckbox.checked) {
                    // Check if we're selecting a different variation than what's currently tracked
                    if (currentCartVariationId !== variation.id) {
                        // Different variation selected - Livewire's increaseQuantity will call
                        // ProductDetail::landing() which destroys cart and adds new variation
                        console.log('Swapping variation from', currentCartVariationId, 'to', variation.id);
                        currentCartVariationId = variation.id;
                        @this.call('increaseQuantity', variation.id);
                    } else if (currentQty === 0) {
                        // No cart item yet, add it
                        console.log('Adding new variation', variation.id);
                        currentCartVariationId = variation.id;
                        @this.call('increaseQuantity', variation.id);
                    } else {
                        // Same variation already selected and in cart
                        console.log('Same variation already selected', variation.id);
                        currentCartVariationId = variation.id;
                    }
                }
            }
        }

        function getVariationId() {
            if (!selectedColorId) return null;

            const selectedOptions = [selectedColorId, ...Object.values(selectedAttributes)];

            const variation = productVariations.find(v => {
                return selectedOptions.every(opt => v.options.includes(opt)) &&
                       selectedOptions.length === v.options.length;
            });

            return variation ? variation.id : null;
        }

        // Make entire color card clickable
        document.addEventListener('click', function(e) {
            // Find if click is within a color card
            const colorCard = e.target.closest('.wcf-qty-row[class*="wcf-qty-row-color-"]');

            if (colorCard) {
                // Don't trigger if clicking on buttons or checkbox itself
                if (e.target.closest('.wcf-qty-selection-btn') ||
                    e.target.closest('.color-checkbox') ||
                    e.target.classList.contains('color-checkbox')) {
                    return;
                }

                // Find and click the checkbox
                const checkbox = colorCard.querySelector('.color-checkbox');
                if (checkbox && !checkbox.checked) {
                    checkbox.click();
                }
            }
        });

        // Initialize on page load - detect if any color is already checked/in cart
        document.addEventListener('DOMContentLoaded', function() {
            // Find the first checked color checkbox
            const checkedColorCheckbox = document.querySelector('.color-checkbox:checked');
            if (checkedColorCheckbox) {
                selectedColorId = parseInt(checkedColorCheckbox.dataset.colorId);
            }

            // Find any pre-selected attribute options
            document.querySelectorAll('.attribute-option-radio:checked').forEach(radio => {
                const attributeId = radio.dataset.attributeId;
                const optionId = parseInt(radio.value);
                selectedAttributes[attributeId] = optionId;
            });

            // Find the current variation ID (what's in cart now)
            if (selectedColorId && Object.keys(selectedAttributes).length > 0) {
                const selectedOptions = [selectedColorId, ...Object.values(selectedAttributes)];
                const variation = productVariations.find(v => {
                    return selectedOptions.every(opt => v.options.includes(opt)) &&
                           selectedOptions.length === v.options.length;
                });
                if (variation) {
                    currentCartVariationId = variation.id;
                }
            }

            // Trigger initial variation selection if color is selected
            if (selectedColorId && Object.keys(selectedAttributes).length > 0) {
                updateVariationSelection();
            }
        });

        // Send data to the server before the user leaves
        window.addEventListener("beforeunload", function (event) {
            navigator.sendBeacon(
                "/save-checkout-progress",
                new Blob([JSON.stringify({
                    name: document.getElementById('billing_first_name').value,
                    phone: document.getElementById('billing_phone').value,
                    address: document.getElementById('billing_address_1').value,
                })], { type: 'application/json' })
            );
        });
    </script>
</section>
