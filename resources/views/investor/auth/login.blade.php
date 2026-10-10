<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ setting('company')->name ?? 'HotashKom' }} - Investor Login</title>
    @include('layouts.light.css')
    <style>
        body {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
        }
        .login-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 440px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .login-header {
            background: #f8fafc;
            padding: 30px 24px 20px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }
        .login-body {
            padding: 30px 28px;
        }
        .btn-investor {
            background: #0ea5e9;
            color: #ffffff;
            font-weight: 600;
            border-radius: 8px;
            padding: 12px;
            font-size: 15px;
            transition: all 0.2s;
        }
        .btn-investor:hover {
            background: #0284c7;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
        }
        .badge-portal {
            background: #e0f2fe;
            color: #0369a1;
            font-size: 12px;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            @if(setting('logo')->login ?? setting('logo')->desktop ?? false)
                <img src="{{ asset(setting('logo')->login ?? setting('logo')->desktop) }}" alt="Logo" style="max-height: 55px; max-width: 180px;">
            @else
                <h3 class="font-weight-bold mb-0 text-dark">{{ setting('company')->name ?? 'HotashKom' }}</h3>
            @endif
            <div>
                <span class="badge-portal"><i class="fa fa-chart-line mr-1"></i> Investor Portal</span>
            </div>
        </div>

        <div class="login-body">
            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('investor.login.submit') }}">
                @csrf

                <div class="form-group mb-3">
                    <label for="login" class="font-weight-600 text-muted small mb-1">Email or Phone Number</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0"><i class="fa fa-user text-muted"></i></span>
                        </div>
                        <input id="login" type="text" class="form-control border-left-0 @error('login') is-invalid @enderror" name="login" value="{{ old('login') }}" required autofocus placeholder="e.g. investor@example.com">
                    </div>
                    @error('login')
                        <span class="text-danger small mt-1 d-block font-weight-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="password" class="font-weight-600 text-muted small mb-1">Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0"><i class="fa fa-lock text-muted"></i></span>
                        </div>
                        <input id="password" type="password" class="form-control border-left-0 @error('password') is-invalid @enderror" name="password" required placeholder="••••••••">
                    </div>
                    @error('password')
                        <span class="text-danger small mt-1 d-block font-weight-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group d-flex justify-content-between align-items-center mb-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="custom-control-label text-muted small" for="remember">Remember me</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-investor btn-block">
                    Sign In to Investor Portal <i class="fa fa-arrow-right ml-1"></i>
                </button>
            </form>

            <div class="mt-4 text-center">
                <small class="text-muted">
                    Investor accounts are strictly managed and issued by administration.
                </small>
            </div>
        </div>
    </div>
</body>
</html>
