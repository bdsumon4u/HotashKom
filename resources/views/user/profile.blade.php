@extends('layouts.yellow.master')

@title('Edit Profile')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Profile</h5>
                    </div>
                    <div class="card-divider"></div>
                    <div class="card-body">
                        @if (session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                {{ session('warning') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @unless (auth()->user()->is_verified)
                            <div class="alert alert-warning">
                                <h4 class="alert-heading">Account Not Verified!</h4>
                                <p>Your reseller account is not verified yet. To get verified:</p>
                                <div class="row">
                                    <div class="col-md-8">
                                        <ol>
                                            <li>Pay verification fee using bKash payment</li>
                                            <li>Your account will be automatically verified</li>
                                            <li>You can apply coupons for discounts</li>
                                        </ol>
                                    </div>
                                    <div class="text-right col-md-4">
                                        <a href="{{ route('user.payment.verification') }}" class="d-flex btn btn-primary align-items-center justify-content-center">
                                            <i class="fa fa-credit-card"></i> Verify Account
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endunless

                        <x-form method="POST" :action="route('user.profile')" has-files>
                            @php($user = auth()->user())
                            <div class="row no-gutters">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <x-input name="name" id="name" placeholder="Full Name" :value="$user->name" />
                                        <x-error field="name" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="shop_name">Shop Name</label>
                                        <x-input name="shop_name" id="shop_name" placeholder="Shop Name"
                                            :value="$user->shop_name" />
                                        <x-error field="shop_name" />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="profile-email">Email Address</label>
                                        <x-input type="email" name="email" id="profile-email"
                                            placeholder="Email Address" :value="$user->email" />
                                        <x-error field="email" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="profile-phone">Phone Number</label>
                                        <x-input type="tel" name="phone_number" id="profile-phone"
                                            placeholder="Phone Number" :value="$user->phone_number" />
                                        <x-error field="phone_number" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bkash_number">bKash Number</label>
                                        <x-input type="tel" name="bkash_number" id="bkash_number"
                                            placeholder="bKash Number" :value="$user->bkash_number" />
                                        <x-error field="bkash_number" />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <x-input name="address" id="address" placeholder="Enter Your Address"
                                            :value="$user->address" />
                                        <x-error field="address" />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="domain">Domain</label>
                                        <x-input name="domain" id="domain" placeholder="Your Domain (e.g. myshop.com)"
                                            :value="$user->domain" />
                                        <x-error field="domain" />
                                        <small class="form-text text-muted">Your custom domain for your shop</small>
                                    </div>
                                </div>

                                <!-- Logo Upload Section -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="logo">Reseller Logo (optional)</label>
                                        @if($user->logo)
                                            <div class="mb-2">
                                                <img id="logo-preview" src="{{ asset('storage/' . $user->logo) }}" alt="Reseller Logo" style="max-height: 80px;">
                                            </div>
                                        @else
                                            <img id="logo-preview" src="" alt="Reseller Logo" style="max-height: 80px; display: none;">
                                        @endif
                                        <input type="file" name="logo" id="logo" class="form-control-file">
                                        <x-error field="logo" />
                                        <small class="form-text text-muted">Upload your shop logo. This will appear on your invoices.</small>
                                    </div>
                                </div>

                                <div class="mb-0 form-group">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </x-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function copyApiToken() {
    var apiTokenInput = document.getElementById('api_token');
    apiTokenInput.select();
    document.execCommand('copy');
    alert('API token copied to clipboard!');
}

// Logo preview
$(document).ready(function() {
    $('#logo').change(function() {
        var input = this;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#logo-preview').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(input.files[0]);
        }
    });
});
</script>
@endpush
