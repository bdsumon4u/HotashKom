<header id="header" class="top-0 site-header header-builder position-fixed w-100" style="z-index: 1000;">
    <div class="header-main">
        <div class="container">
            <div class="row">
                <div class="header-col header-col-left col-lg-3 col-xl-3 d-none d-lg-flex d-xl-flex">
                    <div class="header-logo">
                        <a href="/" rel="home">
                            <img src="{{ asset($logo->desktop ?? '') }}" style="max-height: 70px;" alt="Logo">
                        </a>
                    </div>
                </div>
                <div class="header-col header-col-center col-lg-6 col-xl-6 d-none d-lg-flex d-xl-flex">
                    <div class="main-navigation kapee-navigation">
                        <ul id="menu-mobile-menu" class="menu">
                            @include('layouts.yellow.header-menu-items')
                        </ul>
                    </div>
                </div>
                <div class="header-col header-col-right col-lg-3 col-xl-3 d-none d-lg-flex d-xl-flex">
                    <div class="kapee-ajax-search ajax-search-style-1 ajax-search-square">
                        <form method="get" class="searchform" action="/shop"> <input
                                type="search" class="search-field" name="search" value=""
                                placeholder="Search for products, categories, brands, sku..." autocomplete="off">
                            <button type="submit" class="search-submit">Search</button>
                        </form>
                        <div class="search-results-wrapper woocommerce">
                            <div class="autocomplete-suggestions"
                                style="position: absolute; display: none; max-height: 300px; z-index: 9999;">
                            </div>
                        </div>
                        <div class="trending-search-results" style="display: none;"></div>
                    </div>
                </div>
                <div class="header-col header-col-left col-2 d-flex d-lg-none d-xl-none">
                    <div class="mobile-navbar"> <a href="#" class="navbar-toggle"> <span
                                class="navbar-icon"><i class="fa fa-bars"></i></span> <span
                                class="navbar-label">Menu</span> </a></div>
                </div>
                <div class="header-col header-col-center col-3 justify-content-center d-flex d-lg-none d-xl-none">
                    <div class="header-logo"> <a href="/" rel="home">
                            <img src="{{ asset($logo->mobile ?? '') }}" style="max-height: 50px;" alt="Logo">
                        </a></div>
                </div>
                <div class="header-col header-col-center col-7 justify-content-center d-flex d-lg-none d-xl-none">
                    <div class="kapee-ajax-search ajax-search-style-1 ajax-search-square">
                        <form method="get" class="searchform" action="/shop"> <input
                                type="search" class="search-field" name="search" value=""
                                placeholder="Search for products, categories, brands, sku..." autocomplete="off">
                            <button type="submit" class="search-submit">Search</button>
                        </form>
                        <div class="search-results-wrapper woocommerce">
                            <div class="autocomplete-suggestions"
                                style="position: absolute; display: none; max-height: 300px; z-index: 9999;">
                            </div>
                        </div>
                        <div class="trending-search-results" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
