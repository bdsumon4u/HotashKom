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
                                <ol>
                                    <li>Pay 500 tk to bKash number: 01767677777</li>
                                    <li>Wait for admin verification</li>
                                    <li>For immediate verification, please call us</li>
                                </ol>
                            </div>
                        @endunless

                        <x-form method="POST" :action="route('user.profile')">
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

                                @if($user->api_token)
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="api_token">API Token</label>
                                        <div class="input-group">
                                            <x-input name="api_token" id="api_token" :value="$user->api_token" readonly />
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary" onclick="copyApiToken()">
                                                    Copy
                                                </button>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Use this token to authenticate your API requests</small>
                                    </div>
                                </div>
                                @endif

                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Database Configuration</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="db_name">Database Name</label>
                                                        <x-input name="db_name" id="db_name" placeholder="Database Name"
                                                            :value="$user->db_name" />
                                                        <x-error field="db_name" />
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="db_username">Database Username</label>
                                                        <x-input name="db_username" id="db_username" placeholder="Database Username"
                                                            :value="$user->db_username" />
                                                        <x-error field="db_username" />
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="db_password">Database Password</label>
                                                        <x-input type="password" name="db_password" id="db_password"
                                                            placeholder="Database Password" />
                                                        <x-error field="db_password" />
                                                        <small class="form-text text-muted">Leave blank if you don't want to change the password</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
</script>
@endpush
