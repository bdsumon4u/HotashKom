@extends('layouts.light.master')
@section('title', 'Menus')

@section('breadcrumb-title')
<h3>Menus</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Menus</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card rounded-0 shadow-sm">
                <div class="card-header p-3">
                    <h5>Menus</h5>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th width="10">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- <tr>
                                    <td>0</td>
                                    <td>category-menu</td>
                                    <td>
                                        <a href="{{ route('admin.category-menus.index') }}" class="btn btn-primary">Build</a>
                                    </td>
                                </tr> --}}
                                @foreach($menus as $menu)
                                <tr>
                                    <td>{{ $menu->id }}</td>
                                    <td>{{ $menu->slug }}</td>
                                    <td>
                                        <a href="{{ route('admin.menus.edit', $menu) }}" class="btn btn-primary">Build</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection