@extends('layouts.light.master')
@section('title', 'Landing Page Pro')

@section('breadcrumb-title')
    <h3>Landing Page Pro</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Landing Page Pro</li>
@endsection

@section('content')
    <div class="row mb-5">
        <div class="col-sm-12">
            <div class="card rounded-0 shadow-sm">
                <div class="p-3 card-header">
                    <div class="row px-3 align-items-center justify-content-between">
                        <div>All Landing Page Pro</div>
                        <a href="{{ route('admin.landing-page-pros.create') }}" class="btn btn-sm btn-primary">New Landing
                            Page Pro</a>
                    </div>
                </div>
                <div class="p-3 card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Template</th>
                                    <th>Status</th>
                                    <th>Public URL</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($landings as $landing)
                                    <tr data-row-id="{{ $landing->id }}">
                                        <td>{{ $landing->id }}</td>
                                        <td>{{ $landing->title }}</td>
                                        <td>{{ str($landing->template_key)->replace('-', ' ')->title() }}</td>
                                        <td>
                                            @if ($landing->is_published)
                                                <span class="badge badge-success">Published</span>
                                            @else
                                                <span class="badge badge-secondary">Draft</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($landing->is_published)
                                                <a href="{{ route('landing-pro.show', $landing) }}"
                                                    target="_blank">{{ route('landing-pro.show', $landing) }}</a>
                                            @else
                                                <span class="text-muted">Not published</span>
                                            @endif
                                        </td>
                                        <td width="50">
                                            <x-form action="{{ route('admin.landing-page-pros.destroy', $landing) }}"
                                                method="delete">
                                                <div class="btn-group btn-group-inline">
                                                    <a class="btn btn-sm btn-primary"
                                                        href="{{ route('admin.landing-page-pros.edit', $landing) }}">Edit</a>
                                                    <button class="btn btn-sm btn-danger">Delete</button>
                                                </div>
                                            </x-form>
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
@endsection
