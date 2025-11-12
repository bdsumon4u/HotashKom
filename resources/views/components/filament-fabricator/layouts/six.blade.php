@props(['page'])
<!DOCTYPE html>
<html lang="en-US" class="no-js">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <title>{{ $page->title }}</title>
    <meta name='robots' content='max-image-preview:large' />
    <style>
        img:is([sizes="auto" i], [sizes^="auto," i]) {
            contain-intrinsic-size: 3000px 1500px
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
            --wp--preset--color--ast-global-color-0: var(--ast-global-color-0);
            --wp--preset--color--ast-global-color-1: var(--ast-global-color-1);
            --wp--preset--color--ast-global-color-2: var(--ast-global-color-2);
            --wp--preset--color--ast-global-color-3: var(--ast-global-color-3);
            --wp--preset--color--ast-global-color-4: var(--ast-global-color-4);
            --wp--preset--color--ast-global-color-5: var(--ast-global-color-5);
            --wp--preset--color--ast-global-color-6: var(--ast-global-color-6);
            --wp--preset--color--ast-global-color-7: var(--ast-global-color-7);
            --wp--preset--color--ast-global-color-8: var(--ast-global-color-8);
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

        :root {
            --wp--style--global--content-size: var(--wp--custom--ast-content-width-size);
            --wp--style--global--wide-size: var(--wp--custom--ast-wide-width-size);
        }

        :where(body) {
            margin: 0;
        }

        .wp-site-blocks>.alignleft {
            float: left;
            margin-right: 2em;
        }

        .wp-site-blocks>.alignright {
            float: right;
            margin-left: 2em;
        }

        .wp-site-blocks>.aligncenter {
            justify-content: center;
            margin-left: auto;
            margin-right: auto;
        }

        :where(.wp-site-blocks)>* {
            margin-block-start: 24px;
            margin-block-end: 0;
        }

        :where(.wp-site-blocks)> :first-child {
            margin-block-start: 0;
        }

        :where(.wp-site-blocks)> :last-child {
            margin-block-end: 0;
        }

        :root {
            --wp--style--block-gap: 24px;
        }

        :root :where(.is-layout-flow)> :first-child {
            margin-block-start: 0;
        }

        :root :where(.is-layout-flow)> :last-child {
            margin-block-end: 0;
        }

        :root :where(.is-layout-flow)>* {
            margin-block-start: 24px;
            margin-block-end: 0;
        }

        :root :where(.is-layout-constrained)> :first-child {
            margin-block-start: 0;
        }

        :root :where(.is-layout-constrained)> :last-child {
            margin-block-end: 0;
        }

        :root :where(.is-layout-constrained)>* {
            margin-block-start: 24px;
            margin-block-end: 0;
        }

        :root :where(.is-layout-flex) {
            gap: 24px;
        }

        :root :where(.is-layout-grid) {
            gap: 24px;
        }

        .is-layout-flow>.alignleft {
            float: left;
            margin-inline-start: 0;
            margin-inline-end: 2em;
        }

        .is-layout-flow>.alignright {
            float: right;
            margin-inline-start: 2em;
            margin-inline-end: 0;
        }

        .is-layout-flow>.aligncenter {
            margin-left: auto !important;
            margin-right: auto !important;
        }

        .is-layout-constrained>.alignleft {
            float: left;
            margin-inline-start: 0;
            margin-inline-end: 2em;
        }

        .is-layout-constrained>.alignright {
            float: right;
            margin-inline-start: 2em;
            margin-inline-end: 0;
        }

        .is-layout-constrained>.aligncenter {
            margin-left: auto !important;
            margin-right: auto !important;
        }

        .is-layout-constrained> :where(:not(.alignleft):not(.alignright):not(.alignfull)) {
            max-width: var(--wp--style--global--content-size);
            margin-left: auto !important;
            margin-right: auto !important;
        }

        .is-layout-constrained>.alignwide {
            max-width: var(--wp--style--global--wide-size);
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

        body {
            padding-top: 0px;
            padding-right: 0px;
            padding-bottom: 0px;
            padding-left: 0px;
        }

        a:where(:not(.wp-element-button)) {
            text-decoration: none;
        }

        :root :where(.wp-element-button, .wp-block-button__link) {
            background-color: #32373c;
            border-width: 0;
            color: #fff;
            font-family: inherit;
            font-size: inherit;
            line-height: inherit;
            padding: calc(0.667em + 2px) calc(1.333em + 2px);
            text-decoration: none;
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

        .has-ast-global-color-0-color {
            color: var(--wp--preset--color--ast-global-color-0) !important;
        }

        .has-ast-global-color-1-color {
            color: var(--wp--preset--color--ast-global-color-1) !important;
        }

        .has-ast-global-color-2-color {
            color: var(--wp--preset--color--ast-global-color-2) !important;
        }

        .has-ast-global-color-3-color {
            color: var(--wp--preset--color--ast-global-color-3) !important;
        }

        .has-ast-global-color-4-color {
            color: var(--wp--preset--color--ast-global-color-4) !important;
        }

        .has-ast-global-color-5-color {
            color: var(--wp--preset--color--ast-global-color-5) !important;
        }

        .has-ast-global-color-6-color {
            color: var(--wp--preset--color--ast-global-color-6) !important;
        }

        .has-ast-global-color-7-color {
            color: var(--wp--preset--color--ast-global-color-7) !important;
        }

        .has-ast-global-color-8-color {
            color: var(--wp--preset--color--ast-global-color-8) !important;
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

        .has-ast-global-color-0-background-color {
            background-color: var(--wp--preset--color--ast-global-color-0) !important;
        }

        .has-ast-global-color-1-background-color {
            background-color: var(--wp--preset--color--ast-global-color-1) !important;
        }

        .has-ast-global-color-2-background-color {
            background-color: var(--wp--preset--color--ast-global-color-2) !important;
        }

        .has-ast-global-color-3-background-color {
            background-color: var(--wp--preset--color--ast-global-color-3) !important;
        }

        .has-ast-global-color-4-background-color {
            background-color: var(--wp--preset--color--ast-global-color-4) !important;
        }

        .has-ast-global-color-5-background-color {
            background-color: var(--wp--preset--color--ast-global-color-5) !important;
        }

        .has-ast-global-color-6-background-color {
            background-color: var(--wp--preset--color--ast-global-color-6) !important;
        }

        .has-ast-global-color-7-background-color {
            background-color: var(--wp--preset--color--ast-global-color-7) !important;
        }

        .has-ast-global-color-8-background-color {
            background-color: var(--wp--preset--color--ast-global-color-8) !important;
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

        .has-ast-global-color-0-border-color {
            border-color: var(--wp--preset--color--ast-global-color-0) !important;
        }

        .has-ast-global-color-1-border-color {
            border-color: var(--wp--preset--color--ast-global-color-1) !important;
        }

        .has-ast-global-color-2-border-color {
            border-color: var(--wp--preset--color--ast-global-color-2) !important;
        }

        .has-ast-global-color-3-border-color {
            border-color: var(--wp--preset--color--ast-global-color-3) !important;
        }

        .has-ast-global-color-4-border-color {
            border-color: var(--wp--preset--color--ast-global-color-4) !important;
        }

        .has-ast-global-color-5-border-color {
            border-color: var(--wp--preset--color--ast-global-color-5) !important;
        }

        .has-ast-global-color-6-border-color {
            border-color: var(--wp--preset--color--ast-global-color-6) !important;
        }

        .has-ast-global-color-7-border-color {
            border-color: var(--wp--preset--color--ast-global-color-7) !important;
        }

        .has-ast-global-color-8-border-color {
            border-color: var(--wp--preset--color--ast-global-color-8) !important;
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

        :root :where(.wp-block-pullquote) {
            font-size: 1.5em;
            line-height: 1.6;
        }
    </style>

    <link rel='stylesheet' id='woocommerce-general-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/woocommerce/assets/css/woocommerce.css?ver=10.3.4') }}'
        media='all' />
    <style id='woocommerce-inline-inline-css'>
        .woocommerce form .form-row .required {
            visibility: visible;
        }
    </style>
    <link rel='stylesheet' id='thwcfd-checkout-style-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/woo-checkout-field-editor-pro/public/assets/css/thwcfd-public.min.css?ver=2.1.4') }}'
        media='all' />
    <link rel='stylesheet' id='wcf-normalize-frontend-global-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/cartflows/assets/css/cartflows-normalize.css?ver=2.1.14') }}'
        media='all' />
    <style id='wcf-normalize-frontend-global-inline-css'>
        :root {
            --ast-global-color-0: #0084d6;
            --ast-global-color-1: #0075be;
            --ast-global-color-2: #000000;
            --ast-global-color-3: #333333;
            --ast-global-color-4: #f5f7f9;
            --ast-global-color-5: #ffffff;
            --ast-global-color-6: #243673;
            --ast-global-color-7: #000000;
            --ast-global-color-8: #BFD1FF;
        }
    </style>
    <link rel='stylesheet' id='wcf-frontend-global-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/cartflows/assets/css/frontend.css?ver=2.1.14') }}'
        media='all' />
    <style id='wcf-frontend-global-inline-css'>
        :root {
            --e-global-color-wcfgcpprimarycolor: #f16334;
            --e-global-color-wcfgcpsecondarycolor: #000000;
            --e-global-color-wcfgcptextcolor: #4B5563;
            --e-global-color-wcfgcpaccentcolor: #1F2937;
        }
    </style>
    <link rel='stylesheet' id='wcf-pro-frontend-global-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/cartflows-pro/assets/css/frontend.css?ver=2.0.3') }}'
        media='all' />
    <link rel='stylesheet' id='elementor-frontend-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/elementor/assets/css/frontend.min.css?ver=3.32.2') }}'
        media='all' />
    <link rel='stylesheet' id='elementor-post-22-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/uploads/elementor/css/post-22.css?ver=1762689002') }}' media='all' />
    <link rel='stylesheet' id='elementor-post-120-css' href='{{ asset('
        assets/demo.orioit.com/wp-content/uploads/elementor/css/post-120.css?ver=1736887944') }}' media='all' />
    <link rel='stylesheet' id='wcf-checkout-template-css' href='{{ asset('
        assets/demo.orioit.com/wp-content/plugins/cartflows/assets/css/checkout-template.css?ver=2.0.12') }}' media='all' />

    <link rel='stylesheet' id='wcf-pro-checkout-css' href='{{ asset('
        assets/demo.orioit.com/wp-content/plugins/cartflows-pro/assets/css/checkout-styles.css?ver=2.0.10') }}'
        media='all' />
    <link rel='stylesheet' id='widget-heading-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/elementor/assets/css/widget-heading.min.css?ver=3.32.2') }}'
        media='all' />
    <link rel='stylesheet' id='swiper-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/elementor/assets/lib/swiper/v8/css/swiper.min.css?ver=8.4.5') }}'
        media='all' />
    <link rel='stylesheet' id='e-swiper-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/elementor/assets/css/conditionals/e-swiper.min.css?ver=3.32.2') }}'
        media='all' />
    <link rel='stylesheet' id='widget-image-carousel-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/elementor/assets/css/widget-image-carousel.min.css?ver=3.32.2') }}'
        media='all' />
    <link rel='stylesheet' id='widget-icon-list-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/elementor/assets/css/widget-icon-list.min.css?ver=3.32.2') }}'
        media='all' />
    <link rel='stylesheet' id='e-animation-push-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/elementor/assets/lib/animations/styles/e-animation-push.min.css?ver=3.32.2') }}'
        media='all' />
    <link rel='stylesheet' id='widget-image-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/elementor/assets/css/widget-image.min.css?ver=3.32.2') }}'
        media='all' />
    <link rel='stylesheet' id='elementor-post-31816-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/uploads/elementor/css/post-31816.css?ver=1762689016') }}'
        media='all' />
    <link rel='stylesheet' id='wcf-checkout-template-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/cartflows/assets/css/checkout-template.css?ver=2.1.14') }}'
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
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/cartflows-pro/assets/css/checkout-styles.css?ver=2.0.3') }}'
        media='all' />
    <link rel='stylesheet' id='wcf-pro-multistep-checkout-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/cartflows-pro/assets/css/multistep-checkout.css?ver=2.0.3') }}'
        media='all' />
    <script src="{{ asset('assets/isolatefashionsbd.com/wp-includes/js/jquery/jquery.min.js?ver=3.7.1') }}" id="jquery-core-js">
    </script>
    <script src="{{ asset('assets/isolatefashionsbd.com/wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1') }}"
        id="jquery-migrate-js"></script>
