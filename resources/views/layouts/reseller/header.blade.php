<div class="page-main-header">
  <div class="m-0 main-header-right row">
    <div class="main-header-left">
      <div class="logo-wrapper">
        <a href="{{ route('reseller.dashboard') }}">
          <img class="img-fluid" src="{{ asset($logo->desktop ?? '') }}" alt="Logo">
        </a>
      </div>
      <button class="toggle-nav toggler-sidebar" id="sidebar-toggler" style="padding:2px 10px;border:none;background:none;"><i class="status_toggle middle" data-feather="grid"> </i></button>
    </div>
    <div class="pl-0 left-menu-header col horizontal-wrapper">
      <ul class="horizontal-menu">
        <li class=""><a class="nav-link text-nowrap" href="{{ route('/') }}" target="_blank">View Store</a></li>
      </ul>
    </div>
    <div class="nav-right col-8 pull-right right-menu">
      <ul class="mr-0 nav-menus">
        <li class="maximize"><a class="text-dark" href="#!" onclick="javascript:toggleFullScreen()"><i data-feather="maximize"></i></a></li>

        <!-- Cart Indicator -->
        <a href="{{ route('reseller.checkout') }}" class="mx-3 text-dark" wire:navigate.hover>
            <i data-feather="shopping-cart"></i>
            <livewire:cart-count />
        </a>

        <li class="p-0 profile-nav onhover-dropdown">
          <div class="media profile-media">
            <img class="b-r-10" src="{{ asset('assets/images/dashboard/profile.jpg') }}" alt="">
            <div class="media-body">
              <span>{{ auth('user')->user()->name }}</span>
              <p class="mb-0 font-roboto">Reseller <i class="middle fa fa-angle-down"></i></p>
            </div>
          </div>
          <ul class="profile-dropdown onhover-show-div">
            <li><a href="{{ route('reseller.profile') }}"><i data-feather="user"></i><span>Profile</span></a></li>
            <li>
              <a class="" href="{{ route('user.logout') }}"
                onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
                <i data-feather="log-in"> </i> {{ __('Logout') }}
              </a>

              <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</div>
