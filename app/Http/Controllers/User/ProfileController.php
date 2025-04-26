<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        if ($request->isMethod('GET')) {
            return view('user.profile');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'shop_name' => 'string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'required|string|max:255',
            'bkash_number' => 'string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        auth('user')->user()->update($data);

        return back()->withSuccess('Profile Updated.');
    }
}
