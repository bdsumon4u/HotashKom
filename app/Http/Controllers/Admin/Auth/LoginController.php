<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Notifications\Admin\SendOTP;
use Hotash\LaravelMultiUi\Backend\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    /**
     * Where to redirect users after logout.
     *
     * @var string
     */
    protected $redirectLoggedOut;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws ValidationException
     */
    public function showLoginForm(Request $request)
    {
        if (! (setting('show_option')->admin_otp ?? false)) {
            return view('admin.auth.login');
        }

        return view('admin.auth');
    }

    /**
     * Handle a login request to the application.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (! $user = $this->getUser()) {
            $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);
        }

        $request->merge([
            $this->getLoginType($request) => $request->input('login'),
        ]);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the login field and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ((setting('show_option')->admin_otp ?? false) && Hash::check($request->input('password'), $user->password)) {
            if (! $request->otp) {
                $this->sendOTP($user);

                return redirect()->back()->withInput()->withErrors([
                    'otp' => 'Please enter the OTP.',
                ])->with('token:sent', 'An OTP has been sent to company phone.');
            }

            if (Cache::get('auth:'.$request->input('login')) != $request->otp) {
                return redirect()->back()->withInput()->withErrors([
                    'otp' => 'Invalid OTP.',
                ])->with('token:sent', 'Didn\'t match OTP sent to company phone.');
            }
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new Response('', 204)
            : redirect($this->redirectLoggedOut ?? route('admin.login'));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }

    private function getUser()
    {
        return Admin::query()->firstWhere('email', \request()->get('login'));
    }

    /**
     * @throws ValidationException
     */
    private function sendOTP(&$user): void
    {
        if (Cache::get($key = 'auth:'.\request()->get('login'))) {
            throw ValidationException::withMessages([
                'password' => ['Please wait for OTP.'],
            ]);
        }
        $ttl = (property_exists($this, 'decayMinutes') ? $this->decayMinutes : 2) * 60;
        $otp = Cache::remember($key, $ttl, fn(): int => mt_rand(1000, 999999));
        $user->notify(new SendOTP($otp));
    }
}
