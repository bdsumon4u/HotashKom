<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    public function logoutOthers(Admin $admin)
    {
        if (Hash::check(request()->get('password'), $admin->password)) {
            $authUser = Auth::guard('admin')->user();
            Auth::guard('admin')->setUser($admin)->logoutOtherDevices(request()->get('password'));

            if (! $admin->is($authUser)) {
                DB::table('sessions')
                    ->where('userable_type', Admin::class)
                    ->where('userable_id', $admin->id)
                    ->delete();
            }

            Auth::guard('admin')->setUser($authUser);

            return back()->with('success', 'Logged Out From Other Devices');
        }

        return back()->withErrors(['password' => 'Password is incorrect']);
    }
}
