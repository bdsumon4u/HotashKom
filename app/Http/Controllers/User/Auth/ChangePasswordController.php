<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        if ($request->isMethod('GET')) {
            return view('user.auth.passwords.change');
        }

        $data = $request->validate([
            'old_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = auth('user')->user();
        if (Hash::check($data['old_password'], $user->password)) {
            $user->update(['password' => Hash::make($data['password'])]);

            return back()->withSuccess('Password Changed.');
        }

        return back()->withDanger('Opps! Unknown Errors Occured.');
    }
}
