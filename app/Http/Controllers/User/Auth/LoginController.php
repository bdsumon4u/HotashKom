<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\User\SendOTP;
use Hotash\LaravelMultiUi\Backend\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
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
    protected $redirectTo = '/';

    /**
     * Where to redirect users after logout.
     *
     * @var string
     */
    protected $redirectLoggedOut;

    protected $decayMinutes = 2;

    protected $maxAttempts = 2;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:user')->except('logout');
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
        if ($phone = $request->get('login')) {
            $phone = Str::replace(['-', ' '], '', $phone);
            if (Str::startsWith($phone, '01')) {
                $phone = '+88'.$phone;
            }
            $request->merge(['login' => $phone]);
            // If the class is using the ThrottlesLogins trait, we can automatically throttle
            // the login attempts for this application. We'll key this by the login field and
            // the IP address of the client making these requests into this application.
            if (method_exists($this, 'hasTooManyLoginAttempts') &&
                $this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);

                return $this->sendLockoutResponse($request);
            }

            if (! $user = $this->getUser($phone)) {
                // If the login attempt was unsuccessful we will increment the number of attempts
                // to login and redirect the user back to the login form. Of course, when this
                // user surpasses their maximum number of attempts they will get locked out.
                $this->incrementLoginAttempts($request);

                return back()->withErrors([
                    'login' => [trans('auth.failed')],
                ]);
            }

            $this->sendOTP($user);

            return redirect()->back()->withInput()
                ->with('token:sent', 'An OTP has been sent to your mobile.');
        }

        if (! isOninda()) {
            return redirect('/');
        }

        return view('user.auth.login');
    }

    public function resendOTP(Request $request)
    {
        $phone = Str::replace(['-', ' '], '', $request->phone);
        if (Str::startsWith($phone, '01')) {
            $phone = '+88'.$phone;
        }
        $request->merge(['phone' => $phone]);

        $user = $this->getUser($request->login);
        $this->sendOTP($user);

        return redirect()->back()->withInput()
            ->with('token:sent', 'An OTP has been sent to your mobile.');
    }

    private function getUser($phone)
    {
        \request()->merge([
            'login' => Str::startsWith($phone, '0') ? '+88'.$phone : $phone,
        ]);
        \request()->validate([
            'login' => 'required|regex:/^\+8801\d{9}$/',
        ]);

        return User::query()->firstWhere('phone_number', \request()->get('login'));
    }

    /**
     * @throws ValidationException
     */
    private function sendOTP(&$user): void
    {
        throw_if(Cache::get($key = 'auth:'.\request()->get('login')), ValidationException::withMessages([
            'password' => ['Please wait for OTP.'],
        ]));
        $ttl = (property_exists($this, 'decayMinutes') ? $this->decayMinutes : 2) * 60;
        $otp = Cache::remember($key, $ttl, fn (): int => mt_rand(1000, 999999));
        $user->notify(new SendOTP($otp));
    }

    /**
     * Get the login type to be used by the controller.
     *
     * @return string|array
     */
    public function loginType(): array
    {
        return ['phone_number', 'email'];
    }

    public function isPhoneNumber($login)
    {
        return Str::startsWith($login, '01') || Str::startsWith($login, '+8801');
    }

    /**
     * Attempt to log the user into the application.
     *
     * @return bool
     *
     * @throws ValidationException
     */
    protected function attemptLogin(Request $request)
    {
        $phone = Str::replace(['-', ' '], '', $request->login);
        if (Str::startsWith($phone, '01')) {
            $phone = '+88'.$phone;
        }
        $request->merge(['login' => $phone, 'phone_number' => $phone]);

        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
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
            : redirect($this->redirectLoggedOut ?? route('user.login'));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('user');
    }

    /**
     * Get the failed login response instance.
     *
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request): never
    {
        throw ValidationException::withMessages([
            'password' => [trans('auth.incorrect')],
        ]);
    }
}
