@extends('layouts.light.master')
@section('title', 'Staffs')

@section('breadcrumb-title')
    <h3>Staffs</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Staffs</li>
@endsection

@section('content')
    <div class="row mb-5">
        <div class="col-sm-12">
            <div class="orders-table">
                <div class="card rounded-0 shadow-sm">
                    <div class="card-header p-3">
                        <strong>All</strong>&nbsp;<small>Staffs</small>
                        <a href="{{ route('admin.staffs.create') }}" class="btn btn-sm btn-primary float-right">Create New</a>
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Role</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        @if (request('role_id') == \App\Models\Admin::SALESMAN)
                                            <th>Status</th>
                                        @endif
                                        <th>Edit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($admins as $admin)
                                        <tr data-row-id="{{ $admin->id }}">
                                            <td>{{ $admin->id }}</td>
                                            <td>{{ $admin->is('admin') ? 'Admin' : ($admin->is('manager') ? 'Manager' : 'Salesman') }}
                                            </td>
                                            <td>{{ $admin->name }}</td>
                                            <td>{{ $admin->email }}</td>
                                            @if (request('role_id') == \App\Models\Admin::SALESMAN)
                                                <td>
                                                    @if ($admin->is_active)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                            @endif
                                            <td>
                                                <a href="{{ route('admin.staffs.edit', $admin->id) }}">Edit</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ request('role_id') == \App\Models\Admin::SALESMAN ? 6 : 5 }}"
                                                class="text-center text-danger">No staffs found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
