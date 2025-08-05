<div class="block">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col">
                <div class="border-0 shadow-lg card">
                    <div class="p-0 card-body">
                        <div class="row no-gutters">
                            <!-- Login Form -->
                            <div class="p-4 col-md-6 p-md-5">
                                <div class="mb-4 text-center">
                                    <h4 class="font-weight-bold" style="color: #ffb200;">Login</h4>
                                    <p class="text-muted">Sign in to your account</p>
                                </div>

                                @if(session('error'))
                                    <div class="alert alert-danger" role="alert">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('user.login') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="login">Mobile or Email</label>
                                        <input id="login" type="text"
                                               class="form-control @error('login') is-invalid @enderror"
                                               name="login" value="{{ old('login') }}"
                                               required autocomplete="login" autofocus
                                               placeholder="Enter your mobile or email">
                                        @error('login')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input id="password" type="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               name="password" required
                                               autocomplete="current-password"
                                               placeholder="Enter your password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="remember" id="remember"
                                               {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>

                                    <button type="submit" class="btn btn-block"
                                            style="background: #ffb200; color: #fff; font-weight: bold;">
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
                            </div>

                            <!-- Registration Form -->
                            <div class="p-4 col-md-6 p-md-5" style="background: rgba(255, 178, 0, 0.05);">
                                <div class="mb-4 text-center">
                                    <h4 class="font-weight-bold" style="color: #ffb200;">Register</h4>
                                    <p class="text-muted">Create your reseller account</p>
                                </div>

                                @if($errors->any())
                                    <div class="alert alert-danger" role="alert">
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('user.register') }}">
                                    @csrf
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="name">Reseller Name</label>
                                            <input id="name" type="text"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   name="name" value="{{ old('name') }}"
                                                   required autocomplete="name"
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
                                                   class="form-control @error('shop_name') is-invalid @enderror"
                                                   name="shop_name" value="{{ old('shop_name') }}"
                                                   required autocomplete="shop_name"
                                                   placeholder="Enter your shop name">
                                            @error('shop_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input id="email" type="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               name="email" value="{{ old('email') }}"
                                               required autocomplete="email"
                                               placeholder="Enter your email address">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="phone_number">Phone Number</label>
                                            <input id="phone_number" type="text"
                                                   class="form-control @error('phone_number') is-invalid @enderror"
                                                   name="phone_number" value="{{ old('phone_number') }}"
                                                   required autocomplete="phone_number"
                                                   placeholder="Enter your phone number">
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
                                                   name="bkash_number" value="{{ old('bkash_number') }}"
                                                   required autocomplete="bkash_number"
                                                   placeholder="Enter your bKash number">
                                            @error('bkash_number')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="reg_password">Password</label>
                                            <input id="reg_password" type="password"
                                                   class="form-control @error('password') is-invalid @enderror"
                                                   name="password" required
                                                   autocomplete="new-password"
                                                   placeholder="Create a password">
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="password-confirm">Confirm Password</label>
                                            <input id="password-confirm" type="password"
                                                   class="form-control" name="password_confirmation"
                                                   required autocomplete="new-password"
                                                   placeholder="Confirm your password">
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-block"
                                            style="background: #ffb200; color: #fff; font-weight: bold;">
                                        {{ __('Register') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
