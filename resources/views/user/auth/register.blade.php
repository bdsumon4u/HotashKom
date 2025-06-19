@extends('layouts.yellow.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
                <div class="row no-gutters">
                    <div class="col-md-5 d-none d-md-flex align-items-center justify-content-center bg-white">
                        <div class="w-100 text-center p-4" style="background: rgba(255,255,255,0.85); border-radius: 1rem;">
                            <span class="mb-3 d-block" style="font-size: 2.5rem; font-weight: bold; color: #ffb200; letter-spacing: 2px; font-family: 'Noto Sans Bengali', sans-serif;">Oninda</span>
                            <h4 class="font-weight-bold mb-0" style="color: #ffb200;">Become a Reseller!</h4>
                            <p class="mt-2 mb-0 text-muted">Register to start your journey</p>
                        </div>
                    </div>
                    <div class="col-md-7 bg-white p-4 p-md-5 d-flex align-items-center">
                        <div class="w-100">
                            <h3 class="mb-3 text-center font-weight-bold" style="color: #ffb200;">Reseller Registration</h3>
                            <div class="alert alert-warning text-center mb-4" style="font-size: 1rem;">
                                Registration korar por <strong>01666666666</strong> number a <strong>500 tk</strong> bkash payment korun.<br>
                                Verification er jonno amra apnake call korbo, wait korun.<br>
                                Othoba payment korar por amader k call korun.
                            </div>
                            @foreach($errors->all() as $error)
                                <div class="alert alert-danger" role="alert">
                                    {{ $error }}
                                </div>
                            @endforeach
                            <form method="POST" action="{{ route('user.register') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="name">Reseller Name</label>
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Enter your name">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="shop_name">Shop Name</label>
                                    <input id="shop_name" type="text" class="form-control @error('shop_name') is-invalid @enderror" name="shop_name" value="{{ old('shop_name') }}" required autocomplete="shop_name" placeholder="Enter your shop name">
                                    @error('shop_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="phone_number">Phone Number</label>
                                    <input id="phone_number" type="text" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number') }}" required autocomplete="phone_number" placeholder="Enter your phone number">
                                    @error('phone_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="bkash_number">bKash Number</label>
                                    <input id="bkash_number" type="text" class="form-control @error('bkash_number') is-invalid @enderror" name="bkash_number" value="{{ old('bkash_number') }}" required autocomplete="bkash_number" placeholder="Enter your bKash number">
                                    @error('bkash_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Create a password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="password-confirm">Confirm Password</label>
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password">
                                </div>
                                <button type="submit" class="btn btn-block mt-3" style="background: #ffb200; color: #fff; font-weight: bold;">
                                    {{ __('Register') }}
                                </button>
                                <div class="text-center mt-4">
                                    <a href="{{ route('user.login') }}" class="btn btn-outline-warning font-weight-bold">Already have an account? Login</a>
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
