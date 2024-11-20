@extends('layouts.light.master')
@section('title', 'Pages')

@section('breadcrumb-title')
<h3>Pages</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Pages</li>
@endsection

@section('content')
<div class="row mb-5">
    <div class="col-sm-12">
        <div class="card rounded-0 shadow-sm">
            <div class="card-header p-3">
                <div class="row px-3 justify-content-between align-items-center">
                    <div>All Pages</div>
                    <a href="{{route('admin.pages.create')}}" class="btn btn-sm btn-primary">New Page</a>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="table-responive">
                    <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Content</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pages as $page)
                            <tr data-row-id="{{ $page->id }}">
                                <td>{{ $page->id }}</td>
                                <td>
                                    <a href="{{ url($page->slug) }}">{{ $page->title }}</a>
                                </td>
                                <td>{!! substr(strip_tags($page->content), 0, 100) !!}</td>
                                <td width="50">
                                    <x-form action="{{ route('admin.pages.destroy', $page) }}" method="delete">
                                        <div class="btn-group btn-group-inline">
                                            <a class="btn btn-sm btn-primary" target="_blank" href="{{ route('admin.pages.edit', $page) }}">Edit</a>
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
