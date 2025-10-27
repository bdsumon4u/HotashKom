@extends('layouts.reseller.master')

@section('title', 'Profile')

@section('breadcrumb-title')
    <h3>Profile</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Profile</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Profile</h5>
                    </div>
                    <div class="card-body">
                        @if (session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                {{ session('warning') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('reseller.profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @php($user = auth()->user())
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               name="name" id="name" placeholder="Full Name"
                                               value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="shop_name">Shop Name</label>
                                        <input type="text" class="form-control @error('shop_name') is-invalid @enderror"
                                               name="shop_name" id="shop_name" placeholder="Shop Name"
                                               value="{{ old('shop_name', $user->shop_name) }}" required>
                                        @error('shop_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="profile-email">Email Address</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                               name="email" id="profile-email" placeholder="Email Address"
                                               value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="profile-phone">Phone Number</label>
                                        <input type="tel" class="form-control @error('phone_number') is-invalid @enderror"
                                               name="phone_number" id="profile-phone" placeholder="Phone Number"
                                               value="{{ old('phone_number', $user->phone_number) }}" required>
                                        @error('phone_number')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bkash_number">bKash Number</label>
                                        <input type="tel" class="form-control @error('bkash_number') is-invalid @enderror"
                                               name="bkash_number" id="bkash_number" placeholder="bKash Number"
                                               value="{{ old('bkash_number', $user->bkash_number) }}" required>
                                        @error('bkash_number')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <input type="text" class="form-control @error('address') is-invalid @enderror"
                                               name="address" id="address" placeholder="Enter Your Address"
                                               value="{{ old('address', $user->address) }}">
                                        @error('address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Bottom Section - Split into two columns -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="domain">Domain</label>
                                        <input type="text" class="form-control @error('domain') is-invalid @enderror"
                                               name="domain" id="domain" placeholder="Your Domain (e.g. myshop.com)"
                                               value="{{ old('domain', $user->domain) }}">
                                        @error('domain')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">Your custom domain for your shop</small>
                                    </div>

                                    <!-- Logo Upload Section -->
                                    <div class="form-group">
                                        <label for="logo">Reseller Logo (optional)</label>
                                        @if($user->logo)
                                            <div class="mb-2">
                                                <img id="logo-preview" src="{{ asset('storage/' . $user->logo) }}" alt="Reseller Logo" style="max-height: 80px;">
                                            </div>
                                        @else
                                            <img id="logo-preview" src="" alt="Reseller Logo" style="max-height: 80px; display: none;">
                                        @endif
                                        <input type="file" name="logo" id="logo" class="form-control-file @error('logo') is-invalid @enderror">
                                        @error('logo')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <small class="form-text text-muted">Upload your shop logo. This will appear on your invoices.</small>
                                    </div>
                                </div>

                                <!-- Right Column - Shipping Costs -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="px-4 py-3 shadow-sm rounded-0 card-header">
                                            <h6 class="mb-0">Shipping Cost Settings</h6>
                                        </div>
                                        <div class="p-4 shadow-sm card-body rounded-0">
                                            <div class="form-group">
                                                <label for="inside_dhaka_shipping">Inside Dhaka Shipping Cost</label>
                                                <div class="input-group">
                                                                                                    <input type="text" class="form-control @error('inside_dhaka_shipping') is-invalid @enderror"
                                                       name="inside_dhaka_shipping" id="inside_dhaka_shipping"
                                                       placeholder="0" min="0" step="1"
                                                       value="{{ old('inside_dhaka_shipping', $user->inside_dhaka_shipping ?? 0) }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">{{config('app.currency')}}</span>
                                                    </div>
                                                </div>
                                                @error('inside_dhaka_shipping')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                                <small class="form-text text-muted">Shipping cost for orders within Dhaka city</small>
                                            </div>

                                            <div class="form-group">
                                                <label for="outside_dhaka_shipping">Outside Dhaka Shipping Cost</label>
                                                <div class="input-group">
                                                                                                    <input type="text" class="form-control @error('outside_dhaka_shipping') is-invalid @enderror"
                                                       name="outside_dhaka_shipping" id="outside_dhaka_shipping"
                                                       placeholder="0" min="0" step="1"
                                                       value="{{ old('outside_dhaka_shipping', $user->outside_dhaka_shipping ?? 0) }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">{{config('app.currency')}}</span>
                                                    </div>
                                                </div>
                                                @error('outside_dhaka_shipping')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                                <small class="form-text text-muted">Shipping cost for orders outside Dhaka city</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-0 form-group">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
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
