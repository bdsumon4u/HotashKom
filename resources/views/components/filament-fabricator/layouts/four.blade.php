@props(['page'])


<!DOCTYPE html>
<html lang="en-US" class="no-js">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <title>{{$page->title}}</title>
    <meta name='robots' content='max-image-preview:large' />
    <style>img:is([sizes="auto" i], [sizes^="auto," i]) { contain-intrinsic-size: 3000px 1500px }</style>
    <link rel='stylesheet' id='CF_block-cartflows-style-css-css'
        href='https://demo.orioit.com/wp-content/plugins/cartflows/modules/gutenberg/build/style-blocks.css?ver=2.0.12'
        media='all' />
    <link rel='stylesheet' id='CFP_block-cfp-style-css-css'
        href='https://demo.orioit.com/wp-content/plugins/cartflows-pro/modules/gutenberg/build/style-blocks.css?ver=2.0.10'
        media='all' />
    <style id='classic-theme-styles-inline-css'>
        /*! This file is auto-generated */
        .wp-block-button__link {
            color: #fff;
            background-color: #32373c;
            border-radius: 9999px;
            box-shadow: none;
            text-decoration: none;
            padding: calc(.667em + 2px) calc(1.333em + 2px);
            font-size: 1.125em
        }

        .wp-block-file__button {
            background: #32373c;
            color: #fff;
            text-decoration: none
        }
    </style>
    <style id='global-styles-inline-css'>
        :root {
            --wp--preset--aspect-ratio--square: 1;
            --wp--preset--aspect-ratio--4-3: 4/3;
            --wp--preset--aspect-ratio--3-4: 3/4;
            --wp--preset--aspect-ratio--3-2: 3/2;
            --wp--preset--aspect-ratio--2-3: 2/3;
            --wp--preset--aspect-ratio--16-9: 16/9;
            --wp--preset--aspect-ratio--9-16: 9/16;
            --wp--preset--color--black: #000000;
            --wp--preset--color--cyan-bluish-gray: #abb8c3;
            --wp--preset--color--white: #ffffff;
            --wp--preset--color--pale-pink: #f78da7;
            --wp--preset--color--vivid-red: #cf2e2e;
            --wp--preset--color--luminous-vivid-orange: #ff6900;
            --wp--preset--color--luminous-vivid-amber: #fcb900;
            --wp--preset--color--light-green-cyan: #7bdcb5;
            --wp--preset--color--vivid-green-cyan: #00d084;
            --wp--preset--color--pale-cyan-blue: #8ed1fc;
            --wp--preset--color--vivid-cyan-blue: #0693e3;
            --wp--preset--color--vivid-purple: #9b51e0;
            --wp--preset--gradient--vivid-cyan-blue-to-vivid-purple: linear-gradient(135deg, rgba(6, 147, 227, 1) 0%, rgb(155, 81, 224) 100%);
            --wp--preset--gradient--light-green-cyan-to-vivid-green-cyan: linear-gradient(135deg, rgb(122, 220, 180) 0%, rgb(0, 208, 130) 100%);
            --wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange: linear-gradient(135deg, rgba(252, 185, 0, 1) 0%, rgba(255, 105, 0, 1) 100%);
            --wp--preset--gradient--luminous-vivid-orange-to-vivid-red: linear-gradient(135deg, rgba(255, 105, 0, 1) 0%, rgb(207, 46, 46) 100%);
            --wp--preset--gradient--very-light-gray-to-cyan-bluish-gray: linear-gradient(135deg, rgb(238, 238, 238) 0%, rgb(169, 184, 195) 100%);
            --wp--preset--gradient--cool-to-warm-spectrum: linear-gradient(135deg, rgb(74, 234, 220) 0%, rgb(151, 120, 209) 20%, rgb(207, 42, 186) 40%, rgb(238, 44, 130) 60%, rgb(251, 105, 98) 80%, rgb(254, 248, 76) 100%);
            --wp--preset--gradient--blush-light-purple: linear-gradient(135deg, rgb(255, 206, 236) 0%, rgb(152, 150, 240) 100%);
            --wp--preset--gradient--blush-bordeaux: linear-gradient(135deg, rgb(254, 205, 165) 0%, rgb(254, 45, 45) 50%, rgb(107, 0, 62) 100%);
            --wp--preset--gradient--luminous-dusk: linear-gradient(135deg, rgb(255, 203, 112) 0%, rgb(199, 81, 192) 50%, rgb(65, 88, 208) 100%);
            --wp--preset--gradient--pale-ocean: linear-gradient(135deg, rgb(255, 245, 203) 0%, rgb(182, 227, 212) 50%, rgb(51, 167, 181) 100%);
            --wp--preset--gradient--electric-grass: linear-gradient(135deg, rgb(202, 248, 128) 0%, rgb(113, 206, 126) 100%);
            --wp--preset--gradient--midnight: linear-gradient(135deg, rgb(2, 3, 129) 0%, rgb(40, 116, 252) 100%);
            --wp--preset--font-size--small: 13px;
            --wp--preset--font-size--medium: 20px;
            --wp--preset--font-size--large: 36px;
            --wp--preset--font-size--x-large: 42px;
            --wp--preset--font-family--inter: "Inter", sans-serif;
            --wp--preset--font-family--cardo: Cardo;
            --wp--preset--spacing--20: 0.44rem;
            --wp--preset--spacing--30: 0.67rem;
            --wp--preset--spacing--40: 1rem;
            --wp--preset--spacing--50: 1.5rem;
            --wp--preset--spacing--60: 2.25rem;
            --wp--preset--spacing--70: 3.38rem;
            --wp--preset--spacing--80: 5.06rem;
            --wp--preset--shadow--natural: 6px 6px 9px rgba(0, 0, 0, 0.2);
            --wp--preset--shadow--deep: 12px 12px 50px rgba(0, 0, 0, 0.4);
            --wp--preset--shadow--sharp: 6px 6px 0px rgba(0, 0, 0, 0.2);
            --wp--preset--shadow--outlined: 6px 6px 0px -3px rgba(255, 255, 255, 1), 6px 6px rgba(0, 0, 0, 1);
            --wp--preset--shadow--crisp: 6px 6px 0px rgba(0, 0, 0, 1);
        }

        :where(.is-layout-flex) {
            gap: 0.5em;
        }

        :where(.is-layout-grid) {
            gap: 0.5em;
        }

        body .is-layout-flex {
            display: flex;
        }

        .is-layout-flex {
            flex-wrap: wrap;
            align-items: center;
        }

        .is-layout-flex> :is(*, div) {
            margin: 0;
        }

        body .is-layout-grid {
            display: grid;
        }

        .is-layout-grid> :is(*, div) {
            margin: 0;
        }

        :where(.wp-block-columns.is-layout-flex) {
            gap: 2em;
        }

        :where(.wp-block-columns.is-layout-grid) {
            gap: 2em;
        }

        :where(.wp-block-post-template.is-layout-flex) {
            gap: 1.25em;
        }

        :where(.wp-block-post-template.is-layout-grid) {
            gap: 1.25em;
        }

        .has-black-color {
            color: var(--wp--preset--color--black) !important;
        }

        .has-cyan-bluish-gray-color {
            color: var(--wp--preset--color--cyan-bluish-gray) !important;
        }

        .has-white-color {
            color: var(--wp--preset--color--white) !important;
        }

        .has-pale-pink-color {
            color: var(--wp--preset--color--pale-pink) !important;
        }

        .has-vivid-red-color {
            color: var(--wp--preset--color--vivid-red) !important;
        }

        .has-luminous-vivid-orange-color {
            color: var(--wp--preset--color--luminous-vivid-orange) !important;
        }

        .has-luminous-vivid-amber-color {
            color: var(--wp--preset--color--luminous-vivid-amber) !important;
        }

        .has-light-green-cyan-color {
            color: var(--wp--preset--color--light-green-cyan) !important;
        }

        .has-vivid-green-cyan-color {
            color: var(--wp--preset--color--vivid-green-cyan) !important;
        }

        .has-pale-cyan-blue-color {
            color: var(--wp--preset--color--pale-cyan-blue) !important;
        }

        .has-vivid-cyan-blue-color {
            color: var(--wp--preset--color--vivid-cyan-blue) !important;
        }

        .has-vivid-purple-color {
            color: var(--wp--preset--color--vivid-purple) !important;
        }

        .has-black-background-color {
            background-color: var(--wp--preset--color--black) !important;
        }

        .has-cyan-bluish-gray-background-color {
            background-color: var(--wp--preset--color--cyan-bluish-gray) !important;
        }

        .has-white-background-color {
            background-color: var(--wp--preset--color--white) !important;
        }

        .has-pale-pink-background-color {
            background-color: var(--wp--preset--color--pale-pink) !important;
        }

        .has-vivid-red-background-color {
            background-color: var(--wp--preset--color--vivid-red) !important;
        }

        .has-luminous-vivid-orange-background-color {
            background-color: var(--wp--preset--color--luminous-vivid-orange) !important;
        }

        .has-luminous-vivid-amber-background-color {
            background-color: var(--wp--preset--color--luminous-vivid-amber) !important;
        }

        .has-light-green-cyan-background-color {
            background-color: var(--wp--preset--color--light-green-cyan) !important;
        }

        .has-vivid-green-cyan-background-color {
            background-color: var(--wp--preset--color--vivid-green-cyan) !important;
        }

        .has-pale-cyan-blue-background-color {
            background-color: var(--wp--preset--color--pale-cyan-blue) !important;
        }

        .has-vivid-cyan-blue-background-color {
            background-color: var(--wp--preset--color--vivid-cyan-blue) !important;
        }

        .has-vivid-purple-background-color {
            background-color: var(--wp--preset--color--vivid-purple) !important;
        }

        .has-black-border-color {
            border-color: var(--wp--preset--color--black) !important;
        }

        .has-cyan-bluish-gray-border-color {
            border-color: var(--wp--preset--color--cyan-bluish-gray) !important;
        }

        .has-white-border-color {
            border-color: var(--wp--preset--color--white) !important;
        }

        .has-pale-pink-border-color {
            border-color: var(--wp--preset--color--pale-pink) !important;
        }

        .has-vivid-red-border-color {
            border-color: var(--wp--preset--color--vivid-red) !important;
        }

        .has-luminous-vivid-orange-border-color {
            border-color: var(--wp--preset--color--luminous-vivid-orange) !important;
        }

        .has-luminous-vivid-amber-border-color {
            border-color: var(--wp--preset--color--luminous-vivid-amber) !important;
        }

        .has-light-green-cyan-border-color {
            border-color: var(--wp--preset--color--light-green-cyan) !important;
        }

        .has-vivid-green-cyan-border-color {
            border-color: var(--wp--preset--color--vivid-green-cyan) !important;
        }

        .has-pale-cyan-blue-border-color {
            border-color: var(--wp--preset--color--pale-cyan-blue) !important;
        }

        .has-vivid-cyan-blue-border-color {
            border-color: var(--wp--preset--color--vivid-cyan-blue) !important;
        }

        .has-vivid-purple-border-color {
            border-color: var(--wp--preset--color--vivid-purple) !important;
        }

        .has-vivid-cyan-blue-to-vivid-purple-gradient-background {
            background: var(--wp--preset--gradient--vivid-cyan-blue-to-vivid-purple) !important;
        }

        .has-light-green-cyan-to-vivid-green-cyan-gradient-background {
            background: var(--wp--preset--gradient--light-green-cyan-to-vivid-green-cyan) !important;
        }

        .has-luminous-vivid-amber-to-luminous-vivid-orange-gradient-background {
            background: var(--wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange) !important;
        }

        .has-luminous-vivid-orange-to-vivid-red-gradient-background {
            background: var(--wp--preset--gradient--luminous-vivid-orange-to-vivid-red) !important;
        }

        .has-very-light-gray-to-cyan-bluish-gray-gradient-background {
            background: var(--wp--preset--gradient--very-light-gray-to-cyan-bluish-gray) !important;
        }

        .has-cool-to-warm-spectrum-gradient-background {
            background: var(--wp--preset--gradient--cool-to-warm-spectrum) !important;
        }

        .has-blush-light-purple-gradient-background {
            background: var(--wp--preset--gradient--blush-light-purple) !important;
        }

        .has-blush-bordeaux-gradient-background {
            background: var(--wp--preset--gradient--blush-bordeaux) !important;
        }

        .has-luminous-dusk-gradient-background {
            background: var(--wp--preset--gradient--luminous-dusk) !important;
        }

        .has-pale-ocean-gradient-background {
            background: var(--wp--preset--gradient--pale-ocean) !important;
        }

        .has-electric-grass-gradient-background {
            background: var(--wp--preset--gradient--electric-grass) !important;
        }

        .has-midnight-gradient-background {
            background: var(--wp--preset--gradient--midnight) !important;
        }

        .has-small-font-size {
            font-size: var(--wp--preset--font-size--small) !important;
        }

        .has-medium-font-size {
            font-size: var(--wp--preset--font-size--medium) !important;
        }

        .has-large-font-size {
            font-size: var(--wp--preset--font-size--large) !important;
        }

        .has-x-large-font-size {
            font-size: var(--wp--preset--font-size--x-large) !important;
        }

        :where(.wp-block-post-template.is-layout-flex) {
            gap: 1.25em;
        }

        :where(.wp-block-post-template.is-layout-grid) {
            gap: 1.25em;
        }

        :where(.wp-block-columns.is-layout-flex) {
            gap: 2em;
        }

        :where(.wp-block-columns.is-layout-grid) {
            gap: 2em;
        }

        :root :where(.wp-block-pullquote) {
            font-size: 1.5em;
            line-height: 1.6;
        }
    </style>
    <link rel='stylesheet' id='select2-css'
        href='https://demo.orioit.com/wp-content/plugins/woocommerce/assets/css/select2.css?ver=9.4.2' media='all' />
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
    <link rel='stylesheet' id='widget-image-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/widget-image.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='widget-heading-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/widget-heading.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='e-shapes-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/conditionals/shapes.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='widget-icon-list-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/widget-icon-list.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='widget-divider-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/widget-divider.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='widget-image-carousel-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/css/widget-image-carousel.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='e-animation-pulse-css'
        href='https://demo.orioit.com/wp-content/plugins/elementor/assets/lib/animations/styles/e-animation-pulse.min.css?ver=3.25.9'
        media='all' />
    <link rel='stylesheet' id='elementor-post-168-css'
        href='https://demo.orioit.com/wp-content/uploads/elementor/css/post-168.css?ver=1737102164' media='all' />
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
        href='https://fonts.googleapis.com/css?family=Roboto%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CRoboto+Slab%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CHind+Siliguri%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CMontserrat%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&#038;display=swap&#038;ver=6.7.1'
        media='all' />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <script src="https://demo.orioit.com/wp-includes/js/jquery/jquery.min.js?ver=3.7.1" id="jquery-core-js"></script>
    <script src="https://demo.orioit.com/wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1" id="jquery-migrate-js">
    </script>
    <script
        src="https://demo.orioit.com/wp-content/plugins/woocommerce/assets/js/js-cookie/js.cookie.min.js?ver=2.1.4-wc.9.4.2"
        id="js-cookie-js" defer data-wp-strategy="defer"></script>
    <script id="wc-cart-fragments-js-extra">
        var wc_cart_fragments_params = {
            "ajax_url": "\/wp-admin\/admin-ajax.php",
            "wc_ajax_url": "\/step\/balachao\/?wc-ajax=%%endpoint%%&wcf_checkout_id=168",
            "cart_hash_key": "wc_cart_hash_87af1a8962e26daf8e653499acd4013e",
            "fragment_name": "wc_fragments_87af1a8962e26daf8e653499acd4013e",
            "request_timeout": "5000"
        };
    </script>
    <script src="https://demo.orioit.com/wp-content/plugins/woocommerce/assets/js/frontend/cart-fragments.min.js?ver=9.4.2"
        id="wc-cart-fragments-js" defer data-wp-strategy="defer"></script>
    <script
        src="https://demo.orioit.com/wp-content/plugins/woocommerce/assets/js/jquery-blockui/jquery.blockUI.min.js?ver=2.7.0-wc.9.4.2"
        id="jquery-blockui-js" defer data-wp-strategy="defer"></script>
    <script id="wc-add-to-cart-js-extra">
        var wc_add_to_cart_params = {
            "ajax_url": "\/wp-admin\/admin-ajax.php",
            "wc_ajax_url": "\/step\/balachao\/?wc-ajax=%%endpoint%%&wcf_checkout_id=168",
            "i18n_view_cart": "View cart",
            "cart_url": "https:\/\/demo.orioit.com",
            "is_cart": "",
            "cart_redirect_after_add": "no"
        };
    </script>
    <script src="https://demo.orioit.com/wp-content/plugins/woocommerce/assets/js/frontend/add-to-cart.min.js?ver=9.4.2"
        id="wc-add-to-cart-js" defer data-wp-strategy="defer"></script>
    <script
        src="https://demo.orioit.com/wp-content/plugins/woocommerce/assets/js/selectWoo/selectWoo.full.min.js?ver=1.0.9-wc.9.4.2"
        id="selectWoo-js" defer data-wp-strategy="defer"></script>
    <script id="woocommerce-js-extra">
        var woocommerce_params = {
            "ajax_url": "\/wp-admin\/admin-ajax.php",
            "wc_ajax_url": "\/step\/balachao\/?wc-ajax=%%endpoint%%&wcf_checkout_id=168"
        };
    </script>
    <script src="https://demo.orioit.com/wp-content/plugins/woocommerce/assets/js/frontend/woocommerce.min.js?ver=9.4.2"
        id="woocommerce-js" defer data-wp-strategy="defer"></script>
    <script id="wc-country-select-js-extra">
        var wc_country_select_params = {
            "countries": "{\"BD\":{\"BD-05\":\"Bagerhat\",\"BD-01\":\"Bandarban\",\"BD-02\":\"Barguna\",\"BD-06\":\"Barishal\",\"BD-07\":\"Bhola\",\"BD-03\":\"Bogura\",\"BD-04\":\"Brahmanbaria\",\"BD-09\":\"Chandpur\",\"BD-10\":\"Chattogram\",\"BD-12\":\"Chuadanga\",\"BD-11\":\"Cox's Bazar\",\"BD-08\":\"Cumilla\",\"BD-13\":\"Dhaka\",\"BD-14\":\"Dinajpur\",\"BD-15\":\"Faridpur \",\"BD-16\":\"Feni\",\"BD-19\":\"Gaibandha\",\"BD-18\":\"Gazipur\",\"BD-17\":\"Gopalganj\",\"BD-20\":\"Habiganj\",\"BD-21\":\"Jamalpur\",\"BD-22\":\"Jashore\",\"BD-25\":\"Jhalokati\",\"BD-23\":\"Jhenaidah\",\"BD-24\":\"Joypurhat\",\"BD-29\":\"Khagrachhari\",\"BD-27\":\"Khulna\",\"BD-26\":\"Kishoreganj\",\"BD-28\":\"Kurigram\",\"BD-30\":\"Kushtia\",\"BD-31\":\"Lakshmipur\",\"BD-32\":\"Lalmonirhat\",\"BD-36\":\"Madaripur\",\"BD-37\":\"Magura\",\"BD-33\":\"Manikganj \",\"BD-39\":\"Meherpur\",\"BD-38\":\"Moulvibazar\",\"BD-35\":\"Munshiganj\",\"BD-34\":\"Mymensingh\",\"BD-48\":\"Naogaon\",\"BD-43\":\"Narail\",\"BD-40\":\"Narayanganj\",\"BD-42\":\"Narsingdi\",\"BD-44\":\"Natore\",\"BD-45\":\"Nawabganj\",\"BD-41\":\"Netrakona\",\"BD-46\":\"Nilphamari\",\"BD-47\":\"Noakhali\",\"BD-49\":\"Pabna\",\"BD-52\":\"Panchagarh\",\"BD-51\":\"Patuakhali\",\"BD-50\":\"Pirojpur\",\"BD-53\":\"Rajbari\",\"BD-54\":\"Rajshahi\",\"BD-56\":\"Rangamati\",\"BD-55\":\"Rangpur\",\"BD-58\":\"Satkhira\",\"BD-62\":\"Shariatpur\",\"BD-57\":\"Sherpur\",\"BD-59\":\"Sirajganj\",\"BD-61\":\"Sunamganj\",\"BD-60\":\"Sylhet\",\"BD-63\":\"Tangail\",\"BD-64\":\"Thakurgaon\"}}",
            "i18n_select_state_text": "Select an option\u2026",
            "i18n_no_matches": "No matches found",
            "i18n_ajax_error": "Loading failed",
            "i18n_input_too_short_1": "Please enter 1 or more characters",
            "i18n_input_too_short_n": "Please enter %qty% or more characters",
            "i18n_input_too_long_1": "Please delete 1 character",
            "i18n_input_too_long_n": "Please delete %qty% characters",
            "i18n_selection_too_long_1": "You can only select 1 item",
            "i18n_selection_too_long_n": "You can only select %qty% items",
            "i18n_load_more": "Loading more results\u2026",
            "i18n_searching": "Searching\u2026"
        };
    </script>
    <script src="https://demo.orioit.com/wp-content/plugins/woocommerce/assets/js/frontend/country-select.min.js?ver=9.4.2"
        id="wc-country-select-js" defer data-wp-strategy="defer"></script>
    <script id="wc-address-i18n-js-extra">
        var wc_address_i18n_params = {
            "locale": "{\"BD\":{\"postcode\":{\"required\":false},\"state\":{\"label\":\"District\"}},\"default\":{\"first_name\":{\"required\":true,\"class\":[\"form-row-first\"],\"autocomplete\":\"given-name\"},\"last_name\":{\"required\":true,\"class\":[\"form-row-last\"],\"autocomplete\":\"family-name\"},\"company\":{\"class\":[\"form-row-wide\"],\"autocomplete\":\"organization\",\"required\":false},\"country\":{\"type\":\"country\",\"required\":true,\"class\":[\"form-row-wide\",\"address-field\",\"update_totals_on_change\"],\"autocomplete\":\"country\"},\"address_1\":{\"required\":false,\"class\":[\"form-row-wide\",\"address-field\"],\"autocomplete\":\"address-line1\"},\"address_2\":{\"label_class\":[\"screen-reader-text\"],\"class\":[\"form-row-wide\",\"address-field\"],\"autocomplete\":\"address-line2\",\"required\":false},\"city\":{\"required\":true,\"class\":[\"form-row-wide\",\"address-field\"],\"autocomplete\":\"address-level2\"},\"state\":{\"type\":\"state\",\"required\":true,\"class\":[\"form-row-wide\",\"address-field\"],\"validate\":[\"state\"],\"autocomplete\":\"address-level1\"},\"postcode\":{\"required\":true,\"class\":[\"form-row-wide\",\"address-field\"],\"validate\":[\"postcode\"],\"autocomplete\":\"postal-code\"}}}",
            "locale_fields": "{\"address_1\":\"#billing_address_1_field, #shipping_address_1_field\",\"address_2\":\"#billing_address_2_field, #shipping_address_2_field\",\"state\":\"#billing_state_field, #shipping_state_field, #calc_shipping_state_field\",\"postcode\":\"#billing_postcode_field, #shipping_postcode_field, #calc_shipping_postcode_field\",\"city\":\"#billing_city_field, #shipping_city_field, #calc_shipping_city_field\"}",
            "i18n_required_text": "required",
            "i18n_optional_text": "optional"
        };
    </script>
    <script src="https://demo.orioit.com/wp-content/plugins/woocommerce/assets/js/frontend/address-i18n.min.js?ver=9.4.2"
        id="wc-address-i18n-js" defer data-wp-strategy="defer"></script>
    <script id="wc-checkout-js-extra">
        var wc_checkout_params = {
            "ajax_url": "\/wp-admin\/admin-ajax.php",
            "wc_ajax_url": "\/step\/balachao\/?wc-ajax=%%endpoint%%&wcf_checkout_id=168",
            "update_order_review_nonce": "5fd32ce6d9",
            "apply_coupon_nonce": "b47bfabab6",
            "remove_coupon_nonce": "0811060d73",
            "option_guest_checkout": "yes",
            "checkout_url": "\/?wc-ajax=checkout&wcf_checkout_id=168",
            "is_checkout": "1",
            "debug_mode": "",
            "i18n_checkout_error": "There was an error processing your order. Please check for any charges in your payment method and review your <a href=\"https:\/\/demo.orioit.com\/my-account\/orders\/\">order history<\/a> before placing the order again."
        };
    </script>
    <script src="https://demo.orioit.com/wp-content/plugins/woocommerce/assets/js/frontend/checkout.min.js?ver=9.4.2"
        id="wc-checkout-js" defer data-wp-strategy="defer"></script>
    <script
        src="https://demo.orioit.com/wp-content/plugins/bangladeshi-payment-gateways/assets/public/js/bdpg-public.js?ver=3.0.2"
        id="bdpg-frontend-js"></script>
    <script
        src="https://demo.orioit.com/wp-content/plugins/woocommerce/assets/js/jquery-cookie/jquery.cookie.min.js?ver=1.4.1-wc.9.4.2"
        id="jquery-cookie-js" data-wp-strategy="defer"></script>
    <script src="https://demo.orioit.com/wp-content/plugins/cartflows/assets/js/frontend.js?ver=2.0.12"
        id="wcf-frontend-global-js"></script>
    <script src="https://demo.orioit.com/wp-content/plugins/cartflows-pro/assets/js/frontend.js?ver=2.0.10"
        id="wcf-pro-frontend-global-js"></script>
    <script src="https://demo.orioit.com/wp-content/plugins/cartflows-pro/assets/js/analytics.js?ver=2.0.10"
        id="wcf-pro-analytics-global-js"></script>
    <link rel="https://api.w.org/" href="https://demo.orioit.com/wp-json/" />
    <link rel="alternate" title="JSON" type="application/json"
        href="https://demo.orioit.com/wp-json/wp/v2/cartflows_step/168" />
    <link rel="EditURI" type="application/rsd+xml" title="RSD" href="https://demo.orioit.com/xmlrpc.php?rsd" />
    <meta name="generator" content="WordPress 6.7.1" />
    <meta name="generator" content="WooCommerce 9.4.2" />
    <link rel="canonical" href="https://demo.orioit.com/step/balachao/" />
    <link rel='shortlink' href='https://demo.orioit.com/?p=168' />
    <link rel="alternate" title="oEmbed (JSON)" type="application/json+oembed"
        href="https://demo.orioit.com/wp-json/oembed/1.0/embed?url=https%3A%2F%2Fdemo.orioit.com%2Fstep%2Fbalachao%2F" />
    <link rel="alternate" title="oEmbed (XML)" type="text/xml+oembed"
        href="https://demo.orioit.com/wp-json/oembed/1.0/embed?url=https%3A%2F%2Fdemo.orioit.com%2Fstep%2Fbalachao%2F&#038;format=xml" />
    <noscript>
        <style>
            .woocommerce-product-gallery {
                opacity: 1 !important;
            }
        </style>
    </noscript>
    <meta name="generator"
        content="Elementor 3.25.9; features: e_font_icon_svg, additional_custom_breakpoints, e_optimized_control_loading, e_element_cache; settings: css_print_method-external, google_font-enabled, font_display-swap">
    <style>
        .e-con.e-parent:nth-of-type(n+4):not(.e-lazyloaded):not(.e-no-lazyload),
        .e-con.e-parent:nth-of-type(n+4):not(.e-lazyloaded):not(.e-no-lazyload) * {
            background-image: none !important;
        }

        @media screen and (max-height: 1024px) {

            .e-con.e-parent:nth-of-type(n+3):not(.e-lazyloaded):not(.e-no-lazyload),
            .e-con.e-parent:nth-of-type(n+3):not(.e-lazyloaded):not(.e-no-lazyload) * {
                background-image: none !important;
            }
        }

        @media screen and (max-height: 640px) {

            .e-con.e-parent:nth-of-type(n+2):not(.e-lazyloaded):not(.e-no-lazyload),
            .e-con.e-parent:nth-of-type(n+2):not(.e-lazyloaded):not(.e-no-lazyload) * {
                background-image: none !important;
            }
        }
    </style>
    <style class='wp-fonts-local'>
        @font-face {
            font-family: Inter;
            font-style: normal;
            font-weight: 300 900;
            font-display: fallback;
            src: url('https://demo.orioit.com/wp-content/plugins/woocommerce/assets/fonts/Inter-VariableFont_slnt,wght.woff2') format('woff2');
            font-stretch: normal;
        }

        @font-face {
            font-family: Cardo;
            font-style: normal;
            font-weight: 400;
            font-display: fallback;
            src: url('https://demo.orioit.com/wp-content/plugins/woocommerce/assets/fonts/cardo_normal_400.woff2') format('woff2');
        }
    </style>
