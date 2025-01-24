@props(['page'])


<!doctype html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <title>{{ $page->title }}</title>
    <link rel='stylesheet' id='woocommerce-general-css'
        href='{{ asset('assets/demo.orioit.com/wp-content/plugins/woocommerce/assets/css/woocommerce.css?ver=9.4.2') }}'
        media='all' />
    <link rel='stylesheet' id='elementor-frontend-css'
        href='{{ asset('assets/demo.orioit.com/wp-content/plugins/elementor/assets/css/frontend.min.css?ver=3.25.9') }}'
        media='all' />
    <link rel='stylesheet' id='elementor-post-7-css'
        href='{{ asset('assets/demo.orioit.com/wp-content/uploads/elementor/css/post-7.css?ver=1736836498') }}' media='all' />
    
    <link rel='stylesheet' id='wcf-frontend-global-css'
        href='{{ asset('assets/demo.orioit.com/wp-content/plugins/cartflows/assets/css/frontend.css?ver=2.0.12') }}' media='all' />
    
    <link rel='stylesheet' id='swiper-css'
        href='{{ asset('assets/demo.orioit.com/wp-content/plugins/elementor/assets/lib/swiper/v8/css/swiper.min.css?ver=8.4.5') }}'
        media='all' />
    <link rel='stylesheet' id='e-swiper-css'
        href='{{ asset('assets/demo.orioit.com/wp-content/plugins/elementor/assets/css/conditionals/e-swiper.min.css?ver=3.25.9') }}'
        media='all' />
    <link rel='stylesheet' id='widget-image-css'
        href='{{ asset('assets/demo.orioit.com/wp-content/plugins/elementor/assets/css/widget-image.min.css?ver=3.25.9') }}'
        media='all' />
    <link rel='stylesheet' id='widget-heading-css'
        href='{{ asset('assets/demo.orioit.com/wp-content/plugins/elementor/assets/css/widget-heading.min.css?ver=3.25.9') }}'
        media='all' />
    <link rel='stylesheet' id='widget-video-css'
        href='{{ asset('assets/demo.orioit.com/wp-content/plugins/elementor/assets/css/widget-video.min.css?ver=3.25.9') }}'
        media='all' />
    <link rel='stylesheet' id='widget-price-list-css'
        href='{{ asset('assets/demo.orioit.com/wp-content/plugins/elementor-pro/assets/css/widget-price-list.min.css?ver=3.25.2') }}'
        media='all' />
    <link rel='stylesheet' id='widget-image-carousel-css'
        href='{{ asset('assets/demo.orioit.com/wp-content/plugins/elementor/assets/css/widget-image-carousel.min.css?ver=3.25.9') }}'
        media='all' />
    <link rel='stylesheet' id='elementor-post-120-css'
        href='{{ asset('assets/demo.orioit.com/wp-content/uploads/elementor/css/post-120.css?ver=1736887944') }}' media='all' />
    <link rel='stylesheet' id='wcf-checkout-template-css'
        href='{{ asset('assets/demo.orioit.com/wp-content/plugins/cartflows/assets/css/checkout-template.css?ver=2.0.12') }}'
        media='all' />
    
    <link rel='stylesheet' id='wcf-pro-checkout-css'
        href='{{ asset('assets/demo.orioit.com/wp-content/plugins/cartflows-pro/assets/css/checkout-styles.css?ver=2.0.10') }}'
        media='all' />
    
    <link rel='stylesheet' id='google-fonts-1-css'
        href='https://fonts.googleapis.com/css?family=Roboto%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CRoboto+Slab%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CHind+Siliguri%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CPoppins%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CMontserrat%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&#038;display=swap&#038;ver=6.7.1'
        media='all' />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <script src="{{ asset('assets/demo.orioit.com/wp-includes/js/jquery/jquery.min.js?ver=3.7.1') }}" id="jquery-core-js"></script>
    <script src="{{ asset('assets/demo.orioit.com/wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1') }}" id="jquery-migrate-js">
    </script>
    <script
        src="{{ asset('assets/demo.orioit.com/wp-content/plugins/woocommerce/assets/js/js-cookie/js.cookie.min.js?ver=2.1.4-wc.9.4.2') }}"
        id="js-cookie-js" defer data-wp-strategy="defer"></script>
    
    <script
        src="{{ asset('assets/demo.orioit.com/wp-content/plugins/woocommerce/assets/js/jquery-blockui/jquery.blockUI.min.js?ver=2.7.0-wc.9.4.2') }}"
        id="jquery-blockui-js" defer data-wp-strategy="defer"></script>
    
    
    <script
        src="{{ asset('assets/demo.orioit.com/wp-content/plugins/woocommerce/assets/js/jquery-cookie/jquery.cookie.min.js?ver=1.4.1-wc.9.4.2') }}"
        id="jquery-cookie-js" data-wp-strategy="defer"></script>
    
