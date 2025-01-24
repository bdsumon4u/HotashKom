@props(['page'])




<!DOCTYPE html>
<html lang="en-US" class="no-js">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <title>{{ $page->title }}</title>
    <link rel='stylesheet' id='woocommerce-general-css'
        href='https://demo.orioit.com/wp-content/plugins/woocommerce/assets/css/woocommerce.css?ver=9.4.2'
        media='all' />
    
    <link rel='stylesheet' id='elementor-frontend-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/frontend.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='elementor-post-7-css'
        href='https://demo.orioit.com/wp-content/uploads/elementor/css/post-7.css?ver=1736836498' media='all' />
    
    <link rel='stylesheet' id='swiper-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/lib/swiper/v8/css/swiper.min.css?ver=8.4.5'
        media='all' />
    <link rel='stylesheet' id='e-swiper-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/conditionals/e-swiper.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='widget-image-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/widget-image.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='widget-heading-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/widget-heading.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='e-animation-pop-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/lib/animations/styles/e-animation-pop.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='widget-animated-headline-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor-pro/assets/css/widget-animated-headline.min.css?ver=3.25.2'
        media='all' />
    <link rel='stylesheet' id='e-animation-push-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/lib/animations/styles/e-animation-push.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='widget-icon-list-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/widget-icon-list.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='widget-image-carousel-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/widget-image-carousel.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='elementor-post-618-css'
        href='https://demo.orioit.com/wp-content/uploads/elementor/css/post-618.css?ver=1737204330' media='all' />
    <link rel='stylesheet' id='wcf-checkout-template-css'
        href='https://demo.orioit.com/wp-content/plugins/cartflows/assets/css/checkout-template.css?ver=2.0.12'
        media='all' />
    
    <link rel='stylesheet' id='wcf-pro-checkout-css'
        href='https://demo.orioit.com/wp-content/plugins/cartflows-pro/assets/css/checkout-styles.css?ver=2.0.10'
        media='all' />
    <link rel='stylesheet' id='google-fonts-1-css'
        href='https://fonts.googleapis.com/css?family=Roboto%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CRoboto+Slab%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CPoppins%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CHind+Siliguri%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CAnek+Bangla%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&#038;display=swap&#038;ver=6.7.1'
        media='all' />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <script src="https://demo.orioit.com/wp-includes/js/jquery/jquery.min.js?ver=3.7.1" id="jquery-core-js"></script>
    <script src="https://demo.orioit.com/wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1" id="jquery-migrate-js">
    </script>
    <script
        src="https://demo.orioit.com/wp-content/plugins/woocommerce/assets/js/js-cookie/js.cookie.min.js?ver=2.1.4-wc.9.4.2"
        id="js-cookie-js" defer data-wp-strategy="defer"></script>
    
    <script
        src="https://demo.orioit.com/wp-content/plugins/woocommerce/assets/js/jquery-blockui/jquery.blockUI.min.js?ver=2.7.0-wc.9.4.2"
        id="jquery-blockui-js" defer data-wp-strategy="defer"></script>
    
    <script
        src="https://demo.orioit.com/wp-content/plugins/woocommerce/assets/js/jquery-cookie/jquery.cookie.min.js?ver=1.4.1-wc.9.4.2"
        id="jquery-cookie-js" data-wp-strategy="defer"></script>
    
</head>

