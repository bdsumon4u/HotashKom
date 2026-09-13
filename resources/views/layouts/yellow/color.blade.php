<style>
    @php
        $color = optional($color ?? null);

        $primaryBg = $color->brand->background_color ?? '#059669';
        $primaryHover = $color->brand->background_hover ?? '#047857';
        $primaryText = $color->brand->text_color ?? '#ffffff';
        $primaryTextHover = $color->brand->text_hover ?? '#ffffff';

        if (! function_exists('hk_hex2rgb')) {
            function hk_hex2rgb($hex) {
                $hex = ltrim((string) $hex, '#');
                if (strlen($hex) === 3) {
                    $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
                }
                if (strlen($hex) !== 6) {
                    return [5, 150, 105];
                }
                return [
                    hexdec(substr($hex, 0, 2)),
                    hexdec(substr($hex, 2, 2)),
                    hexdec(substr($hex, 4, 2))
                ];
            }
        }

        if (! function_exists('hk_rgb2hex')) {
            function hk_rgb2hex($r, $g, $b) {
                return sprintf('#%02x%02x%02x', max(0, min(255, (int) $r)), max(0, min(255, (int) $g)), max(0, min(255, (int) $b)));
            }
        }

        if (! function_exists('hk_adjust_brightness')) {
            function hk_adjust_brightness($hex, $percent) {
                $rgb = hk_hex2rgb($hex);
                $r = $rgb[0] * (1 + $percent / 100);
                $g = $rgb[1] * (1 + $percent / 100);
                $b = $rgb[2] * (1 + $percent / 100);
                return hk_rgb2hex($r, $g, $b);
            }
        }

        $rgb = hk_hex2rgb($primaryBg);
        $primaryRgb = implode(', ', $rgb);
        $primaryDark = hk_adjust_brightness($primaryBg, -18);
        $primaryDarker = hk_adjust_brightness($primaryBg, -32);
        $primaryLight = 'rgba(' . $primaryRgb . ', 0.12)';
        $primarySoft = 'rgba(' . $primaryRgb . ', 0.06)';
        $primaryBorder = 'rgba(' . $primaryRgb . ', 0.20)';
    @endphp
    :root {
        --brand: {{ $primaryBg }};
        --brand-rgb: {{ $primaryRgb }};
        --brand-hover: {{ $primaryHover }};
        --brand-dark: {{ $primaryDark }};
        --brand-darker: {{ $primaryDarker }};
        --brand-light: {{ $primaryLight }};
        --brand-soft: {{ $primarySoft }};
        --brand-border: {{ $primaryBorder }};
        --brand-text: {{ $primaryText }};
        --brand-text-hover: {{ $primaryTextHover }};

        /* BagBazarBD Modern Design System Tokens */
        --bb-primary: var(--brand);
        --bb-primary-dark: var(--brand-dark);
        --bb-primary-hover: var(--brand-hover);
        --bb-primary-soft: var(--brand-soft);
        --bb-primary-light: var(--brand-light);
        --bb-primary-border: var(--brand-border);
        
        --bb-green: var(--brand);
        --bb-green-dark: var(--brand-dark);
        --bb-green-hover: var(--brand-hover);
        --bb-green-soft: var(--brand-soft);
        --bb-green-light: var(--brand-light);
        --bb-green-border: var(--brand-border);
        --bb-category-brand: var(--brand);
        --bb-blog-green: var(--brand);

        --bb-slate-900: #0f172a;
        --bb-slate-800: #1e293b;
        --bb-slate-700: #334155;
        --bb-slate-600: #475569;
        --bb-slate-500: #64748b;
        --bb-slate-100: #f1f5f9;
        --bb-slate-50: #f8fafc;

        --bb-card-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06), 0 2px 6px -1px rgba(15, 23, 42, 0.04);
        --bb-card-shadow-hover: 0 16px 32px -4px rgba(15, 23, 42, 0.12), 0 6px 12px -2px rgba(15, 23, 42, 0.06);
        --bb-radius-card: 16px;
        --bb-radius-pill: 9999px;
    }

    ::placeholder {
        color: #94a3b8 !important;
    }

    /* Topbar Modernization */
    .topbar,
    .site-header .topbar {
        background-color: {{ $color->topbar->background_color ?? '#0f172a' }} !important;
        color: {{ $color->topbar->text_color ?? '#f8fafc' }} !important;
        font-size: 13px;
        font-weight: 500;
        letter-spacing: 0.01em;
    }

    .topbar:hover,
    .site-header .topbar:hover {
        background-color: {{ $color->topbar->background_hover ?? '#1e293b' }} !important;
    }

    .topbar .topbar-link,
    .site-header .topbar .topbar-link {
        color: {{ $color->topbar->text_color ?? '#cbd5e1' }} !important;
        transition: color 0.15s ease;
    }

    .topbar .topbar-link:hover,
    .site-header .topbar .topbar-link:hover {
        color: {{ $color->topbar->text_hover ?? '#ffffff' }} !important;
    }

    /* Site Header Middle Modern Clean */
    .site-header {
        background-color: {{ $color->header->background_color ?? '#ffffff' }} !important;
        color: {{ $color->header->text_color ?? '#0f172a' }} !important;
        box-shadow: 0 2px 16px rgba(15, 23, 42, 0.06);
        border-bottom: 1px solid #f1f5f9;
    }

    .mobile-header__panel {
        background-color: {{ $color->header->background_color ?? '#ffffff' }} !important;
        color: {{ $color->header->text_color ?? '#0f172a' }} !important;
        box-shadow: 0 2px 12px rgba(15, 23, 42, 0.07);
        border-bottom: 1px solid #f1f5f9;
    }

    .site-header__phone-title {
        color: #64748b !important;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .site-header__phone-number a {
        color: #0f172a !important;
        font-weight: 800 !important;
        font-size: 16px !important;
    }

    /* Modern Pill Searchbar */
    .site-header .site-header__search input {
        border-radius: 9999px 0 0 9999px !important;
        border: 2px solid var(--brand) !important;
        background: #f8fafc !important;
        color: #0f172a !important;
        font-size: 14px !important;
        padding-left: 20px !important;
        height: 44px !important;
        transition: background 0.2s ease, box-shadow 0.2s ease;
    }

    .site-header .site-header__search input:focus {
        background: #ffffff !important;
        box-shadow: 0 0 0 4px var(--brand-light);
    }

    .site-header .site-header__search figure {
        background: var(--brand) !important;
        color: #ffffff !important;
        border-radius: 0 9999px 9999px 0 !important;
        border: 2px solid var(--brand) !important;
        border-left: none !important;
        height: 44px !important;
        padding: 0 24px !important;
        transition: background 0.2s ease, transform 0.1s ease;
    }

    .site-header .site-header__search figure:hover {
        background: var(--brand-dark) !important;
    }

    /* Navigation Bar */
    .site-header .nav-panel {
        background-color: {{ $color->navbar->background_color ?? '#ffffff' }} !important;
        border-top: 1px solid #f1f5f9;
        border-bottom: 1px solid #e2e8f0;
    }

    .nav-links__item > a span {
        color: {{ $color->navbar->text_color ?? '#1e293b' }} !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        padding: 8px 14px !important;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .nav-links__item:hover > a span {
        background: var(--brand-soft) !important;
        color: var(--brand-dark) !important;
    }

    /* Cart / User Indicators */
    .indicator .indicator__area {
        color: #0f172a !important;
        border-radius: 12px;
        padding: 8px 12px;
        transition: background 0.2s ease;
    }

    .indicator:hover .indicator__area,
    .indicator--opened .indicator__area {
        background: var(--brand-soft) !important;
        color: var(--brand) !important;
    }

    .indicator__value {
        background: var(--brand) !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        border-radius: 9999px;
        font-size: 11px;
        min-width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(var(--brand-rgb), 0.35);
    }

    /* Departments Menu Button */
    .nav-panel__departments .departments__button {
        background: linear-gradient(135deg, var(--brand-dark) 0%, var(--brand) 100%) !important;
        border-radius: 4px !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 14.5px !important;
        letter-spacing: 0.01em;
        padding: 0 34px 0 46px !important;
        height: 100% !important;
        display: flex !important;
        align-items: center !important;
        box-shadow: 0 4px 14px rgba(var(--brand-rgb), 0.25);
        border: none !important;
        position: relative !important;
        transition: all 0.2s ease;
    }

    .nav-panel__departments .departments__button-icon {
        position: absolute !important;
        left: 16px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        fill: #ffffff !important;
    }

    .nav-panel__departments .departments__button-arrow {
        position: absolute !important;
        right: 14px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        fill: #ffffff !important;
    }

    .nav-panel__departments .departments__button:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(var(--brand-rgb), 0.32);
    }

    .nav-panel__departments .departments__body {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0 0 4px 4px !important;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.1) !important;
        overflow: hidden;
    }

    .nav-panel__departments .departments__links {
        padding: 4px 0 6px !important;
        margin: 0 !important;
    }

    .nav-panel__departments .departments__links > li > a {
        color: #1e293b !important;
        font-weight: 600;
        font-size: 13px !important;
        line-height: 1.25 !important;
        border-bottom: 1px solid #f8fafc !important;
        padding: 7px 14px 7px 16px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        transition: all 0.15s ease;
    }

    .nav-panel__departments .departments__links > li:hover > a {
        background: var(--brand-soft) !important;
        color: var(--brand-dark) !important;
        padding-left: 20px !important;
    }

    .nav-panel__departments .departments__link-arrow {
        position: static !important;
        fill: #94a3b8 !important;
        transition: fill 0.15s ease;
    }

    .nav-panel__departments .departments__links > li:hover .departments__link-arrow {
        fill: var(--brand-dark) !important;
    }

    /* Product Cards Modernization */
    .product-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: var(--bb-radius-card);
        box-shadow: var(--bb-card-shadow);
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease;
        overflow: hidden;
    }

    .product-card:hover {
        transform: translateY(-4px);
        border-color: var(--brand-border);
        box-shadow: var(--bb-card-shadow-hover);
    }

    .product-card__badge.product-card__badge--sale {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: #ffffff !important;
        font-weight: 800;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 12px;
        box-shadow: 0 3px 8px rgba(239, 68, 68, 0.3);
    }

    /* Buttons */
    .btn-primary {
        background: linear-gradient(135deg, var(--brand), var(--brand-dark)) !important;
        border-color: var(--brand) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        border-radius: 4px !important;
        box-shadow: 0 4px 14px rgba(var(--brand-rgb), 0.25);
        transition: all 0.2s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--brand-dark), var(--brand-darker)) !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(var(--brand-rgb), 0.35);
    }

    /* Add To Cart & Order Now Buttons */
    .product-card__addtocart,
    .product__addtocart {
        background-color: var(--bb-slate-800) !important;
        border: none !important;
        color: #ffffff !important;
        border-radius: 4px !important;
        font-weight: 700 !important;
        padding: 9px 14px !important;
        font-size: 13px !important;
        transition: all 0.2s ease;
    }

    .product-card__addtocart:hover,
    .product__addtocart:hover {
        background-color: #0f172a !important;
        transform: translateY(-1px);
    }

    .product-card__ordernow,
    .product__ordernow {
        background: linear-gradient(135deg, var(--brand), var(--brand-dark)) !important;
        border: none !important;
        color: #ffffff !important;
        border-radius: 4px !important;
        font-weight: 800 !important;
        padding: 9px 16px !important;
        font-size: 13px !important;
        box-shadow: 0 4px 14px rgba(var(--brand-rgb), 0.25);
        transition: all 0.2s ease;
    }

    .product-card__ordernow:hover,
    .product__ordernow:hover {
        background: linear-gradient(135deg, var(--brand-dark), var(--brand-darker)) !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(var(--brand-rgb), 0.35);
    }

    /* Modern Footer */
    .site-footer {
        background: #0f172a !important;
        color: #cbd5e1 !important;
        border-top: 1px solid #1e293b;
        padding-top: 50px;
    }

    .site-footer h5,
    .site-footer .footer-contacts__title,
    .site-footer .footer-links__title,
    .site-footer .footer-newsletter__title {
        color: #ffffff !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        letter-spacing: 0.02em;
        margin-bottom: 18px;
    }

    .site-footer a,
    .site-footer .footer-links__link {
        color: #94a3b8 !important;
        transition: color 0.15s ease;
    }

    .site-footer a:hover,
    .site-footer .footer-links__link:hover {
        color: var(--brand) !important;
    }

    .site-footer__bottom {
        border-top: 1px solid #1e293b !important;
        padding: 24px 0 !important;
        color: #64748b !important;
        font-size: 13px;
    }
</style>