</head>

<body
    class="cartflows_step-template cartflows_step-template-cartflows-canvas single single-cartflows_step postid-168 theme-hello-elementor woocommerce-checkout woocommerce-page woocommerce-no-js cartflows-2.0.12  cartflows-pro-2.0.10 elementor-default elementor-kit-7 elementor-page elementor-page-168 cartflows-canvas">


    <div class="cartflows-container">

        <div data-elementor-type="wp-post" data-elementor-id="168" class="elementor elementor-168"
            data-elementor-settings="{&quot;element_pack_global_tooltip_width&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;element_pack_global_tooltip_width_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;element_pack_global_tooltip_width_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;element_pack_global_tooltip_padding&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_padding_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_padding_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_border_radius&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_border_radius_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true},&quot;element_pack_global_tooltip_border_radius_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;top&quot;:&quot;&quot;,&quot;right&quot;:&quot;&quot;,&quot;bottom&quot;:&quot;&quot;,&quot;left&quot;:&quot;&quot;,&quot;isLinked&quot;:true}}"
            data-elementor-post-type="cartflows_step">
            


            <x-filament-fabricator::page-blocks :blocks="$page->blocks" />

            
            
            
            
            
            
            <section
                class="elementor-section elementor-top-section elementor-element elementor-element-ab204be elementor-section-boxed elementor-section-height-default"
                data-id="ab204be" data-element_type="section"
                data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
                <div class="elementor-container elementor-column-gap-default">
                    <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-7b7b1f74"
                        data-id="7b7b1f74" data-element_type="column">
                        <div class="elementor-widget-wrap elementor-element-populated">
                            <div class="elementor-element elementor-element-4e1d8f5a animated-slow elementor-invisible elementor-widget elementor-widget-heading"
                                data-id="4e1d8f5a" data-element_type="widget"
                                data-settings="{&quot;_animation&quot;:&quot;pulse&quot;}"
                                data-widget_type="heading.default">
                                <div class="elementor-widget-container">
                                    <div class="elementor-heading-title elementor-size-default">অর্ডার করতে নিচের
                                        ফর্মটি পূরণ করুন</div>
                                </div>
                            </div>
                            <div class="elementor-element elementor-element-3896c04 elementor-widget elementor-widget-heading"
                                data-id="3896c04" data-element_type="widget" data-widget_type="heading.default">
                                <div class="elementor-widget-container">
                                    <h2 class="elementor-heading-title elementor-size-default">অর্ডার করতে এক টাকাও
                                        অগ্রিম দিতে হবে না। প্রোডাক্ট হাতে পেয়ে চেক করে টাকা পরিশোধ করবেন। সারাদেশে
                                        ক্যাশ অন ডেলিভারি দেয়া হয়।</h2>
                                </div>
                            </div>
                            <div class="elementor-element elementor-element-50bfeb0b elementor-widget elementor-widget-checkout-form"
                                data-id="50bfeb0b" data-element_type="widget" id="order-form"
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
                                                    action="https://demo.orioit.com" enctype="multipart/form-data">



                                                    <div
                                                        class="wcf-product-option-wrap wcf-yp-skin-cards wcf-product-option-before-customer">
                                                        <h3 id="your_products_heading"> Your Products </h3>
                                                        <div class="wcf-qty-options">

                                                            <div class="wcf-qty-row wcf-qty-row-164 "
                                                                data-options="{&quot;product_id&quot;:164,&quot;variation_id&quot;:0,&quot;type&quot;:&quot;simple&quot;,&quot;unique_id&quot;:&quot;9fvc99pd&quot;,&quot;mode&quot;:&quot;quantity&quot;,&quot;highlight_text&quot;:&quot;&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;default_quantity&quot;:1,&quot;original_price&quot;:&quot;899&quot;,&quot;discounted_price&quot;:&quot;&quot;,&quot;total_discounted_price&quot;:&quot;&quot;,&quot;currency&quot;:&quot;&amp;#2547;&amp;nbsp;&quot;,&quot;cart_item_key&quot;:&quot;8e9da012b43f86ec4c5c084065433f21&quot;,&quot;save_value&quot;:&quot;&quot;,&quot;save_percent&quot;:&quot;&quot;,&quot;sign_up_fee&quot;:0,&quot;subscription_price&quot;:&quot;899&quot;,&quot;trial_period_string&quot;:&quot;&quot;}">

                                                                <div class="wcf-item">
                                                                    <div
                                                                        class="wcf-item-selector wcf-item-multiple-sel">
                                                                        <input class="wcf-multiple-sel"
                                                                            type="checkbox" name="wcf-multiple-sel"
                                                                            value="164" checked>
                                                                    </div>

                                                                    <div class="wcf-item-content-options">
                                                                        <div class="wcf-item-wrap">
                                                                            <span
                                                                                class="wcf-display-title">chingri-balachao</span><span
                                                                                class="wcf-display-title-quantity"><span
                                                                                    class="dashicons dashicons-no-alt"></span><span
                                                                                    class="wcf-display-quantity">1</span></span>
                                                                        </div>

                                                                        <div class="wcf-qty ">
                                                                            <div class="wcf-qty-selection-wrap">
                                                                                <span
                                                                                    class="wcf-qty-selection-btn wcf-qty-decrement wcf-qty-change-icon"
                                                                                    title="">&minus;</span>
                                                                                <input autocomplete="off"
                                                                                    type="number" value="1"
                                                                                    step="1" min="1"
                                                                                    name="wcf_qty_selection"
                                                                                    class="wcf-qty-selection"
                                                                                    placeholder="1"
                                                                                    data-sale-limit="false"
                                                                                    title="">
                                                                                <span
                                                                                    class="wcf-qty-selection-btn wcf-qty-increment wcf-qty-change-icon"
                                                                                    title="">&plus;</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="wcf-price">
                                                                            <div
                                                                                class="wcf-display-price wcf-field-label">
                                                                                <span
                                                                                    class="woocommerce-Price-amount amount"><span
                                                                                        class="woocommerce-Price-currencySymbol">&#2547;&nbsp;</span>&nbsp;899.00</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="wcf-col2-set col2-set" id="customer_details">
                                                        <div class="wcf-col-1 col-1">
                                                            <wc-order-attribution-inputs></wc-order-attribution-inputs>
                                                            <div class="woocommerce-billing-fields">

                                                                <h3 id="billing_fields_heading">Billing details</h3>



                                                                <div class="woocommerce-billing-fields__field-wrapper">
                                                                    <p class="form-row form-row-first wcf-column-100"
                                                                        id="billing_first_name_field"
                                                                        data-priority="10"><label
                                                                            for="billing_first_name"
                                                                            class="">আপনার নামঃ&nbsp;<span
                                                                                class="optional">(optional)</span></label><span
                                                                            class="woocommerce-input-wrapper"><input
                                                                                type="text" class="input-text "
                                                                                name="billing_first_name"
                                                                                id="billing_first_name" placeholder=""
                                                                                value=""
                                                                                autocomplete="given-name" /></span></p>
                                                                    <p class="form-row form-row-wide address-field wcf-column-100"
                                                                        id="billing_address_1_field"
                                                                        data-priority="50"><label
                                                                            for="billing_address_1"
                                                                            class="">সম্পুর্ন ঠিকানাঃ&nbsp;<span
                                                                                class="optional">(optional)</span></label><span
                                                                            class="woocommerce-input-wrapper"><input
                                                                                type="text" class="input-text "
                                                                                name="billing_address_1"
                                                                                id="billing_address_1"
                                                                                placeholder="House number and street name"
                                                                                value=""
                                                                                autocomplete="address-line1" /></span>
                                                                    </p>
                                                                    <p class="form-row form-row-wide wcf-column-100 validate-phone"
                                                                        id="billing_phone_field" data-priority="100">
                                                                        <label for="billing_phone" class="">ফোন
                                                                            নাম্বারঃ&nbsp;<span
                                                                                class="optional">(optional)</span></label><span
                                                                            class="woocommerce-input-wrapper"><input
                                                                                type="tel" class="input-text "
                                                                                name="billing_phone"
                                                                                id="billing_phone" placeholder=""
                                                                                value=""
                                                                                autocomplete="tel" /></span></p>
                                                                </div>

                                                            </div>

                                                        </div>

                                                        <div class="wcf-col-2 col-2">

                                                            <div class="woocommerce-shipping-fields">
                                                            </div>
                                                            <div class="woocommerce-additional-fields">


                                                                <input type="hidden"
                                                                    class="input-hidden _wcf_flow_id"
                                                                    name="_wcf_flow_id" value="167"><input
                                                                    type="hidden"
                                                                    class="input-hidden _wcf_checkout_id"
                                                                    name="_wcf_checkout_id" value="168">
                                                            </div>
                                                        </div>
                                                    </div>



                                                    <div class='wcf-order-wrap'>



                                                        <h3 id="order_review_heading">Your order</h3>


                                                        <div id="order_review"
                                                            class="woocommerce-checkout-review-order">
                                                            <table
                                                                class="shop_table woocommerce-checkout-review-order-table"
                                                                data-update-time="1737433153">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="product-name">Product</th>
                                                                        <th class="product-total">Subtotal</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr class="cart_item">
                                                                        <td class="product-name">
                                                                            chingri-balachao&nbsp; <strong
                                                                                class="product-quantity">&times;&nbsp;1</strong>
                                                                        </td>
                                                                        <td class="product-total">
                                                                            <span
                                                                                class="woocommerce-Price-amount amount"><bdi><span
                                                                                        class="woocommerce-Price-currencySymbol">&#2547;&nbsp;</span>&nbsp;899.00</bdi></span>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                                <tfoot>

                                                                    <tr class="cart-subtotal">
                                                                        <th>Subtotal</th>
                                                                        <td><span
                                                                                class="woocommerce-Price-amount amount"><bdi><span
                                                                                        class="woocommerce-Price-currencySymbol">&#2547;&nbsp;</span>&nbsp;899.00</bdi></span>
                                                                        </td>
                                                                    </tr>




                                                                    <tr class="woocommerce-shipping-totals shipping">
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
                                                                                            class="woocommerce-Price-currencySymbol">&#2547;&nbsp;</span>&nbsp;949.00</bdi></span></strong>
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
                                                                        <br /><button type="submit"
                                                                            class="button alt"
                                                                            name="woocommerce_checkout_update_totals"
                                                                            value="Update totals">Update
                                                                            totals</button>
                                                                    </noscript>

                                                                    <div
                                                                        class="woocommerce-terms-and-conditions-wrapper">
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
                                                                        name="woocommerce_checkout_place_order"
                                                                        id="place_order"
                                                                        value="অর্ডার করুন&nbsp;&nbsp;&#2547;&nbsp;&nbsp;949.00"
                                                                        data-value="অর্ডার করুন&nbsp;&nbsp;&#2547;&nbsp;&nbsp;949.00">অর্ডার
                                                                        করুন&nbsp;&nbsp;&#2547;&nbsp;&nbsp;949.00</button>

                                                                    <input type="hidden"
                                                                        id="woocommerce-process-checkout-nonce"
                                                                        name="woocommerce-process-checkout-nonce"
                                                                        value="e1ef866e11" /><input type="hidden"
                                                                        name="_wp_http_referer"
                                                                        value="/step/balachao/" />
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
            <section
                class="elementor-section elementor-top-section elementor-element elementor-element-4428f0e elementor-section-boxed elementor-section-height-default"
                data-id="4428f0e" data-element_type="section">
                <div class="elementor-container elementor-column-gap-default">
                    <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-b2b75c9"
                        data-id="b2b75c9" data-element_type="column">
                        <div class="elementor-widget-wrap elementor-element-populated">
                            <div class="elementor-element elementor-element-f8dd721 elementor-widget elementor-widget-heading"
                                data-id="f8dd721" data-element_type="widget" data-widget_type="heading.default">
                                <div class="elementor-widget-container">
                                    <h2 class="elementor-heading-title elementor-size-default">Copyright © 2024 Orio IT
                                        | This website made with by <a href="https://orioit.com/"
                                            style="color:green">Orio IT</a></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
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
    <script src="https://demo.orioit.com/wp-content/plugins/cartflows/assets/js/checkout-template.js?ver=2.0.12"
        id="wcf-checkout-template-js"></script>
    <script src="https://demo.orioit.com/wp-content/plugins/cartflows-pro/assets/js/checkout.js?ver=2.0.10"
        id="wcf-pro-checkout-js"></script>
    <script id="bdt-uikit-js-extra">
        var element_pack_ajax_login_config = {
            "ajaxurl": "https:\/\/demo.orioit.com\/wp-admin\/admin-ajax.php",
            "language": "en",
            "loadingmessage": "Sending user info, please wait...",
            "unknownerror": "Unknown error, make sure access is correct!"
        };
        var ElementPackConfig = {
            "ajaxurl": "https:\/\/demo.orioit.com\/wp-admin\/admin-ajax.php",
            "nonce": "45b8721943",
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
            "wc_ajax_url": "\/step\/balachao\/?wc-ajax=%%endpoint%%&wcf_checkout_id=168",
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
            "nonce": "e790dc6eed",
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
                    "fragments_nonce": "996aeb2ddc"
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
                "floatingButtonsClickTracking": "671c0bd053"
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
                "id": 168,
                "title": "Store%20Checkout%2002",
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
            "nonce": "ddd16d074c"
        };
    </script>
    <script src="https://demo.orioit.com/wp-content/plugins/elementskit-lite/widgets/init/assets/js/elementor.js?ver=3.3.2"
        id="elementskit-elementor-js"></script>
</body>

</html>



<!-- Page cached by LiteSpeed Cache 6.5.4 on 2025-01-21 04:19:13 -->