</head>

<body
    class="wp-singular cartflows_step-template cartflows_step-template-cartflows-canvas single single-cartflows_step postid-31816 wp-theme-astra theme-astra woocommerce-checkout woocommerce-page woocommerce-no-js ast-desktop ast-separate-container ast-left-sidebar astra-4.11.12 ast-blog-single-style-1 ast-custom-post-type ast-single-post ast-replace-site-logo-transparent ast-inherit-site-logo-transparent ast-hfb-header ast-full-width-primary-header cartflows-2.1.14 cartflows-pro-2.0.3 elementor-default elementor-kit-22 elementor-page elementor-page-31816 cartflows-canvas">


    <div class="cartflows-container">

        <div data-elementor-type="wp-post" data-elementor-id="31816" class="elementor elementor-31816">
            <x-filament-fabricator::page-blocks :blocks="$page->blocks" />

            <livewire:fabricator.checkout layout="six" :product="$page->product" />
        </div>
    </div>

    <link rel='stylesheet' id='wc-blocks-style-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/woocommerce/assets/client/blocks/wc-blocks.css?ver=wc-10.3.4') }}'
        media='all' />
    <link rel='stylesheet' id='cartflows-elementor-style-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/cartflows/modules/elementor/widgets-css/frontend.css?ver=2.1.14') }}'
        media='all' />
    <link rel='stylesheet' id='cartflows-pro-elementor-style-css'
        href='{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/cartflows-pro/modules/elementor/widgets-css/frontend.css?ver=2.0.3') }}'
        media='all' />
    <script src="{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/elementor/assets/js/webpack.runtime.min.js?ver=3.32.2') }}"
        id="elementor-webpack-runtime-js"></script>
    <script
        src="{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/elementor/assets/js/frontend-modules.min.js?ver=3.32.2') }}"
        id="elementor-frontend-modules-js"></script>
    <script src="{{ asset('assets/isolatefashionsbd.com/wp-includes/js/jquery/ui/core.min.js?ver=1.13.3') }}" id="jquery-ui-core-js">
    </script>
    <script id="elementor-frontend-js-before">
        var elementorFrontendConfig = {"environmentMode":{"edit":false,"wpPreview":false,"isScriptDebug":false},"i18n":{"shareOnFacebook":"Share on Facebook","shareOnTwitter":"Share on Twitter","pinIt":"Pin it","download":"Download","downloadImage":"Download image","fullscreen":"Fullscreen","zoom":"Zoom","share":"Share","playVideo":"Play Video","previous":"Previous","next":"Next","close":"Close","a11yCarouselPrevSlideMessage":"Previous slide","a11yCarouselNextSlideMessage":"Next slide","a11yCarouselFirstSlideMessage":"This is the first slide","a11yCarouselLastSlideMessage":"This is the last slide","a11yCarouselPaginationBulletMessage":"Go to slide"},"is_rtl":false,"breakpoints":{"xs":0,"sm":480,"md":768,"lg":1025,"xl":1440,"xxl":1600},"responsive":{"breakpoints":{"mobile":{"label":"Mobile Portrait","value":767,"default_value":767,"direction":"max","is_enabled":true},"mobile_extra":{"label":"Mobile Landscape","value":880,"default_value":880,"direction":"max","is_enabled":false},"tablet":{"label":"Tablet Portrait","value":1024,"default_value":1024,"direction":"max","is_enabled":true},"tablet_extra":{"label":"Tablet Landscape","value":1200,"default_value":1200,"direction":"max","is_enabled":false},"laptop":{"label":"Laptop","value":1366,"default_value":1366,"direction":"max","is_enabled":false},"widescreen":{"label":"Widescreen","value":2400,"default_value":2400,"direction":"min","is_enabled":false}},"hasCustomBreakpoints":false},"version":"3.32.2","is_static":false,"experimentalFeatures":{"e_font_icon_svg":true,"additional_custom_breakpoints":true,"container":true,"nested-elements":true,"home_screen":true,"global_classes_should_enforce_capabilities":true,"e_variables":true,"cloud-library":true,"e_opt_in_v4_page":true,"import-export-customization":true},"urls":{"assets":"{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/elementor/assets/') }}","ajaxurl":"{{ asset('assets/isolatefashionsbd.com/wp-admin/admin-ajax.php') }}","uploadUrl":"{{ asset('assets/isolatefashionsbd.com/wp-content/uploads') }}"},"nonces":{"floatingButtonsClickTracking":"d6c8310021"},"swiperClass":"swiper","settings":{"page":[],"editorPreferences":[]},"kit":{"active_breakpoints":["viewport_mobile","viewport_tablet"],"global_image_lightbox":"yes","lightbox_enable_counter":"yes","lightbox_enable_fullscreen":"yes","lightbox_enable_zoom":"yes","lightbox_enable_share":"yes","lightbox_title_src":"title","lightbox_description_src":"description"},"post":{"id":31816,"title":"MENS%20padding%20jacket%20%E2%80%93%20isolatefashions%20bd","excerpt":"","featuredImage":false}};
    </script>
    <script src="{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/elementor/assets/js/frontend.min.js?ver=3.32.2') }}"
        id="elementor-frontend-js"></script>
    <script
        src="{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/elementor/assets/lib/swiper/v8/swiper.min.js?ver=8.4.5') }}"
        id="swiper-js"></script>
    <script
        src="{{ asset('assets/isolatefashionsbd.com/wp-content/plugins/woocommerce/assets/js/flexslider/jquery.flexslider.min.js?ver=2.7.2-wc.10.3.4') }}"
        id="wc-flexslider-js" defer data-wp-strategy="defer"></script>
</body>

</html>
