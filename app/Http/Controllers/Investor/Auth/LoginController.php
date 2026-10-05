<?php

declare(strict_types=1);

namespace App\Http\Controllers\Investor\Auth;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Where to redirect after login.
     */
    protected string $redirectTo = '/investor/dashboard';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest:investor')->except('logout');
    }

    /**
     * Show the login form.
     */
    public function showLoginForm(): View
    {
        return view('investor.auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginValue = trim($request->input('login'));
        $fieldType = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';

        /** @var Investor|null $investor */
        $investor = Investor::where($fieldType, $loginValue)->first();

        if (! $investor) {
            throw ValidationException::withMessages([
                'login' => [trans('auth.failed')],
            ]);
        }

        if (! $investor->is_active) {
            throw ValidationException::withMessages([
                'login' => ['Your investor account is currently inactive. Please contact administration.'],
            ]);
        }

        $credentials = [
            $fieldType => $loginValue,
            'password' => $request->input('password'),
        ];

        if ($this->guard()->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended($this->redirectTo);
        }

        throw ValidationException::withMessages([
            'login' => [trans('auth.failed')],
        ]);
    }

    /**
     * Log out the investor.
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('investor.login');
    }

    /**
     * Get the investor guard.
     */
    protected function guard(): StatefulGuard
    {
        return Auth::guard('investor');
    }
}
