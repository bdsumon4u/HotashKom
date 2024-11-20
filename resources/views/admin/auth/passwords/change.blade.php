@extends('layouts.light.master')
@section('title', 'Edit Profile')

@section('breadcrumb-title')
<h3>Edit Profile</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Edit Profile</li>
@endsection

@section('content')
<div class="row mb-5">
    <div class="col-sm-12">
        <div class="card rounded-0 shadow-sm">
            <div class="card-header p-3">Edit <strong>Profile</strong></div>
            <div class="card-body p-3">
                <x-form action="{{ route('admin.password.change') }}">
                    <div class="form-group">
                        <label for="name">Name</label><span class="text-danger">*</span>
                        <x-input name="name" :value="$admin->name" />
                        <x-error field="name" />
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label><span class="text-danger">*</span>
                        <x-input name="email" :value="$admin->email" />
                        <x-error field="email" />
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <x-input name="password" />
                        <x-error field="password" />
                    </div>
                    @if($admin->is('salesman'))
                    <div class="form-group">
                        <div class="checkbox checkbox-secondary">
                            <input type="hidden" name="is_active" value="0">
                            <x-checkbox name="is_active" value="1" :checked="$admin->is_active" />
                            <x-label for="is_active" />
                        </div>
                    </div>
                    @endif
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </x-form>
            </div>
        </div>
    </div>
</div>

@endsection