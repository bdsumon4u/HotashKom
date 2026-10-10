<?php

declare(strict_types=1);

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the profile form.
     */
    public function index(): View
    {
        /** @var Investor $investor */
        $investor = auth('investor')->user();

        return view('investor.profile', compact('investor'));
    }

    /**
     * Update the investor's profile.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var Investor $investor */
        $investor = auth('investor')->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'bkash_number' => ['nullable', 'string', 'max:50'],
            'bank_details' => ['nullable', 'string', 'max:1000'],
            'address' => ['nullable', 'string', 'max:1000'],
            'current_password' => ['nullable', 'required_with:new_password', 'string'],
            'new_password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        if ($request->filled('new_password')) {
            if (! Hash::check($request->input('current_password'), $investor->password)) {
                return back()->withErrors(['current_password' => 'Current password does not match.']);
            }

            $investor->password = $request->input('new_password');
        }

        $investor->name = $request->input('name');
        $investor->phone_number = $request->input('phone_number');
        $investor->bkash_number = $request->input('bkash_number');
        $investor->bank_details = $request->input('bank_details');
        $investor->address = $request->input('address');
        $investor->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