<body
    class="cartflows_step-template cartflows_step-template-cartflows-canvas single single-cartflows_step postid-618 theme-hello-elementor woocommerce-checkout woocommerce-page woocommerce-no-js cartflows-2.0.12  cartflows-pro-2.0.10 elementor-default elementor-kit-7 elementor-page elementor-page-618 cartflows-canvas">


    <div class="cartflows-container">

        <div data-elementor-type="wp-post" data-elementor-id="618" class="elementor elementor-618"
            data-elementor-settings="{&quot;element_pack_global_tooltip_width&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;element_pack_global_tooltip_width_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;element_pack_global_tooltip_width_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;element_pack_global_tooltip_padding&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_padding_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_padding_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_border_radius&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_border_radius_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_border_radius_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true}}"
            data-elementor-post-type="cartflows_step">
            
            
            <x-filament-fabricator::page-blocks :blocks="$page->blocks" />
            
            
            <section
                class="elementor-section elementor-top-section elementor-element elementor-element-529359f elementor-section-boxed elementor-section-height-default"
                data-id="529359f" data-element_type="section">
                <div class="elementor-container elementor-column-gap-no">
                    <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-4cafbbd"
                        data-id="4cafbbd" data-element_type="column">
                        <div class="elementor-widget-wrap elementor-element-populated">
                            <div class="elementor-element elementor-element-1564efa elementor-align-justify elementor-widget__width-initial elementor-widget elementor-widget-button"
                                data-id="1564efa" data-element_type="widget" data-widget_type="button.default">
                                <div class="elementor-widget-container">
                                    <div class="elementor-button-wrapper">
                                        <a class="elementor-button elementor-button-link elementor-size-xl elementor-animation-push"
                                            href="#order">
                                            <span class="elementor-button-content-wrapper">
                                                <span class="elementor-button-text">অর্ডার করতে চাই</span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <livewire:fabricator.checkout layout="five" :product="$page->product" />
        </div>
    </div>


    
    <script
        src="https://demo.orioit.com/wp-content/plugins/woocommerce/assets/js/flexslider/jquery.flexslider.min.js?ver=2.7.2-wc.9.4.2"
        id="flexslider-js" defer data-wp-strategy="defer"></script>
    <script src="https://demo.orioit.com/wp-content/plugins/elementor-pro/assets/js/webpack-pro.runtime.min.js?ver=3.25.2"
        id="elementor-pro-webpack-runtime-js"></script>
    <script src="https://demo.orioit.com/wp-content/plugins/elementor/assets/js/webpack.runtime.min.js?ver=3.25.9"
        id="elementor-webpack-runtime-js"></script>
    <script src="https://demo.orioit.com/wp-content/plugins/elementor/assets/js/frontend-modules.min.js?ver=3.25.9"
        id="elementor-frontend-modules-js"></script>
    <script src="https://demo.orioit.com/wp-includes/js/dist/hooks.min.js?ver=4d63a3d491d11ffd8ac6" id="wp-hooks-js">
    </script>
    
    <script id="elementor-pro-frontend-js-before">
        var ElementorProFrontendConfig = {
            "ajaxurl": "https:\/\/demo.orioit.com\/wp-admin\/admin-ajax.php",
            "nonce": "4f12714a6c",
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
                    "fragments_nonce": "1687a67f84"
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
    <script src="https://demo.orioit.com/wp-content/plugins/elementor-pro/assets/js/frontend.min.js?ver=3.25.2"
        id="elementor-pro-frontend-js"></script>
    <script src="https://demo.orioit.com/wp-includes/js/jquery/ui/core.min.js?ver=1.13.3" id="jquery-ui-core-js"></script>
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
                "floatingButtonsClickTracking": "ec7430768c"
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
                "id": 618,
                "title": "Rice%20Skin%20Beauty%20Serum",
                "excerpt": "",
                "featuredImage": false
            }
        };
    </script>
    <script src="https://demo.orioit.com/wp-content/plugins/elementor/assets/js/frontend.min.js?ver=3.25.9"
        id="elementor-frontend-js"></script>
    <script src="https://demo.orioit.com/wp-content/plugins/elementor-pro/assets/js/elements-handlers.min.js?ver=3.25.2"
        id="pro-elements-handlers-js"></script>
    <script
        src="https://demo.orioit.com/wp-content/plugins/elementskit-lite/widgets/init/assets/js/animate-circle.min.js?ver=3.3.2"
        id="animate-circle-js"></script>
    
</body>

</html>



<!-- Page cached by LiteSpeed Cache 6.5.4 on 2025-01-24 07:55:49 -->
