<div class="col-12 col-lg-3 d-flex">
    <div class="account-nav flex-grow-1">
        <h4 class="account-nav__title">Navigation</h4>
        <ul class="account-nav__list">
            <li class="account-nav__item">
                <a href="{{ route('user.profile') }}" class="account-nav__link{{ Route::is('user.profile') ? ' account-nav__link--active' : '' }}">Profile</a>
            </li>
            <li class="account-nav__item">
                <a href="{{ route('user.orders') }}" class="account-nav__link{{ Route::is('user.orders') ? ' account-nav__link--active' : '' }}">Order History</a>
            </li>
            <li class="account-nav__item">
                <a href="{{ route('user.transactions') }}" class="account-nav__link{{ Route::is('user.transactions') ? ' account-nav__link--active' : '' }}">Transactions</a>
            </li>
        </ul>
    </div>
</div>

@push('styles')
<style>
.account-nav {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 6px;
    padding: 2rem 1.5rem;
}
.account-nav__title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #343a40;
    margin-bottom: 1.5rem;
}
.account-nav__list {
    list-style: none;
    margin: 0;
    padding: 0;
}
.account-nav__item {
    margin-bottom: 0.75rem;
}
.account-nav__item:last-child {
    margin-bottom: 0;
}
.account-nav__link {
    display: block;
    color: #6c757d;
    font-weight: 400;
    text-decoration: none;
    padding: 0.1rem 0 0.1rem 0.75rem;
    border-left: 3px solid transparent;
    transition: color 0.2s;
    background: none;
}
.account-nav__link--active {
    color: #23272b !important;
    font-weight: 700;
    border-left: 3px solid #ffc107;
    background: none;
}
</style>
@endpush
