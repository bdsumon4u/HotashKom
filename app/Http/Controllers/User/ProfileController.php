<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProfileController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        if ($request->isMethod('GET')) {
            return view('user.profile');
        }

        $data = $request->validate([
            'name' => ['required'],
            'email' => ['nullable'],
            'phone_number' => ['required'],
            'address' => ['nullable'],
            'shop_name' => ['nullable'],
            'domain' => ['nullable'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $user = auth('user')->user();
        unset($data['logo']);
        $user->fill($data);
        if ($request->hasFile('logo')) {
            $user->updateLogo($request->file('logo'));
        } else {
            $user->save();
        }

        return back()->withSuccess('Profile Updated.');
    }
}
