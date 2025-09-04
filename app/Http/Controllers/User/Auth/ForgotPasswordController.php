<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\User\SendOTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        return view('user.auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $phone = Str::replace(['-', ' '], '', $request->phone);
        if (Str::startsWith($phone, '01')) {
            $phone = '+88'.$phone;
        }
        $request->merge(['phone' => $phone]);

        $request->validate([
            'phone' => 'required|string|regex:/^\+8801\d{9}$/',
        ]);

        // Check rate limiting
        if ($this->hasTooManyOtpRequests($request)) {
            return back()->withErrors(['phone' => 'Too many OTP requests. Please try again in a minute.']);
        }

        $user = User::where('phone_number', $request->phone)->first();

        if (! $user) {
            return back()->withErrors(['phone' => 'We can\'t find a user with that phone number.']);
        }

        // Generate OTP
        $otp = $this->generateOtp();

        // Store OTP in cache with expiration time (1 hour)
        $otpKey = 'password_reset_otp_'.$request->phone;
        Cache::put($otpKey, $otp, now()->addHour());

        // Send OTP via SMS
        $user->notify(new SendOTP($otp));

        // Generate a token for password reset form
        $token = Str::random(60);
        Cache::put('password_reset_token_'.$token, $user->id, now()->addHour());

        // Record this request for rate limiting
        RateLimiter::hit($this->throttleKey($request));

        return redirect()->route('user.password.reset', ['token' => $token, 'phone' => $request->phone])
            ->with('status', 'We have sent your OTP code to reset your password!');
    }

    /**
     * Resend OTP to the phone number.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendOtp(Request $request)
    {
        $phone = Str::replace(['-', ' '], '', $request->phone);
        if (Str::startsWith($phone, '01')) {
            $phone = '+88'.$phone;
        }
        $request->merge(['phone' => $phone]);

        $request->validate([
            'phone' => 'required|string',
            'token' => 'required|string',
        ]);

        // Check rate limiting
        if ($this->hasTooManyOtpRequests($request)) {
            return back()->withErrors(['phone' => 'Too many OTP requests. Please try again in a minute.']);
        }

        // Verify the token
        $userId = Cache::get('password_reset_token_'.$request->token);
        if (! $userId) {
            return back()->withErrors(['token' => 'This password reset token is invalid.']);
        }

        $user = User::where('phone_number', $request->phone)->where('id', $userId)->first();
        if (! $user) {
            return back()->withErrors(['phone' => 'We can\'t find a user with that phone number.']);
        }

        // Generate new OTP
        $otp = $this->generateOtp();

        // Store OTP in cache with expiration time (1 hour)
        $otpKey = 'password_reset_otp_'.$request->phone;
        Cache::put($otpKey, $otp, now()->addHour());

        // Send OTP via SMS
        $user->notify(new SendOTP($otp));

        // Record this request for rate limiting
        RateLimiter::hit($this->throttleKey($request));

        return back()->with('status', 'We have resent your OTP code!');
    }

    /**
     * Generate a random 6-digit OTP code.
     *
     * @return string
     */
    protected function generateOtp()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('users');
    }

    /**
     * Determine if the user has too many failed OTP requests.
     *
     * @return bool
     */
    protected function hasTooManyOtpRequests(Request $request)
    {
        return RateLimiter::tooManyAttempts(
            $this->throttleKey($request),
            $this->maxAttempts()
        );
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input('phone')).'|'.request()->ip();
    }

    /**
     * Get the maximum number of attempts to allow.
     *
     * @return int
     */
    protected function maxAttempts()
    {
        return 3; // Allow 3 OTP requests per minute
    }
}
