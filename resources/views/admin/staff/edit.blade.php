@extends('layouts.light.master')
@section('title', 'Edit Staff')

@section('breadcrumb-title')
<h3>Edit Staff</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Edit Staff</li>
@endsection

@section('content')
<div class="mb-5 row">
    <div class="col-md-4">
        <div class="shadow-sm card rounded-0">
            <div class="p-3 card-header">Edit <strong>Staff</strong></div>
            <div class="p-3 card-body">
                <x-form action="{{ route('admin.staffs.update', $admin) }}" method="patch">
                    <div class="form-group">
                        <label for="name">Name</label><span class="text-danger">*</span>
                        <x-input name="name" :value="$admin->name" />
                        <x-error field="name" />
                    </div>
                    <div class="form-group">
                        <label for="email">Email/Phone</label><span class="text-danger">*</span>
                        <x-input name="email" :value="$admin->email" />
                        <x-error field="email" />
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <x-input name="password" />
                        <x-error field="password" />
                    </div>
                    <div class="form-group">
                        <label for="role-id">Role</label>
                        <select name="role_id" id="role-id" class="form-control">
                            <option value="{{$admin::ADMIN}}" {{ $admin->is('admin') ? 'selected' : '' }}>Admin</option>
                            <option value="{{$admin::MANAGER}}" {{ $admin->is('manager') ? 'selected' : '' }}>Manager</option>
                            <option value="{{$admin::SALESMAN}}" {{ $admin->is('salesman') ? 'selected' : '' }}>Salesman</option>
                            <option value="{{$admin::UPLOADER}}" {{ $admin->is('uploader') ? 'selected' : '' }}>Uploader</option>
                        </select>
                    </div>
                    @if($admin->is('salesman'))
                    <div class="form-group">
                        <div class="checkbox checkbox-secondary">
                            <x-checkbox name="is_active" value="1" :checked="$admin->is_active" />
                            <x-label for="is_active" />
                        </div>
                    </div>
                    @endif
                    <div class="mb-0 form-group">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </x-form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="shadow-sm card rounded-0">
            <div class="p-3 card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="mt-0 page-title d-inline">{{ $admin->name }} :=: <span>{{$logins->count()}}</span> Active Devices </h4>
                    </div>
                    <div class="col-md-6">
                        <form action="{{ route('admin.logout-others', $admin) }}" method="POST" class="d-flex justify-content-end">
                            @csrf
                            @foreach ($errors->all() as $error)
                                <span class="text-danger">{{ $error }}</span>
                            @endforeach
                            <input type="text" name="password" placeholder="User's Password" class="px-1 mr-1">
                            <button class="float-right btn btn-danger btn-add btn-xs waves-effect waves-light"><i class="mr-1 fas fa-fire"></i> Logout Other Devices</button>
                        </form>
                    </div><!-- end col-->
                </div>
            </div>
            <div class="p-3 card-body">
                <div class="table-responsive">
                    <table class="table mb-0 table-centered table-borderless table-hover" id="stockTable" width="100%">
                        <thead class="thead-light">
                        <tr>
                            <!--<th>IP Address</th>-->
                            <th>User Agent</th>
                            <th>Last Activity</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($logins as $session)
                                <tr>
                                    <!--<td>{{ $session->ip_address }}</td>-->
                                    <td>{{ $session->user_agent }}</td>
                                    <td>{{ date('d-M-Y h:i A', $session->last_activity) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div>
</div>
@endsection
