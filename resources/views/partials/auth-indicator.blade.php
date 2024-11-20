@guest('user')
@if($show_option->customer_login ?? false)
<div class="indicator ml-2">
    <a href="{{ auth('admin')->check() ? route('admin.home') : route('auth') }}" class="indicator__button">
        <span class="indicator__area">
            <!-- <svg xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 0 32 32" width="32">
                <g data-name="1" id="_1">
                    <path d="M27,3V29a1,1,0,0,1-1,1H6a1,1,0,0,1-1-1V27H7v1H25V4H7V7H5V3A1,1,0,0,1,6,2H26A1,1,0,0,1,27,3ZM12.29,20.29l1.42,1.42,5-5a1,1,0,0,0,0-1.42l-5-5-1.42,1.42L15.59,15H5v2H15.59Z" id="login_account_enter_door"/>
                </g>
            </svg> -->
            <svg width="20" height="20">
                <path d="M13.7,10.7C15.1,9.6,16,7.9,16,6c0-3.3-2.7-6-6-6S4,2.7,4,6c0,1.9,0.9,3.6,2.3,4.7C2.6,12.2,0,15.8,0,20h2c0-4.4,3.6-8,8-8 s8,3.6,8,8h2C20,15.8,17.4,12.2,13.7,10.7z M6,6c0-2.2,1.8-4,4-4s4,1.8,4,4c0,2.2-1.8,4-4,4S6,8.2,6,6z"></path>
            </svg>
        </span>
    </a>
</div>
@endif
@else
<div class="indicator ml-2 indicator--trigger--click indicator--hover">
    <a href="#" class="indicator__button">
        <span class="indicator__area">
            <svg width="20" height="20">
                <path d="M13.7,10.7C15.1,9.6,16,7.9,16,6c0-3.3-2.7-6-6-6S4,2.7,4,6c0,1.9,0.9,3.6,2.3,4.7C2.6,12.2,0,15.8,0,20h2c0-4.4,3.6-8,8-8 s8,3.6,8,8h2C20,15.8,17.4,12.2,13.7,10.7z M6,6c0-2.2,1.8-4,4-4s4,1.8,4,4c0,2.2-1.8,4-4,4S6,8.2,6,6z"></path>
            </svg>
        </span>
    </a>
    <div class="indicator__dropdown">
        <div class="account-menu">
            <div class="account-menu__user-info w-100 px-3 py-2">
                <div class="account-menu__user-name">{{ auth('user')->user()->name }}</div>
                <div class="account-menu__user-email">
                    Registered number: {{ auth('user')->user()->phone_number }}
                </div>
            </div>
            <div class="account-menu__divider"></div>
            <ul class="account-menu__links">
                <li><a href="{{ route('user.profile.edit') }}">Edit Profile</a></li>
                <li><a href="{{ route('user.orders') }}">Order History</a></li>
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
@endguest
