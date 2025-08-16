@extends('layouts.light.master')
@section('title', 'Edit Banner Section')

@include('admin.home-sections.partials.banner-styles')

@section('breadcrumb-title')
<h3>Edit Banner Section</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Edit Banner Section</li>
@endsection

@section('content')
<div class="mb-3 row justify-content-center">
    <div class="col-md-10">
        <x-form :action="route('admin.home-sections.update', [$section, 'banner' => true])" method="PATCH" class="shadow-sm card rounded-0">
            <div class="p-2 card-header d-flex justify-content-between align-items-center">
                <div><strong>Edit Banner Section</strong></div>
                <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
            </div>
            <div class="p-2 card-body">
                <livewire:banner-section :$categories :$section />
            </div>
            <div class="p-2 card-footer text-center">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </x-form>
    </div>
</div>

@include('admin.home-sections.partials.banner-preview')

@include('admin.images.single-picker', ['selected' => old('base_image', 0), 'resize' => false])
@endsection

@include('admin.home-sections.partials.banner-scripts')
