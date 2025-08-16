<!DOCTYPE html>
<html lang="en" dir="ltr">

@include('layouts.yellow.head')

<body class="header-fixed has-mobile-bottom-navbar" style="margin: 0; padding: 0;">
    @include('googletagmanager::body')
    <x-metapixel-body />

    <div id="page" class="site-wrapper">
        @include('layouts.yellow.header')
        <div id="main-content" class="site-content">
            <div class="-container">
                <div class="row">
                    <div id="primary" class="content-area col-md-12">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.yellow.footer')
    </div>

    <!-- Floating Cart Counter -->
    <div id="floating-cart-counter" class="floating-cart-counter">
        <div class="cart-counter-icon">
            <i class="fa fa-shopping-cart"></i>
            <span class="cart-count-badge" id="floating-cart-count">{{ count(session('cart', [])) }}</span>
        </div>
        <div class="cart-counter-tooltip">
            <span class="tooltip-text">Cart Items</span>
        </div>
    </div>

    <div class="kapee-mask-overaly"></div>
    <div class="minicart-slide-wrapper">
        <div class="minicart-header">
            <h3 class="minicart-title">My Shopping cart</h3> <a href="#" class="close-sidebar">Close</a>
        </div>
        <div class="woocommerce widget_shopping_cart">
            <div class="widget_shopping_cart_content">

                <div class="woocommerce-mini-cart-empty">
                    <i class="cart-empty-icon"></i>
                    <p class="woocommerce-mini-cart__empty-message">No products in the cart.</p>
                    <p class="woocommerce-empty-mini-cart__buttons">
                        <a class="button" href="https://maroonedbd.com/shop-2/">Shopping Now</a>
                    </p>
                </div>


            </div>
        </div>
    </div>

    <!-- JavaScript for Floating Cart Counter -->
    <script>
                document.addEventListener('DOMContentLoaded', function() {
            const cartCounter = document.getElementById('floating-cart-counter');
            const cartCountBadge = document.getElementById('floating-cart-count');
            const mobileCartElement = document.querySelector('.mobile-element-cart');
            const mobileCartCount = document.querySelector('.mobile-cart-count');

            if (!cartCounter || !cartCountBadge) return;

            // Function to update cart count
            function updateCartCount() {
                fetch('/api/cart-count')
                    .then(response => response.json())
                    .then(data => {
                        const count = data.count || 0;
                        cartCountBadge.textContent = count;

                                                // Add/remove empty class for floating counter
                        if (count === 0) {
                            cartCounter.classList.add('cart-empty');
                        } else {
                            cartCounter.classList.remove('cart-empty');
                        }

                        // Update mobile cart count
                        if (mobileCartCount) {
                            mobileCartCount.textContent = count;
                        }

                        // Add/remove empty class for mobile counter
                        if (mobileCartElement) {
                            if (count === 0) {
                                mobileCartElement.classList.add('cart-empty');
                            } else {
                                mobileCartElement.classList.remove('cart-empty');
                            }
                        }

                        // Add animation class to both counters
                        cartCountBadge.classList.add('cart-count-updated');
                        if (mobileCartCount) {
                            mobileCartCount.classList.add('cart-count-updated');
                        }

                        setTimeout(() => {
                            cartCountBadge.classList.remove('cart-count-updated');
                            if (mobileCartCount) {
                                mobileCartCount.classList.remove('cart-count-updated');
                            }
                        }, 300);
                    })
                    .catch(error => {
                        console.log('Error updating cart count:', error);
                    });
            }

            // Listen for Livewire cart update events
            window.addEventListener('cartUpdated', function() {
                updateCartCount();
            });

            // Listen for cart box updates
            window.addEventListener('cartBoxUpdated', function() {
                updateCartCount();
            });

            // Listen for custom cart update events
            document.addEventListener('cartUpdated', function() {
                updateCartCount();
            });

            // Update cart count every 10 seconds as fallback
            setInterval(updateCartCount, 10000);

            // Make cart counter clickable
            cartCounter.addEventListener('click', function() {
                // Redirect to checkout page
                window.location.href = '/checkout';
            });

            // Initial cart count update
            updateCartCount();
        });
    </script>

    <div id="mobile-menu-wrapper" class="mobile-menu-wrapper">
        <div class="mobile-menu-header"> <a class="customer-signinup" href="/">Login
                &amp; Signup</a></div>
        <div class="mobile-nav-tabs">
            <ul>
                <li class="primary active" data-menu="primary"><span>Menu</span></li>
                <li class="categories" data-menu="categories"><span>Categories</span></li>
            </ul>
        </div>
        <div class="mobile-primary-menu mobile-nav-content active">
            <ul id="menu-mobile-menu-2" class="mobile-main-menu">
                @include('layouts.yellow.header-menu-items')
            </ul>
        </div>
        <div class="mobile-categories-menu mobile-nav-content">
            <ul id="menu-mobile-menu-3" class="mobile-main-menu">
                @include('layouts.yellow.header-menu-items')
            </ul>
        </div>
        <div class="mobile-topbar">
            <div class="kapee-social icons-default icons-shape-circle icons-size-default">
                @foreach($social ?? [] as $item => $data)
                @if(($link = $data->link ?? false) && $link != '#')
                <a href="{{ url($link ?? '#') }}" target="_blank" class="social-{{$item}}">
                    @switch($item)
                        @case('facebook')
                        <i class="fa fab fa-facebook-f"></i>
                        @break
                        @case('twitter')
                        <i class="fa fab fa-twitter"></i>
                        @break
                        @case('instagram')
                        <i class="fa fab fa-instagram"></i>
                        @break
                        @case('youtube')
                        <i class="fa fab fa-youtube"></i>
                        @break
                        @case('tiktok')
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tiktok" viewBox="0 0 16 16">
                        <path d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3z"/>
                        </svg>
                        @break
                    @endswitch
                </a>
                @endif
                @endforeach
            </div>
        </div>
    </div>
    <script type="text/javascript">
        const lazyloadRunObserver = () => {
            const dataAttribute = 'data-e-bg-lazyload';
            const lazyloadBackgrounds = document.querySelectorAll(`[${ dataAttribute }]:not(.lazyloaded)`);
            const lazyloadBackgroundObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        let lazyloadBackground = entry.target;
                        const lazyloadSelector = lazyloadBackground.getAttribute(dataAttribute);
                        if (lazyloadSelector) {
                            lazyloadBackground = entry.target.querySelector(lazyloadSelector);
                        }
                        if (lazyloadBackground) {
                            lazyloadBackground.classList.add('lazyloaded');
                        }
                        lazyloadBackgroundObserver.unobserve(entry.target);
                    }
                });
            }, {
                rootMargin: '100px 0px 100px 0px'
            });
            lazyloadBackgrounds.forEach((lazyloadBackground) => {
                lazyloadBackgroundObserver.observe(lazyloadBackground);
            });
        };
        const events = [
            'DOMContentLoaded',
            'elementor/lazyload/observe',
        ];
        events.forEach((event) => {
            document.addEventListener(event, lazyloadRunObserver);
        });
    </script>
    <script type="text/javascript">
        (function() {
            var c = document.body.className;
            c = c.replace(/woocommerce-no-js/, 'woocommerce-js');
            document.body.className = c;
        })();
    </script>
    <link rel="stylesheet" id="e-animations-css"
        href="/wp-content/cache/busting/1/wp-content/plugins/elementor/assets/lib/animations/animations.min-3.14.1.css"
        type="text/css" media="all">
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/yith-woocommerce-wishlist/assets/js/jquery.selectBox.min-1.2.0.js"
        id="jquery-selectBox-js"></script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/woocommerce/assets/js/prettyPhoto/jquery.prettyPhoto.min-3.1.6.js"
        id="prettyPhoto-js"></script>
    <script type="text/javascript" id="jquery-yith-wcwl-js-extra">
        /* <![CDATA[ */
        var yith_wcwl_l10n = {
            "ajax_url": "\/wp-admin\/admin-ajax.php",
            "redirect_to_cart": "no",
            "multi_wishlist": "",
            "hide_add_button": "1",
            "enable_ajax_loading": "",
            "ajax_loader_url": "https:\/\/maroonedbd.com\/wp-content\/plugins\/yith-woocommerce-wishlist\/assets\/images\/ajax-loader-alt.svg",
            "remove_from_wishlist_after_add_to_cart": "1",
            "is_wishlist_responsive": "1",
            "time_to_close_prettyphoto": "3000",
            "fragments_index_glue": ".",
            "reload_on_found_variation": "1",
            "mobile_media_query": "768",
            "labels": {
                "cookie_disabled": "We are sorry, but this feature is available only if cookies on your browser are enabled.",
                "added_to_cart_message": "<div class=\"woocommerce-notices-wrapper\"><div class=\"woocommerce-message\" role=\"alert\">Product added to cart successfully<\/div><\/div>"
            },
            "actions": {
                "add_to_wishlist_action": "add_to_wishlist",
                "remove_from_wishlist_action": "remove_from_wishlist",
                "reload_wishlist_and_adding_elem_action": "reload_wishlist_and_adding_elem",
                "load_mobile_action": "load_mobile",
                "delete_item_action": "delete_item",
                "save_title_action": "save_title",
                "save_privacy_action": "save_privacy",
                "load_fragments": "load_fragments"
            },
            "nonce": {
                "add_to_wishlist_nonce": "07623a7251",
                "remove_from_wishlist_nonce": "41c7c1da49",
                "reload_wishlist_and_adding_elem_nonce": "5fc2c7da5a",
                "load_mobile_nonce": "8381a325f4",
                "delete_item_nonce": "6ac552e67c",
                "save_title_nonce": "80d1946960",
                "save_privacy_nonce": "a78cdd450c",
                "load_fragments_nonce": "e532b56ed5"
            }
        }; /* ]]> */
    </script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/yith-woocommerce-wishlist/assets/js/jquery.yith-wcwl.min-3.8.0.js"
        id="jquery-yith-wcwl-js"></script>
    <script type="text/javascript" src="/wp-content/cache/busting/1/wp-content/plugins/bkash/js/scripts-1.0.js"
        id="stb-script-js"></script>
    <script type="text/javascript" id="chaty-front-end-js-extra">
        /* <![CDATA[ */
        var chaty_settings = {
            "ajax_url": "",
            "analytics": "0",
            "chaty_widgets": [{
                "id": 0,
                "identifier": 0,
                "settings": {
                    "cta_type": "simple-view",
                    "cta_body": "",
                    "cta_head": "",
                    "cta_head_bg_color": "",
                    "cta_head_text_color": "",
                    "show_close_button": 1,
                    "position": "right",
                    "custom_position": 1,
                    "bottom_spacing": "25",
                    "side_spacing": "25",
                    "icon_view": "vertical",
                    "default_state": "click",
                    "cta_text": "",
                    "cta_text_color": "rgb(255, 255, 255)",
                    "cta_bg_color": "rgb(68, 0, 0)",
                    "show_cta": "all_time",
                    "is_pending_mesg_enabled": "off",
                    "pending_mesg_count": "1",
                    "pending_mesg_count_color": "#ffffff",
                    "pending_mesg_count_bgcolor": "#dd0000",
                    "widget_icon": "chat-bubble",
                    "widget_icon_url": "",
                    "font_family": "Montserrat",
                    "widget_size": "45",
                    "custom_widget_size": "45",
                    "is_google_analytics_enabled": 0,
                    "close_text": "Message Now",
                    "widget_color": "#4F6ACA",
                    "widget_rgb_color": "79,106,202",
                    "has_custom_css": 0,
                    "custom_css": "",
                    "widget_token": "aebc5c4d17",
                    "widget_index": "",
                    "attention_effect": "pulse-icon"
                },
                "triggers": {
                    "has_time_delay": 1,
                    "time_delay": "0",
                    "exit_intent": 0,
                    "has_display_after_page_scroll": 0,
                    "display_after_page_scroll": "0",
                    "auto_hide_widget": 0,
                    "hide_after": 0,
                    "show_on_pages_rules": [],
                    "time_diff": 0,
                    "has_date_scheduling_rules": 0,
                    "date_scheduling_rules": {
                        "start_date_time": "",
                        "end_date_time": ""
                    },
                    "date_scheduling_rules_timezone": 0,
                    "day_hours_scheduling_rules_timezone": 0,
                    "has_day_hours_scheduling_rules": [],
                    "day_hours_scheduling_rules": [],
                    "day_time_diff": 0,
                    "show_on_direct_visit": 0,
                    "show_on_referrer_social_network": 0,
                    "show_on_referrer_search_engines": 0,
                    "show_on_referrer_google_ads": 0,
                    "show_on_referrer_urls": [],
                    "has_show_on_specific_referrer_urls": 0,
                    "has_traffic_source": 0,
                    "has_countries": 0,
                    "countries": [],
                    "has_target_rules": 0
                },
                "channels": [{
                    "channel": "Phone",
                    "value": "{{$company->phone}}",
                    "hover_text": "Call Now",
                    "svg_icon": "<svg width=\"39\" height=\"39\" viewBox=\"0 0 39 39\" fill=\"none\" xmlns=\"http:\/\/www.w3.org\/2000\/svg\"><circle class=\"color-element\" cx=\"19.4395\" cy=\"19.4395\" r=\"19.4395\" fill=\"#03E78B\"\/><path d=\"M19.3929 14.9176C17.752 14.7684 16.2602 14.3209 14.7684 13.7242C14.0226 13.4259 13.1275 13.7242 12.8292 14.4701L11.7849 16.2602C8.65222 14.6193 6.11623 11.9341 4.47529 8.95057L6.41458 7.90634C7.16046 7.60799 7.45881 6.71293 7.16046 5.96705C6.56375 4.47529 6.11623 2.83435 5.96705 1.34259C5.96705 0.596704 5.22117 0 4.47529 0H0.745882C0.298353 0 5.69062e-07 0.298352 5.69062e-07 0.745881C5.69062e-07 3.72941 0.596704 6.71293 1.93929 9.3981C3.87858 13.575 7.30964 16.8569 11.3374 18.7962C14.0226 20.1388 17.0061 20.7355 19.9896 20.7355C20.4371 20.7355 20.7355 20.4371 20.7355 19.9896V16.4094C20.7355 15.5143 20.1388 14.9176 19.3929 14.9176Z\" transform=\"translate(9.07179 9.07178)\" fill=\"white\"\/><\/svg>",
                    "is_desktop": 1,
                    "is_mobile": 1,
                    "icon_color": "#03E78B",
                    "icon_rgb_color": "3,231,139",
                    "channel_type": "Phone",
                    "custom_image_url": "",
                    "order": "",
                    "pre_set_message": "",
                    "is_use_web_version": "1",
                    "is_open_new_tab": "1",
                    "is_default_open": "0",
                    "has_welcome_message": "0",
                    "chat_welcome_message": "",
                    "qr_code_image_url": "",
                    "mail_subject": "",
                    "channel_account_type": "personal",
                    "contact_form_settings": [],
                    "contact_fields": [],
                    "url": "tel:{{$company->phone}}",
                    "mobile_target": "",
                    "desktop_target": "",
                    "target": "",
                    "is_agent": 0,
                    "agent_data": [],
                    "header_text": "",
                    "header_sub_text": "",
                    "header_bg_color": "",
                    "header_text_color": "",
                    "widget_token": "aebc5c4d17",
                    "widget_index": "",
                    "click_event": ""
                }, {
                    "channel": "Facebook_Messenger",
                    "value": "{{$company->messenger}}",
                    "hover_text": "Facebook Messenger",
                    "svg_icon": "<svg width=\"39\" height=\"39\" viewBox=\"0 0 39 39\" fill=\"none\" xmlns=\"http:\/\/www.w3.org/2000/svg\"><circle class=\"color-element\" cx=\"19.4395\" cy=\"19.4395\" r=\"19.4395\" fill=\"#1E88E5\"\/><path fill-rule=\"evenodd\" clip-rule=\"evenodd\" d=\"M0 9.63934C0 4.29861 4.68939 0 10.4209 0C16.1524 0 20.8418 4.29861 20.8418 9.63934C20.8418 14.98 16.1524 19.2787 10.4209 19.2787C9.37878 19.2787 8.33673 19.1484 7.42487 18.8879L3.90784 20.8418V17.1945C1.56311 15.3708 0 12.6353 0 9.63934ZM8.85779 10.1604L11.463 13.0261L17.1945 6.90384L12.1143 9.76959L9.37885 6.90384L3.64734 13.0261L8.85779 10.1604Z\" transform=\"translate(9.01854 10.3146)\" fill=\"white\"\/><\/svg>",
                    "is_desktop": 1,
                    "is_mobile": 1,
                    "icon_color": "#1E88E5",
                    "icon_rgb_color": "30,136,229",
                    "channel_type": "Facebook_Messenger",
                    "custom_image_url": "",
                    "order": "",
                    "pre_set_message": "",
                    "is_use_web_version": "1",
                    "is_open_new_tab": "1",
                    "is_default_open": "0",
                    "has_welcome_message": "0",
                    "chat_welcome_message": "",
                    "qr_code_image_url": "",
                    "mail_subject": "",
                    "channel_account_type": "personal",
                    "contact_form_settings": [],
                    "contact_fields": [],
                    "url": "{{$company->messenger}}",
                    "mobile_target": "",
                    "desktop_target": "",
                    "target": "",
                    "is_agent": 0,
                    "agent_data": [],
                    "header_text": "",
                    "header_sub_text": "",
                    "header_bg_color": "",
                    "header_text_color": "",
                    "widget_token": "aebc5c4d17",
                    "widget_index": "",
                    "click_event": ""
                }, {
                    "channel": "Whatsapp",
                    "value": "{{$company->whatsapp}}",
                    "hover_text": "WhatsApp",
                    "svg_icon": "<svg width=\"39\" height=\"39\" viewBox=\"0 0 39 39\" fill=\"none\" xmlns=\"http:\/\/www.w3.org/2000/svg\"><circle class=\"color-element\" cx=\"19.4395\" cy=\"19.4395\" r=\"19.4395\" fill=\"#49E670\"\/><path d=\"M12.9821 10.1115C12.7029 10.7767 11.5862 11.442 10.7486 11.575C10.1902 11.7081 9.35269 11.8411 6.84003 10.7767C3.48981 9.44628 1.39593 6.25317 1.25634 6.12012C1.11674 5.85403 2.13001e-06 4.39053 2.13001e-06 2.92702C2.13001e-06 1.46351 0.83755 0.665231 1.11673 0.399139C1.39592 0.133046 1.8147 1.01506e-06 2.23348 1.01506e-06C2.37307 1.01506e-06 2.51267 1.01506e-06 2.65226 1.01506e-06C2.93144 1.01506e-06 3.21063 -2.02219e-06 3.35022 0.532183C3.62941 1.19741 4.32736 2.66092 4.32736 2.79397C4.46696 2.92702 4.46696 3.19311 4.32736 3.32616C4.18777 3.59225 4.18777 3.59224 3.90858 3.85834C3.76899 3.99138 3.6294 4.12443 3.48981 4.39052C3.35022 4.52357 3.21063 4.78966 3.35022 5.05576C3.48981 5.32185 4.18777 6.38622 5.16491 7.18449C6.42125 8.24886 7.39839 8.51496 7.81717 8.78105C8.09636 8.91409 8.37554 8.9141 8.65472 8.648C8.93391 8.38191 9.21309 7.98277 9.49228 7.58363C9.77146 7.31754 10.0507 7.1845 10.3298 7.31754C10.609 7.45059 12.2841 8.11582 12.5633 8.38191C12.8425 8.51496 13.1217 8.648 13.1217 8.78105C13.1217 8.78105 13.1217 9.44628 12.9821 10.1115Z\" transform=\"translate(12.9597 12.9597)\" fill=\"#FAFAFA\"\/><path d=\"M0.196998 23.295L0.131434 23.4862L0.323216 23.4223L5.52771 21.6875C7.4273 22.8471 9.47325 23.4274 11.6637 23.4274C18.134 23.4274 23.4274 18.134 23.4274 11.6637C23.4274 5.19344 18.134 -0.1 11.6637 -0.1C5.19344 -0.1 -0.1 5.19344 -0.1 11.6637C-0.1 13.9996 0.624492 16.3352 1.93021 18.2398L0.196998 23.295ZM5.87658 19.8847L5.84025 19.8665L5.80154 19.8788L2.78138 20.8398L3.73978 17.9646L3.75932 17.906L3.71562 17.8623L3.43104 17.5777C2.27704 15.8437 1.55796 13.8245 1.55796 11.6637C1.55796 6.03288 6.03288 1.55796 11.6637 1.55796C17.2945 1.55796 21.7695 6.03288 21.7695 11.6637C21.7695 17.2945 17.2945 21.7695 11.6637 21.7695C9.64222 21.7695 7.76778 21.1921 6.18227 20.039L6.17557 20.0342L6.16817 20.0305L5.87658 19.8847Z\" transform=\"translate(7.7758 7.77582)\" fill=\"white\" stroke=\"white\" stroke-width=\"0.2\"\/><\/svg>",
                    "is_desktop": 1,
                    "is_mobile": 1,
                    "icon_color": "#49E670",
                    "icon_rgb_color": "73,230,112",
                    "channel_type": "Whatsapp",
                    "custom_image_url": "",
                    "order": "",
                    "pre_set_message": "",
                    "is_use_web_version": "0",
                    "is_open_new_tab": "1",
                    "is_default_open": "0",
                    "has_welcome_message": "1",
                    "chat_welcome_message": "<p>Welcome to Marooned!<br \/>How can I help you? :)<\/p>",
                    "qr_code_image_url": "",
                    "mail_subject": "",
                    "channel_account_type": "personal",
                    "contact_form_settings": [],
                    "contact_fields": [],
                    "url": "https:\/\/wa.me\/{{$company->whatsapp}}",
                    "mobile_target": "",
                    "desktop_target": "",
                    "target": "",
                    "is_agent": 0,
                    "agent_data": [],
                    "header_text": "",
                    "header_sub_text": "",
                    "header_bg_color": "",
                    "header_text_color": "",
                    "widget_token": "aebc5c4d17",
                    "widget_index": "",
                    "click_event": ""
                }]
            }],
            "data_analytics_settings": "off"
        }; /* ]]> */
    </script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/chaty/js/cht-front-script.min-3.0.71684126333.js"
        id="chaty-front-end-js"></script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/woocommerce/assets/js/js-cookie/js.cookie.min-2.1.4-wc.6.4.0.js"
        id="js-cookie-js"></script>
    <script type="text/javascript" id="woocommerce-js-extra">
        /* <![CDATA[ */
        var woocommerce_params = {
            "ajax_url": "\/wp-admin\/admin-ajax.php",
            "wc_ajax_url": "\/?wc-ajax=%%endpoint%%"
        }; /* ]]> */
    </script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/woocommerce/assets/js/frontend/woocommerce.min-6.4.0.js"
        id="woocommerce-js"></script>
    <script type="text/javascript" id="wc-cart-fragments-js-extra">
        /* <![CDATA[ */
        var wc_cart_fragments_params = {
            "ajax_url": "\/wp-admin\/admin-ajax.php",
            "wc_ajax_url": "\/?wc-ajax=%%endpoint%%",
            "cart_hash_key": "wc_cart_hash_cd9f1782569a223df9f4cdfae936816d",
            "fragment_name": "wc_fragments_cd9f1782569a223df9f4cdfae936816d",
            "request_timeout": "5000"
        }; /* ]]> */
    </script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/woocommerce/assets/js/frontend/cart-fragments.min-6.4.0.js"
        id="wc-cart-fragments-js"></script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/wp-smush-pro/app/assets/js/smush-lazy-load-native.min-3.8.6.js"
        id="smush-lazy-load-js"></script>
    <script type="text/javascript" id="yith-woocompare-main-js-extra">
        /* <![CDATA[ */
        var yith_woocompare = {
            "ajaxurl": "\/?wc-ajax=%%endpoint%%",
            "actionadd": "yith-woocompare-add-product",
            "actionremove": "yith-woocompare-remove-product",
            "actionview": "yith-woocompare-view-table",
            "actionreload": "yith-woocompare-reload-product",
            "added_label": "Added",
            "table_title": "Product Comparison",
            "auto_open": "yes",
            "loader": "https:\/\/maroonedbd.com\/wp-content\/plugins\/yith-woocommerce-compare\/assets\/images\/loader.gif",
            "button_text": "Compare",
            "cookie_name": "yith_woocompare_list",
            "close_label": "Close"
        }; /* ]]> */
    </script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/yith-woocommerce-compare/assets/js/woocompare.min-2.13.0.js"
        id="yith-woocompare-main-js"></script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/yith-woocommerce-compare/assets/js/jquery.colorbox-min-1.4.21.js"
        id="jquery-colorbox-js"></script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/js/waypoints.min-2.0.2.js" id="waypoints-js">
    </script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/js_composer/assets/js/dist/js_composer_front.min-6.4.2.js"
        id="wpb_composer_front_js-js"></script>
    <script type="text/javascript" src="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/js/popper.min-4.0.0.js"
        id="popper-js"></script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/js/bootstrap.min-4.0.0.js" id="bootstrap-js">
    </script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/js/owl.carousel.min-2.3.3.js" id="owl-carousel-js">
    </script>
    <script type="text/javascript" src="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/js/slick.min-1.8.0.js"
        id="slick-js"></script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/js_composer/assets/lib/bower/isotope/dist/isotope.pkgd.min-6.4.2.js"
        id="isotope-js"></script>
    <script type="text/javascript" src="/wp-content/themes/kapee/assets/js/cookie.min.js" id="cookie-js"></script>
    <script type="text/javascript" src="/wp-content/themes/kapee/assets/js/jquery.magnific-popup.min.js"
        id="magnific-popup-js"></script>
    <script type="text/javascript" src="/wp-content/themes/kapee/assets/js/jquery.autocomplete.min.js" id="autocomplete-js">
    </script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/js/jquery.lazyload.min-1.2.4.js" id="lazyload-js">
    </script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/js/jquery.plugin.min-1.2.0.js" id="jqplugin-js">
    </script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/js/jquery.countdown.min-1.2.0.js" id="countdown-js">
    </script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/js/jquery.counterup.min-1.2.0.js" id="counterup-js">
    </script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/js/sticky-kit.min-1.0.0.js" id="sticky-kit-js">
    </script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/js/hideMaxListItem-min-1.3.6.js"
        id="hideMaxListItem-js"></script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/js/jquery.nanoscroller.min-0.8.7.js"
        id="nanoscroller-js"></script>
    <script type="text/javascript" src="/wp-content/cache/busting/1/wp-includes/js/underscore.min-1.13.4.js"
        id="underscore-js"></script>
    <script type="text/javascript" id="wp-util-js-extra">
        /* <![CDATA[ */
        var _wpUtilSettings = {
            "ajax": {
                "url": "\/wp-admin\/admin-ajax.php"
            }
        }; /* ]]> */
    </script>
    <script type="text/javascript" src="/wp-includes/js/wp-util.min.js" id="wp-util-js"></script>
    <script type="text/javascript" id="wc-add-to-cart-variation-js-extra">
        /* <![CDATA[ */
        var wc_add_to_cart_variation_params = {
            "wc_ajax_url": "\/?wc-ajax=%%endpoint%%",
            "i18n_no_matching_variations_text": "Sorry, no products matched your selection. Please choose a different combination.",
            "i18n_make_a_selection_text": "Please select some product options before adding this product to your cart.",
            "i18n_unavailable_text": "Sorry, this product is unavailable. Please choose a different combination."
        }; /* ]]> */
    </script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/woocommerce/assets/js/frontend/add-to-cart-variation.min-6.4.0.js"
        id="wc-add-to-cart-variation-js"></script>
    <script type="text/javascript" id="kapee-script-js-extra">
        /* <![CDATA[ */
        var kapee_options = {
            "rtl": "",
            "ajax_url": "/api/search-product",
            "0": "",
            "nonce": "2a8e95d9fb",
            "sticky_header": "1",
            "sticky_header_tablet": "1",
            "sticky_header_mobile": "1",
            "login_register_popup": "",
            "header_minicart_popup": "",
            "lazy_load": "",
            "cookie_path": "\/",
            "cookie_expire": "2592000",
            "permalink": "",
            "newsletter_args": {
                "popup_enable": false,
                "popup_display_on": "page_load",
                "popup_delay": "5",
                "popup_x_scroll": "30",
                "show_for_mobile": "1"
            },
            "js_translate_text": {
                "days_text": "Days",
                "hours_text": "Hrs",
                "mins_text": "Mins",
                "secs_text": "Secs",
                "sdays_text": "d",
                "shours_text": "h",
                "smins_text": "m",
                "ssecs_text": "s",
                "show_more": "+ Show more",
                "show_less": "- Show less",
                "loading_txt": "Loading..."
            },
            "product_tooltip": "1",
            "product_image_zoom": "1",
            "product_add_to_cart_ajax": "1",
            "product_open_cart_mini": "1",
            "product_quickview_button": "1",
            "sticky_image_wrapper": "1",
            "sticky_summary_wrapper": "1",
            "sticky_sidebar": "1",
            "widget_toggle": "1",
            "widget_menu_toggle": "1",
            "widget_hide_max_limit_item": "1",
            "sidebar_canvas_mobile": "1",
            "number_of_show_widget_items": "8",
            "bought_together_success": "Added all items to cart",
            "bought_together_error": "Someting wrong",
            "maintenance_mode": "",
            "dokan_active": "",
            "price_format": "%1$s%2$s",
            "price_decimals": "2",
            "price_thousand_separator": ",",
            "price_decimal_separator": ".",
            "currency_symbol": "\u09f3\u00a0",
            "wc_tax_enabled": "",
            "cart_url": "https:\/\/maroonedbd.com\/?page_id=18",
            "ex_tax_or_vat": ""
        };
        var kapeeOwlParam = null; /* ]]> */
    </script>
    <script type="text/javascript" src="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/js/functions-1.1.0.js"
        id="kapee-script-js"></script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/elementor/assets/js/webpack.runtime.min-3.14.1.js"
        id="elementor-webpack-runtime-js"></script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/elementor/assets/js/frontend-modules.min-3.14.1.js"
        id="elementor-frontend-modules-js"></script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/elementor/assets/lib/waypoints/waypoints.min-4.0.2.js"
        id="elementor-waypoints-js"></script>
    <script type="text/javascript" src="/wp-content/cache/busting/1/wp-includes/js/jquery/ui/core.min-1.13.2.js"
        id="jquery-ui-core-js"></script>
    <script type="text/javascript" id="elementor-frontend-js-before">
        /* <![CDATA[ */
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
                }
            },
            "version": "3.14.1",
            "is_static": false,
            "experimentalFeatures": {
                "e_dom_optimization": true,
                "e_optimized_assets_loading": true,
                "e_optimized_css_loading": true,
                "e_font_icon_svg": true,
                "a11y_improvements": true,
                "additional_custom_breakpoints": true,
                "e_swiper_latest": true,
                "editor_v2": true,
                "landing-pages": true,
                "nested-elements": true,
                "e_lazyload": true,
                "e_global_styleguide": true
            },
            "urls": {
                "assets": "https:\/\/maroonedbd.com\/wp-content\/plugins\/elementor\/assets\/"
            },
            "swiperClass": "swiper",
            "settings": {
                "page": [],
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
                "lightbox_description_src": "description"
            },
            "post": {
                "id": 1210,
                "title": "Marooned%20%E2%80%93%20Clothing%20Brand",
                "excerpt": "",
                "featuredImage": false
            }
        }; /* ]]> */
    </script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/elementor/assets/js/frontend.min-3.14.1.js"
        id="elementor-frontend-js"></script><span id="elementor-device-mode" class="elementor-screen-only"></span>
    <div style="display: none" class="chaty chaty-id-0 chaty-widget-0 chaty-key-0 all_time active"
        id="chaty-widget-0" data-key="0" data-id="0" data-identifier="0" data-nonce="aebc5c4d17"
        data-animation="pulse-icon">
        <div class="chaty-widget right-position">
            <div class="chaty-channels">
                <div class="chaty-channel-list">
                    <div class="chaty-channel Phone-channel" id="Phone-0-channel" data-id="Phone-0" data-widget="0"
                        data-channel="Phone"><a href="tel:{{$company->phone}}" target=""
                            class="chaty-tooltip pos-left" data-form="chaty-form-0-Phone" data-hover="Call Now"><span
                                class="chaty-icon channel-icon-Phone"><span class="chaty-svg"><svg width="39"
                                        height="39" viewBox="0 0 39 39" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <circle class="color-element" cx="19.4395" cy="19.4395" r="19.4395"
                                            fill="#03E78B"></circle>
                                        <path
                                            d="M19.3929 14.9176C17.752 14.7684 16.2602 14.3209 14.7684 13.7242C14.0226 13.4259 13.1275 13.7242 12.8292 14.4701L11.7849 16.2602C8.65222 14.6193 6.11623 11.9341 4.47529 8.95057L6.41458 7.90634C7.16046 7.60799 7.45881 6.71293 7.16046 5.96705C6.56375 4.47529 6.11623 2.83435 5.96705 1.34259C5.96705 0.596704 5.22117 0 4.47529 0H0.745882C0.298353 0 5.69062e-07 0.298352 5.69062e-07 0.745881C5.69062e-07 3.72941 0.596704 6.71293 1.93929 9.3981C3.87858 13.575 7.30964 16.8569 11.3374 18.7962C14.0226 20.1388 17.0061 20.7355 19.9896 20.7355C20.4371 20.7355 20.7355 20.4371 20.7355 19.9896V16.4094C20.7355 15.5143 20.1388 14.9176 19.3929 14.9176Z"
                                            transform="translate(9.07179 9.07178)" fill="white"></path>
                                    </svg></span></span></a></div>
                    <div class="chaty-channel Facebook_Messenger-channel" id="Facebook_Messenger-0-channel"
                        data-id="Facebook_Messenger-0" data-widget="0" data-channel="Facebook_Messenger"><a
                            href="{{$company->messenger}}" target="" class="chaty-tooltip pos-left"
                            data-form="chaty-form-0-Facebook_Messenger" data-hover="Facebook Messenger"><span
                                class="chaty-icon channel-icon-Facebook_Messenger"><span class="chaty-svg"><svg
                                        width="39" height="39" viewBox="0 0 39 39" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <circle class="color-element" cx="19.4395" cy="19.4395" r="19.4395"
                                            fill="#1E88E5"></circle>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M0 9.63934C0 4.29861 4.68939 0 10.4209 0C16.1524 0 20.8418 4.29861 20.8418 9.63934C20.8418 14.98 16.1524 19.2787 10.4209 19.2787C9.37878 19.2787 8.33673 19.1484 7.42487 18.8879L3.90784 20.8418V17.1945C1.56311 15.3708 0 12.6353 0 9.63934ZM8.85779 10.1604L11.463 13.0261L17.1945 6.90384L12.1143 9.76959L9.37885 6.90384L3.64734 13.0261L8.85779 10.1604Z"
                                            transform="translate(9.01854 10.3146)" fill="white"></path>
                                    </svg></span></span></a></div>
                    <div class="chaty-channel Whatsapp-channel" id="Whatsapp-0-channel" data-id="Whatsapp-0"
                        data-widget="0" data-channel="Whatsapp"><a href="https://wa.me/{{$company->whatsapp}}" target=""
                            class="chaty-tooltip pos-left has-chaty-box chaty-whatsapp-form"
                            data-form="chaty-form-0-Whatsapp" data-hover="WhatsApp"><span
                                class="chaty-icon channel-icon-Whatsapp"><span class="chaty-svg"><svg width="39"
                                        height="39" viewBox="0 0 39 39" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <circle class="color-element" cx="19.4395" cy="19.4395" r="19.4395"
                                            fill="#49E670"></circle>
                                        <path
                                            d="M12.9821 10.1115C12.7029 10.7767 11.5862 11.442 10.7486 11.575C10.1902 11.7081 9.35269 11.8411 6.84003 10.7767C3.48981 9.44628 1.39593 6.25317 1.25634 6.12012C1.11674 5.85403 2.13001e-06 4.39053 2.13001e-06 2.92702C2.13001e-06 1.46351 0.83755 0.665231 1.11673 0.399139C1.39592 0.133046 1.8147 1.01506e-06 2.23348 1.01506e-06C2.37307 1.01506e-06 2.51267 1.01506e-06 2.65226 1.01506e-06C2.93144 1.01506e-06 3.21063 -2.02219e-06 3.35022 0.532183C3.62941 1.19741 4.32736 2.66092 4.32736 2.79397C4.46696 2.92702 4.46696 3.19311 4.32736 3.32616C4.18777 3.59225 4.18777 3.59224 3.90858 3.85834C3.76899 3.99138 3.6294 4.12443 3.48981 4.39052C3.35022 4.52357 3.21063 4.78966 3.35022 5.05576C3.48981 5.32185 4.18777 6.38622 5.16491 7.18449C6.42125 8.24886 7.39839 8.51496 7.81717 8.78105C8.09636 8.91409 8.37554 8.9141 8.65472 8.648C8.93391 8.38191 9.21309 7.98277 9.49228 7.58363C9.77146 7.31754 10.0507 7.1845 10.3298 7.31754C10.609 7.45059 12.2841 8.11582 12.5633 8.38191C12.8425 8.51496 13.1217 8.648 13.1217 8.78105C13.1217 8.78105 13.1217 9.44628 12.9821 10.1115Z"
                                            transform="translate(12.9597 12.9597)" fill="#FAFAFA"></path>
                                        <path
                                            d="M0.196998 23.295L0.131434 23.4862L0.323216 23.4223L5.52771 21.6875C7.4273 22.8471 9.47325 23.4274 11.6637 23.4274C18.134 23.4274 23.4274 18.134 23.4274 11.6637C23.4274 5.19344 18.134 -0.1 11.6637 -0.1C5.19344 -0.1 -0.1 5.19344 -0.1 11.6637C-0.1 13.9996 0.624492 16.3352 1.93021 18.2398L0.196998 23.295ZM5.87658 19.8847L5.84025 19.8665L5.80154 19.8788L2.78138 20.8398L3.73978 17.9646L3.75932 17.906L3.71562 17.8623L3.43104 17.5777C2.27704 15.8437 1.55796 13.8245 1.55796 11.6637C1.55796 6.03288 6.03288 1.55796 11.6637 1.55796C17.2945 1.55796 21.7695 6.03288 21.7695 11.6637C21.7695 17.2945 17.2945 21.7695 11.6637 21.7695C9.64222 21.7695 7.76778 21.1921 6.18227 20.039L6.17557 20.0342L6.16817 20.0305L5.87658 19.8847Z"
                                            transform="translate(7.7758 7.77582)" fill="white" stroke="white"
                                            stroke-width="0.2"></path>
                                    </svg></span></span></a></div>
                </div>
                <div class="chaty-i-trigger">
                    <div class="chaty-channel chaty-cta-main chaty-tooltip has-on-hover pos-left active"
                        data-widget="0">
                        <span class="on-hover-text"></span>
                        <div class="chaty-cta-button chaty-animation-pulse-icon"><button type="button"
                                class="open-chaty"><span class="chaty-svg"><svg version="1.1"
                                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        x="0px" y="0px" viewBox="-496.9 507.1 54 54"
                                        style="enable-background-color:new -496.9 507.1 54 54;" xml:space="preserve">
                                        <style type="text/css">
                                            .chaty-sts1 {
                                                fill: #FFFFFF;
                                            }
                                        </style>
                                        <g>
                                            <circle cx="-469.9" cy="534.1" r="27" fill="#4F6ACA"></circle>
                                        </g>
                                        <path class="chaty-sts1"
                                            d="M-472.6,522.1h5.3c3,0,6,1.2,8.1,3.4c2.1,2.1,3.4,5.1,3.4,8.1c0,6-4.6,11-10.6,11.5v4.4c0,0.4-0.2,0.7-0.5,0.9   c-0.2,0-0.2,0-0.4,0c-0.2,0-0.5-0.2-0.7-0.4l-4.6-5c-3,0-6-1.2-8.1-3.4s-3.4-5.1-3.4-8.1C-484.1,527.2-478.9,522.1-472.6,522.1z   M-462.9,535.3c1.1,0,1.8-0.7,1.8-1.8c0-1.1-0.7-1.8-1.8-1.8c-1.1,0-1.8,0.7-1.8,1.8C-464.6,534.6-463.9,535.3-462.9,535.3z   M-469.9,535.3c1.1,0,1.8-0.7,1.8-1.8c0-1.1-0.7-1.8-1.8-1.8c-1.1,0-1.8,0.7-1.8,1.8C-471.7,534.6-471,535.3-469.9,535.3z   M-477,535.3c1.1,0,1.8-0.7,1.8-1.8c0-1.1-0.7-1.8-1.8-1.8c-1.1,0-1.8,0.7-1.8,1.8C-478.8,534.6-478.1,535.3-477,535.3z">
                                        </path>
                                    </svg></span></button><button type="button" class="open-chaty-channel"></button>
                        </div>
                    </div>
                    <div class="chaty-channel chaty-cta-close chaty-tooltip pos-left" data-hover="Message Now">
                        <div class="chaty-cta-button"><button type="button"><span class="chaty-svg"><svg
                                        viewBox="0 0 52 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <ellipse cx="26" cy="26" rx="26" ry="26"
                                            fill="#4F6ACA"></ellipse>
                                        <rect width="27.1433" height="3.89857" rx="1.94928"
                                            transform="translate(18.35 15.6599) scale(0.998038 1.00196) rotate(45)"
                                            fill="white"></rect>
                                        <rect width="27.1433" height="3.89857" rx="1.94928"
                                            transform="translate(37.5056 18.422) scale(0.998038 1.00196) rotate(135)"
                                            fill="white"></rect>
                                    </svg></span></button></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div style="" class="chaty-outer-forms chaty-whatsapp-form chaty-form-0 pos-right" data-channel="Whatsapp"
        id="chaty-form-0-Whatsapp" data-widget="0" data-index="0">
        <div class="chaty-whatsapp-form">
            <div class="chaty-whatsapp-body">
                <div role="button" class="close-chaty-form is-whatsapp-btn">
                    <div class="chaty-close-button"></div>
                </div>
                <div class="chaty-whatsapp-content">
                    <div class="chaty-whatsapp-message">
                        <p>Welcome to Marooned!<br>How can I help you? :)</p>
                    </div>
                </div>
            </div>
            <div class="chaty-whatsapp-footer">
                <form action="https://wa.me/8801825181840" target="_blank" class="whatsapp-chaty-form"
                    data-widget="0" data-channel="Whatsapp">
                    <div class="chaty-whatsapp-data">
                        <div class="chaty-whatsapp-field"><input name="text" type="text"
                                class="csass-whatsapp-input"></div>
                        <div class="chaty-whatsapp-button"><button type="submit"><svg
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                    height="24">
                                    <path fill="#ffffff"
                                        d="M1.101 21.757L23.8 12.028 1.101 2.3l.011 7.912 13.623 1.816-13.623 1.817-.011 7.912z">
                                    </path>
                                </svg></button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="cboxOverlay" style="display: none;"></div>
    <div id="colorbox" class="" role="dialog" tabindex="-1" style="display: none;">
        <div id="cboxWrapper">
            <div>
                <div id="cboxTopLeft" style="float: left;"></div>
                <div id="cboxTopCenter" style="float: left;"></div>
                <div id="cboxTopRight" style="float: left;"></div>
            </div>
            <div style="clear: left;">
                <div id="cboxMiddleLeft" style="float: left;"></div>
                <div id="cboxContent" style="float: left;">
                    <div id="cboxTitle" style="float: left;"></div>
                    <div id="cboxCurrent" style="float: left;"></div><button type="button"
                        id="cboxPrevious"></button><button type="button" id="cboxNext"></button><button
                        id="cboxSlideshow"></button>
                    <div id="cboxLoadingOverlay" style="float: left;"></div>
                    <div id="cboxLoadingGraphic" style="float: left;"></div>
                </div>
                <div id="cboxMiddleRight" style="float: left;"></div>
            </div>
            <div style="clear: left;">
                <div id="cboxBottomLeft" style="float: left;"></div>
                <div id="cboxBottomCenter" style="float: left;"></div>
                <div id="cboxBottomRight" style="float: left;"></div>
            </div>
        </div>
        <div style="position: absolute; width: 9999px; visibility: hidden; display: none; max-width: none;"></div>
    </div><iframe src="chrome-extension://dplkeopkcdalfhbmfpcenfinodanncej/dialog.html"
        style="position: fixed; top: 50%; left: 50%; width: 780px; height: 600px; margin-left: -390px; margin-top: -300px; display: none; background-color: transparent; z-index: 2147483647; border: 0px; opacity: 0; transition: opacity 0.3s;"></iframe>
    @livewireScripts
    @stack('scripts')
    </body>

</html>
