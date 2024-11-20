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
            'name' => 'required',
            'email' => 'nullable',
            'phone_number' => 'required',
            'address' => 'nullable',
        ]);

        auth('user')->user()->update($data);

        return back()->withSuccess('Profile Updated.');
    }
}
