<header id="header" class="site-header header-builder">
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
                        <form method="get" class="searchform" action="https://maroonedbd.com/"> <input
                                type="search" class="search-field" name="s" value=""
                                placeholder="Search for products, categories, brands, sku..." autocomplete="off">
                            <button type="submit" class="search-submit">Search</button>
                            <input type="hidden" name="post_type" value="product">
                        </form>
                        <div class="search-results-wrapper woocommerce">
                            <div class="autocomplete-suggestions"
                                style="position: absolute; display: none; max-height: 300px; z-index: 9999;">
                            </div>
                        </div>
                        <div class="trending-search-results" style="display: none;"></div>
                    </div>
                </div>
                <div class="header-col header-col-left col-6 d-flex d-lg-none d-xl-none">
                    <div class="mobile-navbar"> <a href="#" class="navbar-toggle"> <span
                                class="navbar-icon"><i class="fa fa-bars"></i></span> <span
                                class="navbar-label">Menu</span> </a></div>
                    <div class="header-logo"> <a href="/" rel="home">
                            <img src="{{ asset($logo->mobile ?? '') }}" style="max-height: 50px;" alt="Logo">
                        </a></div>
                </div>
                <div class="header-col header-col-right col-6 d-flex d-lg-none d-xl-none"></div>
            </div>
        </div>
    </div>
    <div class="header-navigation d-flex d-lg-none d-xl-none">
        <div class="container">
            <div class="row">
                <div class="header-col header-col-left col-lg-3 col-xl-3 d-none d-lg-flex d-xl-flex"></div>
                <div class="header-col header-col-center col-lg-9 col-xl-9 d-none d-lg-flex d-xl-flex"></div>
                <div class="header-col header-col-center col-12 d-flex d-lg-none d-xl-none">
                    <div class="kapee-ajax-search ajax-search-style-1 ajax-search-square">
                        <form method="get" class="searchform" action="https://maroonedbd.com/"> <input
                                type="search" class="search-field" name="s" value=""
                                placeholder="Search for products, categories, brands, sku..." autocomplete="off">
                            <button type="submit" class="search-submit">Search</button>
                            <input type="hidden" name="post_type" value="product">
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
    <div class="header-sticky" style="top: 0px;">
        <div class="container">
            <div class="row">
                <div class="header-col header-col-left col-lg-3 col-xl-3 d-none d-lg-flex d-xl-flex">
                    <div class="header-logo"> <a href="/" rel="home">
                            <a href="/" rel="home">
                                <img src="{{ asset($logo->mobile ?? '') }}" style="max-height: 50px;" alt="Logo">
                            </a>
                        </a></div>
                </div>
                <div class="header-col header-col-center col-lg-6 col-xl-6 d-none d-lg-flex d-xl-flex">
                    <div class="main-navigation kapee-navigation">
                        <ul id="menu-mobile-menu-1" class="menu">
                            @include('layouts.yellow.header-menu-items')
                        </ul>
                    </div>
                </div>
                <div class="header-col header-col-right col-lg-3 col-xl-3 d-none d-lg-flex d-xl-flex">
                    <div class="kapee-ajax-search ajax-search-style-1 ajax-search-square">
                        <form method="get" class="searchform" action="https://maroonedbd.com/"> <input
                                type="search" class="search-field" name="s" value=""
                                placeholder="Search for products, categories, brands, sku..." autocomplete="off">
                            <button type="submit" class="search-submit">Search</button>
                            <input type="hidden" name="post_type" value="product">
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
                <div class="header-col header-col-center col-8 justify-content-center d-flex d-lg-none d-xl-none">
                    <div class="kapee-ajax-search ajax-search-style-1 ajax-search-square">
                        <form method="get" class="searchform" action="https://maroonedbd.com/"> <input
                                type="search" class="search-field" name="s" value=""
                                placeholder="Search for products, categories, brands, sku..." autocomplete="off">
                            <button type="submit" class="search-submit">Search</button>
                            <input type="hidden" name="post_type" value="product">
                        </form>
                        <div class="search-results-wrapper woocommerce">
                            <div class="autocomplete-suggestions"
                                style="position: absolute; display: none; max-height: 300px; z-index: 9999;">
                            </div>
                        </div>
                        <div class="trending-search-results" style="display: none;"></div>
                    </div>
                </div>
                <div class="header-col header-col-right col-2 d-flex d-lg-none d-xl-none"></div>
            </div>
        </div>
    </div>
</header>
