@extends('layouts.light.master')
@section('title', 'Edit Slide')

@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/dropzone.css')}}">
@endpush

@section('breadcrumb-title')
<h3>Edit Slide</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">
    <a href="{{ route('admin.slides.index') }}">Slides</a>
</li>
<li class="breadcrumb-item">Edit Slide</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="mb-5 row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="p-3 card-header">Edit Slide</div>
                <div class="p-3 card-body">
                    <x-form method="patch" :action="route('admin.slides.update', $slide)" has-files>
                        <div class="form-group">
                            <x-label for="title" />
                            <x-input name="title" :value="$slide->title" />
                            <x-error field="title" />
                        </div>
                        <div class="form-group">
                            <x-label for="text" />
                            <x-textarea name="text">{{ $slide->text }}</x-textarea>
                            <x-error field="title" />
                        </div>
                        <div class="form-group">
                            <div class="checkbox checkbox-secondary">
                                <input type="hidden" name="is_active" value="0">
                                <x-checkbox name="is_active" value="1" :checked="$slide->is_active" />
                                <x-label for="is_active" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="btn_name">Button Text</label>
                                    <x-input name="btn_name" :value="$slide->btn_name" />
                                </div>
                                </div>
                                    <div class="col-md-6">
                                <div class="form-group">
                                    <label for="btn_href">Button Link</label>
                                    <x-input name="btn_href" :value="$slide->btn_href" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-secondary">Submit</button>
                        </div>
                    </x-form>
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
