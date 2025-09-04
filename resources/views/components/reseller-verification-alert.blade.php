@if(auth()->check() && isOninda() && !auth()->user()->is_verified && !request()->routeIs('user.payment.verification'))
<div class="alert alert-warning">
    <h4 class="alert-heading">
        <svg class="me-2" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
            style="flex-shrink:0;">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
            <line x1="12" y1="8" x2="12" y2="12" stroke="currentColor" stroke-width="2" />
            <circle cx="12" cy="16" r="1" fill="currentColor" />
        </svg>
        Account Not Verified!
    </h4>
    <p>Your reseller account is not verified yet. To get verified:</p>
    <div class="row">
        <div class="col-md-8">
            <ol>
                <li>Pay verification fee using bKash payment</li>
                <li>Your account will be automatically verified</li>
                <li>You can apply coupons for discounts</li>
            </ol>
            <a href="{{ route('user.payment.verification') }}" class="alert-link">Click here to verify your account.</a>
        </div>
        <div class="text-right col-md-4">
            <a href="{{ route('user.payment.verification') }}"
                class="mt-2 d-flex btn btn-primary align-items-center justify-content-center mt-md-0">
                <i class="mr-2 fa fa-credit-card"></i> Verify Account
            </a>
        </div>
    </div>
</div>
@endif
