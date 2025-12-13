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
    <div class="row justify-content-center mb-5">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header p-3">Upload Images</div>
                <div class="card-body p-3">
                    <x-form method="post" :action="route('admin.slides.store')" id="slides-dropzone" class="dropzone" has-files>
                        <div class="dz-message needsclick">
                            <i class="icon-cloud-up"></i>
                            <h6>Drop files here or click to upload.</h6>
                            <span class="note needsclick">(Recommended <strong>1125x395</strong> dimension.)</span>
                        </div>
                    </x-form>
                </div>
            </div>
            <div class="card mb-5">
                <div class="card-header p-3">Current Slides</div>
                <div class="card-body p-3">
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

@push('scripts')
<script>
    // Destroy any existing instances (SPA nav safety)
    if (window.Dropzone && Dropzone.instances) {
        Dropzone.instances.forEach(function (dz) { try { dz.destroy(); } catch(e) {} });
        Dropzone.instances = [];
    }

    if (window.Dropzone) {
        new Dropzone("#slides-dropzone", {
            init: function () {
                this.on('complete', function(){
                    if(this.getQueuedFiles().length === 0 && this.getUploadingFiles().length === 0) {
                        window.location.reload();
                    }
                });
            }
        });
    }
</script>
@endpush