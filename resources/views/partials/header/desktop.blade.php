<header class="site__header d-lg-block d-none position-fixed" style="top: 0; left: 0; right: 0; z-index: 100;">
    <div class="site-header">
        <!-- .topbar -->
        @include('partials.topbar')
        <!-- .topbar / end -->
        <div class="container site-header__middle">
            <div class="site-header__logo">
                <a href="{{ url('/') }}" wire:navigate.hover>
                    <img src="{{ asset($logo->desktop ?? '') }}" alt="Logo"
                        style="max-width: 100%; max-height: 84px; width: auto; height: auto; display: block;"
                        width="auto" height="84">
                </a>
            </div>
            <div class="site-header__search">
                <div class="search">



                    <form action="/shop">
                                            <div style="grid-area:search" class="md:ml-4 position-relative">
                                                <div class="transition-all duration-75 ease-linear Searchbar__CustomCombobox-xnx3kr-6 joXPnU overflow-initial"
                                                    data-reach-combobox="" data-state="idle">
                                                    <div class="Searchbar__Container-xnx3kr-1 kWQExC" style="display: flex;">
                                                        <input
                                                            name="search" aria-autocomplete="both" aria-controls="listbox--1"
                                                            aria-expanded="false" aria-haspopup="listbox" aria-labelledby="demo"
                                                            role="combobox" placeholder="Search for..." data-reach-combobox-input=""
                                                            data-state="idle" value="{{ request('search') }}"
                                                            style="letter-spacing: 0.025em;font-weight: 500;font-size: 0.875rem;height: 40px;display: flex;flex: 1 1 0%;padding: 0px 17px;border: 2px solid;border-radius: 4px 0px 0px 4px;outline: none;width: 100%;">
                                                        <button type="submit" style="border: none; padding: 0;">
                                                            <figure color="black" class="Searchbar__Button-xnx3kr-3 duKdNo"
                                                                style="cursor: pointer;display: flex;-webkit-box-align: center;align-items: center;padding-right: 29px;padding-left: 29px;color: rgb(255, 255, 255);height: 40px;min-height: 100%;margin: 0;">
                                                                <svg stroke="currentColor" fill="currentColor" stroke-width="0"
                                                                    viewBox="0 0 24 24"
                                                                    class="Searchbar___StyledMdSearch-xnx3kr-5 fHBAIp" height="1em"
                                                                    width="1em" xmlns="http://www.w3.org/2000/svg"
                                                                    style="font-size: 25px;">
                                                                    <path
                                                                        d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z">
                                                                    </path>
                                                                </svg>
                                                            </figure>
                                                        </button>
                                                    </div>
                                                    <div id="desktop-search-suggestions" class="search-suggestions-dropdown" style="display:none;position:absolute;top:100%;left:0;width:100%;z-index:9999;background:#fff;border:1px solid #eee;box-shadow:0 2px 8px rgba(0,0,0,0.08);padding:0;max-height:320px;overflow-y:auto;"></div>
                                                                                                    <div id="desktop-search-suggestions" class="search-suggestions-dropdown" style="display:none;position:absolute;top:100%;left:0;width:100%;z-index:9999;background:#fff;border:1px solid #eee;box-shadow:0 2px 8px rgba(0,0,0,0.08);padding:0;max-height:320px;overflow-y:auto;"></div>
                                                </div>
                                            </div>
                    </form>



                    <!-- HTML Markup -->
                    <!--<div class="aa-input-container" id="aa-input-container">-->
                    <!--    <input type="search" id="aa-search-input" class="aa-input-search" placeholder="Search for products..." name="search" value="{{ request('search') }}" autocomplete="off" />-->
                    <!--    <svg class="aa-input-icon" viewBox="654 -372 1664 1664">-->
                    <!--        <path d="M1806,332c0-123.3-43.8-228.8-131.5-316.5C1586.8-72.2,1481.3-116,1358-116s-228.8,43.8-316.5,131.5  C953.8,103.2,910,208.7,910,332s43.8,228.8,131.5,316.5C1129.2,736.2,1234.7,780,1358,780s228.8-43.8,316.5-131.5  C1762.2,560.8,1806,455.3,1806,332z M2318,1164c0,34.7-12.7,64.7-38,90s-55.3,38-90,38c-36,0-66-12.7-90-38l-343-342  c-119.3,82.7-252.3,124-399,124c-95.3,0-186.5-18.5-273.5-55.5s-162-87-225-150s-113-138-150-225S654,427.3,654,332  s18.5-186.5,55.5-273.5s87-162,150-225s138-113,225-150S1262.7-372,1358-372s186.5,18.5,273.5,55.5s162,87,225,150s113,138,150,225  S2062,236.7,2062,332c0,146.7-41.3,279.7-124,399l343,343C2305.7,1098.7,2318,1128.7,2318,1164z" />-->
                    <!--    </svg>-->
                    <!--</div>-->
                </div>
            </div>
            <div class="site-header__phone d-flex align-items-center">
                {{-- Lazy load call-now.gif - it's 279KB and not critical for initial render --}}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"
                    fill="currentColor">
                    <path
                        d="M9.36556 10.6821C10.302 12.3288 11.6712 13.698 13.3179 14.6344L14.2024 13.3961C14.4965 12.9845 15.0516 12.8573 15.4956 13.0998C16.9024 13.8683 18.4571 14.3353 20.0789 14.4637C20.599 14.5049 21 14.9389 21 15.4606V19.9234C21 20.4361 20.6122 20.8657 20.1022 20.9181C19.5723 20.9726 19.0377 21 18.5 21C9.93959 21 3 14.0604 3 5.5C3 4.96227 3.02742 4.42771 3.08189 3.89776C3.1343 3.38775 3.56394 3 4.07665 3H8.53942C9.0611 3 9.49513 3.40104 9.5363 3.92109C9.66467 5.54288 10.1317 7.09764 10.9002 8.50444C11.1427 8.9484 11.0155 9.50354 10.6039 9.79757L9.36556 10.6821ZM6.84425 10.0252L8.7442 8.66809C8.20547 7.50514 7.83628 6.27183 7.64727 5H5.00907C5.00303 5.16632 5 5.333 5 5.5C5 12.9558 11.0442 19 18.5 19C18.667 19 18.8337 18.997 19 18.9909V16.3527C17.7282 16.1637 16.4949 15.7945 15.3319 15.2558L13.9748 17.1558C13.4258 16.9425 12.8956 16.6915 12.3874 16.4061L12.3293 16.373C10.3697 15.2587 8.74134 13.6303 7.627 11.6707L7.59394 11.6126C7.30849 11.1044 7.05754 10.5742 6.84425 10.0252Z">
                    </path>
                </svg>
                <div>
                    <div class="mb-0 site-header__phone-title">Help Line</div>
                    <div class="site-header__phone-number">
                        <div class="topbar__item topbar__item--link">
                            <a style="font-family: monospace; font-size: 16px;" class="topbar-link"
                                href="tel:{{ $company->phone ?? '' }}">{{ $company->phone ?? '' }}</a>
                        </div>
                    </div>
                </div>
            </div>
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
                        </div>
                        @include('partials.auth-indicator')
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
