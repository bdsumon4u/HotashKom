<?php

namespace App\Http\Controllers\Admin\Auth;

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
            return view('admin.auth.passwords.change', [
                'admin' => auth('admin')->user(),
            ]);
        }

        $data = $request->validate([
            'name' => ['required'],
            'email' => 'required|email|unique:admins,email,'.auth('admin')->id(),
            'password' => ['nullable', 'min:8'],
        ]);

        if ($data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        auth('admin')->user()->update($data);

        return back()->withSuccess('Password Changed.');
    }
}
