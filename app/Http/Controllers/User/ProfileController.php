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
            'website' => 'nullable|url|max:255',
            'domain' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/',
            'db_name' => 'nullable|string|max:255',
            'db_username' => 'nullable|string|max:255',
            'db_password' => 'nullable|string|min:6',
        ]);

        $user = auth('user')->user();

        // Only update database password if provided
        if (empty($data['db_password'])) {
            unset($data['db_password']);
        }

        $user->update($data);

        return back()->withSuccess('Profile Updated.');
    }
}
