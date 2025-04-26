<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ResellerController extends Controller
{
    /**
     * Display a listing of the resellers.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.resellers.index');
    }

    /**
     * Show the form for editing the specified reseller.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $reseller = User::findOrFail($id);

        return view('admin.resellers.edit', compact('reseller'));
    }

    /**
     * Update the specified reseller in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $reseller = User::findOrFail($id);

        $validated = $request->mergeIfMissing([
            'is_verified' => 0,
        ])->validate([
            'name' => 'required|string|max:255',
            'shop_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'bkash_number' => 'required|string|max:255',
            'is_verified' => 'boolean',
        ]);

        $reseller->update($validated);

        return response()->json(['message' => 'Reseller updated successfully']);
    }
}
