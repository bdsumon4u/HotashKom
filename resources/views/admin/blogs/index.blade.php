@extends('layouts.light.master')
@section('title', 'Blogs')

@section('breadcrumb-title')
<h3>Blogs</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Blogs</li>
@endsection

@section('content')
<div class="row mb-5">
    <div class="col-sm-12">
        <div class="card rounded-0 shadow-sm">
            <div class="card-header p-3">
                <div class="row px-3 justify-content-between align-items-center">
                    <div>All Blogs</div>
                    <a href="{{ route('admin.blogs.create') }}" class="btn btn-sm btn-primary">New Blog</a>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Content</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($blogs as $blog)
                            <tr data-row-id="{{ $blog->id }}">
                                <td>{{ $blog->id }}</td>
                                <td width="100">
                                    @if($blog->image)
                                        <img src="{{ $blog->image }}" alt="Thumbnail" style="width: 80px; height: 45px; object-fit: cover;">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('blogs.show', $blog) }}" target="_blank">{{ $blog->title }}</a>
                                </td>
                                <td>{!! substr(strip_tags($blog->content), 0, 100) !!}...</td>
                                <td width="150">
                                    <x-form action="{{ route('admin.blogs.destroy', $blog) }}" method="delete">
                                        <div class="btn-group btn-group-inline">
                                            <a class="btn btn-sm btn-primary" href="{{ route('admin.blogs.edit', $blog) }}">Edit</a>
                                            <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this blog?')">Delete</button>
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
