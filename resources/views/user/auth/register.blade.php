@extends('layouts.yellow.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center align-items-center">
        <div class="col-md-10">
            <div class="overflow-hidden rounded-lg border-0 shadow-lg card">
                <div class="row no-gutters">
                    <div class="bg-white col-md-4 align-items-center justify-content-center">
                        <div class="p-4 text-center w-100"
                            style="background: rgba(255,255,255,0.85); border-radius: 1rem;">
                            <span class="mb-3 d-block"
                                style="font-size: 2.5rem; font-weight: bold; color: #ffb200; letter-spacing: 2px; font-family: 'Noto Sans Bengali', sans-serif;">{{ config('app.name') }}</span>
                            <h4 class="mb-0 font-weight-bold" style="color: #ffb200;">Become a Reseller!</h4>
                            <p class="mt-2 mb-0 text-muted">Register to start your journey</p>
                        </div>
                    </div>
                    <div class="p-4 bg-white col-md-8 p-md-5 d-flex align-items-center">
                        <div class="w-100">
                            <h3 class="mb-3 text-center d-none d-md-block font-weight-bold" style="color: #ffb200;">
                                Create an Account
                            </h3>
                            @foreach($errors->all() as $error)
                            <div class="alert alert-danger" role="alert">
                                {{ $error }}
                            </div>
                            @endforeach
                            <form method="POST" action="{{ route('user.register') }}">
                                @csrf
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="name">Reseller Name</label>
                                        <input id="name" type="text"
                                            class="form-control @error('name') is-invalid @enderror" name="name"
                                            value="{{ old('name') }}" required autocomplete="name" autofocus
                                            placeholder="Enter your name">
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="shop_name">Shop Name</label>
                                        <input id="shop_name" type="text"
                                            class="form-control @error('shop_name') is-invalid @enderror" name="shop_name"
                                            value="{{ old('shop_name') }}" required autocomplete="shop_name"
                                            placeholder="Enter your shop name">
                                        @error('shop_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="email">Email Address</label>
                                        <input id="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            name="email" value="{{ old('email') }}" required
                                            autocomplete="email" placeholder="Enter your email address">
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="phone_number">Phone Number</label>
                                        <input id="phone_number" type="text"
                                            class="form-control @error('phone_number') is-invalid @enderror"
                                            name="phone_number" value="{{ old('phone_number') }}" required
                                            autocomplete="phone_number" placeholder="Enter your phone number">
                                        @error('phone_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="bkash_number">bKash Number</label>
                                        <input id="bkash_number" type="text"
                                            class="form-control @error('bkash_number') is-invalid @enderror"
                                            name="bkash_number" value="{{ old('bkash_number') }}" required
                                            autocomplete="bkash_number" placeholder="Enter your bKash number">
                                        @error('bkash_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="password">Password</label>
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            required autocomplete="new-password" placeholder="Create a password">
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="password-confirm">Confirm Password</label>
                                        <input id="password-confirm" type="password" class="form-control"
                                            name="password_confirmation" required autocomplete="new-password"
                                            placeholder="Confirm your password">
                                    </div>
                                </div>
                                <button type="submit" class="mt-3 btn btn-block"
                                    style="background: #ffb200; color: #fff; font-weight: bold;">
                                    {{ __('Register') }}
                                </button>
                                <div class="mt-4 text-center">
                                    <a href="{{ route('user.login') }}"
                                        class="btn btn-outline-warning font-weight-bold">Already have an account?
                                        Login</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
