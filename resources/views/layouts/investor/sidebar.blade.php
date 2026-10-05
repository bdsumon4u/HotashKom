<header class="main-nav">
    <div class="px-3 py-2 logo-wrapper d-flex align-items-center justify-content-between">
        <a href="{{ route('investor.dashboard') }}">
            <img class="img-fluid but-not-fluid" src="{{ asset($logo->login ?? (setting('logo')->desktop ?? '')) }}" alt="Logo">
        </a>
        <div class="px-3 py-2 back-btn"><i class="fa fa-angle-left"></i></div>
    </div>
    <div class="logo-icon-wrapper">
        <a href="{{ route('investor.dashboard') }}">
            <img class="img-fluid" src="{{ asset($logo->favicon ?? '') }}" width="36" height="36" alt="Logo">
        </a>
    </div>
    <nav>
        <div class="main-navbar">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="mainnav">
                <ul class="pb-5 nav-menu custom-scrollbar">
                    <li class="back-btn">
                        <a href="{{ route('investor.dashboard') }}">
                            <img class="img-fluid" src="{{ asset($logo->favicon ?? '') }}" height="36" width="36" alt="Logo">
                        </a>
                        <div class="text-right mobile-back"><span>Back</span><i class="pl-2 fa fa-angle-right" aria-hidden="true"></i></div>
                    </li>

                    <li>
                        <a class="nav-link menu-title link-nav {{ Route::currentRouteName() == 'investor.dashboard' ? 'active' : '' }}"
                            href="{{ route('investor.dashboard') }}">
                            <i data-feather="home"> </i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li class="sidebar-title">
                        <h6>Investment Portal</h6>
                    </li>

                    <li>
                        <a class="nav-link menu-title link-nav {{ request()->is('investor/investments*') ? 'active' : '' }}"
                            href="{{ route('investor.investments.index') }}">
                            <i data-feather="trending-up"> </i>
                            <span>My Investments</span>
                        </a>
                    </li>

                    <li>
                        <a class="nav-link menu-title link-nav {{ request()->is('investor/referrals*') ? 'active' : '' }}"
                            href="{{ route('investor.referrals.index') }}">
                            <i data-feather="users"> </i>
                            <span>Referral Program</span>
                        </a>
                    </li>

                    <li>
                        <a class="nav-link menu-title link-nav {{ request()->is('investor/transactions*') ? 'active' : '' }}"
                            href="{{ route('investor.transactions.index') }}">
                            <i data-feather="dollar-sign"> </i>
                            <span>Wallet & Withdraw</span>
                            @php
                                $pendingWithdrawal = auth('investor')->user()?->getPendingWithdrawalAmount() ?? 0;
                            @endphp
                            @if ($pendingWithdrawal > 0)
                                <span class="ml-auto text-white d-flex badge badge-warning align-items-center">
                                    {{ number_format($pendingWithdrawal, 0) }} tk
                                </span>
                            @endif
                        </a>
                    </li>

                    <li>
                        <a class="nav-link menu-title link-nav {{ request()->is('investor/profile*') ? 'active' : '' }}"
                            href="{{ route('investor.profile') }}">
                            <i data-feather="user"> </i>
                            <span>My Profile</span>
                        </a>
                    </li>

                    <li>
                        <a class="nav-link menu-title link-nav" href="{{ route('investor.logout') }}"
                           onclick="event.preventDefault(); document.getElementById('sidebar-investor-logout-form').submit();">
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

<form id="sidebar-investor-logout-form" action="{{ route('investor.logout') }}" method="POST" class="d-none">
    @csrf
</form>
