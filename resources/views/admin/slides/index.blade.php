@extends('layouts.light.master')
@section('title', 'Slides')

@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/dropzone.css')}}">
@endpush

@section('breadcrumb-title')
<h3>Slides</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Slides</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="mb-5 row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="p-3 card-header">Upload Images</div>
                <div class="p-3 card-body">
                    <x-form method="post" :action="route('admin.slides.store')" id="slides-dropzone" class="dropzone" has-files>
                        <div class="dz-message needsclick">
                            <i class="icon-cloud-up"></i>
                            <h6>Drop files here or click to upload.</h6>
                            <span class="note needsclick">(Recommended <strong>{{implode('x', config('services.slides.desktop'))}}</strong> dimension.)</span>
                        </div>
                    </x-form>
                </div>
            </div>
            <div class="mb-5 card">
                <div class="p-3 card-header">Current Slides</div>
                <div class="p-3 card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($slides as $slide)
                                <tr>
                                    <td width="10">{{ $slide->id }}</td>
                                    <td width="200">
                                        <img src="{{ asset($slide->mobile_src) }}" width="200" height="100" alt="">
                                    </td>
                                    <td>{{ $slide->title }}</td>
                                    <td width="10">
                                        @if($slide->is_active)
                                        <span class="badge badge-success">Active</span>
                                        @else
                                        <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td width="80">
                                        <x-form method="delete" :action="route('admin.slides.destroy', $slide)">
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <a class="btn btn-primary" href="{{ route('admin.slides.edit', $slide) }}">Edit</a>
                                                <button class="btn btn-danger" type="submit">Delete</button>
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
</div>
@endsection

@push('js')
<script src="{{asset('assets/js/dropzone/dropzone.js')}}"></script>
<script src="{{asset('assets/js/dropzone/dropzone-script.js')}}"></script>
@endpush

@push('scripts')
<script>
    Dropzone.options.slidesDropzone = {
        init: function () {
            this.on('complete', function(){
                if(this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0) {
                    location.reload();
                }
            });
        }
    };
</script>
@endpush