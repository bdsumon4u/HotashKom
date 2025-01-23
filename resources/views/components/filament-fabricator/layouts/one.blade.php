@props(['page'])


<!DOCTYPE html>
<html lang="en-US" class="no-js">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <title>{{ $page->title }}</title>
    <link rel='stylesheet' id='woocommerce-layout-css'
        href='https://demo.orioit.com/wp-content/plugins/woocommerce/assets/css/woocommerce-layout.css?ver=9.4.2'
        media='all' />
    <link rel='stylesheet' id='woocommerce-smallscreen-css'
        href='https://demo.orioit.com/wp-content/plugins/woocommerce/assets/css/woocommerce-smallscreen.css?ver=9.4.2'
        media='only screen and (max-width: 768px)' />
    <link rel='stylesheet' id='woocommerce-general-css'
        href='https://demo.orioit.com/wp-content/plugins/woocommerce/assets/css/woocommerce.css?ver=9.4.2'
        media='all' />
    <style id='woocommerce-inline-inline-css'>
        .woocommerce form .form-row .required {
            visibility: visible;
        }
    </style>
    <link rel='stylesheet' id='bdpg-frontend-css'
        href='https://demo.orioit.com/wp-content/plugins/bangladeshi-payment-gateways/assets/public/css/bdpg-public.css?ver=3.0.2'
        media='all' />
    <link rel='stylesheet' id='elementor-frontend-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/frontend.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='elementor-post-7-css'
        href='https://demo.orioit.com/wp-content/uploads/elementor/css/post-7.css?ver=1736836498' media='all' />
    <link rel='stylesheet' id='elementor-icons-ekiticons-css'
        href='https://demo.orioit.com/wp-content/plugins/elementskit-lite/modules/elementskit-icon-pack/assets/css/ekiticons.css?ver=3.3.2'
        media='all' />
    <link rel='stylesheet' id='wcf-normalize-frontend-global-css'
        href='https://demo.orioit.com/wp-content/plugins/cartflows/assets/css/cartflows-normalize.css?ver=2.0.12'
        media='all' />
    <link rel='stylesheet' id='wcf-frontend-global-css'
        href='https://demo.orioit.com/wp-content/plugins/cartflows/assets/css/frontend.css?ver=2.0.12' media='all' />
    <link rel='stylesheet' id='wcf-pro-frontend-global-css'
        href='https://demo.orioit.com/wp-content/plugins/cartflows-pro/assets/css/frontend.css?ver=2.0.10'
        media='all' />
    <link rel='stylesheet' id='swiper-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/lib/swiper/v8/css/swiper.min.css?ver=8.4.5'
        media='all' />
    <link rel='stylesheet' id='e-swiper-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/conditionals/e-swiper.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='e-popup-style-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor-pro/assets/css/conditionals/popup.min.css?ver=3.25.2'
        media='all' />
    <link rel='stylesheet' id='widget-animated-headline-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor-pro/assets/css/widget-animated-headline.min.css?ver=3.25.2'
        media='all' />
    <link rel='stylesheet' id='widget-text-editor-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/widget-text-editor.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='widget-video-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/widget-video.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='e-shapes-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/conditionals/shapes.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='widget-heading-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/widget-heading.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='widget-icon-list-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/widget-icon-list.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='widget-image-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/widget-image.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='elementor-post-459-css'
        href='https://demo.orioit.com/wp-content/uploads/elementor/css/post-459.css?ver=1737102139' media='all' />
    <link rel='stylesheet' id='wcf-checkout-template-css'
        href='https://demo.orioit.com/wp-content/plugins/cartflows/assets/css/checkout-template.css?ver=2.0.12'
        media='all' />
    <style id='wcf-checkout-template-inline-css'>
        .wcf-embed-checkout-form .woocommerce #payment #place_order:before {
            content: "\e902";
            font-family: "cartflows-icon" !important;
            margin-right: 10px;
            font-size: 16px;
            font-weight: 500;
            top: 0px;
            position: relative;
            opacity: 1;
            display: block;
        }
    </style>
    <link rel='stylesheet' id='wcf-pro-checkout-css'
        href='https://demo.orioit.com/wp-content/plugins/cartflows-pro/assets/css/checkout-styles.css?ver=2.0.10'
        media='all' />
    <link rel='stylesheet' id='wcf-pro-multistep-checkout-css'
        href='https://demo.orioit.com/wp-content/plugins/cartflows-pro/assets/css/multistep-checkout.css?ver=2.0.10'
        media='all' />
    <link rel='stylesheet' id='dashicons-css'
        href='https://demo.orioit.com/wp-includes/css/dashicons.min.css?ver=6.7.1' media='all' />
    <link rel='stylesheet' id='ekit-widget-styles-css'
        href='https://demo.orioit.com/wp-content/plugins/elementskit-lite/widgets/init/assets/css/widget-styles.css?ver=3.3.2'
        media='all' />
    <link rel='stylesheet' id='ekit-responsive-css'
        href='https://demo.orioit.com/wp-content/plugins/elementskit-lite/widgets/init/assets/css/responsive.css?ver=3.3.2'
        media='all' />
    <link rel='stylesheet' id='bdt-uikit-css'
        href='https://demo.orioit.com/wp-content/plugins/bdthemes-element-pack/assets/css/bdt-uikit.css?ver=3.21.7'
        media='all' />
    <link rel='stylesheet' id='ep-helper-css'
        href='https://demo.orioit.com/wp-content/plugins/bdthemes-element-pack/assets/css/ep-helper.css?ver=7.18.10'
        media='all' />
    <link rel='stylesheet' id='google-fonts-1-css'
        href='https://fonts.googleapis.com/css?family=Roboto%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CRoboto+Slab%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CAnek+Bangla%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CHind+Siliguri%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CMontserrat%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&#038;display=swap&#038;ver=6.7.1'
        media='all' />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <script src="https://demo.orioit.com/wp-includes/js/jquery/jquery.min.js?ver=3.7.1" id="jquery-core-js"></script>
    <script src="https://demo.orioit.com/wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1" id="jquery-migrate-js">
    </script>
    <script
        src="https://demo.orioit.com/wp-content/plugins/woocommerce/assets/js/js-cookie/js.cookie.min.js?ver=2.1.4-wc.9.4.2"
        id="js-cookie-js" defer data-wp-strategy="defer"></script>
