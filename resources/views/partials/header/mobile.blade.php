<header class="site__header d-lg-none">
    @include('partials.topbar')
    <div class="mobile-header mobile-header--sticky mobile-header--stuck">
        <div class="mobile-header__panel">
            <div class="container">
                <div class="mobile-header__body">
                    <button class="mobile-header__menu-button">
                        <svg width="18px" height="14px" viewBox="0 0 18 14"><path d="M0 8V6h18v2H0zm0-8h18v2H0V0zm14 14H0v-2h14v2z"/></svg>
                    </button>
                    <a class="mobile-header__logo" href="{{ url('/') }}" wire:navigate.hover>
                        <img src="{{ asset($logo->mobile ?? '') }}" alt="Logo"
                            style="max-width: 100%; max-height: 54px; width: auto; height: auto; display: block;"
                            width="auto" height="54">
                    </a>
                    <div class="mobile-header__search">
                        <div class="search mobile-header__search-form">
                            <!-- HTML Markup -->
                            <form action="shop" class="aa-input-container" id="bb-input-container">
                                <input type="search" id="bb-search-input"
                                    class="aa-input-search mobile-header__search-input"
                                    placeholder="Search for products..." name="search" value="{{ request('search') }}"
                                    autocomplete="off" />
                                <svg class="aa-input-icon" viewBox="654 -372 1664 1664">
                                    <path
                                        d="M1806,332c0-123.3-43.8-228.8-131.5-316.5C1586.8-72.2,1481.3-116,1358-116s-228.8,43.8-316.5,131.5  C953.8,103.2,910,208.7,910,332s43.8,228.8,131.5,316.5C1129.2,736.2,1234.7,780,1358,780s228.8-43.8,316.5-131.5  C1762.2,560.8,1806,455.3,1806,332z M2318,1164c0,34.7-12.7,64.7-38,90s-55.3,38-90,38c-36,0-66-12.7-90-38l-343-342  c-119.3,82.7-252.3,124-399,124c-95.3,0-186.5-18.5-273.5-55.5s-162-87-225-150s-113-138-150-225S654,427.3,654,332  s18.5-186.5,55.5-273.5s87-162,150-225s138-113,225-150S1262.7-372,1358-372s186.5,18.5,273.5,55.5s162,87,225,150s113,138,150,225  S2062,236.7,2062,332c0,146.7-41.3,279.7-124,399l343,343C2305.7,1098.7,2318,1128.7,2318,1164z" />
                                </svg>
                                <button class="mobile-header__search-button mobile-header__search-button--close"
                                    type="button"><svg width="20px" height="20px" viewBox="0 0 20 20"><path d="M17.71 17.71a.99.99 0 0 1-1.4 0L10 11.4l-6.31 6.31a.99.99 0 1 1-1.4-1.4L8.6 10 2.29 3.69a.99.99 0 1 1 1.4-1.4L10 8.6l6.31-6.31a.99.99 0 1 1 1.4 1.4L11.4 10l6.31 6.31a.99.99 0 0 1 0 1.4z"/></svg>
                                </button>
                                <div id="mobile-search-suggestions" class="search-suggestions-dropdown"
                                    style="display:none;position:absolute;top:100%;left:0;width:100%;z-index:9999;background:#fff;border:1px solid #eee;box-shadow:0 2px 8px rgba(0,0,0,0.08);padding:0;max-height:320px;height:220px;overflow-y:auto;">
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="mobile-header__indicators">
                        <div class="indicator indicator--mobile-search indicator--mobile d-sm-none">
                            <button class="indicator__button">
                                <span class="indicator__area">
                                    <svg width="20px" height="20px" viewBox="0 0 20 20"><path d="M19.2 17.8s-.2.5-.5.8c-.4.4-.9.6-.9.6s-.9.7-2.8-1.6c-1.1-1.4-2.2-2.8-3.1-3.9-1 .8-2.4 1.3-3.9 1.3-3.9 0-7-3.1-7-7s3.1-7 7-7 7 3.1 7 7c0 1.5-.5 2.9-1.3 4 1.1.8 2.5 2 4 3.1 2.3 1.7 1.5 2.7 1.5 2.7zM8 3C5.2 3 3 5.2 3 8s2.2 5 5 5 5-2.2 5-5-2.2-5-5-5z"/></svg>
                                </span>
                            </button>
                        </div>
                        <div class="indicator indicator--trigger--click">
                            <a href="#" class="indicator__button">
                                <span class="indicator__area">
                                    <svg width="20" height="20">
                                        <circle cx="7" cy="17" r="2"></circle>
                                        <circle cx="15" cy="17" r="2"></circle>
                                        <path
                                            d="M20,4.4V5l-1.8,6.3c-0.1,0.4-0.5,0.7-1,0.7H6.7c-0.4,0-0.8-0.3-1-0.7L3.3,3.9C3.1,3.3,2.6,3,2.1,3H0.4C0.2,3,0,2.8,0,2.6 V1.4C0,1.2,0.2,1,0.4,1h2.5c1,0,1.8,0.6,2.1,1.6L5.1,3l2.3,6.8c0,0.1,0.2,0.2,0.3,0.2h8.6c0.1,0,0.3-0.1,0.3-0.2l1.3-4.4 C17.9,5.2,17.7,5,17.5,5H9.4C9.2,5,9,4.8,9,4.6V3.4C9,3.2,9.2,3,9.4,3h9.2C19.4,3,20,3.6,20,4.4z">
                                        </path>
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
