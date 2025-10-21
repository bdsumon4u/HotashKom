<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        $admins = Admin::query();
        if (request()->has('role_id')) {
            $admins->where('role_id', request()->role_id);
        }

        return $this->view([
            'admins' => $admins->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');

        return $this->view();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');
        $data = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'unique:admins'],
            'password' => ['required'],
            'role_id' => ['sometimes'],
        ]);
        $data['password'] = bcrypt($data['password']);
        if (! isset($data['role_id'])) {
            $data['role_id'] = Admin::SALESMAN;
        }

        $data['is_active'] = true;

        Admin::create($data);

        return back()->with('success', 'Staff Has Been Created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function edit(Admin $staff)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');
        abort_if($staff->email == 'support@hotash.tech' && request()->user()->email != 'support@hotash.tech', 403, 'You don\'t have permission.');

        return $this->view([
            'admin' => $staff,
            'logins' => DB::table('sessions')
                ->where('userable_type', Admin::class)
                ->where('userable_id', $staff->id)
                ->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Admin $staff)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');
        abort_if($staff->email == 'support@hotash.tech' && request()->user()->email != 'support@hotash.tech', 403, 'You don\'t have permission.');
        $data = $request->validate([
            'name' => ['required'],
            'email' => 'required|unique:admins,email,'.$staff->id,
            'password' => ['nullable'],
            'role_id' => ['required'],
            'is_active' => ['sometimes'],
        ]);
        if ($data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        if (! isset($data['is_active'])) {
            $data['is_active'] = $data['role_id'] != Admin::SALESMAN;
        }
        $staff->update($data);

        return back()->with('success', 'Staff Has Been Updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin): void
    {
        //
    }
}