</head>

<body
    class="cartflows_step-template cartflows_step-template-cartflows-canvas single single-cartflows_step postid-459 theme-hello-elementor woocommerce-checkout woocommerce-page woocommerce-no-js cartflows-2.0.12  cartflows-pro-2.0.10 elementor-default elementor-kit-7 elementor-page elementor-page-459 cartflows-canvas">
    <div class="cartflows-container">
        <div data-elementor-type="wp-post" data-elementor-id="459" class="elementor elementor-459"
            data-elementor-settings="{&quot;element_pack_global_tooltip_width&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;element_pack_global_tooltip_width_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;element_pack_global_tooltip_width_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;element_pack_global_tooltip_padding&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_padding_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_padding_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_border_radius&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_border_radius_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_border_radius_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true}}"
            data-elementor-post-type="cartflows_step">
            

            <x-filament-fabricator::page-blocks :blocks="$page->blocks" />

            <livewire:fabricator.one.checkout :product="$page->product" />
        </div>
    </div>


    <div class="wcf-quick-view-wrapper">
        <div class="wcf-quick-view-bg">
            <div class="wcf-quick-view-loader"></div>
        </div>
        <div id="wcf-quick-view-modal">
            <div class="wcf-content-main-wrapper"><!--
  -->
                <div class="wcf-content-main">
                    <div class="wcf-lightbox-content">
                        <div class="wcf-content-main-head">
                            <a href="#" id="wcf-quick-view-close"
                                class="wcf-quick-view-close-btn cfa cfa-close"><span
                                    class="cartflows-icon-close"></span></a>
                        </div>
                        <div id="wcf-quick-view-content" class="woocommerce single-product"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type='text/javascript'>
        const lazyloadRunObserver = () => {
            const lazyloadBackgrounds = document.querySelectorAll(`.e-con.e-parent:not(.e-lazyloaded)`);
            const lazyloadBackgroundObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        let lazyloadBackground = entry.target;
                        if (lazyloadBackground) {
                            lazyloadBackground.classList.add('e-lazyloaded');
                        }
                        lazyloadBackgroundObserver.unobserve(entry.target);
                    }
                });
            }, {
                rootMargin: '200px 0px 200px 0px'
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
    <script>
        (function() {
            var c = document.body.className;
            c = c.replace(/woocommerce-no-js/, 'woocommerce-js');
            document.body.className = c;
        })();
    </script>
    <script type="text/template" id="tmpl-unavailable-variation-template">
	<p role="alert">Sorry, this product is unavailable. Please choose a different combination.</p>
</script>
    <link rel='stylesheet' id='wc-blocks-style-css'
        href='https://demo.orioit.com/wp-content/plugins/woocommerce/assets/client/blocks/wc-blocks.css?ver=wc-9.4.2'
        media='all' />
    <link rel='stylesheet' id='cartflows-elementor-style-css'
        href='https://demo.orioit.com/wp-content/plugins/cartflows/modules/elementor/widgets-css/frontend.css?ver=2.0.12'
        media='all' />
    <link rel='stylesheet' id='cartflows-pro-elementor-style-css'
        href='https://demo.orioit.com/wp-content/plugins/cartflows-pro/modules/elementor/widgets-css/frontend.css?ver=2.0.10'
        media='all' />
    <script
        src="https://demo.orioit.com/wp-content/plugins/elementskit-lite/libs/framework/assets/js/frontend-script.js?ver=3.3.2"
        id="elementskit-framework-js-frontend-js"></script>
    <script id="elementskit-framework-js-frontend-js-after">
        var elementskit = {
            resturl: 'https://demo.orioit.com/wp-json/elementskit/v1/',
        }
    </script>
    <script
        src="https://demo.orioit.com/wp-content/plugins/elementskit-lite/widgets/init/assets/js/widget-scripts.js?ver=3.3.2"
        id="ekit-widget-scripts-js"></script>
    <script
        src="https://demo.orioit.com/wp-content/plugins/woocommerce/assets/js/sourcebuster/sourcebuster.min.js?ver=9.4.2"
        id="sourcebuster-js-js"></script>
    <script id="wc-order-attribution-js-extra">
        var wc_order_attribution = {
            "params": {
                "lifetime": 1.0e-5,
                "session": 30,
                "base64": false,
                "ajaxurl": "https:\/\/demo.orioit.com\/wp-admin\/admin-ajax.php",
                "prefix": "wc_order_attribution_",
                "allowTracking": true
            },
            "fields": {
                "source_type": "current.typ",
                "referrer": "current_add.rf",
                "utm_campaign": "current.cmp",
                "utm_source": "current.src",
                "utm_medium": "current.mdm",
                "utm_content": "current.cnt",
                "utm_id": "current.id",
                "utm_term": "current.trm",
                "utm_source_platform": "current.plt",
                "utm_creative_format": "current.fmt",
                "utm_marketing_tactic": "current.tct",
                "session_entry": "current_add.ep",
                "session_start_time": "current_add.fd",
                "session_pages": "session.pgs",
                "session_count": "udata.vst",
                "user_agent": "udata.uag"
            }
        };
    </script>
    <script
        src="https://demo.orioit.com/wp-content/plugins/woocommerce/assets/js/frontend/order-attribution.min.js?ver=9.4.2"
        id="wc-order-attribution-js"></script>
    
    <script id="bdt-uikit-js-extra">
        var element_pack_ajax_login_config = {
            "ajaxurl": "https:\/\/demo.orioit.com\/wp-admin\/admin-ajax.php",
            "language": "en",
            "loadingmessage": "Sending user info, please wait...",
            "unknownerror": "Unknown error, make sure access is correct!"
        };
        var ElementPackConfig = {
            "ajaxurl": "https:\/\/demo.orioit.com\/wp-admin\/admin-ajax.php",
            "nonce": "0526db8a9b",
            "data_table": {
                "language": {
                    "sLengthMenu": "Show _MENU_ Entries",
                    "sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "sSearch": "Search :",
                    "sZeroRecords": "No matching records found",
                    "oPaginate": {
                        "sPrevious": "Previous",
                        "sNext": "Next"
                    }
                }
            },
            "contact_form": {
                "sending_msg": "Sending message please wait...",
                "captcha_nd": "Invisible captcha not defined!",
                "captcha_nr": "Could not get invisible captcha response!"
            },
            "mailchimp": {
                "subscribing": "Subscribing you please wait..."
            },
            "search": {
                "more_result": "More Results",
                "search_result": "SEARCH RESULT",
                "not_found": "not found"
            },
            "words_limit": {
                "read_more": "[read more]",
                "read_less": "[read less]"
            },
            "elements_data": {
                "sections": [],
                "columns": [],
                "widgets": []
            }
        };
    </script>
    <script src="https://demo.orioit.com/wp-content/plugins/bdthemes-element-pack/assets/js/bdt-uikit.min.js?ver=3.21.7"
        id="bdt-uikit-js"></script>
    <script
        src="https://demo.orioit.com/wp-content/plugins/bdthemes-element-pack/assets/js/common/helper.min.js?ver=7.18.10"
        id="element-pack-helper-js"></script>
    <script src="https://demo.orioit.com/wp-includes/js/underscore.min.js?ver=1.13.7" id="underscore-js"></script>
    <script id="wp-util-js-extra">
        var _wpUtilSettings = {
            "ajax": {
                "url": "\/wp-admin\/admin-ajax.php"
            }
        };
    </script>
    <script src="https://demo.orioit.com/wp-includes/js/wp-util.min.js?ver=6.7.1" id="wp-util-js"></script>
    <script id="wc-add-to-cart-variation-js-extra">
        var wc_add_to_cart_variation_params = {
            "wc_ajax_url": "\/step\/red-rice\/?wc-ajax=%%endpoint%%&wcf_checkout_id=459",
            "i18n_no_matching_variations_text": "Sorry, no products matched your selection. Please choose a different combination.",
            "i18n_make_a_selection_text": "Please select some product options before adding this product to your cart.",
            "i18n_unavailable_text": "Sorry, this product is unavailable. Please choose a different combination."
        };
    </script>
    <script
        src="https://demo.orioit.com/wp-content/plugins/woocommerce/assets/js/frontend/add-to-cart-variation.min.js?ver=9.4.2"
        id="wc-add-to-cart-variation-js" defer data-wp-strategy="defer"></script>
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
    <script src="https://demo.orioit.com/wp-includes/js/dist/i18n.min.js?ver=5e580eb46a90c2b997e6" id="wp-i18n-js"></script>
    <script id="wp-i18n-js-after">
        wp.i18n.setLocaleData({
            'text direction\u0004ltr': ['ltr']
        });
    </script>
    <script id="elementor-pro-frontend-js-before">
        var ElementorProFrontendConfig = {
            "ajaxurl": "https:\/\/demo.orioit.com\/wp-admin\/admin-ajax.php",
            "nonce": "e10816e5b2",
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
                    "fragments_nonce": "b7f1b70a29"
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
                "floatingButtonsClickTracking": "ce24bd209a"
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
                "id": 459,
                "title": "Red%20Rice",
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
    <script id="elementskit-elementor-js-extra">
        var ekit_config = {
            "ajaxurl": "https:\/\/demo.orioit.com\/wp-admin\/admin-ajax.php",
            "nonce": "f5d481d45b"
        };
    </script>
    <script src="https://demo.orioit.com/wp-content/plugins/elementskit-lite/widgets/init/assets/js/elementor.js?ver=3.3.2"
        id="elementskit-elementor-js"></script>
</body>

</html>



<!-- Page cached by LiteSpeed Cache 6.5.4 on 2025-01-18 01:45:35 -->
