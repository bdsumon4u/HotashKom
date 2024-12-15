<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <script>
        document.documentElement.className = document.documentElement.className + ' yes-js js_active js'
    </script>
    <script>
        (function(html) {
            html.className = html.className.replace(/\bno-js\b/, 'js')
        })(document.documentElement);
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <title>{{ $company->name }} - @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset($logo->favicon) }}"><!-- fonts -->
    <!-- css -->
    @include('googletagmanager::head')
    <x-metapixel-head/>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Montserrat%7CRoboto%3A100%2C300%2C400%2C500%2C700%2C900%2C100italic%2C300italic%2C400italic%2C500italic%2C700italic%2C900italic%7CLato%3A100%2C300%2C400%2C700%2C900%2C100italic%2C300italic%2C400italic%2C700italic%2C900italic%7CLato%3A100%2C100i%2C300%2C300i%2C400%2C400i%2C700%2C700i%2C900%2C900i%7CRoboto%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CRoboto%20Slab%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&amp;subset=latin%2Clatin-ext&amp;display=swap">
    <meta name="robots" content="max-image-preview:large">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <meta name="theme-color" content="#2370F4">
    <style id="wp-emoji-styles-inline-css" type="text/css">
        img.wp-smiley,
        img.emoji {
            display: inline !important;
            border: none !important;
            box-shadow: none !important;
            height: 1em !important;
            width: 1em !important;
            margin: 0 0.07em !important;
            vertical-align: -0.1em !important;
            background: none !important;
            padding: 0 !important
        }
    </style>
    <link rel="stylesheet" id="wp-block-library-css"
        href="/wp-includes/css/dist/block-library/style.min.css" type="text/css" media="all">
    <style id="wp-block-library-theme-inline-css" type="text/css">
        .wp-block-audio figcaption {
            color: #555;
            font-size: 13px;
            text-align: center
        }

        .is-dark-theme .wp-block-audio figcaption {
            color: hsla(0, 0%, 100%, .65)
        }

        .wp-block-audio {
            margin: 0 0 1em
        }

        .wp-block-code {
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: Menlo, Consolas, monaco, monospace;
            padding: .8em 1em
        }

        .wp-block-embed figcaption {
            color: #555;
            font-size: 13px;
            text-align: center
        }

        .is-dark-theme .wp-block-embed figcaption {
            color: hsla(0, 0%, 100%, .65)
        }

        .wp-block-embed {
            margin: 0 0 1em
        }

        .blocks-gallery-caption {
            color: #555;
            font-size: 13px;
            text-align: center
        }

        .is-dark-theme .blocks-gallery-caption {
            color: hsla(0, 0%, 100%, .65)
        }

        .wp-block-image figcaption {
            color: #555;
            font-size: 13px;
            text-align: center
        }

        .is-dark-theme .wp-block-image figcaption {
            color: hsla(0, 0%, 100%, .65)
        }

        .wp-block-image {
            margin: 0 0 1em
        }

        .wp-block-pullquote {
            border-bottom: 4px solid;
            border-top: 4px solid;
            color: currentColor;
            margin-bottom: 1.75em
        }

        .wp-block-pullquote cite,
        .wp-block-pullquote footer,
        .wp-block-pullquote__citation {
            color: currentColor;
            font-size: .8125em;
            font-style: normal;
            text-transform: uppercase
        }

        .wp-block-quote {
            border-left: .25em solid;
            margin: 0 0 1.75em;
            padding-left: 1em
        }

        .wp-block-quote cite,
        .wp-block-quote footer {
            color: currentColor;
            font-size: .8125em;
            font-style: normal;
            position: relative
        }

        .wp-block-quote.has-text-align-right {
            border-left: none;
            border-right: .25em solid;
            padding-left: 0;
            padding-right: 1em
        }

        .wp-block-quote.has-text-align-center {
            border: none;
            padding-left: 0
        }

        .wp-block-quote.is-large,
        .wp-block-quote.is-style-large,
        .wp-block-quote.is-style-plain {
            border: none
        }

        .wp-block-search .wp-block-search__label {
            font-weight: 700
        }

        .wp-block-search__button {
            border: 1px solid #ccc;
            padding: .375em .625em
        }

        :where(.wp-block-group.has-background) {
            padding: 1.25em 2.375em
        }

        .wp-block-separator.has-css-opacity {
            opacity: .4
        }

        .wp-block-separator {
            border: none;
            border-bottom: 2px solid;
            margin-left: auto;
            margin-right: auto
        }

        .wp-block-separator.has-alpha-channel-opacity {
            opacity: 1
        }

        .wp-block-separator:not(.is-style-wide):not(.is-style-dots) {
            width: 100px
        }

        .wp-block-separator.has-background:not(.is-style-dots) {
            border-bottom: none;
            height: 1px
        }

        .wp-block-separator.has-background:not(.is-style-wide):not(.is-style-dots) {
            height: 2px
        }

        .wp-block-table {
            margin: 0 0 1em
        }

        .wp-block-table td,
        .wp-block-table th {
            word-break: normal
        }

        .wp-block-table figcaption {
            color: #555;
            font-size: 13px;
            text-align: center
        }

        .is-dark-theme .wp-block-table figcaption {
            color: hsla(0, 0%, 100%, .65)
        }

        .wp-block-video figcaption {
            color: #555;
            font-size: 13px;
            text-align: center
        }

        .is-dark-theme .wp-block-video figcaption {
            color: hsla(0, 0%, 100%, .65)
        }

        .wp-block-video {
            margin: 0 0 1em
        }

        .wp-block-template-part.has-background {
            margin-bottom: 0;
            margin-top: 0;
            padding: 1.25em 2.375em
        }
    </style>
    <link rel="stylesheet" id="wc-blocks-vendors-style-css"
        href="/wp-content/cache/busting/1/wp-content/plugins/woocommerce/packages/woocommerce-blocks/build/wc-blocks-vendors-style-7.2.1.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="wc-blocks-style-css"
        href="/wp-content/cache/busting/1/wp-content/plugins/woocommerce/packages/woocommerce-blocks/build/wc-blocks-style-7.2.1.css"
        type="text/css" media="all">
    <style id="classic-theme-styles-inline-css" type="text/css">
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
    <style id="global-styles-inline-css" type="text/css">
        body {
            --wp--preset--color--black: #000;
            --wp--preset--color--cyan-bluish-gray: #abb8c3;
            --wp--preset--color--white: #fff;
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
            --wp--preset--spacing--20: .44rem;
            --wp--preset--spacing--30: .67rem;
            --wp--preset--spacing--40: 1rem;
            --wp--preset--spacing--50: 1.5rem;
            --wp--preset--spacing--60: 2.25rem;
            --wp--preset--spacing--70: 3.38rem;
            --wp--preset--spacing--80: 5.06rem;
            --wp--preset--shadow--natural: 6px 6px 9px rgba(0, 0, 0, .2);
            --wp--preset--shadow--deep: 12px 12px 50px rgba(0, 0, 0, .4);
            --wp--preset--shadow--sharp: 6px 6px 0 rgba(0, 0, 0, .2);
            --wp--preset--shadow--outlined: 6px 6px 0 -3px rgba(255, 255, 255, 1), 6px 6px rgba(0, 0, 0, 1);
            --wp--preset--shadow--crisp: 6px 6px 0 rgba(0, 0, 0, 1)
        }

        :where(.is-layout-flex) {
            gap: .5em
        }

        :where(.is-layout-grid) {
            gap: .5em
        }

        body .is-layout-flow>.alignleft {
            float: left;
            margin-inline-start: 0;
            margin-inline-end: 2em
        }

        body .is-layout-flow>.alignright {
            float: right;
            margin-inline-start: 2em;
            margin-inline-end: 0
        }

        body .is-layout-flow>.aligncenter {
            margin-left: auto !important;
            margin-right: auto !important
        }

        body .is-layout-constrained>.alignleft {
            float: left;
            margin-inline-start: 0;
            margin-inline-end: 2em
        }

        body .is-layout-constrained>.alignright {
            float: right;
            margin-inline-start: 2em;
            margin-inline-end: 0
        }

        body .is-layout-constrained>.aligncenter {
            margin-left: auto !important;
            margin-right: auto !important
        }

        body .is-layout-constrained>:where(:not(.alignleft):not(.alignright):not(.alignfull)) {
            max-width: var(--wp--style--global--content-size);
            margin-left: auto !important;
            margin-right: auto !important
        }

        body .is-layout-constrained>.alignwide {
            max-width: var(--wp--style--global--wide-size)
        }

        body .is-layout-flex {
            display: flex
        }

        body .is-layout-flex {
            flex-wrap: wrap;
            align-items: center
        }

        body .is-layout-flex>* {
            margin: 0
        }

        body .is-layout-grid {
            display: grid
        }

        body .is-layout-grid>* {
            margin: 0
        }

        :where(.wp-block-columns.is-layout-flex) {
            gap: 2em
        }

        :where(.wp-block-columns.is-layout-grid) {
            gap: 2em
        }

        :where(.wp-block-post-template.is-layout-flex) {
            gap: 1.25em
        }

        :where(.wp-block-post-template.is-layout-grid) {
            gap: 1.25em
        }

        .has-black-color {
            color: var(--wp--preset--color--black) !important
        }

        .has-cyan-bluish-gray-color {
            color: var(--wp--preset--color--cyan-bluish-gray) !important
        }

        .has-white-color {
            color: var(--wp--preset--color--white) !important
        }

        .has-pale-pink-color {
            color: var(--wp--preset--color--pale-pink) !important
        }

        .has-vivid-red-color {
            color: var(--wp--preset--color--vivid-red) !important
        }

        .has-luminous-vivid-orange-color {
            color: var(--wp--preset--color--luminous-vivid-orange) !important
        }

        .has-luminous-vivid-amber-color {
            color: var(--wp--preset--color--luminous-vivid-amber) !important
        }

        .has-light-green-cyan-color {
            color: var(--wp--preset--color--light-green-cyan) !important
        }

        .has-vivid-green-cyan-color {
            color: var(--wp--preset--color--vivid-green-cyan) !important
        }

        .has-pale-cyan-blue-color {
            color: var(--wp--preset--color--pale-cyan-blue) !important
        }

        .has-vivid-cyan-blue-color {
            color: var(--wp--preset--color--vivid-cyan-blue) !important
        }

        .has-vivid-purple-color {
            color: var(--wp--preset--color--vivid-purple) !important
        }

        .has-black-background-color {
            background-color: var(--wp--preset--color--black) !important
        }

        .has-cyan-bluish-gray-background-color {
            background-color: var(--wp--preset--color--cyan-bluish-gray) !important
        }

        .has-white-background-color {
            background-color: var(--wp--preset--color--white) !important
        }

        .has-pale-pink-background-color {
            background-color: var(--wp--preset--color--pale-pink) !important
        }

        .has-vivid-red-background-color {
            background-color: var(--wp--preset--color--vivid-red) !important
        }

        .has-luminous-vivid-orange-background-color {
            background-color: var(--wp--preset--color--luminous-vivid-orange) !important
        }

        .has-luminous-vivid-amber-background-color {
            background-color: var(--wp--preset--color--luminous-vivid-amber) !important
        }

        .has-light-green-cyan-background-color {
            background-color: var(--wp--preset--color--light-green-cyan) !important
        }

        .has-vivid-green-cyan-background-color {
            background-color: var(--wp--preset--color--vivid-green-cyan) !important
        }

        .has-pale-cyan-blue-background-color {
            background-color: var(--wp--preset--color--pale-cyan-blue) !important
        }

        .has-vivid-cyan-blue-background-color {
            background-color: var(--wp--preset--color--vivid-cyan-blue) !important
        }

        .has-vivid-purple-background-color {
            background-color: var(--wp--preset--color--vivid-purple) !important
        }

        .has-black-border-color {
            border-color: var(--wp--preset--color--black) !important
        }

        .has-cyan-bluish-gray-border-color {
            border-color: var(--wp--preset--color--cyan-bluish-gray) !important
        }

        .has-white-border-color {
            border-color: var(--wp--preset--color--white) !important
        }

        .has-pale-pink-border-color {
            border-color: var(--wp--preset--color--pale-pink) !important
        }

        .has-vivid-red-border-color {
            border-color: var(--wp--preset--color--vivid-red) !important
        }

        .has-luminous-vivid-orange-border-color {
            border-color: var(--wp--preset--color--luminous-vivid-orange) !important
        }

        .has-luminous-vivid-amber-border-color {
            border-color: var(--wp--preset--color--luminous-vivid-amber) !important
        }

        .has-light-green-cyan-border-color {
            border-color: var(--wp--preset--color--light-green-cyan) !important
        }

        .has-vivid-green-cyan-border-color {
            border-color: var(--wp--preset--color--vivid-green-cyan) !important
        }

        .has-pale-cyan-blue-border-color {
            border-color: var(--wp--preset--color--pale-cyan-blue) !important
        }

        .has-vivid-cyan-blue-border-color {
            border-color: var(--wp--preset--color--vivid-cyan-blue) !important
        }

        .has-vivid-purple-border-color {
            border-color: var(--wp--preset--color--vivid-purple) !important
        }

        .has-vivid-cyan-blue-to-vivid-purple-gradient-background {
            background: var(--wp--preset--gradient--vivid-cyan-blue-to-vivid-purple) !important
        }

        .has-light-green-cyan-to-vivid-green-cyan-gradient-background {
            background: var(--wp--preset--gradient--light-green-cyan-to-vivid-green-cyan) !important
        }

        .has-luminous-vivid-amber-to-luminous-vivid-orange-gradient-background {
            background: var(--wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange) !important
        }

        .has-luminous-vivid-orange-to-vivid-red-gradient-background {
            background: var(--wp--preset--gradient--luminous-vivid-orange-to-vivid-red) !important
        }

        .has-very-light-gray-to-cyan-bluish-gray-gradient-background {
            background: var(--wp--preset--gradient--very-light-gray-to-cyan-bluish-gray) !important
        }

        .has-cool-to-warm-spectrum-gradient-background {
            background: var(--wp--preset--gradient--cool-to-warm-spectrum) !important
        }

        .has-blush-light-purple-gradient-background {
            background: var(--wp--preset--gradient--blush-light-purple) !important
        }

        .has-blush-bordeaux-gradient-background {
            background: var(--wp--preset--gradient--blush-bordeaux) !important
        }

        .has-luminous-dusk-gradient-background {
            background: var(--wp--preset--gradient--luminous-dusk) !important
        }

        .has-pale-ocean-gradient-background {
            background: var(--wp--preset--gradient--pale-ocean) !important
        }

        .has-electric-grass-gradient-background {
            background: var(--wp--preset--gradient--electric-grass) !important
        }

        .has-midnight-gradient-background {
            background: var(--wp--preset--gradient--midnight) !important
        }

        .has-small-font-size {
            font-size: var(--wp--preset--font-size--small) !important
        }

        .has-medium-font-size {
            font-size: var(--wp--preset--font-size--medium) !important
        }

        .has-large-font-size {
            font-size: var(--wp--preset--font-size--large) !important
        }

        .has-x-large-font-size {
            font-size: var(--wp--preset--font-size--x-large) !important
        }

        .wp-block-navigation a:where(:not(.wp-element-button)) {
            color: inherit
        }

        :where(.wp-block-post-template.is-layout-flex) {
            gap: 1.25em
        }

        :where(.wp-block-post-template.is-layout-grid) {
            gap: 1.25em
        }

        :where(.wp-block-columns.is-layout-flex) {
            gap: 2em
        }

        :where(.wp-block-columns.is-layout-grid) {
            gap: 2em
        }

        .wp-block-pullquote {
            font-size: 1.5em;
            line-height: 1.6
        }
    </style>
    <link rel="stylesheet" id="stb-style-css" href="/wp-content/plugins/bkash/css/style.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="chaty-front-css-css"
        href="/wp-content/cache/busting/1/wp-content/plugins/chaty/css/chaty-front.min-3.0.71684126333.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="kapee-ext-front-css"
        href="/wp-content/cache/busting/1/wp-content/plugins/kapee-extensions/assets/css/kapee-front-1.1.4.css"
        type="text/css" media="all">
    <style id="rs-plugin-settings-inline-css" type="text/css"></style>
    <style id="woocommerce-inline-inline-css" type="text/css">
        .woocommerce form .form-row .required {
            visibility: visible
        }
    </style>
    <link rel="stylesheet" id="jquery-colorbox-css"
        href="/wp-content/cache/busting/1/wp-content/plugins/yith-woocommerce-compare/assets/css/colorbox-1.4.21.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="wccs-public-css"
        href="/wp-content/plugins/easy-woocommerce-discounts/public/css/wccs-public.min.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="elementor-lazyload-css"
        href="/wp-content/cache/busting/1/wp-content/plugins/elementor/assets/css/modules/lazyload/frontend.min-3.14.1.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="elementor-frontend-css"
        href="/wp-content/cache/busting/1/wp-content/plugins/elementor/assets/css/frontend-lite.min-3.14.1.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="swiper-css"
        href="/wp-content/cache/busting/1/wp-content/plugins/elementor/assets/lib/swiper/v8/css/swiper.min-8.4.5.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="elementor-post-11502-css"
        href="/wp-content/cache/busting/1/wp-content/uploads/elementor/css/post-11502-1689686240.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="elementor-global-css"
        href="/wp-content/cache/busting/1/wp-content/uploads/elementor/css/global-1689686368.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="elementor-post-1210-css"
        href="/wp-content/cache/busting/1/wp-content/uploads/elementor/css/post-1210-1699873323.css"
        type="text/css" media="all"><noscript></noscript>
    <link rel="stylesheet" id="kapee-style-css" href="/wp-content/themes/kapee/style.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="js_composer_front-css"
        href="/wp-content/cache/busting/1/wp-content/plugins/js_composer/assets/css/js_composer.min-6.4.2.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="bootstrap-css"
        href="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/css/third/bootstrap.min-4.0.0.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="kapee-woocommerce-css"
        href="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/css/third/woocommerce-3.4.5.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="font-awesome-css"
        href="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/css/third/font-awesome.min-4.7.0.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="kapee-fonts-css"
        href="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/css/third/kapee-font-1.0.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="simple-line-css"
        href="/wp-content/themes/kapee/assets/css/third/simple-line-icons.css" type="text/css"
        media="all">
    <link rel="stylesheet" id="owl-carousel-css"
        href="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/css/third/owl.carousel.min-2.3.3.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="slick-css"
        href="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/css/third/slick-1.8.0.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="animate-css"
        href="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/css/third/animate.min-3.7.0.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="magnific-popup-css"
        href="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/css/third/magnific-popup-1.1.0.css"
        type="text/css" media="all">
    <link rel="stylesheet" id="kapee-base-css"
        href="/wp-content/cache/busting/1/wp-content/themes/kapee/assets/css/style-1.2.4.css"
        type="text/css" media="all">
    <style id="kapee-base-inline-css" type="text/css">
        text,
        select,
        textarea,
        number {
            font-family: Roboto, sans-serif
        }

        ::-webkit-input-placeholder {
            font-family: Roboto, sans-serif
        }

        :-moz-placeholder {
            font-family: Roboto, sans-serif
        }

        ::-moz-placeholder {
            font-family: Roboto, sans-serif
        }

        :-ms-input-placeholder {
            font-family: Roboto, sans-serif
        }

        .wrapper-boxed .site-wrapper,
        .site-wrapper .container,
        .wrapper-boxed .header-sticky {
            max-width: 1920px
        }

        .kapee-site-preloader {
            background-color: #2370f4;
            background-image: url()
        }

        .header-logo .logo,
        .header-logo .logo-light {
            max-width: 120px
        }

        .header-logo .sticky-logo {
            max-width: 120px
        }

        .header-logo .mobile-logo {
            max-width: 86px
        }

        @media (max-width:991px) {

            .header-logo .logo,
            .header-logo .logo-light,
            .header-logo .mobile-logo {
                max-width: 86px
            }
        }

        body {
            color: #555
        }

        select option,
        .kapee-ajax-search .search-field,
        .kapee-ajax-search .product_cat,
        .products .product-cats a,
        .products:not(.product-style-2) .whishlist-button a:before,
        .products.list-view .whishlist-button a:before,
        .products .woocommerce-loop-category__title .product-count,
        .woocommerce div.product .kapee-breadcrumb,
        .woocommerce div.product .kapee-breadcrumb a,
        .product_meta>span span,
        .product_meta>span a,
        .multi-step-checkout .panel-heading,
        .kapee-tabs.tabs-classic .nav-tabs .nav-link,
        .kapee-tour.tour-classic .nav-tabs .nav-link,.kapee-accordion[class*="accordion-icon-"] .card-title a:after,
        .woocommerce table.wishlist_table tr td.product-remove a:before,
        .slick-slider button.slick-arrow,
        
        .mobile-menu-wrapper ul.mobile-main-menu li.menu-item-has-children>.menu-toggle {
            color: #555
        }

        a,
        label,
        thead th,
        .kapee-dropdown ul.sub-dropdown li a,div[class*="wpml-ls-legacy-dropdown"] .wpml-ls-sub-menu a,div[class*="wcml-dropdown"] .wcml-cs-submenu li a,
        .woocommerce-currency-switcher-form .dd-options a.dd-option,
        .header-topbar ul li li a,
        .header-topbar ul li li a:not([href]):not([tabindex]),
        .header-myaccount .myaccount-items li a,
        .search-results-wrapper .autocomplete-suggestions,
        .trending-search-results,
        .kapee-ajax-search .trending-search-results ul li a,
        .trending-search-results .recent-search-title,
        .trending-search-results .trending-title,
        .entry-date,
        .format-link .entry-content a,
        .woocommerce .widget_price_filter .price_label span,
        .woocommerce-or-login-with,
        .products-header .product-show span,
        .fancy-rating-summery .rating-avg,
        .rating-histogram .rating-star,
        div.product p.price,
        div.product span.price,
        .product-buttons a:before,
        .whishlist-button a:before,
        .product-buttons a.compare:before,
        .woocommerce div.summary a.compare,
        .woocommerce div.summary .countdown-box .product-countdown>span span,
        .woocommerce div.summary .price-summary span,
        .woocommerce div.summary .product-offers-list .product-offer-item,
        .woocommerce div.summary .product_meta>span,
        .quantity input[type="button"],
        .woocommerce div.summary-inner>.product-share .share-label,
        .woocommerce div.summary .items-total-price-button .item-price,
        .woocommerce div.summary .items-total-price-button .items-price,
        .woocommerce div.summary .items-total-price-button .total-price,
        .woocommerce-tabs .woocommerce-Tabs-panel--seller ul li span:not(.details),
        .single-product-page>.kapee-bought-together-products .items-total-price-button .item-price,
        .single-product-page>.kapee-bought-together-products .items-total-price-button .items-price,
        .single-product-page>.kapee-bought-together-products .items-total-price-button .total-price,
        .single-product-page>.woocommerce-tabs .items-total-price-button .item-price,
        .single-product-page>.woocommerce-tabs .items-total-price-button .items-price,
        .single-product-page>.woocommerce-tabs .items-total-price-button .total-price,
        .woocommerce-cart .cart-totals .cart_totals tr th,
        .wcppec-checkout-buttons__separator,
        .multi-step-checkout .user-info span:last-child,
        .tabs-layout.tabs-normal .nav-tabs .nav-item.show .nav-link,
        .tabs-layout.tabs-normal .nav-tabs .nav-link.active,
        .kapee-tabs.tabs-classic .nav-tabs .nav-link.active,
        .kapee-tour.tour-classic .nav-tabs .nav-link.active,
        .kapee-accordion.accordion-outline .card-header a,
        .kapee-accordion.accordion-outline .card-header a:after,
        .kapee-accordion.accordion-pills .card-header a,
        .wishlist_table .product-price,
        .mfp-close-btn-in .mfp-close,
        .woocommerce ul.cart_list li span.amount,
        .woocommerce ul.product_list_widget li span.amount,
        .gallery-caption,
        .mobile-menu-wrapper ul.mobile-main-menu li>a {
            color: #333
        }

        a:hover,
        .header-topbar .header-col ul li li:hover a,
        .header-myaccount .myaccount-items li:hover a,
        .header-myaccount .myaccount-items li i,
        .kapee-ajax-search .trending-search-results ul li:hover a,
        .mobile-menu-wrapper ul.mobile-main-menu li>a:hover,
        .mobile-menu-wrapper ul.mobile-main-menu li.active>a,
        .mobile-topbar-wrapper span a:hover,
        .products .product-cats a:hover,
        .woocommerce div.summary a.compare:hover,
        .format-link .entry-content a:hover {
            color: #2370F4
        }

        .ajax-search-style-3 .search-submit,
        .ajax-search-style-4 .search-submit,
        .customer-support::before,
        .kapee-pagination .next,
        .kapee-pagination .prev,
        .woocommerce-pagination .next,
        .woocommerce-pagination .prev,
        .fancy-square-date .entry-date .date-day,
        .entry-post .post-highlight,
        .read-more-btn,
        .read-more-btn .more-link,
        .read-more-button-fill .read-more-btn .more-link,
        .post-navigation a:hover .nav-title,
        .nav-archive:hover a,
        .format-link .entry-link:before,
        .format-quote .entry-quote:before,
        .format-quote .entry-quote:after,
        blockquote cite,
        blockquote cite a,
        .comment-reply-link,
        .widget .maxlist-more a,
        .widget_calendar tbody td a,
        .widget_calendar tfoot td a,
        .portfolio-post-loop .categories,
        .portfolio-post-loop .categories a,
        .woocommerce form .woocommerce-rememberme-lost_password label,
        .woocommerce form .woocommerce-rememberme-lost_password a,
        .woocommerce-new-signup .button,
        .widget_shopping_cart .total .amount,
        .products-header .products-view a.active,
        .products .product-wrapper:hover .product-title a,
        .products:not(.product-style-2) .whishlist-button .yith-wcwl-wishlistaddedbrowse a:before,
        .products:not(.product-style-2) .whishlist-button .yith-wcwl-wishlistexistsbrowse a:before,
        .products.list-view .whishlist-button .yith-wcwl-wishlistaddedbrowse a:before,
        .products.list-view .whishlist-button .yith-wcwl-wishlistexistsbrowse a:before,
        .woocommerce div.product .kapee-breadcrumb a:hover,
        .woocommerce div.summary .countdown-box .product-countdown>span,
        .woocommerce div.product div.summary .sold-by a,
        .woocommerce-tabs .woocommerce-Tabs-panel--seller ul li.seller-name span.details a,
        .products .product-category.category-style-1:hover .woocommerce-loop-category__title,
        .woocommerce div.summary .product-term-text,
        .tab-content-wrap .accordion-title.open,
        .tab-content-wrap .accordion-title.open:after,
        table.shop_table td .amount,
        .woocommerce-cart .cart-totals .shipping-calculator-button,
        .woocommerce-MyAccount-navigation li a::before,
        .woocommerce-account .addresses .title .edit,
        .woocommerce-Pagination a.button,
        .woocommerce table.my_account_orders .woocommerce-orders-table__cell-order-number a,
        .woocommerce-checkout .woocommerce-info .showcoupon,
        .multi-step-checkout .panel.completed .panel-title:after,
        .multi-step-checkout .panel-title .step-numner,
        .multi-step-checkout .logged-in-user-info .user-logout,
        .multi-step-checkout .panel-heading .edit-action,
        .kapee-testimonials.image-middle-center .testimonial-description:before,
        .kapee-testimonials.image-middle-center .testimonial-description:after,
        .products-and-categories-box .section-title h3,
        .categories-sub-categories-box .sub-categories-content .show-all-cate a,
        .categories-sub-categories-vertical .show-all-cate a,
        .kapee-tabs.tabs-outline .nav-tabs .nav-link.active,
        .kapee-tour.tour-outline .nav-tabs .nav-link.active,
        .kapee-accordion.accordion-outline .card-header a:not(.collapsed),
        .kapee-accordion.accordion-outline .card-header a:not(.collapsed):after,
        .kapee-button .btn-style-outline.btn-color-primary,
        .kapee-button .btn-style-link.btn-color-primary,
        .mobile-nav-tabs li.active{color:#000}input[type="checkbox"]::before,
        .header-cart-count,
        .header-wishlist-count,
        .minicart-header .minicart-title,
        .minicart-header .close-sidebar:before,
        .page-numbers.current,
        .page-links>span.current .page-number,
        .entry-date .date-year,
        .fancy-box2-date .entry-date,
        .post-share .meta-share-links .kapee-social a,
        .read-more-button .read-more-btn .more-link,
        .read-more-button-fill .read-more-btn .more-link:hover,
        .format-link .entry-link a,
        .format-quote .entry-quote,
        .format-quote .entry-quote .quote-author a,
        .widget .tagcloud a:hover,
        .widget .tagcloud a:focus,
        .widget.widget_tag_cloud a:hover,
        .widget.widget_tag_cloud a:focus,
        .wp_widget_tag_cloud a:hover,
        .wp_widget_tag_cloud a:focus,
        .back-to-top,
        .kapee-posts-lists .post-categories a,
        .kapee-recent-posts .post-categories a,
        .widget.widget_layered_nav li.chosen a:after,
        .widget.widget_rating_filter li.chosen a:after,
        .filter-categories a.active,
        .portfolio-post-loop .action-icon a:before,
        .portfolio-style-3 .portfolio-post-loop .entry-content-wrapper .categories,
        .portfolio-style-3 .portfolio-post-loop .entry-content-wrapper a,
        .portfolio-style-4 .portfolio-post-loop .entry-content-wrapper .categories,
        .portfolio-style-4 .portfolio-post-loop .entry-content-wrapper a,
        .portfolio-style-5 .portfolio-post-loop .entry-content-wrapper .categories,
        .portfolio-style-5 .portfolio-post-loop .entry-content-wrapper a,
        .portfolio-style-6 .portfolio-post-loop .entry-content-wrapper .categories,
        .portfolio-style-6 .portfolio-post-loop .entry-content-wrapper a,
        .portfolio-style-7 .portfolio-post-loop .entry-content-wrapper .categories,
        .portfolio-style-7 .portfolio-post-loop .entry-content-wrapper a,
        .customer-login-left,
        .customer-signup-left,
        .customer-login-left h2,
        .customer-signup-left h2,
        .products .product-image .product-countdown>span,
        .products .product-image .product-countdown>span>span,
        .products.product-style-1.grid-view .product-buttons a:before,
        .products:not(.product-style-1):not(.product-style-2).grid-view .cart-button a:before,
        .woocommerce-account .user-info .display-name,
        .multi-step-checkout .panel.active .panel-heading,
        .multi-step-checkout .checkout-next-step a,
        .kapee-team.image-top-with-box .color-scheme-inherit .member-info,
        .kapee-team.image-top-with-box-2 .color-scheme-inherit .member-info,
        .kapee-team.image-top-with-box .color-scheme-inherit .member-info h3,
        .kapee-team.image-top-with-box-2 .color-scheme-inherit .member-info h3,
        .kapee-team .color-scheme-inherit .member-social a,
        .kapee-team.image-middle-swap-box .color-scheme-inherit .flip-front,
        .kapee-team.image-middle-swap-box .color-scheme-inherit .flip-front h3,
        .kapee-team.image-middle-swap-box .color-scheme-inherit .member-info,
        .kapee-team.image-middle-swap-box .color-scheme-inherit .member-info h3,
        .kapee-team.image-bottom-overlay .color-scheme-inherit .member-info .kapee-team.image-bottom-overlay .color-scheme-inherit .member-info h3,
        .kapee-tabs.tabs-pills .nav-tabs .nav-link.active,
        .kapee-tour.tour-pills .nav-tabs .nav-link.active,
        .kapee-accordion.accordion-pills .card-header a:not(.collapsed),
        .kapee-accordion.accordion-pills .card-header a:not(.collapsed):after,
        .kapee-social.icons-theme-colour a:hover,
        
        .slick-slider .slick-arrow:hover,
        .kapee-button .btn-style-outline.btn-color-primary:hover,
        .mobile-menu-header a,
        #yith-wcwl-popup-message,
        .mobile-menu-header a:hover {
            color: #FFF
        }

        .woocommerce-new-signup .button,
        .kapee-video-player .video-play-btn,
        .mobile-nav-tabs li.active{background-color:#FFF}input[type="radio"]::before,
        input[type="checkbox"]::before,
        .header-cart-count,
        .header-wishlist-count,
        .minicart-header,
        .page-numbers.current,
        .page-links>span.current .page-number,
        .entry-date .date-year,
        .fancy-box2-date .entry-date,
        .entry-meta .meta-share-links,
        .read-more-button .read-more-btn .more-link,
        .read-more-button-fill .read-more-btn .more-link:hover,
        .format-link .entry-link,
        .format-quote .entry-quote,
        .related.posts>h3:after,
        .related.portfolios>h3:after,
        .comment-respond>h3:after,
        .comments-area>h3:after,
        .portfolio-entry-summary h3:after,
        .widget-title-bordered-short .widget-title::before,
        .widget-title-bordered-full .widget-title::before,
        .widget .tagcloud a:hover,
        .widget .tagcloud a:focus,
        .widget.widget_tag_cloud a:hover,
        .widget.widget_tag_cloud a:focus,
        .wp_widget_tag_cloud a:hover,
        .wp_widget_tag_cloud a:focus,
        .back-to-top,
        .kapee-posts-lists .post-categories a,
        .kapee-recent-posts .post-categories a,
        .woocommerce .widget_price_filter .ui-slider .ui-slider-range,
        .woocommerce .widget_price_filter .ui-slider .ui-slider-handle,
        .widget.widget_layered_nav li.chosen a:before,
        .widget.widget_rating_filter li.chosen a:before,
        .filter-categories a.active,
        .customer-login-left,
        .customer-signup-left,
        .products.product-style-1.grid-view .product-buttons .whishlist-button a,
        .products.product-style-1.grid-view .product-buttons .compare-button a,
        .products.product-style-1.grid-view .product-buttons .quickview-button a,
        .products:not(.product-style-2).grid-view .product-buttons .cart-button a,
        .products.list-view .product-buttons .cart-button a,
        .products .product-image .product-countdown.is-countdown,
        .tabs-layout .tabs li:after,
        section.related>h2::after,
        section.upsells>h2::after,
        div.cross-sells>h2::after,
        section.recently-viewed>h2::after,
        .woocommerce-account .kapee-user-profile,
        .multi-step-checkout .panel.active .panel-heading,
        .kapee-countdown.countdown-box .product-countdown>span,
        .tabs-layout.tabs-line .nav-tabs .nav-link::after,
        .kapee-team.image-top-with-box-2 .member-info,
        .kapee-team.image-middle-swap-box .member-info,
        .kapee-team.image-top-with-box .member-info,
        .kapee-team.image-middle-swap-box .flip-front,
        .kapee-team.image-bottom-overlay .member-info,
        .kapee-team.image-bottom-overlay .member-info::before,
        .kapee-team.image-bottom-overlay .member-info::after,
        .kapee-video-player .video-wrapper:hover .video-play-btn,
        .kapee-tabs.tabs-line .nav-tabs .nav-link::after,
        .kapee-tabs.tabs-pills .nav-tabs .nav-link.active,
        .kapee-tour.tour-line .nav-tabs .nav-link::after,
        .kapee-tour.tour-pills .nav-tabs .nav-link.active,
        .kapee-accordion.accordion-pills .card-header a:not(.collapsed),
        .kapee-social.icons-theme-colour a:hover,
        
        .slick-slider .slick-arrow:hover,
        .kapee-button .btn-style-flat.btn-color-primary,
        .kapee-button .btn-style-outline.btn-color-primary:hover,
        #yith-wcwl-popup-message,
        .mobile-menu-header,
        .slick-slider .slick-dots li.slick-active button {
            background-color: #000
        }

        .kapee-dropdown ul.sub-dropdown,div[class*="wpml-ls-legacy-dropdown"] .wpml-ls-sub-menu,div[class*="wcml-dropdown"] .wcml-cs-submenu,
        .woocommerce-currency-switcher-form .dd-options,
        .header-mini-search .kapee-mini-ajax-search,
        .entry-content-wrapper,
        .myaccount-items,
        .search-results-wrapper .autocomplete-suggestions,
        .trending-search-results,
        .entry-content-wrapper,
        .entry-date,
        .entry-post .post-highlight span:before,
        .woocommerce .widget_price_filter .ui-slider .ui-slider-handle::after,
        .widget.widget_layered_nav li a:before,
        .widget.widget_rating_filter li a:before,
        .widget.kapee_widget_product_sorting li.chosen a:after,
        .widget.kapee_widget_price_filter_list li.chosen a:after,
        .widget.kapee_widget_product_sorting li.chosen a:after,
        .widget.kapee_widget_price_filter_list li.chosen a:after,
        .kapee-login-signup,
        .kapee-signin-up-popup,
        .minicart-slide-wrapper,
        .fancy-rating-summery,
        .product-style-2.grid-view .product-buttons a,
        .products.product-style-4.grid-view div.product:hover .product-info,
        .products.product-style-4.grid-view div.product:hover .product-variations,
        .products.product-style-5.grid-view .product-buttons-variations,
        .products:not(.product-style-5):not(.list-view) .product-variations,
        .kapee-quick-view,
        .woocommerce div.product div.images .woocommerce-product-gallery__trigger,
        .woocommerce-product-gallery .product-video-btn a,
        .product-navigation-share .kapee-social,
        .product-navigation .product-info-wrap,
        .woocommerce div.summary .countdown-box .product-countdown>span,
        .woocommerce div.summary .price-summary,
        .woocommerce div.summary .product-term-detail,
        .kapee-product-sizechart,
        .kapee-bought-together-products .kapee-out-of-stock,
        .multi-step-checkout .panel-title.active .step-numner,
        .tabs-layout.tabs-normal .nav-tabs .nav-item.show .nav-link,
        .tabs-layout.tabs-normal .nav-tabs .nav-link.active,
        .kapee-tabs.tabs-classic .nav-tabs .nav-link.active,
        .kapee-tabs.tabs-classic .nav-tabs+.tab-content,
        .kapee-tour.tour-classic .nav-tabs .nav-link.active,
        .kapee-tour.tour-classic .nav-tabs+.tab-content .tab-pane,
        .slick-slider button.slick-arrow,
        
        .kapee-canvas-sidebar,
        .mobile-menu-wrapper,
        .kapee-mobile-navbar {
            background-color: #fff
        }

        select option {
            background-color: #fff
        }

        .header-topbar ul li li:hover a,
        .search-results-wrapper .autocomplete-selected,
        .trending-search-results ul li:hover a,
        .header-myaccount .myaccount-items li:hover a,
        .kapee-navigation ul.sub-menu>li:hover>a,
        .minicart-slide-wrapper .mini_cart_item:hover,
        .woocommerce-MyAccount-navigation li.is-active a,
        .woocommerce-MyAccount-navigation li:hover a {
            background-color: #96BABA
        }

        .woocommerce .widget_price_filter .price_slider_wrapper .ui-widget-content,
        

        .portfolio-post-loop .post-thumbnail:after {
            background-color: rgba(0, 0, 0, .4)
        }

        .portfolio-style-4 .portfolio-post-loop .post-thumbnail:after,
        .portfolio-style-5 .portfolio-post-loop .post-thumbnail:after,
        .portfolio-style-6 .portfolio-post-loop .post-thumbnail:after,
        .portfolio-style-7 .portfolio-post-loop .post-thumbnail:after {
            background-color: rgba(0, 0, 0, .7)
        }

        .portfolio-post-loop .action-icon a:hover:before,
        .portfolio-style-3 .portfolio-post-loop .entry-content-wrapper,
        .portfolio-style-3 .portfolio-post-loop .action-icon a:hover:before{background-color:rgba(0,0,0,1)}fieldset,input[type="text"],
        input[type="email"],
        input[type="url"],
        input[type="password"],
        input[type="search"],
        input[type="number"],
        input[type="tel"],
        input[type="range"],
        input[type="date"],
        input[type="month"],
        input[type="week"],
        input[type="time"],
        input[type="datetime"],
        input[type="datetime-local"],
        input[type="color"],
        textarea,
        select,
        input[type="checkbox"],
        input[type="radio"],
        .exclamation-mark:before,
        .question-mark:before,
        .select2-container--default .select2-selection--multiple,
        .select2-container--default .select2-selection--single,
        tr,
        .tag-social-share .single-tags a,
        .widget .tagcloud a,
        .widget.widget_tag_cloud a,
        .wp_widget_tag_cloud a,
        .widget_calendar table,
        .widget_calendar caption,
        .widget_calendar td,
        .widget div[class*="wpml-ls-legacy-dropdown"] a.wpml-ls-item-toggle,
        .widget div[class*="wcml-dropdown"] .wcml-cs-item-toggle,
        .widget .woocommerce-currency-switcher-form .dd-select .dd-selected,
        .widget.widget_layered_nav li a:before,
        .widget.widget_rating_filter li a:before,
        .products:not(.product-style-1):not(.product-style-2) .product-buttons .compare-button a,
        .products.list-view .product-buttons .compare-button a,
        .products:not(.product-style-1):not(.product-style-2) .product-buttons .quickview-button a,
        .products.list-view .product-buttons .quickview-button a,
        .woocommerce-product-gallery .product-gallery-image,
        .product-gallery-thumbnails .slick-slide,
        .woocommerce div.summary .kapee-bought-together-products,
        .single-product-page>.kapee-bought-together-products,
        .accordion-layout .tab-content-wrap,
        .toggle-layout .tab-content-wrap,
        .woocommerce-MyAccount-navigation ul,
        .products-and-categories-box .section-inner.row,
        .kapee-product-categories-thumbnails.categories-circle .category-image,
        .kapee-product-brands.brand-circle .brand-image,
        .kapee-tabs.tabs-classic .nav-tabs+.tab-content,
        .kapee-tour.tour-classic .nav-tabs .nav-link,
        .kapee-tour.tour-classic .nav-tabs+.tab-content .tab-pane,
        .kapee-accordion.accordion-classic .card,
        .mobile-menu-wrapper ul.mobile-main-menu li.menu-item-has-children>.menu-toggle,
        #wcfm_products_manage_form_wc_product_kapee_offer_expander .kapee_offer_option,
        #wcfm_products_manage_form_wc_product_kapee_offer_expander .kapee_service_option {
            border-top-width: 1px;
            border-bottom-width: 1px;
            border-left-width: 1px;
            border-right-width: 1px;
            border-style: solid;
            border-color: #e9e9e9
        }

        .kapee-pagination,
        .woocommerce-pagination,
        .post-navigation,
        .comment-list .children,
        .comment-navigation .nex-prev-nav,
        .woocommerce div.summary .price-summary .total-discount,
        .woocommerce div.summary .price-summary .overall-discount,
        .woocommerce div.summary .kapee-bought-together-products .items-total-price-button,
        .single-product-page>.kapee-bought-together-products .items-total-price-button .items-total-price>div:last-child,
        .single-product-page>.woocommerce-tabs .items-total-price-button .items-total-price>div:last-child,
        .woocommerce table.shop_table td,
        .woocommerce-checkout .woocommerce-form-coupon-toggle .woocommerce-info,
        .kapee-accordion.accordion-line .card {
            border-top-width: 1px;
            border-top-style: solid;
            border-top-color: #e9e9e9
        }

        .single-featured-image-header,
        .kapee-dropdown ul.sub-dropdown li a,div[class*="wpml-ls-legacy-dropdown"] .wpml-ls-sub-menu a,div[class*="wcml-dropdown"] .wcml-cs-submenu li a,
        .woocommerce-currency-switcher-form .dd-options a.dd-option,
        .header-myaccount .myaccount-items li a,
        .post-navigation,
        .comment-list>li:not(:last-child),
        .comment-navigation .nex-prev-nav,
        .widget,
        .widget-title-bordered-full .widget-title,
        .widget_rss ul li:not(:last-child),
        .kapee-posts-lists .widget-post-item:not(:last-child),
        .kapee-recent-posts .widget-post-item:not(:last-child),
        .kapee-tab-posts .widget-post-item:not(:last-child),
        .kapee-widget-portfolios-list:not(.style-3) .widget-portfolio-item:not(:last-child),
        .kapee-recent-comments .post-comment:not(:last-child),
        .kapee-tab-posts .post-comment:not(:last-child),
        .woocommerce ul.cart_list li:not(:last-child),
        .woocommerce ul.product_list_widget li:not(:last-child),
        .woocommerce-or-login-with:after,
        .woocommerce-or-login-with:before,
        .woocommerce-or-login-with:after,
        .woocommerce-or-login-with:before,
        .minicart-slide-wrapper .mini_cart_item,
        .empty-cart-browse-categories .browse-categories-title,
        .products-header,
        .kapee-filter-widgets .kapee-filter-inner,
        .products.list-view div.product:not(.product-category) .product-wrapper,
        .kapee-product-sizechart .sizechart-header h2,
        .tabs-layout .tabs,
        .wishlist_table.mobile>li,
        .woocommerce-cart table.cart,
        .woocommerce-MyAccount-navigation li:not(:last-child) a,
        .woocommerce-checkout .woocommerce-form-coupon-toggle .woocommerce-info,
        .section-heading,
        .tabs-layout.tabs-normal .nav-tabs,
        .products-and-categories-box .section-title,
        .kapee-accordion.accordion-classic .card-header,
        .kapee-accordion.accordion-line .card:last-child,
        .mobile-menu-wrapper ul.mobile-main-menu li a,
        .mobile-topbar>*:not(:last-child) {
            border-bottom-width: 1px;
            border-bottom-style: solid;
            border-bottom-color: #e9e9e9
        }

        .kapee-heading.separator-underline .separator-right {
            border-bottom-color: #000
        }

        .kapee-ajax-search .search-field,
        .kapee-ajax-search .product_cat,
        .products-and-categories-box .section-categories,
        .products-and-categories-box .section-banner,
        .kapee-tabs.tabs-classic .nav-tabs .nav-link {
            border-right-width: 1px;
            border-right-style: solid;
            border-right-color: #e9e9e9
        }

        .single-product-page>.kapee-bought-together-products .items-total-price-button,
        .single-product-page .woocommerce-tabs .kapee-bought-together-products .items-total-price-button,
        .kapee-tabs.tabs-classic .nav-tabs .nav-link {
            border-left-width: 1px;
            border-left-style: solid;
            border-left-color: #e9e9e9
        }

        .kapee-tour.tour-classic.position-left .nav-tabs .nav-link.active,blockquote,.wp-block-quote,.wp-block-quote[style*="text-align:right"],
        .kapee-video-player .video-play-btn:before {
            border-left-color: #000
        }

        .kapee-video-player .video-wrapper:hover .video-play-btn:before {
            border-left-color: #FFF
        }

        .kapee-tour.tour-classic.position-right .nav-tabs .nav-link.active {
            border-right-color: #000
        }

        .kapee-social.icons-theme-colour a,
        .kapee-spinner::before,
        .loading::before,
        .woocommerce .blockUI.blockOverlay::before,
        .dokan-report-abuse-button.working::before,
        .kapee-accordion.accordion-outline .card-header a,
        .kapee-vendors-list .store-product {
            border-color: #e9e9e9
        }

        .kapee-tabs.tabs-classic .nav-tabs .nav-link {
            border-top-color: #e9e9e9
        }

        .tabs-layout.tabs-normal .nav-tabs .nav-item.show .nav-link,
        .tabs-layout.tabs-normal .nav-tabs .nav-link.active,
        .woocommerce ul.cart_list li dl,
        .woocommerce ul.product_list_widget li dl {
            border-left-color: #e9e9e9
        }

        .tabs-layout.tabs-normal .nav-tabs .nav-item.show .nav-link,
        .tabs-layout.tabs-normal .nav-tabs .nav-link.active {
            border-right-color: #e9e9e9
        }

        .tag-social-share .single-tags a:hover,
        .widget .tagcloud a:hover,
        .widget .tagcloud a:focus,
        .widget.widget_tag_cloud a:hover,
        .widget.widget_tag_cloud a:focus,
        .wp_widget_tag_cloud a:hover,
        .wp_widget_tag_cloud a:focus,
        .kapee-swatches .swatch.swatch-selected,
        .product-gallery-thumbnails .slick-slide.flex-active-slide,
        .product-gallery-thumbnails .slick-slide:hover,
        .woocommerce-checkout form.checkout_coupon,
        .tabs-layout.tabs-normal .nav-tabs .nav-item.show .nav-link,
        .kapee-tabs.tabs-outline .nav-tabs .nav-link.active,
        .kapee-tour.tour-outline .nav-tabs .nav-link.active,
        .kapee-accordion.accordion-outline .card-header a:not(.collapsed),
        .kapee-social.icons-theme-colour a:hover,
        .kapee-button .btn-style-outline.btn-color-primary,
        .kapee-button .btn-style-link.btn-color-primary {
            border-color: #000
        }

        .read-more-button-fill .read-more-btn .more-link,
        .widget.widget_layered_nav li.chosen a:before,
        .widget.widget_rating_filter li.chosen a:before,
        .kapee-element .section-heading h2:after,
        .woocommerce-account .kapee-user-profile {
            border-top-width: 1px;
            border-bottom-width: 1px;
            border-left-width: 1px;
            border-right-width: 1px;
            border-style: solid;
            border-color: #000
        }

        .entry-meta .meta-share-links:after,
        .kapee-tabs.tabs-classic .nav-tabs .nav-link.active,
        .tabs-layout.tabs-normal .nav-tabs .nav-link.active,
        .kapee-spinner::before,
        .loading::before,
        .woocommerce .blockUI.blockOverlay::before,
        .dokan-report-abuse-button.working::before {
            border-top-color: #000
        }

        .kapee-arrow:after,
        #add_payment_method #payment div.payment_box::after,
        .woocommerce-cart #payment div.payment_box::after,
        .woocommerce-checkout #payment div.payment_box::after {
            border-bottom-color: #fff
        }

        .entry-date .date-month:after{border-top-color:#fff}.button,.btn,button,input[type="button"],
        input[type="submit"],
        .button:not([href]):not([tabindex]),
        .btn:not([href]):not([tabindex]) {
            color: #fff;
            background-color: #2370F4
        }

        .kapee-button .btn-color-default.btn-style-outline,
        .kapee-button .btn-color-default.btn-style-link {
            color: #2370F4
        }

        .kapee-button .btn-color-default.btn-style-outline,
        .kapee-button .btn-color-default.btn-style-link{border-color:#2370F4}.button:hover,.btn:hover,button:hover,button:focus,input[type="button"]:hover,
        input[type="button"]:focus,
        input[type="submit"]:hover,
        input[type="submit"]:focus,
        .button:not([href]):not([tabindex]):hover,
        .btn:not([href]):not([tabindex]):hover,
        .kapee-button .btn-color-default.btn-style-outline:hover {
            color: #fcfcfc;
            background-color: #2370F4
        }

        .kapee-button .btn-color-default.btn-style-link:hover {
            color: #2370F4
        }

        .kapee-button .btn-color-default.btn-style-outline:hover,
        .kapee-button .btn-color-default.btn-style-link:hover {
            border-color: #2370F4
        }

        div.summary form.cart .button {
            color: #fff;
            background-color: #ff9f00
        }

        div.summary form.cart .button:hover,
        div.summary form.cart .button:focus {
            color: #fcfcfc;
            background-color: #ff9f00
        }

        .kapee-quick-buy .kapee_quick_buy_button,
        .kapee-bought-together-products .add-items-to-cart {
            color: #fff;
            background-color: #FB641B
        }

        .kapee-quick-buy .kapee_quick_buy_button:hover,
        .kapee-quick-buy .kapee_quick_buy_button:focus,
        .kapee-bought-together-products .add-items-to-cart:hover,
        .kapee-bought-together-products .add-items-to-cart:focus {
            color: #fcfcfc;
            background-color: #FB641B
        }

        .widget_shopping_cart .button.checkout,
        .woocommerce-cart a.checkout-button,
        .woocommerce_checkout_login .checkout-next-step .btn,
        .woocommerce_checkout_login .checkout-next-step.btn,
        .woocommerce-checkout-payment #place_order {
            color: #fff;
            background-color: #FB641B
        }

        .widget_shopping_cart .button.checkout:hover,
        .widget_shopping_cart .button.checkout:focus,
        .woocommerce-cart a.checkout-button:hover,
        .woocommerce-cart a.checkout-button:focus,
        .woocommerce_checkout_login .checkout-next-step .btn:hover,
        .woocommerce_checkout_login .checkout-next-step .btn:focus,
        .woocommerce_checkout_login .checkout-next-step.btn:hover,
        .woocommerce_checkout_login .checkout-next-step.btn:focus,
        .woocommerce-checkout-payment #place_order:hover,
        .woocommerce-checkout-payment #place_order:focus {
            color: #fcfcfc;
            background-color: #FB641B
        }

        text,
        select,
        textarea,
        number {
            color: #555;
            background-color: #fff
        }

        .mc4wp-form-fields p:first-child::before {
            color: #555
        }

        ::-webkit-input-placeholder {
            color: #555
        }

        :-moz-placeholder {
            color: #555
        }

        ::-moz-placeholder {
            color: #555
        }

        :-ms-input-placeholder {
            color: #555
        }

        ::-moz-selection {
            color: #FFF;
            background: #000
        }

        ::selection {
            color: #FFF;
            background: #000
        }

        .header-topbar {
            color: #FFF
        }

        .header-topbar a {
            color: #FFF
        }

        .header-topbar a:hover {
            color: #F1F1F1
        }

        .header-topbar {
            border-bottom-width: 1px;
            border-bottom-style: solid;
            border-bottom-color: #00c2d3
        }

        .header-topbar .header-col>*,
        .topbar-navigation ul.menu>li:not(:first-child) {
            border-left-width: 1px;
            border-left-style: solid;
            border-left-color: #00c2d3
        }

        .header-topbar .header-col>*:last-child {
            border-right-width: 1px;
            border-right-style: solid;
            border-right-color: #00c2d3
        }

        .header-topbar {
            max-height: 42px
        }

        .header-topbar .header-col>* {
            line-height: 40px
        }

        .header-main {
            color: #555
        }

        .header-main a,
        .header-main .header-mini-search a {
            color: #333
        }

        .header-main a:hover,
        .header-main .header-mini-search a:hover {
            color: #006eed
        }

        .header-main .kapee-ajax-search .searchform {
            border-top-width: 1px;
            border-bottom-width: 1px;
            border-left-width: 1px;
            border-right-width: 1px;
            border-style: solid;
            border-color: #e9e9e9
        }

        .header-main {
            min-height: 100px
        }

        .header-main .search-field,
        .header-main .search-categories>select {
            color: #555
        }

        .header-main .searchform,
        .header-main .search-field,
        .header-main .search-categories>select {
            background-color: #fff
        }

        .header-main ::-webkit-input-placeholder {
            color: #555
        }

        .header-main :-moz-placeholder {
            color: #555
        }

        .header-main ::-moz-placeholder {
            color: #555
        }

        .header-main :-ms-input-placeholder {
            color: #555
        }

        .header-navigation {
            color: #555
        }

        .header-navigation a {
            color: #333
        }

        .header-navigation a:hover {
            color: #2370F4
        }

        .header-navigation .kapee-ajax-search .searchform {
            border-top-width: 1px;
            border-bottom-width: 1px;
            border-left-width: 1px;
            border-right-width: 1px;
            border-style: solid;
            border-color: #e9e9e9
        }

        .header-navigation {
            border-top-width: 1px;
            border-top-style: solid;
            border-top-color: #e9e9e9
        }

        .header-navigation {
            border-bottom-width: 1px;
            border-bottom-style: solid;
            border-bottom-color: #e9e9e9
        }

        .header-navigation,
        .header-navigation .main-navigation ul.menu>li>a {
            min-height: 50px
        }

        .header-navigation .categories-menu-title {
            height: 52px
        }

        .header-navigation ::-webkit-input-placeholder {
            color: #555
        }

        .header-navigation :-moz-placeholder {
            color: #555
        }

        .header-navigation ::-moz-placeholder {
            color: #555
        }

        .header-navigation :-ms-input-placeholder {
            color: #555
        }

        .header-sticky {
            color: #555
        }

        .header-sticky a {
            color: #333
        }

        .header-sticky a:hover {
            color: #2370f4
        }

        .header-sticky .kapee-ajax-search .searchform {
            border-top-width: 1px;
            border-bottom-width: 1px;
            border-left-width: 1px;
            border-right-width: 1px;
            border-style: solid;
            border-color: #e9e9e9
        }

        .header-sticky,
        .header-sticky .main-navigation ul.menu>li>a {
            min-height: 56px
        }

        .header-sticky .categories-menu-title {
            line-height: 56px
        }

        .header-sticky .search-field,
        .header-main .search-categories>select {
            color: #555
        }

        .header-sticky .searchform,
        .header-sticky .search-field,
        .header-sticky .search-categories>select {
            background-color: #fff
        }

        .header-sticky ::-webkit-input-placeholder {
            color: #555
        }

        .header-sticky :-moz-placeholder {
            color: #555
        }

        .header-sticky ::-moz-placeholder {
            color: #555
        }

        .header-sticky :-ms-input-placeholder {
            color: #555
        }

        .main-navigation ul.menu>li>a {
            color: #333
        }

        .main-navigation ul.menu>li:hover>a {
            color: #2370F4
        }

        .main-navigation ul.menu>li:hover>a {
            background-color: transparent
        }

        .header-sticky .main-navigation ul.menu>li>a {
            color: #333
        }

        .header-sticky .main-navigation ul.menu>li:hover>a {
            color: #2370F4
        }

        .header-sticky .main-navigation ul.menu>li:hover>a {
            background-color: transparent
        }

        .categories-menu-title {
            background-color: #2370F4;
            color: #fff
        }

        .categories-menu {
            background-color: #fff
        }

        .categories-menu ul.menu>li>a {
            color: #333
        }

        .categories-menu ul.menu>li:hover>a {
            color: #2370F4
        }

        .categories-menu ul.menu>li:hover>a {
            background-color: #F5FAFF
        }

        .categories-menu {
            border-top-width: 1px;
            border-bottom-width: 1px;
            border-left-width: 1px;
            border-right-width: 1px;
            border-style: solid;
            border-color: #e9e9e9
        }

        .categories-menu ul.menu>li:not(:last-child) {
            border-bottom-width: 1px;
            border-bottom-style: solid;
            border-bottom-color: #e9e9e9
        }

        .site-header ul.menu ul.sub-menu a,
        .kapee-megamenu-wrapper a.nav-link {
            color: #333
        }

        .site-header ul.menu ul.sub-menu>li:hover>a,
        .kapee-megamenu-wrapper li.menu-item a:hover {
            color: #2370F4;
            background-color: #F5FAFF
        }

        #page-title {
            padding-top: 50pxpx;
            padding-bottom: 50pxpx
        }

        .footer-main,
        .site-footer .caption {
            color: #f1f1f1
        }

        .site-footer .widget-title {
            color: #fff
        }

        .footer-main a,
        .footer-main label,
        .footer-main thead th {
            color: #fff
        }

        .footer-main a:hover {
            color: #f1f1f1
        }

        .site-footer text,
        .site-footer select,
        .site-footer textarea,
        .site-footer number {
            color: #555;
            background-color: #fff
        }

        .site-footer .mc4wp-form-fields p:first-child::before {
            color: #555
        }

        .site-footer ::-webkit-input-placeholder {
            color: #555
        }

        .site-footer :-moz-placeholder {
            color: #555
        }

        .site-footer ::-moz-placeholder {
            color: #555
        }

        .site-footer :-ms-input-placeholder {
            color: #555
        }

        .footer-copyright {
            color: #f1f1f1
        }

        .footer-copyright a {
            color: #fff
        }

        .footer-copyright a:hover {
            color: #f1f1f1
        }

        .footer-copyright {
            border-top-width: 1px;
            border-top-style: solid;
            border-top-color: #454d5e
        }

        .woocommerce ul.cart_list li .product-title,
        .woocommerce ul.product_list_widget li .product-title,
        .widget.widget_layered_nav li .nav-title,
        .products .product-cats,
        .products.grid-view .product-title,
        .kapee-bought-together-products .product-title,
        .products .woocommerce-loop-category__title {
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden
        }

        .product-labels span.on-sale {
            background-color: #388E3C
        }

        .product-labels span.new {
            background-color: #82B440
        }

        .product-labels span.featured {
            background-color: #ff9f00
        }

        .product-labels span.out-of-stock {
            background-color: #ff6161
        }

        .kapee-newsletter-popup input[type="submit"] {
            color: #fff;
            background-color: #2370F4
        }

        .kapee-newsletter-popup input[type="submit"]:hover {
            color: #fff;
            background-color: #2370F4
        }

        @media (max-width:991px) {

            .site-header .header-main,
            .site-header .header-navigation,
            .site-header .header-sticky {
                color: #FFF;
                background-color: #000
            }

            .ajax-search-style-1 .search-submit,
            .ajax-search-style-2 .search-submit,
            .ajax-search-style-3 .search-submit,
            .ajax-search-style-4 .search-submit,
            .header-cart-icon .header-cart-count,
            .header-wishlist-icon .header-wishlist-count {
                color: #000;
                background-color: #FFF
            }

            .header-main a,
            .header-navigation a,
            .header-sticky a {
                color: #FFF
            }

            .header-main a:hover,
            .header-navigation a:hover,
            .header-sticky a:hover {
                color: #FFF
            }

            .site-header .header-main,
            .site-header .header-navigation,
            .site-header .header-sticky {
                border-color: #000
            }

            .woocommerce div.summary .price-summary .price-summary-header,
            .woocommerce div.summary .product-term-detail .terms-header,
            .tabs-layout .tab-content-wrap:last-child {
                border-bottom-width: 1px;
                border-bottom-style: solid;
                border-bottom-color: #e9e9e9
            }

            .tabs-layout .tab-content-wrap {
                border-top-width: 1px;
                border-top-style: solid;
                border-top-color: #e9e9e9
            }

            .site-header text,
            .site-header select,
            .site-header textarea,
            .site-header number,
            .site-header input[type="search"],
            .header-sticky .search-categories>select,
            .site-header .product_cat {
                color: #555;
                background-color: #fff
            }

            .site-header ::-webkit-input-placeholder {
                color: #555
            }

            .site-header :-moz-placeholder {
                color: #555
            }

            .site-header ::-moz-placeholder {
                color: #555
            }

            .site-header :-ms-input-placeholder {
                color: #555
            }
        }

        @media (max-width:767px) {
            .widget-area {
                background-color: #fff
            }

            .single-product-page>.kapee-bought-together-products .items-total-price-button,
            .single-product-page .woocommerce-tabs .kapee-bought-together-products .items-total-price-button {
                border-top-width: 1px;
                border-top-style: solid;
                border-top-color: #e9e9e9
            }

            .products-and-categories-box .section-categories,
            .woocommerce-cart table.cart tr {
                border-bottom-width: 1px;
                border-bottom-style: solid;
                border-bottom-color: #e9e9e9
            }

            .nav-subtitle {
                color: #333
            }
        }

        @media (max-width:576px) {
            .mfp-close-btn-in .mfp-close {
                color: #FFF
            }
        }

        [data-vc-full-width] {
            width: 100vw;
            left: -2.5vw
        }

        @media (min-width:1990px) {
            [data-vc-full-width] {
                left: calc((-100vw - -1920px) / 2)
            }

            [data-vc-full-width]:not([data-vc-stretch-content]) {
                padding-left: calc((100vw - 1920px) / 2);
                padding-right: calc((100vw - 1920px) / 2)
            }
        }
    </style>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="">
    <link rel="stylesheet" type="text/css"
        href="/wp-content/cache/busting/1/wp-content/plugins/smart-slider-3/Public/SmartSlider3/Application/Frontend/Assets/dist/smartslider.min-4e06d1a7.css"
        media="all">
    <style data-related="n2-ss-1">
        div#n2-ss-1 .n2-ss-slider-1 {
            display: grid;
            position: relative
        }

        div#n2-ss-1 .n2-ss-slider-2 {
            display: grid;
            position: relative;
            overflow: hidden;
            padding: 0 0 0 0;
            border: 0 solid RGBA(62, 62, 62, 1);
            border-radius: 0;
            background-clip: padding-box;
            background-repeat: repeat;
            background-position: 50% 50%;
            background-size: cover;
            background-attachment: scroll;
            z-index: 1
        }

        div#n2-ss-1:not(.n2-ss-loaded) .n2-ss-slider-2 {
            background-image: none !important
        }

        div#n2-ss-1 .n2-ss-slider-3 {
            display: grid;
            grid-template-areas: 'cover';
            position: relative;
            overflow: hidden;
            z-index: 10
        }

        div#n2-ss-1 .n2-ss-slider-3>* {
            grid-area: cover
        }

        div#n2-ss-1 .n2-ss-slide-backgrounds,
        div#n2-ss-1 .n2-ss-slider-3>.n2-ss-divider {
            position: relative
        }

        div#n2-ss-1 .n2-ss-slide-backgrounds {
            z-index: 10
        }

        div#n2-ss-1 .n2-ss-slide-backgrounds>* {
            overflow: hidden
        }

        div#n2-ss-1 .n2-ss-slide-background {
            transform: translateX(-100000px)
        }

        div#n2-ss-1 .n2-ss-slider-4 {
            place-self: center;
            position: relative;
            width: 100%;
            height: 100%;
            z-index: 20;
            display: grid;
            grid-template-areas: 'slide'
        }

        div#n2-ss-1 .n2-ss-slider-4>* {
            grid-area: slide
        }

        div#n2-ss-1.n2-ss-full-page--constrain-ratio .n2-ss-slider-4 {
            height: auto
        }

        div#n2-ss-1 .n2-ss-slide {
            display: grid;
            place-items: center;
            grid-auto-columns: 100%;
            position: relative;
            z-index: 20;
            -webkit-backface-visibility: hidden;
            transform: translateX(-100000px)
        }

        div#n2-ss-1 .n2-ss-slide {
            perspective: 1500px
        }

        div#n2-ss-1 .n2-ss-slide-active {
            z-index: 21
        }

        .n2-ss-background-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 3
        }

        div#n2-ss-1 .n2-ss-background-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 3
        }

        div#n2-ss-1 .n2-ss-background-animation .n2-ss-slide-background {
            z-index: auto
        }

        div#n2-ss-1 .n2-bganim-side {
            position: absolute;
            left: 0;
            top: 0;
            overflow: hidden;
            background: RGBA(51, 51, 51, 1)
        }

        div#n2-ss-1 .n2-bganim-tile-overlay-colored {
            z-index: 100000;
            background: RGBA(51, 51, 51, 1)
        }

        div#n2-ss-1 .nextend-arrow {
            cursor: pointer;
            overflow: hidden;
            line-height: 0 !important;
            z-index: 18;
            -webkit-user-select: none
        }

        div#n2-ss-1 .nextend-arrow img {
            position: relative;
            display: block
        }

        div#n2-ss-1 .nextend-arrow img.n2-arrow-hover-img {
            display: none
        }

        div#n2-ss-1 .nextend-arrow:FOCUS img.n2-arrow-hover-img,
        div#n2-ss-1 .nextend-arrow:HOVER img.n2-arrow-hover-img {
            display: inline
        }

        div#n2-ss-1 .nextend-arrow:FOCUS img.n2-arrow-normal-img,
        div#n2-ss-1 .nextend-arrow:HOVER img.n2-arrow-normal-img {
            display: none
        }

        div#n2-ss-1 .nextend-arrow-animated {
            overflow: hidden
        }

        div#n2-ss-1 .nextend-arrow-animated>div {
            position: relative
        }

        div#n2-ss-1 .nextend-arrow-animated .n2-active {
            position: absolute
        }

        div#n2-ss-1 .nextend-arrow-animated-fade {
            transition: background 0.3s, opacity 0.4s
        }

        div#n2-ss-1 .nextend-arrow-animated-horizontal>div {
            transition: all 0.4s;
            transform: none
        }

        div#n2-ss-1 .nextend-arrow-animated-horizontal .n2-active {
            top: 0
        }

        div#n2-ss-1 .nextend-arrow-previous.nextend-arrow-animated-horizontal .n2-active {
            left: 100%
        }

        div#n2-ss-1 .nextend-arrow-next.nextend-arrow-animated-horizontal .n2-active {
            right: 100%
        }

        div#n2-ss-1 .nextend-arrow-previous.nextend-arrow-animated-horizontal:HOVER>div,
        div#n2-ss-1 .nextend-arrow-previous.nextend-arrow-animated-horizontal:FOCUS>div {
            transform: translateX(-100%)
        }

        div#n2-ss-1 .nextend-arrow-next.nextend-arrow-animated-horizontal:HOVER>div,
        div#n2-ss-1 .nextend-arrow-next.nextend-arrow-animated-horizontal:FOCUS>div {
            transform: translateX(100%)
        }

        div#n2-ss-1 .nextend-arrow-animated-vertical>div {
            transition: all 0.4s;
            transform: none
        }

        div#n2-ss-1 .nextend-arrow-animated-vertical .n2-active {
            left: 0
        }

        div#n2-ss-1 .nextend-arrow-previous.nextend-arrow-animated-vertical .n2-active {
            top: 100%
        }

        div#n2-ss-1 .nextend-arrow-next.nextend-arrow-animated-vertical .n2-active {
            bottom: 100%
        }

        div#n2-ss-1 .nextend-arrow-previous.nextend-arrow-animated-vertical:HOVER>div,
        div#n2-ss-1 .nextend-arrow-previous.nextend-arrow-animated-vertical:FOCUS>div {
            transform: translateY(-100%)
        }

        div#n2-ss-1 .nextend-arrow-next.nextend-arrow-animated-vertical:HOVER>div,
        div#n2-ss-1 .nextend-arrow-next.nextend-arrow-animated-vertical:FOCUS>div {
            transform: translateY(100%)
        }

        div#n2-ss-1 .n-uc-Lu39nDKkTyIB {
            padding: 0 10px 0 0
        }

        div#n2-ss-1 .n-uc-Y9DnUjJpc1ni {
            padding: 10px 10px 10px 10px
        }

        div#n2-ss-1 .n-uc-wHKZPTEdvu34 {
            padding: 10px 10px 10px 10px
        }

        div#n2-ss-1 .n-uc-1h5BS2yOO5n8 {
            padding: 10px 10px 10px 10px
        }

        div#n2-ss-1 .n-uc-y2qdUZyE04hQ {
            padding: 10px 10px 10px 10px
        }

        div#n2-ss-1 .n-uc-ogqIdxVYVfeT {
            padding: 10px 10px 10px 10px
        }

        div#n2-ss-1 .nextend-arrow img {
            width: 32px
        }

        @media (min-width:1200px) {
            div#n2-ss-1 [data-hide-desktopportrait="1"] {
                display: none !important
            }
        }

        @media (orientation:landscape) and (max-width:1199px) and (min-width:901px),
        (orientation:portrait) and (max-width:1199px) and (min-width:701px) {
            div#n2-ss-1 [data-hide-tabletportrait="1"] {
                display: none !important
            }
        }

        @media (orientation:landscape) and (max-width:900px),
        (orientation:portrait) and (max-width:700px) {
            div#n2-ss-1 [data-hide-mobileportrait="1"] {
                display: none !important
            }

            div#n2-ss-1 .nextend-arrow img {
                width: 16px
            }
        }
    </style>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-includes/js/jquery/jquery.min-3.7.1.js"
        id="jquery-core-js"></script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-includes/js/jquery/jquery-migrate.min-3.4.1.js"
        id="jquery-migrate-js"></script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/revslider/public/assets/js/rbtools.min-6.3.1.js"
        id="tp-tools-js"></script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/revslider/public/assets/js/rs6.min-6.3.1.js"
        id="revmin-js"></script>
    <script type="text/javascript"
        src="/wp-content/cache/busting/1/wp-content/plugins/woocommerce/assets/js/jquery-blockui/jquery.blockUI.min-2.7.0-wc.6.4.0.js"
        id="jquery-blockui-js"></script>
    
    <script>
        document.documentElement.className = document.documentElement.className.replace('no-js', 'js');
    </script>
    <style>
        .no-js img.lazyload {
            display: none
        }

        figure.wp-block-image img.lazyloading {
            min-width: 150px
        }

        .lazyload,
        .lazyloading {
            opacity: 0
        }

        .lazyloaded {
            opacity: 1;
            transition: opacity 400ms;
            transition-delay: 0ms
        }
    </style><noscript>
        <style>
            .woocommerce-product-gallery {
                opacity: 1 !important
            }
        </style>
    </noscript>
    <meta name="generator"
        content="Elementor 3.14.1; features: e_dom_optimization, e_optimized_assets_loading, e_optimized_css_loading, e_font_icon_svg, a11y_improvements, additional_custom_breakpoints; settings: css_print_method-external, google_font-enabled, font_display-auto">
    <meta name="generator" content="Powered by WPBakery Page Builder - drag and drop page builder for WordPress.">
    <meta name="generator"
        content="Powered by Slider Revolution 6.3.1 - responsive, Mobile-Friendly Slider Plugin for WordPress with comfortable drag and drop interface.">
    <script type="text/javascript">
        function setREVStartSize(e) {
            //window.requestAnimationFrame(function() {				 
            window.RSIW = window.RSIW === undefined ? window.innerWidth : window.RSIW;
            window.RSIH = window.RSIH === undefined ? window.innerHeight : window.RSIH;
            try {
                var pw = document.getElementById(e.c).parentNode.offsetWidth,
                    newh;
                pw = pw === 0 || isNaN(pw) ? window.RSIW : pw;
                e.tabw = e.tabw === undefined ? 0 : parseInt(e.tabw);
                e.thumbw = e.thumbw === undefined ? 0 : parseInt(e.thumbw);
                e.tabh = e.tabh === undefined ? 0 : parseInt(e.tabh);
                e.thumbh = e.thumbh === undefined ? 0 : parseInt(e.thumbh);
                e.tabhide = e.tabhide === undefined ? 0 : parseInt(e.tabhide);
                e.thumbhide = e.thumbhide === undefined ? 0 : parseInt(e.thumbhide);
                e.mh = e.mh === undefined || e.mh == "" || e.mh === "auto" ? 0 : parseInt(e.mh, 0);
                if (e.layout === "fullscreen" || e.l === "fullscreen")
                    newh = Math.max(e.mh, window.RSIH);
                else {
                    e.gw = Array.isArray(e.gw) ? e.gw : [e.gw];
                    for (var i in e.rl)
                        if (e.gw[i] === undefined || e.gw[i] === 0) e.gw[i] = e.gw[i - 1];
                    e.gh = e.el === undefined || e.el === "" || (Array.isArray(e.el) && e.el.length == 0) ? e.gh : e.el;
                    e.gh = Array.isArray(e.gh) ? e.gh : [e.gh];
                    for (var i in e.rl)
                        if (e.gh[i] === undefined || e.gh[i] === 0) e.gh[i] = e.gh[i - 1];

                    var nl = new Array(e.rl.length),
                        ix = 0,
                        sl;
                    e.tabw = e.tabhide >= pw ? 0 : e.tabw;
                    e.thumbw = e.thumbhide >= pw ? 0 : e.thumbw;
                    e.tabh = e.tabhide >= pw ? 0 : e.tabh;
                    e.thumbh = e.thumbhide >= pw ? 0 : e.thumbh;
                    for (var i in e.rl) nl[i] = e.rl[i] < window.RSIW ? 0 : e.rl[i];
                    sl = nl[0];
                    for (var i in nl)
                        if (sl > nl[i] && nl[i] > 0) {
                            sl = nl[i];
                            ix = i;
                        }
                    var m = pw > (e.gw[ix] + e.tabw + e.thumbw) ? 1 : (pw - (e.tabw + e.thumbw)) / (e.gw[ix]);
                    newh = (e.gh[ix] * m) + (e.tabh + e.thumbh);
                }
                if (window.rs_init_css === undefined) window.rs_init_css = document.head.appendChild(document.createElement(
                    "style"));
                document.getElementById(e.c).height = newh + "px";
                window.rs_init_css.innerHTML += "#" + e.c + "_wrapper { height: " + newh + "px }";
            } catch (e) {
                console.log("Failure at Presize of Slider:" + e)
            }
            //});
        };
    </script>
    <style type="text/css" id="wp-custom-css">
        .kapee-contact-us:first-child {
            margin-bottom: 10px
        }

        .header-myaccount {
            display: none
        }

        .search-results-wrapper .search-price {
            display: none
        }
    </style>
    <style id="kapee_options-dynamic-css" title="dynamic-css" class="redux-options-output">
        body {
            font-family: Roboto, Arial, Helvetica, sans-serif;
            font-weight: 400;
            font-style: normal;
            font-size: 14px;
            font-display: swap
        }

        p {
            font-family: Lato, Arial, Helvetica, sans-serif;
            font-weight: 400;
            font-style: normal;
            font-size: 14px;
            font-display: swap
        }

        h1,
        .h1 {
            font-family: Roboto, Arial, Helvetica, sans-serif;
            text-transform: inherit;
            font-weight: 700;
            font-style: normal;
            color: #333;
            font-size: 28px;
            font-display: swap
        }

        h2,
        .h2 {
            font-family: Roboto, Arial, Helvetica, sans-serif;
            text-transform: inherit;
            font-weight: 700;
            font-style: normal;
            color: #333;
            font-size: 26px;
            font-display: swap
        }

        h3,
        .h3 {
            font-family: Roboto, Arial, Helvetica, sans-serif;
            text-transform: inherit;
            font-weight: 700;
            font-style: normal;
            color: #333;
            font-size: 24px;
            font-display: swap
        }

        h4,
        .h4 {
            font-family: Roboto, Arial, Helvetica, sans-serif;
            text-transform: inherit;
            font-weight: 700;
            font-style: normal;
            color: #333;
            font-size: 20px;
            font-display: swap
        }

        h5,
        .h5 {
            font-family: Roboto, Arial, Helvetica, sans-serif;
            text-transform: inherit;
            font-weight: 700;
            font-style: normal;
            color: #333;
            font-size: 16px;
            font-display: swap
        }

        h6,
        .h6 {
            font-family: Roboto, Arial, Helvetica, sans-serif;
            text-transform: inherit;
            font-weight: 700;
            font-style: normal;
            color: #333;
            font-size: 14px;
            font-display: swap
        }

        .main-navigation ul.menu>li>a {
            font-family: Arial, Helvetica, sans-serif, Arial, Helvetica, sans-serif;
            text-transform: uppercase;
            letter-spacing: 0;
            font-weight: 400;
            font-style: normal;
            font-size: 16px;
            font-display: swap
        }

        .categories-menu ul.menu>li>a {
            font-family: Arial, Helvetica, sans-serif, Arial, Helvetica, sans-serif;
            text-transform: uppercase;
            letter-spacing: 0;
            font-weight: 700;
            font-style: normal;
            font-size: 14px;
            font-display: swap
        }

        body {
            background-color: #fff
        }

        .site-wrapper {
            background-color: #fff
        }

        .header-topbar {
            background-color: #1ca9c9
        }

        .header-main {
            background-color: #fff
        }

        .header-sticky {
            background-color: #fff
        }

        .header-navigation {
            background-color: #fff
        }

        .kapee-navigation ul.menu ul.sub-menu,
        .kapee-navigation .kapee-megamenu-wrapper {
            background-color: #fff
        }

        #page-title {
            background-color: #f8f8f8;
            background-position: center center;
            background-size: cover
        }

        .site-footer .footer-main {
            background-color: #172337
        }

        .site-footer .footer-copyright {
            background-color: #172337
        }
    </style><noscript>
        <style>
            .wpb_animate_when_almost_visible {
                opacity: 1
            }
        </style>
    </noscript>
    <script>
        (function() {
            this._N2 = this._N2 || {
                _r: [],
                _d: [],
                r: function() {
                    this._r.push(arguments)
                },
                d: function() {
                    this._d.push(arguments)
                }
            }
        }).call(window);
        ! function(a) {
            a.indexOf("Safari") > 0 && -1 === a.indexOf("Chrome") && document.documentElement.style.setProperty(
                "--ss-safari-fix-225962", "1px")
        }(navigator.userAgent);
    </script>
    <script
        src="/wp-content/cache/busting/1/wp-content/plugins/smart-slider-3/Public/SmartSlider3/Application/Frontend/Assets/dist/n2.min-4e06d1a7.js"
        defer="" async=""></script>
    <script
        src="/wp-content/cache/busting/1/wp-content/plugins/smart-slider-3/Public/SmartSlider3/Application/Frontend/Assets/dist/smartslider-frontend.min-4e06d1a7.js"
        defer="" async=""></script>
    <script
        src="/wp-content/cache/busting/1/wp-content/plugins/smart-slider-3/Public/SmartSlider3/Slider/SliderType/Simple/Assets/dist/ss-simple.min-4e06d1a7.js"
        defer="" async=""></script>
    <script
        src="/wp-content/cache/busting/1/wp-content/plugins/smart-slider-3/Public/SmartSlider3/Slider/SliderType/Simple/Assets/dist/smartslider-backgroundanimation.min-4e06d1a7.js"
        defer="" async=""></script>
    <script
        src="/wp-content/cache/busting/1/wp-content/plugins/smart-slider-3/Public/SmartSlider3/Widget/Arrow/ArrowImage/Assets/dist/w-arrow-image.min-4e06d1a7.js"
        defer="" async=""></script>
    <script>
        _N2.r('documentReady', function() {
            _N2.r(["documentReady", "smartslider-frontend", "smartslider-backgroundanimation",
                "SmartSliderWidgetArrowImage", "ss-simple"
            ], function() {
                new _N2.SmartSliderSimple('n2-ss-1', {
                    "admin": false,
                    "background.video.mobile": 1,
                    "loadingTime": 1000,
                    "alias": {
                        "id": 1,
                        "smoothScroll": 1,
                        "slideSwitch": 1,
                        "scroll": 1
                    },
                    "align": "normal",
                    "isDelayed": 0,
                    "responsive": {
                        "mediaQueries": {
                            "all": false,
                            "desktopportrait": ["(min-width: 1200px)"],
                            "tabletportrait": [
                                "(orientation: landscape) and (max-width: 1199px) and (min-width: 901px)",
                                "(orientation: portrait) and (max-width: 1199px) and (min-width: 701px)"
                            ],
                            "mobileportrait": ["(orientation: landscape) and (max-width: 900px)",
                                "(orientation: portrait) and (max-width: 700px)"
                            ]
                        },
                        "base": {
                            "slideOuterWidth": 1200,
                            "slideOuterHeight": 600,
                            "sliderWidth": 1200,
                            "sliderHeight": 600,
                            "slideWidth": 1200,
                            "slideHeight": 600
                        },
                        "hideOn": {
                            "desktopLandscape": false,
                            "desktopPortrait": false,
                            "tabletLandscape": false,
                            "tabletPortrait": false,
                            "mobileLandscape": false,
                            "mobilePortrait": false
                        },
                        "onResizeEnabled": true,
                        "type": "fullwidth",
                        "sliderHeightBasedOn": "real",
                        "focusUser": 1,
                        "focusEdge": "auto",
                        "breakpoints": [{
                            "device": "tabletPortrait",
                            "type": "max-screen-width",
                            "portraitWidth": 1199,
                            "landscapeWidth": 1199
                        }, {
                            "device": "mobilePortrait",
                            "type": "max-screen-width",
                            "portraitWidth": 700,
                            "landscapeWidth": 900
                        }],
                        "enabledDevices": {
                            "desktopLandscape": 0,
                            "desktopPortrait": 1,
                            "tabletLandscape": 0,
                            "tabletPortrait": 1,
                            "mobileLandscape": 0,
                            "mobilePortrait": 1
                        },
                        "sizes": {
                            "desktopPortrait": {
                                "width": 1200,
                                "height": 600,
                                "max": 3000,
                                "min": 1200
                            },
                            "tabletPortrait": {
                                "width": 701,
                                "height": 350,
                                "customHeight": false,
                                "max": 1199,
                                "min": 701
                            },
                            "mobilePortrait": {
                                "width": 320,
                                "height": 160,
                                "customHeight": false,
                                "max": 900,
                                "min": 320
                            }
                        },
                        "overflowHiddenPage": 0,
                        "focus": {
                            "offsetTop": "#wpadminbar",
                            "offsetBottom": ""
                        }
                    },
                    "controls": {
                        "mousewheel": 0,
                        "touch": "vertical",
                        "keyboard": 1,
                        "blockCarouselInteraction": 1
                    },
                    "playWhenVisible": 1,
                    "playWhenVisibleAt": 0.5,
                    "lazyLoad": 0,
                    "lazyLoadNeighbor": 0,
                    "blockrightclick": 0,
                    "maintainSession": 0,
                    "autoplay": {
                        "enabled": 1,
                        "start": 1,
                        "duration": 8000,
                        "autoplayLoop": 1,
                        "allowReStart": 0,
                        "pause": {
                            "click": 1,
                            "mouse": "0",
                            "mediaStarted": 1
                        },
                        "resume": {
                            "click": 0,
                            "mouse": "0",
                            "mediaEnded": 1,
                            "slidechanged": 0
                        },
                        "interval": 1,
                        "intervalModifier": "loop",
                        "intervalSlide": "current"
                    },
                    "perspective": 1500,
                    "layerMode": {
                        "playOnce": 0,
                        "playFirstLayer": 1,
                        "mode": "skippable",
                        "inAnimation": "mainInEnd"
                    },
                    "bgAnimations": {
                        "global": [{
                            "type": "Flat",
                            "tiles": {
                                "delay": 0,
                                "sequence": "ForwardDiagonal"
                            },
                            "main": {
                                "type": "both",
                                "duration": 1,
                                "zIndex": 2,
                                "current": {
                                    "ease": "easeOutCubic",
                                    "opacity": 0
                                }
                            }
                        }],
                        "color": "RGBA(51,51,51,1)",
                        "speed": "superSlow",
                        "slides": [{
                            "animation": [{
                                "type": "Flat",
                                "rows": 1,
                                "columns": 25,
                                "tiles": {
                                    "delay": 0.03,
                                    "sequence": "BackwardCol"
                                },
                                "main": {
                                    "type": "next",
                                    "duration": 0.35,
                                    "next": {
                                        "ease": "easeInQuart",
                                        "opacity": "0",
                                        "xP": -100
                                    }
                                },
                                "invert": {
                                    "next": {
                                        "xP": 100
                                    }
                                },
                                "invertTiles": {
                                    "sequence": "ForwardCol"
                                },
                                "desktopOnly": true
                            }],
                            "speed": "superSlow",
                            "color": "RGBA(51,51,51,1)"
                        }, {
                            "animation": [{
                                "type": "Flat",
                                "rows": 1,
                                "columns": 25,
                                "tiles": {
                                    "delay": 0.03,
                                    "sequence": "BackwardCol"
                                },
                                "main": {
                                    "type": "next",
                                    "duration": 0.35,
                                    "next": {
                                        "ease": "easeInQuart",
                                        "opacity": "0",
                                        "xP": -100
                                    }
                                },
                                "invert": {
                                    "next": {
                                        "xP": 100
                                    }
                                },
                                "invertTiles": {
                                    "sequence": "ForwardCol"
                                },
                                "desktopOnly": true
                            }],
                            "speed": "superSlow",
                            "color": "RGBA(51,51,51,1)"
                        }, {
                            "animation": [{
                                "type": "Flat",
                                "rows": 1,
                                "columns": 25,
                                "tiles": {
                                    "delay": 0.03,
                                    "sequence": "BackwardCol"
                                },
                                "main": {
                                    "type": "next",
                                    "duration": 0.35,
                                    "next": {
                                        "ease": "easeInQuart",
                                        "opacity": "0",
                                        "xP": -100
                                    }
                                },
                                "invert": {
                                    "next": {
                                        "xP": 100
                                    }
                                },
                                "invertTiles": {
                                    "sequence": "ForwardCol"
                                },
                                "desktopOnly": true
                            }],
                            "speed": "superSlow",
                            "color": "RGBA(51,51,51,1)"
                        }, {
                            "animation": [{
                                "type": "Flat",
                                "rows": 1,
                                "columns": 25,
                                "tiles": {
                                    "delay": 0.03,
                                    "sequence": "BackwardCol"
                                },
                                "main": {
                                    "type": "next",
                                    "duration": 0.35,
                                    "next": {
                                        "ease": "easeInQuart",
                                        "opacity": "0",
                                        "xP": -100
                                    }
                                },
                                "invert": {
                                    "next": {
                                        "xP": 100
                                    }
                                },
                                "invertTiles": {
                                    "sequence": "ForwardCol"
                                },
                                "desktopOnly": true
                            }],
                            "speed": "superSlow",
                            "color": "RGBA(51,51,51,1)"
                        }, {
                            "animation": [{
                                "type": "Flat",
                                "rows": 1,
                                "columns": 25,
                                "tiles": {
                                    "delay": 0.03,
                                    "sequence": "BackwardCol"
                                },
                                "main": {
                                    "type": "next",
                                    "duration": 0.35,
                                    "next": {
                                        "ease": "easeInQuart",
                                        "opacity": "0",
                                        "xP": -100
                                    }
                                },
                                "invert": {
                                    "next": {
                                        "xP": 100
                                    }
                                },
                                "invertTiles": {
                                    "sequence": "ForwardCol"
                                },
                                "desktopOnly": true
                            }],
                            "speed": "superSlow",
                            "color": "RGBA(51,51,51,1)"
                        }, {
                            "animation": [{
                                "type": "Flat",
                                "tiles": {
                                    "delay": 0,
                                    "sequence": "ForwardDiagonal"
                                },
                                "main": {
                                    "type": "both",
                                    "duration": 1,
                                    "zIndex": 2,
                                    "current": {
                                        "ease": "easeOutCubic",
                                        "opacity": 0
                                    }
                                }
                            }],
                            "speed": "superSlow",
                            "color": "RGBA(51,51,51,1)"
                        }]
                    },
                    "mainanimation": {
                        "type": "fade",
                        "duration": 800,
                        "delay": 0,
                        "ease": "easeOutQuad",
                        "shiftedBackgroundAnimation": 0
                    },
                    "carousel": 1,
                    "initCallbacks": function() {
                        new _N2.SmartSliderWidgetArrowImage(this)
                    }
                })
            })
        });
    </script>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css?family=Montserrat&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat&amp;display=swap">
    <style id="custom-chaty-css">
        #chaty-widget-0 .Phone-channel .color-element {
            fill: #03E78B;
            color: #03E78B;
        }

        #chaty-widget-0 .channel-icon-Phone .color-element {
            fill: #03E78B;
            color: #03E78B;
        }

        #chaty-widget-0 .Phone-channel .chaty-custom-icon {
            background-color: #03E78B;
        }

        #chaty-widget-0 .Phone-channel .chaty-svg {
            background-color: #03E78B;
        }

        #chaty-widget-0 .channel-icon-Phone .chaty-svg {
            background-color: #03E78B;
        }

        .chaty-chat-view-0 .Phone-channel .chaty-custom-icon {
            background-color: #03E78B;
        }

        .chaty-chat-view-0 .Phone-channel .chaty-svg {
            background-color: #03E78B;
        }

        .chaty-chat-view-0 .channel-icon-Phone .chaty-svg {
            background-color: #03E78B;
        }

        #chaty-widget-0 .Facebook_Messenger-channel .color-element {
            fill: #1E88E5;
            color: #1E88E5;
        }

        #chaty-widget-0 .channel-icon-Facebook_Messenger .color-element {
            fill: #1E88E5;
            color: #1E88E5;
        }

        #chaty-widget-0 .Facebook_Messenger-channel .chaty-custom-icon {
            background-color: #1E88E5;
        }

        #chaty-widget-0 .Facebook_Messenger-channel .chaty-svg {
            background-color: #1E88E5;
        }

        #chaty-widget-0 .channel-icon-Facebook_Messenger .chaty-svg {
            background-color: #1E88E5;
        }

        .chaty-chat-view-0 .Facebook_Messenger-channel .chaty-custom-icon {
            background-color: #1E88E5;
        }

        .chaty-chat-view-0 .Facebook_Messenger-channel .chaty-svg {
            background-color: #1E88E5;
        }

        .chaty-chat-view-0 .channel-icon-Facebook_Messenger .chaty-svg {
            background-color: #1E88E5;
        }

        #chaty-widget-0 .Whatsapp-channel .color-element {
            fill: #49E670;
            color: #49E670;
        }

        #chaty-widget-0 .channel-icon-Whatsapp .color-element {
            fill: #49E670;
            color: #49E670;
        }

        #chaty-widget-0 .Whatsapp-channel .chaty-custom-icon {
            background-color: #49E670;
        }

        #chaty-widget-0 .Whatsapp-channel .chaty-svg {
            background-color: #49E670;
        }

        #chaty-widget-0 .channel-icon-Whatsapp .chaty-svg {
            background-color: #49E670;
        }

        .chaty-chat-view-0 .Whatsapp-channel .chaty-custom-icon {
            background-color: #49E670;
        }

        .chaty-chat-view-0 .Whatsapp-channel .chaty-svg {
            background-color: #49E670;
        }

        .chaty-chat-view-0 .channel-icon-Whatsapp .chaty-svg {
            background-color: #49E670;
        }

        #chaty-widget-0 .chaty-channel-list .chaty-channel {
            width: 45px;
            height: 45px;
        }

        #chaty-widget-0 .chaty-channel-list .chaty-channel>a {
            width: 45px;
            height: 45px;
        }

        #chaty-widget-0 .chaty-channel-list .chaty-channel>a .chaty-custom-icon {
            display: block;
            width: 45px;
            height: 45px;
            line-height: 45px;
            font-size: 22px;
        }

        #chaty-widget-0 .chaty-channel-list .chaty-channel button {
            width: 45px;
            height: 45px;
            margin: 0;
            padding: 0;
            outline: none;
            border-radius: 50%;
        }

        #chaty-widget-0 .chaty-channel-list .chaty-channel .chaty-svg {
            width: 45px;
            height: 45px;
        }

        #chaty-widget-0 .chaty-channel-list .chaty-channel .chaty-svg img {
            width: 45px;
            height: 45px;
        }

        #chaty-widget-0 .chaty-channel-list .chaty-channel span.chaty-icon {
            width: 45px;
            height: 45px;
        }

        #chaty-widget-0 .chaty-channel-list .chaty-channel a {
            width: 45px;
            height: 45px;
        }

        #chaty-widget-0 .chaty-channel-list .chaty-channel .chaty-svg .chaty-custom-channel-icon {
            width: 45px;
            height: 45px;
            line-height: 45px;
            display: block;
            font-size: 22px;
        }

        #chaty-widget-0 .chaty-i-trigger .chaty-cta-button {
            background-color: #4F6ACA;
        }

        #chaty-widget-0 .chaty-i-trigger .chaty-cta-button button {
            background-color: #4F6ACA;
        }

        #chaty-widget-0 .chaty-i-trigger .chaty-channel {
            width: 45px;
            height: 45px;
        }

        #chaty-widget-0 .chaty-i-trigger .chaty-channel>a {
            width: 45px;
            height: 45px;
        }

        #chaty-widget-0 .chaty-i-trigger .chaty-channel>a .chaty-custom-icon {
            display: block;
            width: 45px;
            height: 45px;
            line-height: 45px;
            font-size: 22px;
        }

        #chaty-widget-0 .chaty-i-trigger .chaty-channel button {
            width: 45px;
            height: 45px;
            margin: 0;
            padding: 0;
            outline: none;
            border-radius: 50%;
        }

        #chaty-widget-0 .chaty-i-trigger .chaty-channel .chaty-svg {
            width: 45px;
            height: 45px;
        }

        #chaty-widget-0 .chaty-i-trigger .chaty-channel .chaty-svg img {
            width: 45px;
            height: 45px;
        }

        #chaty-widget-0 .chaty-i-trigger .chaty-channel span.chaty-icon {
            width: 45px;
            height: 45px;
        }

        #chaty-widget-0 .chaty-i-trigger .chaty-channel a {
            width: 45px;
            height: 45px;
        }

        #chaty-widget-0 .chaty-i-trigger .chaty-channel .chaty-svg .chaty-custom-channel-icon {
            width: 45px;
            height: 45px;
            line-height: 45px;
            display: block;
            font-size: 22px;
        }

        #chaty-widget-0 .chaty-i-trigger .ch-pending-msg {
            background-color: #dd0000;
            color: #ffffff;
        }

        #chaty-widget-0 .chaty-channel-list {
            height: 159px;
        }

        #chaty-widget-0 .chaty-channel-list {
            width: 53px;
        }

        #chaty-widget-0 .chaty-open .chaty-channel-list .chaty-channel:nth-child(1) {
            -webkit-transform: translateY(-159px);
            transform: translateY(-159px);
        }

        #chaty-widget-0 .chaty-open .chaty-channel-list .chaty-channel:nth-child(2) {
            -webkit-transform: translateY(-106px);
            transform: translateY(-106px);
        }

        #chaty-widget-0 .chaty-open .chaty-channel-list .chaty-channel:nth-child(3) {
            -webkit-transform: translateY(-53px);
            transform: translateY(-53px);
        }

        #chaty-widget-0 .chaty-open .chaty-channel-list .chaty-channel:nth-child(4) {
            -webkit-transform: translateY(-0px);
            transform: translateY(-0px);
        }

        #chaty-widget-0 .chaty-widget {
            bottom: 25px
        }

        #chaty-widget-0 .chaty-widget {
            right: 25px;
            left: auto;
        }

        .chaty-outer-forms.pos-right.chaty-form-0 {
            right: 25px;
            left: auto;
        }

        .chaty-outer-forms.active.chaty-form-0 {
            -webkit-transform: translateY(-85px);
            transform: translateY(-85px)
        }

        #chaty-widget-0.chaty:not(.form-open) .chaty-widget.chaty-open+.chaty-chat-view {
            -webkit-transform: translateY(-85px);
            transform: translateY(-85px)
        }

        #chaty-widget-0 .chaty-tooltip:after {
            background-color: rgb(68, 0, 0);
            color: rgb(255, 255, 255)
        }

        #chaty-widget-0 .chaty-tooltip.pos-top:before {
            border-top-color: rgb(68, 0, 0);
        }

        #chaty-widget-0 .chaty-tooltip.pos-left:before {
            border-left-color: rgb(68, 0, 0);
        }

        #chaty-widget-0 .chaty-tooltip.pos-right:before {
            border-right-color: rgb(68, 0, 0);
        }

        #chaty-widget-0 .on-hover-text {
            background-color: rgb(68, 0, 0);
            color: rgb(255, 255, 255)
        }

        #chaty-widget-0 .chaty-tooltip.pos-top .on-hover-text:before {
            border-top-color: rgb(68, 0, 0);
        }

        #chaty-widget-0 .chaty-tooltip.pos-left .on-hover-text:before {
            border-left-color: rgb(68, 0, 0);
        }

        #chaty-widget-0 .chaty-tooltip.pos-right .on-hover-text:before {
            border-right-color: rgb(68, 0, 0);
        }

        .chaty-outer-forms.chaty-form-0 .chaty-agent-body {
            max-height: calc(100vh - 202px);
            overflow-y: auto;
        }

        #chaty-form-0-chaty-chat-view .chaty-view-header {
            background-color: ;
        }

        #chaty-form-0-chaty-chat-view .chaty-view-header {
            color: ;
        }

        #chaty-form-0-chaty-chat-view .chaty-view-header svg {
            fill: ;
        }

        .chaty-outer-forms.chaty-whatsapp-form.chaty-form-0 .chaty-whatsapp-content {
            max-height: calc(100vh - 202px);
            overflow-y: auto;
        }

        .chaty-outer-forms.chaty-contact-form-box.chaty-form-0 .chaty-contact-inputs {
            max-height: calc(100vh - 212px);
            overflow-y: auto;
        }

        #chaty-widget-0,
        #chaty-widget-0 .chaty-tooltip:after {
            font-family: Montserrat
        }
    </style>
    <style id="custom-advance-chaty-css"></style>
    @stack('styles')
    @livewireStyles
    <script>
        jQuery(document).ready(function() {
            $('.block-slideshow .owl-carousel').owlCarousel({
                items: 1,
                nav: true,
                dots: true,
                loop: true,
                autoplay: true,
                autoplayTimeout: 3000,
                autoplaySpeed: 500,
                autoplayHoverPause: true,
            });
        });
    </script>
</head>
