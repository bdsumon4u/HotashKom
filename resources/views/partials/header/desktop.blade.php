<header class="site__header d-lg-block d-none position-fixed" style="top: 0; left: 0; right: 0; z-index: 100;">
    <div class="site-header">
        <!-- .topbar -->
        @include('partials.topbar')
        <!-- .topbar / end -->
        <div class="site-header__middle container">
            <div class="site-header__logo">
                <a href="{{ url('/') }}" wire:navigate.hover>
                    <img src="{{ asset($logo->desktop ?? '') }}" alt="Logo" style="max-width: 100%; max-height: 84px;">
                </a>
            </div>
            <div class="site-header__search">
                <div class="search">
                    
                    
                    
                    <form action="/shop">
                        <div style="grid-area:search" class="md:ml-4"><div class="Searchbar__CustomCombobox-xnx3kr-6 joXPnU transition-all duration-75 ease-linear overflow-initial" data-reach-combobox="" data-state="idle"><div class="Searchbar__Container-xnx3kr-1 kWQExC" style="
    display: flex;
"><input name="search" aria-autocomplete="both" aria-controls="listbox--1" aria-expanded="false" aria-haspopup="listbox" aria-labelledby="demo" role="combobox" placeholder="Search for..." data-reach-combobox-input="" data-state="idle" value="{{ request('search') }}" style="
    letter-spacing: 0.025em;
    font-weight: 500;
    font-size: 0.875rem;
    height: 40px;
    display: flex;
    flex: 1 1 0%;
    padding: 0px 17px;
    border: 2px solid;
    border-radius: 4px 0px 0px 4px;
    outline: none;
    width: 100%;
">

<button type="submit" style="border: none; padding: 0;">
    <figure color="black" class="Searchbar__Button-xnx3kr-3 duKdNo" style="
    cursor: pointer;
    display: flex;
    -webkit-box-align: center;
    align-items: center;
    padding-right: 29px;
    padding-left: 29px;
    color: rgb(255, 255, 255);
    height: 40px;
    min-height: 100%;
    margin: 0;
"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" _css2="
    @media (max-width: ,768px,) {
      ,
            font-size:20px;
          ,
    }
  " class="Searchbar___StyledMdSearch-xnx3kr-5 fHBAIp" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg" style="
    font-size: 25px;
"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path></svg></figure>
</button>

</div></div> </div>
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
                <img style="height: 35px;" class="img-responsive mr-1" src="{{ asset('call-now.gif') }}">
                <div>
                    <div class="site-header__phone-title mb-0">Help Line</div>
                    <div class="site-header__phone-number">
                        <div class="topbar__item topbar__item--link">
                            <a style="font-family: monospace; font-size: 16px;" class="topbar-link" href="tel:{{ $company->phone ?? '' }}">{{ $company->phone ?? '' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="site-header__nav-panel">
            <div class="nav-panel">
                <div class="nav-panel__container container">
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
                                            <path d="M20,4.4V5l-1.8,6.3c-0.1,0.4-0.5,0.7-1,0.7H6.7c-0.4,0-0.8-0.3-1-0.7L3.3,3.9C3.1,3.3,2.6,3,2.1,3H0.4C0.2,3,0,2.8,0,2.6 V1.4C0,1.2,0.2,1,0.4,1h2.5c1,0,1.8,0.6,2.1,1.6L5.1,3l2.3,6.8c0,0.1,0.2,0.2,0.3,0.2h8.6c0.1,0,0.3-0.1,0.3-0.2l1.3-4.4 C17.9,5.2,17.7,5,17.5,5H9.4C9.2,5,9,4.8,9,4.6V3.4C9,3.2,9.2,3,9.4,3h9.2C19.4,3,20,3.6,20,4.4z"></path>
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