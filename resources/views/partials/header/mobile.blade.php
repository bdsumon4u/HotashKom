<style id="bb-ios-safari-logo-fix">@supports (-webkit-touch-callout:none){@media(max-width:375px){.mobile-header__body{min-width:0}.mobile-header__logo{display:flex!important;align-items:center!important;flex:0 0 112px!important;width:112px!important;min-width:112px!important;margin:0 8px!important}.mobile-header__logo img{display:block!important;width:112px!important;max-width:112px!important;height:auto!important;max-height:32px!important}.mobile-header__indicators{flex:0 0 auto!important}}}</style>
<header class="site__header d-lg-none">
    @include('partials.topbar')
    <div class="mobile-header mobile-header--sticky">
        <div class="mobile-header__panel">
            <div class="container">
                <div class="mobile-header__body">
                    <button class="mobile-header__menu-button" aria-label="Open menu">
                        <svg width="20px" height="16px" viewBox="0 0 18 14"><path d="M0 8V6h18v2H0zm0-8h18v2H0V0zm14 14H0v-2h14v2z"/></svg>
                    </button>
                    <a class="mobile-header__logo" style="display:flex;align-items:center;visibility:visible;opacity:1;" href="{{ url('/') }}" wire:navigate.hover>
                        @php
                            $mobileLogo = (isset($logo->mobile) && $logo->mobile) ? asset($logo->mobile) : ((isset($logo->desktop) && $logo->desktop) ? asset($logo->desktop) : null);
                            $hasPerfLogo = file_exists(public_path('performance/images/hk-logo-320.webp'));
                            $perfLogo320 = asset('performance/images/hk-logo-320.webp');
                            $perfLogo640 = asset('performance/images/hk-logo-640.webp');
                            $siteBrand = $company->name ?? config('app.name');
                        @endphp
                        @if($hasPerfLogo)
                            <img
                                src="{{ $perfLogo320 }}"
                                srcset="{{ $perfLogo320 }} 320w, {{ $perfLogo640 }} 640w"
                                sizes="200px"
                                alt="{{ $siteBrand }}"
                                width="320"
                                height="81"
                                decoding="async"
                                style="max-width: 100%; max-height: 46px; width: auto; height: auto; display: block;"
                            >
                        @elseif($mobileLogo)
                            <img
                                src="{{ $mobileLogo }}"
                                alt="{{ $siteBrand }}"
                                style="max-width: 100%; max-height: 46px; width: auto; height: auto; display: block;"
                            >
                        @else
                            <span class="font-weight-bold" style="font-size: 20px; color: var(--brand-dark);">{{ $siteBrand }}</span>
                        @endif
                    </a>
                    <div class="mobile-header__search">
                        <div class="search mobile-header__search-form">
                            <!-- HTML Markup -->
                            <form action="shop" class="aa-input-container" id="bb-input-container" style="position: relative;">
                                <input type="search" id="bb-search-input"
                                    class="aa-input-search mobile-header__search-input"
                                    placeholder="Search products..." name="search" value="{{ request('search') }}"
                                    autocomplete="off"
                                    style="border-radius: 6px; border: 1.5px solid var(--brand); padding: 0 35px 0 16px; height: 38px; font-size: 13px; background: #f8fafc;" />
                                <svg class="aa-input-icon" viewBox="654 -372 1664 1664" style="right: 12px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; fill: var(--brand);">
                                    <path
                                        d="M1806,332c0-123.3-43.8-228.8-131.5-316.5C1586.8-72.2,1481.3-116,1358-116s-228.8,43.8-316.5,131.5  C953.8,103.2,910,208.7,910,332s43.8,228.8,131.5,316.5C1129.2,736.2,1234.7,780,1358,780s228.8-43.8,316.5-131.5  C1762.2,560.8,1806,455.3,1806,332z M2318,1164c0,34.7-12.7,64.7-38,90s-55.3,38-90,38c-36,0-66-12.7-90-38l-343-342  c-119.3,82.7-252.3,124-399,124c-95.3,0-186.5-18.5-273.5-55.5s-162-87-225-150s-113-138-150-225S654,427.3,654,332  s18.5-186.5,55.5-273.5s87-162,150-225s138-113,225-150S1262.7-372,1358-372s186.5,18.5,273.5,55.5s162,87,225,150s113,138,150,225  S2062,236.7,2062,332c0,146.7-41.3,279.7-124,399l343,343C2305.7,1098.7,2318,1128.7,2318,1164z" />
                                </svg>
                                <button class="mobile-header__search-button mobile-header__search-button--close"
                                    type="button"><svg width="18px" height="18px" viewBox="0 0 20 20"><path d="M17.71 17.71a.99.99 0 0 1-1.4 0L10 11.4l-6.31 6.31a.99.99 0 1 1-1.4-1.4L8.6 10 2.29 3.69a.99.99 0 1 1 1.4-1.4L10 8.6l6.31-6.31a.99.99 0 1 1 1.4 1.4L11.4 10l6.31 6.31a.99.99 0 0 1 0 1.4z"/></svg>
                                </button>
                                <div id="mobile-search-suggestions" class="search-suggestions-dropdown"
                                    style="display:none;position:absolute;top:100%;left:0;width:100%;z-index:9999;background:#fff;border:1px solid #e2e8f0;box-shadow:0 8px 24px rgba(15,23,42,0.12);border-radius:6px;margin-top:6px;padding:0;max-height:300px;overflow-y:auto;">
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="mobile-header__indicators">
                        <div class="indicator indicator--mobile-search indicator--mobile d-sm-none">
                            <button class="indicator__button" aria-label="Search">
                                <span class="indicator__area">
                                    <svg width="20px" height="20px" viewBox="0 0 20 20"><path d="M19.2 17.8s-.2.5-.5.8c-.4.4-.9.6-.9.6s-.9.7-2.8-1.6c-1.1-1.4-2.2-2.8-3.1-3.9-1 .8-2.4 1.3-3.9 1.3-3.9 0-7-3.1-7-7s3.1-7 7-7 7 3.1 7 7c0 1.5-.5 2.9-1.3 4 1.1.8 2.5 2 4 3.1 2.3 1.7 1.5 2.7 1.5 2.7zM8 3C5.2 3 3 5.2 3 8s2.2 5 5 5 5-2.2 5-5-2.2-5-5-5z"/></svg>
                                </span>
                            </button>
                        </div>
                        <div class="indicator indicator--trigger--click">
                            <a href="#" class="indicator__button" aria-label="Shopping cart">
                                <span class="indicator__area d-flex align-items-center">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="9" cy="21" r="1"></circle>
                                        <circle cx="20" cy="21" r="1"></circle>
                                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                    </svg>
                                    <livewire:cart-count />
                                </span>
                            </a>
                            <div class="indicator__dropdown">
                                <!-- .dropcart -->
                                <livewire:cart-box />
                            </div>
                        </div>
                        @include('partials.auth-indicator')
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
