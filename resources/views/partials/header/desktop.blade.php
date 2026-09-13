<header class="site__header d-lg-block d-none position-fixed" style="top: 0; left: 0; right: 0; z-index: 100;">
    <div class="site-header">
        <!-- .topbar -->
        @include('partials.topbar')
        <!-- .topbar / end -->
        <div class="container site-header__middle">
            <div class="site-header__logo">
                <a href="{{ url('/') }}" wire:navigate.hover>
                    @php
                        $desktopLogo = (isset($logo->desktop) && $logo->desktop) ? asset($logo->desktop) : null;
                        $hasPerfLogo = file_exists(public_path('performance/images/hk-logo-320.webp'));
                        $perfLogo320 = asset('performance/images/hk-logo-320.webp');
                        $perfLogo640 = asset('performance/images/hk-logo-640.webp');
                        $siteBrand = $company->name ?? config('app.name');
                    @endphp
                    @if($hasPerfLogo)
                        <img
                            src="{{ $perfLogo320 }}"
                            srcset="{{ $perfLogo320 }} 320w, {{ $perfLogo640 }} 640w"
                            sizes="255px"
                            alt="{{ $siteBrand }}"
                            width="320"
                            height="81"
                            decoding="async"
                            style="max-width: 100%; max-height: 72px; width: auto; height: auto; display: block;"
                        >
                    @elseif($desktopLogo)
                        <img
                            src="{{ $desktopLogo }}"
                            alt="{{ $siteBrand }}"
                            style="max-width: 100%; max-height: 72px; width: auto; height: auto; display: block;"
                        >
                    @else
                        <span class="font-weight-bold" style="font-size: 26px; color: var(--brand-dark); letter-spacing: -0.02em;">{{ $siteBrand }}</span>
                    @endif
                </a>
            </div>
            <div class="site-header__search">
                <div class="search">
                    <form action="/shop">
                        <div style="grid-area:search" class="md:ml-4 position-relative">
                            <div class="transition-all duration-75 ease-linear Searchbar__CustomCombobox-xnx3kr-6 joXPnU overflow-initial"
                                data-reach-combobox="" data-state="idle">
                                <div class="Searchbar__Container-xnx3kr-1 kWQExC" style="display: flex;">
                                    <input name="search" aria-autocomplete="both" aria-controls="listbox--1"
                                        aria-expanded="false" aria-haspopup="listbox" aria-labelledby="demo"
                                        role="combobox" placeholder="Search for products, brands & more..." data-reach-combobox-input=""
                                        data-state="idle" value="{{ request('search') }}"
                                        style="letter-spacing: 0.01em;font-weight: 500;font-size: 0.875rem;height: 44px;display: flex;flex: 1 1 0%;padding: 0px 20px;border: 2px solid var(--brand);border-radius: 6px 0px 0px 6px;outline: none;width: 100%;background: #f8fafc;">
                                    <button type="submit" style="border: none; padding: 0; background: none;">
                                        <figure color="black" class="Searchbar__Button-xnx3kr-3 duKdNo"
                                            style="cursor: pointer;display: flex;-webkit-box-align: center;align-items: center;padding-right: 24px;padding-left: 24px;color: rgb(255, 255, 255);height: 44px;min-height: 100%;margin: 0;background: var(--brand);border-radius: 0 6px 6px 0;">
                                            <svg stroke="currentColor" fill="currentColor" stroke-width="0"
                                                viewBox="0 0 24 24" class="Searchbar___StyledMdSearch-xnx3kr-5 fHBAIp"
                                                height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"
                                                style="font-size: 22px;">
                                                <path
                                                    d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z">
                                                </path>
                                            </svg>
                                        </figure>
                                    </button>
                                </div>
                                <div id="desktop-search-suggestions" class="search-suggestions-dropdown"
                                    style="display:none;position:absolute;top:100%;left:0;width:100%;z-index:9999;background:#fff;border:1px solid #e2e8f0;box-shadow:0 10px 25px -5px rgba(15,23,42,0.12);border-radius:6px;margin-top:6px;padding:0;max-height:320px;overflow-y:auto;">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @if(!empty($company->phone))
            <div class="site-header__phone d-flex align-items-center gap-2 pl-3">
                <div class="d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 6px; background: var(--brand-soft); color: var(--brand-dark); margin-right: 4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                        <path d="M9.36556 10.6821C10.302 12.3288 11.6712 13.698 13.3179 14.6344L14.2024 13.3961C14.4965 12.9845 15.0516 12.8573 15.4956 13.0998C16.9024 13.8683 18.4571 14.3353 20.0789 14.4637C20.599 14.5049 21 14.9389 21 15.4606V19.9234C21 20.4361 20.6122 20.8657 20.1022 20.9181C19.5723 20.9726 19.0377 21 18.5 21C9.93959 21 3 14.0604 3 5.5C3 4.96227 3.02742 4.42771 3.08189 3.89776C3.1343 3.38775 3.56394 3 4.07665 3H8.53942C9.0611 3 9.49513 3.40104 9.5363 3.92109C9.66467 5.54288 10.1317 7.09764 10.9002 8.50444C11.1427 8.9484 11.0155 9.50354 10.6039 9.79757L9.36556 10.6821ZM6.84425 10.0252L8.7442 8.66809C8.20547 7.50514 7.83628 6.27183 7.64727 5H5.00907C5.00303 5.16632 5 5.333 5 5.5C5 12.9558 11.0442 19 18.5 19C18.667 19 18.8337 18.997 19 18.9909V16.3527C17.7282 16.1637 16.4949 15.7945 15.3319 15.2558L13.9748 17.1558C13.4258 16.9425 12.8956 16.6915 12.3874 16.4061L12.3293 16.373C10.3697 15.2587 8.74134 13.6303 7.627 11.6707L7.59394 11.6126C7.30849 11.1044 7.05754 10.5742 6.84425 10.0252Z"></path>
                    </svg>
                </div>
                <div>
                    <div class="mb-0 site-header__phone-title">Customer Support</div>
                    <div class="site-header__phone-number">
                        <a style="font-family: inherit; font-size: 15px; font-weight: 700; color: #0f172a;" href="tel:{{ $company->phone }}">{{ $company->phone }}</a>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <div class="site-header__nav-panel">
            <div class="nav-panel">
                <div class="container nav-panel__container">
                    <div class="nav-panel__row">
                        @include('partials.departments')
                        <!-- .nav-links -->
                        @include('partials.header.menu.desktop')
                        <!-- .nav-links / end -->
                        <div class="nav-panel__indicators">
                            <div class="indicator indicator--trigger--click">
                                <a href="#" class="indicator__button" aria-label="Shopping Cart">
                                    <span class="indicator__area d-flex align-items-center gap-1">
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
                        </div>
                        @include('partials.auth-indicator')
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
