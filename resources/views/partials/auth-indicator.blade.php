@if ($show_option->customer_login ?? isOninda())
<div class="ml-2 indicator">
    <a href="{{ auth('user')->check() ? route('reseller.dashboard') : route('user.login') }}" class="indicator__button">
        <span class="indicator__area">
            <svg width="20" height="20">
                <path
                    d="M13.7,10.7C15.1,9.6,16,7.9,16,6c0-3.3-2.7-6-6-6S4,2.7,4,6c0,1.9,0.9,3.6,2.3,4.7C2.6,12.2,0,15.8,0,20h2c0-4.4,3.6-8,8-8 s8,3.6,8,8h2C20,15.8,17.4,12.2,13.7,10.7z M6,6c0-2.2,1.8-4,4-4s4,1.8,4,4c0,2.2-1.8,4-4,4S6,8.2,6,6z">
                </path>
            </svg>
        </span>
    </a>
</div>
@endif

{{-- @guest('user')
    @if ($show_option->customer_login ?? isOninda())
        <div class="ml-2 indicator">
            <a href="{{ auth('admin')->check() ? route('admin.home') : route('user.login') }}" class="indicator__button">
                <span class="indicator__area">
                    <svg width="20" height="20">
                        <path
                            d="M13.7,10.7C15.1,9.6,16,7.9,16,6c0-3.3-2.7-6-6-6S4,2.7,4,6c0,1.9,0.9,3.6,2.3,4.7C2.6,12.2,0,15.8,0,20h2c0-4.4,3.6-8,8-8 s8,3.6,8,8h2C20,15.8,17.4,12.2,13.7,10.7z M6,6c0-2.2,1.8-4,4-4s4,1.8,4,4c0,2.2-1.8,4-4,4S6,8.2,6,6z">
                        </path>
                    </svg>
                </span>
            </a>
        </div>
    @endif
@else
    <div class="ml-2 indicator indicator--trigger--click indicator--hover">
        <a href="#" class="indicator__button">
            <span class="indicator__area">
                <svg width="20" height="20">
                    <path
                        d="M13.7,10.7C15.1,9.6,16,7.9,16,6c0-3.3-2.7-6-6-6S4,2.7,4,6c0,1.9,0.9,3.6,2.3,4.7C2.6,12.2,0,15.8,0,20h2c0-4.4,3.6-8,8-8 s8,3.6,8,8h2C20,15.8,17.4,12.2,13.7,10.7z M6,6c0-2.2,1.8-4,4-4s4,1.8,4,4c0,2.2-1.8,4-4,4S6,8.2,6,6z">
                    </path>
                </svg>
            </span>
        </a>
        <div class="indicator__dropdown">
            <div class="account-menu">
                <div class="px-3 py-2 account-menu__user-info w-100">
                    <div class="account-menu__user-name">{{ auth('user')->user()->name }}</div>
                    <div class="account-menu__user-email">
                        {{ auth('user')->user()->phone_number }}
                    </div>
                </div>
                <div class="account-menu__divider"></div>
                <ul class="account-menu__links">
                    <li><a href="{{ route('reseller.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('user.profile') }}">Edit Profile</a></li>
                    <li><a href="{{ route('user.orders') }}">Order History</a></li>
                    <li><a href="{{ route('user.transactions') }}">Transactions</a></li>
                </ul>
                <div class="account-menu__divider"></div>
                <ul class="account-menu__links">
                    <li>
                        <x-form action="{{ route('user.logout') }}" method="POST">
                            <a href="{{ route('user.logout') }}"
                                onclick="event.preventDefault();
                            this.parentNode.submit();">
                                {{ __('Logout') }}
                            </a>
                        </x-form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endguest --}}
