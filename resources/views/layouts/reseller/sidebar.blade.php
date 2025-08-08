<header class="main-nav">
    <div class="px-3 py-2 logo-wrapper d-flex align-items-center justify-content-between">
        <a href="{{ route('reseller.dashboard') }}">
            <img class="img-fluid but-not-fluid" src="{{ asset($logo->login ?? (setting('logo')->desktop ?? '')) }}"
                alt="">
        </a>
        <div class="px-3 py-2 back-btn"><i class="fa fa-angle-left"></i></div>
    </div>
    <div class="logo-icon-wrapper">
        <a href="{{ route('reseller.dashboard') }}">
            <img class="img-fluid" src="{{ asset($logo->favicon ?? '') }}" width="36" height="36" alt="">
        </a>
    </div>
    <nav>
        <div class="main-navbar">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="mainnav">
                <ul class="pb-5 nav-menu custom-scrollbar">
                    <li class="back-btn">
                        <a href="{{ route('reseller.dashboard') }}">
                            <img class="img-fluid" src="{{ asset($logo->favicon ?? '') }}" height="36" width="36"
                                alt="">
                        </a>
                        <div class="text-right mobile-back"><span>Back</span><i class="pl-2 fa fa-angle-right"
                                aria-hidden="true"></i></div>
                    </li>
                    <li>
                        <a class="nav-link menu-title link-nav {{ Route::currentRouteName() == 'reseller.dashboard' ? 'active' : '' }}"
                            href="{{ route('reseller.dashboard') }}">
                            <i data-feather="home"> </i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li class="sidebar-title">
                        <h6>Reseller Panel</h6>
                    </li>

                    <li>
                        <a class="nav-link menu-title link-nav {{ request()->is('reseller/orders*') ? 'active' : '' }}"
                            href="{{ route('reseller.orders') }}">
                            <i data-feather="shopping-bag"> </i>
                            <span>Orders</span>
                        </a>
                    </li>

                    <li>
                        <a class="nav-link menu-title link-nav {{ request()->is('reseller/transactions*') ? 'active' : '' }}"
                            href="{{ route('reseller.transactions') }}">
                            <i data-feather="credit-card"> </i>
                            <span>Transactions</span>
                        </a>
                    </li>

                    <li>
                        <a class="nav-link menu-title link-nav {{ request()->is('reseller/profile*') ? 'active' : '' }}"
                            href="{{ route('reseller.profile') }}">
                            <i data-feather="user"> </i>
                            <span>Profile</span>
                        </a>
                    </li>

                    <li>
                        <a class="nav-link menu-title link-nav" href="{{ route('/') }}" target="_blank">
                            <i data-feather="external-link"> </i>
                            <span>View Store</span>
                        </a>
                    </li>

                    <li>
                        <a class="nav-link menu-title link-nav" href="{{ route('user.logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i data-feather="log-out"> </i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </div>
    </nav>
</header>

<form id="logout-form" action="{{ route('user.logout') }}" method="POST" class="d-none">
    @csrf
</form>