</head>

<body
    class="cartflows_step-template-default single single-cartflows_step postid-120 theme-hello-elementor woocommerce-checkout woocommerce-page woocommerce-no-js cartflows-2.0.12  cartflows-pro-2.0.10 elementor-default elementor-kit-7 elementor-page elementor-page-120">


    <a class="skip-link screen-reader-text" href="#content">Skip to content</a>

    <header id="site-header" class="site-header dynamic-header ">
        <div class="header-inner">
            <div class="site-branding show-title">
            </div>

        </div>
    </header>

    <main id="content" class="site-main post-120 cartflows_step type-cartflows_step status-publish hentry">


        <div class="page-content">
            <div data-elementor-type="wp-post" data-elementor-id="120" class="elementor elementor-120"
                data-elementor-settings="{&quot;element_pack_global_tooltip_width&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;element_pack_global_tooltip_width_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;element_pack_global_tooltip_width_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;element_pack_global_tooltip_padding&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_padding_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_padding_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_border_radius&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_border_radius_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_border_radius_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true}}"
                data-elementor-post-type="cartflows_step">

                <x-filament-fabricator::page-blocks :blocks="$page->blocks" />

                <section
                    class="elementor-section elementor-top-section elementor-element elementor-element-f8cfaca landing elementor-section-boxed elementor-section-height-default"
                    data-id="f8cfaca" data-element_type="section" id="order"
                    data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
                    <div class="elementor-container elementor-column-gap-no">
                        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-82536c1"
                            data-id="82536c1" data-element_type="column"
                            data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
                            <div class="elementor-widget-wrap elementor-element-populated">
                                <div class="elementor-element elementor-element-68acae2 elementor-widget elementor-widget-heading"
                                    data-id="68acae2" data-element_type="widget" data-widget_type="heading.default">
                                    <div class="elementor-widget-container">
                                        <h2 class="elementor-heading-title elementor-size-default">অর্ডার করতে নিচের
                                            ফর্মটি পূরন করুন</h2>
                                    </div>
                                </div>
                                <div class="elementor-element elementor-element-86626e9 elementor-widget elementor-widget-checkout-form"
                                    data-id="86626e9" data-element_type="widget"
                                    data-widget_type="checkout-form.default">
                                    <div class="elementor-widget-container">
                                        <div class = "wcf-el-checkout-form cartflows-elementor__checkout-form">
                                            <div id="wcf-embed-checkout-form"
                                                class="wcf-embed-checkout-form wcf-embed-checkout-form-two-column wcf-field-default">
                                                <!-- CHECKOUT SHORTCODE -->

                                                <div class="woocommerce">
                                                    <div class="woocommerce-notices-wrapper"></div>
                                                    <div class="woocommerce-notices-wrapper"></div>
                                                    <form name="checkout" method="post"
                                                        class="checkout woocommerce-checkout"
                                                        action="https://demo.orioit.com"
                                                        enctype="multipart/form-data">



                                                        <div class="wcf-col2-set col2-set" id="customer_details">
                                                            <div class="wcf-col-1 col-1">
                                                                <wc-order-attribution-inputs></wc-order-attribution-inputs>
                                                                <div class="woocommerce-billing-fields">

                                                                    <h3 id="billing_fields_heading">Billing details
                                                                    </h3>



                                                                    <div
                                                                        class="woocommerce-billing-fields__field-wrapper">
                                                                        <p class="form-row form-row-first wcf-column-100 validate-required"
                                                                            id="billing_first_name_field"
                                                                            data-priority="10"><label
                                                                                for="billing_first_name"
                                                                                class="">আপনার নাম&nbsp;<abbr
                                                                                    class="required"
                                                                                    title="required">*</abbr></label><span
                                                                                class="woocommerce-input-wrapper"><input
                                                                                    type="text" class="input-text "
                                                                                    name="billing_first_name"
                                                                                    id="billing_first_name"
                                                                                    placeholder="" value=""
                                                                                    aria-required="true"
                                                                                    autocomplete="given-name" /></span>
                                                                        </p>
                                                                        <p class="form-row form-row-wide address-field wcf-column-100 validate-required"
                                                                            id="billing_address_1_field"
                                                                            data-priority="40"><label
                                                                                for="billing_address_1"
                                                                                class="">আপনার ঠিকানা&nbsp;<abbr
                                                                                    class="required"
                                                                                    title="required">*</abbr></label><span
                                                                                class="woocommerce-input-wrapper"><input
                                                                                    type="text" class="input-text "
                                                                                    name="billing_address_1"
                                                                                    id="billing_address_1"
                                                                                    placeholder="আপনার জেলা,থানা ও যে লোকেশন থেকে নিবেন ঐ এরিয়ার নাম দিন"
                                                                                    value=""
                                                                                    aria-required="true"
                                                                                    autocomplete="address-line1" /></span>
                                                                        </p>
                                                                        <p class="form-row form-row-wide wcf-column-100 validate-required validate-phone"
                                                                            id="billing_phone_field"
                                                                            data-priority="90"><label
                                                                                for="billing_phone"
                                                                                class="">মোবাইল
                                                                                নাম্বার&nbsp;<abbr class="required"
                                                                                    title="required">*</abbr></label><span
                                                                                class="woocommerce-input-wrapper"><input
                                                                                    type="tel" class="input-text "
                                                                                    name="billing_phone"
                                                                                    id="billing_phone" placeholder=""
                                                                                    value=""
                                                                                    aria-required="true"
                                                                                    autocomplete="tel" /></span></p>
                                                                        <p class="form-row form-row-wide address-field update_totals_on_change wcf-column-100 validate-required"
                                                                            id="billing_country_field"
                                                                            data-priority="110"><label
                                                                                for="billing_country"
                                                                                class="">Country /
                                                                                Region&nbsp;<abbr class="required"
                                                                                    title="required">*</abbr></label><span
                                                                                class="woocommerce-input-wrapper"><strong>Bangladesh</strong><input
                                                                                    type="hidden"
                                                                                    name="billing_country"
                                                                                    id="billing_country"
                                                                                    value="BD"
                                                                                    aria-required="true"
                                                                                    autocomplete="country"
                                                                                    class="country_to_state"
                                                                                    readonly="readonly" /></span></p>
                                                                        <p class="form-row form-row-wide wcf-column-100 wcf-input-radio-field-wrapper validate-required"
                                                                            id="billing_size_field"
                                                                            data-priority="120"><label
                                                                                for="billing_size_M"
                                                                                class="input-radio">টিশার্টের
                                                                                সাইজ&nbsp;<abbr class="required"
                                                                                    title="required">*</abbr></label><span
                                                                                class="woocommerce-input-wrapper"><input
                                                                                    type="radio"
                                                                                    class="input-radio "
                                                                                    value="M" name="billing_size"
                                                                                    aria-required="true"
                                                                                    id="billing_size_M" /><label
                                                                                    for="billing_size_M"
                                                                                    class="radio input-radio">M</label><input
                                                                                    type="radio"
                                                                                    class="input-radio "
                                                                                    value="L" name="billing_size"
                                                                                    aria-required="true"
                                                                                    id="billing_size_L" /><label
                                                                                    for="billing_size_L"
                                                                                    class="radio input-radio">L</label><input
                                                                                    type="radio"
                                                                                    class="input-radio "
                                                                                    value="XL" name="billing_size"
                                                                                    aria-required="true"
                                                                                    id="billing_size_XL" /><label
                                                                                    for="billing_size_XL"
                                                                                    class="radio input-radio">XL</label><input
                                                                                    type="radio"
                                                                                    class="input-radio "
                                                                                    value="2XL" name="billing_size"
                                                                                    aria-required="true"
                                                                                    id="billing_size_2XL" /><label
                                                                                    for="billing_size_2XL"
                                                                                    class="radio input-radio">2XL</label></span>
                                                                        </p>
                                                                    </div>

                                                                </div>

                                                            </div>

                                                            <div class="wcf-col-2 col-2">

                                                                <div class="woocommerce-shipping-fields">
                                                                </div>
                                                                <div class="woocommerce-additional-fields">


                                                                    <input type="hidden"
                                                                        class="input-hidden _wcf_flow_id"
                                                                        name="_wcf_flow_id" value="119"><input
                                                                        type="hidden"
                                                                        class="input-hidden _wcf_checkout_id"
                                                                        name="_wcf_checkout_id" value="120">
                                                                </div>
                                                            </div>
                                                        </div>



                                                        <div class='wcf-order-wrap'>



                                                            <h3 id="order_review_heading">Your order</h3>


                                                            <div id="order_review"
                                                                class="woocommerce-checkout-review-order">
                                                                <table
                                                                    class="shop_table woocommerce-checkout-review-order-table"
                                                                    data-update-time="1737379941">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="product-name">Product</th>
                                                                            <th class="product-total">Subtotal</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr class="cart_item">
                                                                            <td class="product-name">
                                                                                Mens Premium T-Shirt- Jamming&nbsp;
                                                                                <strong
                                                                                    class="product-quantity">&times;&nbsp;1</strong>
                                                                            </td>
                                                                            <td class="product-total">
                                                                                <span
                                                                                    class="woocommerce-Price-amount amount"><bdi><span
                                                                                            class="woocommerce-Price-currencySymbol">&#2547;&nbsp;</span>&nbsp;599.00</bdi></span>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                    <tfoot>

                                                                        <tr class="cart-subtotal">
                                                                            <th>Subtotal</th>
                                                                            <td><span
                                                                                    class="woocommerce-Price-amount amount"><bdi><span
                                                                                            class="woocommerce-Price-currencySymbol">&#2547;&nbsp;</span>&nbsp;599.00</bdi></span>
                                                                            </td>
                                                                        </tr>




                                                                        <tr
                                                                            class="woocommerce-shipping-totals shipping">
                                                                            <th>Shipping</th>
                                                                            <td data-title="Shipping">
                                                                                <ul id="shipping_method"
                                                                                    class="woocommerce-shipping-methods">
                                                                                    <li>
                                                                                        <input type="radio"
                                                                                            name="shipping_method[0]"
                                                                                            data-index="0"
                                                                                            id="shipping_method_0_flat_rate1"
                                                                                            value="flat_rate:1"
                                                                                            class="shipping_method"
                                                                                            checked='checked' /><label
                                                                                            for="shipping_method_0_flat_rate1">Inside
                                                                                            Dhaka: <span
                                                                                                class="woocommerce-Price-amount amount"><bdi><span
                                                                                                        class="woocommerce-Price-currencySymbol">&#2547;&nbsp;</span>&nbsp;50.00</bdi></span></label>
                                                                                    </li>
                                                                                    <li>
                                                                                        <input type="radio"
                                                                                            name="shipping_method[0]"
                                                                                            data-index="0"
                                                                                            id="shipping_method_0_flat_rate2"
                                                                                            value="flat_rate:2"
                                                                                            class="shipping_method" /><label
                                                                                            for="shipping_method_0_flat_rate2">Outside
                                                                                            Dhaka: <span
                                                                                                class="woocommerce-Price-amount amount"><bdi><span
                                                                                                        class="woocommerce-Price-currencySymbol">&#2547;&nbsp;</span>&nbsp;100.00</bdi></span></label>
                                                                                    </li>
                                                                                </ul>


                                                                            </td>
                                                                        </tr>






                                                                        <tr class="order-total">
                                                                            <th>Total</th>
                                                                            <td><strong><span
                                                                                        class="woocommerce-Price-amount amount"><bdi><span
                                                                                                class="woocommerce-Price-currencySymbol">&#2547;&nbsp;</span>&nbsp;649.00</bdi></span></strong>
                                                                            </td>
                                                                        </tr>


                                                                    </tfoot>
                                                                </table>
                                                                <div id="payment"
                                                                    class="woocommerce-checkout-payment">
                                                                    <ul
                                                                        class="wc_payment_methods payment_methods methods">
                                                                        <li
                                                                            class="wc_payment_method payment_method_cod">
                                                                            <input id="payment_method_cod"
                                                                                type="radio" class="input-radio"
                                                                                name="payment_method" value="cod"
                                                                                checked='checked'
                                                                                data-order_button_text="" />

                                                                            <label for="payment_method_cod">
                                                                                Cash on delivery </label>
                                                                            <div
                                                                                class="payment_box payment_method_cod">
                                                                                <p>Pay with cash upon delivery.</p>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                    <div class="form-row place-order">
                                                                        <noscript>
                                                                            Since your browser does not support
                                                                            JavaScript, or it is disabled, please ensure
                                                                            you click the <em>Update Totals</em> button
                                                                            before placing your order. You may be
                                                                            charged more than the amount stated above if
                                                                            you fail to do so. <br /><button
                                                                                type="submit" class="button alt"
                                                                                name="woocommerce_checkout_update_totals"
                                                                                value="Update totals">Update
                                                                                totals</button>
                                                                        </noscript>

                                                                        <div
                                                                            class="woocommerce-terms-and-conditions-wrapper">
                                                                            <div
                                                                                class="woocommerce-privacy-policy-text">
                                                                                <p>Your personal data will be used to
                                                                                    process your order, support your
                                                                                    experience throughout this website,
                                                                                    and for other purposes described in
                                                                                    our <a
                                                                                        href="{{ asset('assets/demo.orioit.com/?page_id=3') }}"
                                                                                        class="woocommerce-privacy-policy-link"
                                                                                        target="_blank">privacy
                                                                                        policy</a>.</p>
                                                                            </div>
                                                                        </div>


                                                                        <button type="submit" class="button alt"
                                                                            name="woocommerce_checkout_place_order"
                                                                            id="place_order"
                                                                            value="অর্ডার করুন&nbsp;&nbsp;&#2547;&nbsp;&nbsp;649.00"
                                                                            data-value="অর্ডার করুন&nbsp;&nbsp;&#2547;&nbsp;&nbsp;649.00">অর্ডার
                                                                            করুন&nbsp;&nbsp;&#2547;&nbsp;&nbsp;649.00</button>

                                                                        <input type="hidden"
                                                                            id="woocommerce-process-checkout-nonce"
                                                                            name="woocommerce-process-checkout-nonce"
                                                                            value="59118672f2" /><input type="hidden"
                                                                            name="_wp_http_referer"
                                                                            value="/step/tshirt/" />
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
            </div>


        </div>


    </main>
    
    <script
        src="{{ asset('assets/demo.orioit.com/wp-content/plugins/woocommerce/assets/js/flexslider/jquery.flexslider.min.js?ver=2.7.2-wc.9.4.2') }}"
        id="flexslider-js" defer data-wp-strategy="defer"></script>
    <script src="{{ asset('assets/demo.orioit.com/wp-content/plugins/elementor-pro/assets/js/webpack-pro.runtime.min.js?ver=3.25.2') }}"
        id="elementor-pro-webpack-runtime-js"></script>
    <script src="{{ asset('assets/demo.orioit.com/wp-content/plugins/elementor/assets/js/webpack.runtime.min.js?ver=3.25.9') }}"
        id="elementor-webpack-runtime-js"></script>
    <script src="{{ asset('assets/demo.orioit.com/wp-content/plugins/elementor/assets/js/frontend-modules.min.js?ver=3.25.9') }}"
        id="elementor-frontend-modules-js"></script>
    <script src="{{ asset('assets/demo.orioit.com/wp-includes/js/dist/hooks.min.js?ver=4d63a3d491d11ffd8ac6') }}" id="wp-hooks-js">
    </script>
    <script id="elementor-pro-frontend-js-before">
        var ElementorProFrontendConfig = {
            "ajaxurl": "https:\/\/demo.orioit.com\/wp-admin\/admin-ajax.php",
            "nonce": "e020cc32a2",
            "urls": {
                "assets": "https:\/\/demo.orioit.com\/wp-content\/plugins\/elementor-pro\/assets\/",
                "rest": "https:\/\/demo.orioit.com\/wp-json\/"
            },
            "settings": {
                "lazy_load_background_images": true
            },
            "popup": {
                "hasPopUps": false
            },
            "shareButtonsNetworks": {
                "facebook": {
                    "title": "Facebook",
                    "has_counter": true
                },
                "twitter": {
                    "title": "Twitter"
                },
                "linkedin": {
                    "title": "LinkedIn",
                    "has_counter": true
                },
                "pinterest": {
                    "title": "Pinterest",
                    "has_counter": true
                },
                "reddit": {
                    "title": "Reddit",
                    "has_counter": true
                },
                "vk": {
                    "title": "VK",
                    "has_counter": true
                },
                "odnoklassniki": {
                    "title": "OK",
                    "has_counter": true
                },
                "tumblr": {
                    "title": "Tumblr"
                },
                "digg": {
                    "title": "Digg"
                },
                "skype": {
                    "title": "Skype"
                },
                "stumbleupon": {
                    "title": "StumbleUpon",
                    "has_counter": true
                },
                "mix": {
                    "title": "Mix"
                },
                "telegram": {
                    "title": "Telegram"
                },
                "pocket": {
                    "title": "Pocket",
                    "has_counter": true
                },
                "xing": {
                    "title": "XING",
                    "has_counter": true
                },
                "whatsapp": {
                    "title": "WhatsApp"
                },
                "email": {
                    "title": "Email"
                },
                "print": {
                    "title": "Print"
                },
                "x-twitter": {
                    "title": "X"
                },
                "threads": {
                    "title": "Threads"
                }
            },
            "woocommerce": {
                "menu_cart": {
                    "cart_page_url": "https:\/\/demo.orioit.com",
                    "checkout_page_url": "https:\/\/demo.orioit.com",
                    "fragments_nonce": "7d7f529b9d"
                },
                "productAddedToCart": true
            },
            "facebook_sdk": {
                "lang": "en_US",
                "app_id": ""
            },
            "lottie": {
                "defaultAnimationUrl": "https:\/\/demo.orioit.com\/wp-content\/plugins\/elementor-pro\/modules\/lottie\/assets\/animations\/default.json"
            }
        };
    </script>
    <script src="{{ asset('assets/demo.orioit.com/wp-content/plugins/elementor-pro/assets/js/frontend.min.js?ver=3.25.2') }}"
        id="elementor-pro-frontend-js"></script>
    <script src="{{ asset('assets/demo.orioit.com/wp-includes/js/jquery/ui/core.min.js?ver=1.13.3') }}" id="jquery-ui-core-js"></script>
    <script id="elementor-frontend-js-before">
        var elementorFrontendConfig = {
            "environmentMode": {
                "edit": false,
                "wpPreview": false,
                "isScriptDebug": false
            },
            "i18n": {
                "shareOnFacebook": "Share on Facebook",
                "shareOnTwitter": "Share on Twitter",
                "pinIt": "Pin it",
                "download": "Download",
                "downloadImage": "Download image",
                "fullscreen": "Fullscreen",
                "zoom": "Zoom",
                "share": "Share",
                "playVideo": "Play Video",
                "previous": "Previous",
                "next": "Next",
                "close": "Close",
                "a11yCarouselWrapperAriaLabel": "Carousel | Horizontal scrolling: Arrow Left & Right",
                "a11yCarouselPrevSlideMessage": "Previous slide",
                "a11yCarouselNextSlideMessage": "Next slide",
                "a11yCarouselFirstSlideMessage": "This is the first slide",
                "a11yCarouselLastSlideMessage": "This is the last slide",
                "a11yCarouselPaginationBulletMessage": "Go to slide"
            },
            "is_rtl": false,
            "breakpoints": {
                "xs": 0,
                "sm": 480,
                "md": 768,
                "lg": 1025,
                "xl": 1440,
                "xxl": 1600
            },
            "responsive": {
                "breakpoints": {
                    "mobile": {
                        "label": "Mobile Portrait",
                        "value": 767,
                        "default_value": 767,
                        "direction": "max",
                        "is_enabled": true
                    },
                    "mobile_extra": {
                        "label": "Mobile Landscape",
                        "value": 880,
                        "default_value": 880,
                        "direction": "max",
                        "is_enabled": false
                    },
                    "tablet": {
                        "label": "Tablet Portrait",
                        "value": 1024,
                        "default_value": 1024,
                        "direction": "max",
                        "is_enabled": true
                    },
                    "tablet_extra": {
                        "label": "Tablet Landscape",
                        "value": 1200,
                        "default_value": 1200,
                        "direction": "max",
                        "is_enabled": false
                    },
                    "laptop": {
                        "label": "Laptop",
                        "value": 1366,
                        "default_value": 1366,
                        "direction": "max",
                        "is_enabled": false
                    },
                    "widescreen": {
                        "label": "Widescreen",
                        "value": 2400,
                        "default_value": 2400,
                        "direction": "min",
                        "is_enabled": false
                    }
                },
                "hasCustomBreakpoints": false
            },
            "version": "3.25.9",
            "is_static": false,
            "experimentalFeatures": {
                "e_font_icon_svg": true,
                "additional_custom_breakpoints": true,
                "container": true,
                "e_swiper_latest": true,
                "e_nested_atomic_repeaters": true,
                "e_optimized_control_loading": true,
                "e_onboarding": true,
                "e_css_smooth_scroll": true,
                "theme_builder_v2": true,
                "hello-theme-header-footer": true,
                "home_screen": true,
                "nested-elements": true,
                "editor_v2": true,
                "e_element_cache": true,
                "link-in-bio": true,
                "floating-buttons": true,
                "launchpad-checklist": true
            },
            "urls": {
                "assets": "https:\/\/demo.orioit.com\/wp-content\/plugins\/elementor\/assets\/",
                "ajaxurl": "https:\/\/demo.orioit.com\/wp-admin\/admin-ajax.php",
                "uploadUrl": "https:\/\/demo.orioit.com\/wp-content\/uploads"
            },
            "nonces": {
                "floatingButtonsClickTracking": "b90916dce7"
            },
            "swiperClass": "swiper",
            "settings": {
                "page": {
                    "element_pack_global_tooltip_width": {
                        "unit": "px",
                        "size": "",
                        "sizes": []
                    },
                    "element_pack_global_tooltip_width_tablet": {
                        "unit": "px",
                        "size": "",
                        "sizes": []
                    },
                    "element_pack_global_tooltip_width_mobile": {
                        "unit": "px",
                        "size": "",
                        "sizes": []
                    },
                    "element_pack_global_tooltip_padding": {
                        "unit": "px",
                        "top": "",
                        "right": "",
                        "bottom": "",
                        "left": "",
                        "isLinked": true
                    },
                    "element_pack_global_tooltip_padding_tablet": {
                        "unit": "px",
                        "top": "",
                        "right": "",
                        "bottom": "",
                        "left": "",
                        "isLinked": true
                    },
                    "element_pack_global_tooltip_padding_mobile": {
                        "unit": "px",
                        "top": "",
                        "right": "",
                        "bottom": "",
                        "left": "",
                        "isLinked": true
                    },
                    "element_pack_global_tooltip_border_radius": {
                        "unit": "px",
                        "top": "",
                        "right": "",
                        "bottom": "",
                        "left": "",
                        "isLinked": true
                    },
                    "element_pack_global_tooltip_border_radius_tablet": {
                        "unit": "px",
                        "top": "",
                        "right": "",
                        "bottom": "",
                        "left": "",
                        "isLinked": true
                    },
                    "element_pack_global_tooltip_border_radius_mobile": {
                        "unit": "px",
                        "top": "",
                        "right": "",
                        "bottom": "",
                        "left": "",
                        "isLinked": true
                    }
                },
                "editorPreferences": []
            },
            "kit": {
                "active_breakpoints": ["viewport_mobile", "viewport_tablet"],
                "global_image_lightbox": "yes",
                "lightbox_enable_counter": "yes",
                "lightbox_enable_fullscreen": "yes",
                "lightbox_enable_zoom": "yes",
                "lightbox_enable_share": "yes",
                "lightbox_title_src": "title",
                "lightbox_description_src": "description",
                "woocommerce_notices_elements": [],
                "hello_header_logo_type": "title",
                "hello_footer_logo_type": "logo"
            },
            "post": {
                "id": 120,
                "title": "T-Shirt%20Landing",
                "excerpt": "",
                "featuredImage": false
            }
        };
    </script>
    <script src="{{ asset('assets/demo.orioit.com/wp-content/plugins/elementor/assets/js/frontend.min.js?ver=3.25.9') }}"
        id="elementor-frontend-js"></script>

</body>

</html>
