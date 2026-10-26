<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Hotash\LaravelMultiUi\Backend\RegistersUsers;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/profile';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:user');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $this->prefixCountryCode($data);

        if (! empty($data['referral_code'])) {
            $data['referral_code'] = strtoupper(trim($data['referral_code']));
        }

        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'shop_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'regex:/^\+8801\d{9}$/', 'unique:users'],
            'bkash_number' => ['required', 'string', 'regex:/^\+8801\d{9}$/', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'referral_code' => ['nullable', 'string', 'max:50', 'exists:reseller_affiliates,referral_code'],
        ], [
            'referral_code.exists' => 'The provided referral code is invalid.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return \App\User
     */
    protected function create(array $data)
    {
        $this->prefixCountryCode($data);

        $referralCode = ! empty($data['referral_code'])
            ? strtoupper(trim($data['referral_code']))
            : null;

        $user = new User([
            'name' => $data['name'],
            'shop_name' => $data['shop_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'bkash_number' => $data['bkash_number'],
            'password' => Hash::make($data['password']),
        ]);

        if ($referralCode) {
            $user->referral_code_input = $referralCode;
            session()->forget('ref');
        } elseif (array_key_exists('referral_code', $data)) {
            $user->referral_code_input = '';
            session()->forget('ref');
        }

        $user->save();

        return $user;
    }

    /**
     * Show the application registration form.
     *
     * @return Response
     */
    public function showRegistrationForm()
    {
        if (! isOninda()) {
            return redirect('/');
        }

        $ref = request('ref') ?? request('referral_code');
        if (! empty($ref)) {
            session(['ref' => $ref]);
        }

        return view('user.auth.register');
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('user');
    }

    private function prefixCountryCode(array &$data): void
    {
        foreach (['phone', 'bkash'] as $type) {
            $number = Str::replace(['-', ' '], '', $data[$type.'_number']);
            if (Str::startsWith($number, '01')) {
                $number = '+88'.$number;
            }
            $data[$type.'_number'] = $number;
        }
    }
}
