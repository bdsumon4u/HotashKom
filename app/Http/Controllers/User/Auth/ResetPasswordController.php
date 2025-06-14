<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Hotash\LaravelMultiUi\Backend\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('user.auth.passwords.reset')->with(
            ['token' => $token, 'phone' => $request->phone]
        );
    }

    /**
     * Reset the given user's password.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $phone = Str::replace(['-', ' '], '', $request->phone);
        if (Str::startsWith($phone, '01')) {
            $phone = '+88'.$phone;
        }
        $request->merge(['phone' => $phone]);
        $request->validate([
            'token' => 'required',
            'phone' => 'required|string',
            'password' => 'required|string|confirmed|min:8',
            'otp' => 'required|string|size:6',
        ]);

        // Verify the token
        $userId = Cache::get('password_reset_token_'.$request->token);
        if (! $userId) {
            return back()->withErrors(['token' => 'This password reset token is invalid.']);
        }

        // Get user by phone
        $user = User::where('phone_number', $request->phone)->where('id', $userId)->first();
        if (! $user) {
            return back()->withErrors(['phone' => 'We can\'t find a user with that phone number.']);
        }

        // Verify the OTP
        $otpKey = 'password_reset_otp_'.$request->phone;
        $storedOtp = Cache::get($otpKey);

        if (! $storedOtp || $storedOtp !== $request->otp) {
            return back()->withErrors(['otp' => 'The OTP is invalid.']);
        }

        // Reset password
        $user->password = Hash::make($request->password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Clear OTP and token from cache
        Cache::forget($otpKey);
        Cache::forget('password_reset_token_'.$request->token);

        Auth::guard('user')->login($user);

        return redirect($this->redirectPath())
            ->with('status', 'Your password has been reset successfully.');
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
     * Get the guard to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('user');
    }
}
