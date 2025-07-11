@extends('layouts.yellow.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center align-items-center">
        <div class="col-md-8">
            <div class="overflow-hidden rounded-lg border-0 shadow-lg card">
                <div class="row no-gutters">
                    <div class="bg-white col-md-5 d-none d-md-flex align-items-center justify-content-center">
                        <div class="p-4 text-center w-100" style="background: rgba(255,255,255,0.85); border-radius: 1rem;">
                            <span class="mb-3 d-block" style="font-size: 2.5rem; font-weight: bold; color: #ffb200; letter-spacing: 2px; font-family: 'Noto Sans Bengali', sans-serif;">{{ config('app.name') }}</span>
                            <h4 class="mb-0 font-weight-bold" style="color: #ffb200;">Welcome Back!</h4>
                            <p class="mt-2 mb-0 text-muted">Sign in to your account to continue</p>
                        </div>
                    </div>
                    <div class="p-4 bg-white col-md-7 p-md-5 d-flex align-items-center">
                        <div class="w-100">
                            <h3 class="mb-4 text-center font-weight-bold" style="color: #ffb200;">Login</h3>
                            @foreach($errors->all() as $error)
                                <div class="alert alert-danger" role="alert">
                                    {{ $error }}
                                </div>
                            @endforeach
                            <form method="POST" action="{{ route('user.login') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="login">Mobile or Email</label>
                                    <input id="login" type="text" class="form-control @error('login') is-invalid @enderror" name="login" value="{{ old('login') }}" required autocomplete="login" autofocus placeholder="Enter your mobile or email">
                                    @error('login')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Enter your password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-block" style="background: #ffb200; color: #fff; font-weight: bold;">
                                    {{ __('Login') }}
                                </button>
                                @if (Route::has('user.password.request'))
                                    <div class="mt-2 text-right">
                                        <a class="text-muted" href="{{ route('user.password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    </div>
                                @endif
                            </form>
                            <div class="mt-4 text-center">
                                <a href="{{ route('user.register') }}" class="btn btn-outline-warning font-weight-bold">Create an Account</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
