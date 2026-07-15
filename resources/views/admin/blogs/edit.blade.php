@extends('layouts.light.master')
@section('title', 'Edit blog')

@section('breadcrumb-title')
<h3>Edit blog</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Edit blog</li>
@endsection

@section('content')
<div class="row mb-5">
    <div class="col-sm-12">
        <div class="card rounded-0 shadow-sm">
            <div class="card-header p-3">Edit <strong>Blog</strong></div>
            <div class="card-body p-3">
                <x-form action="{{ route('admin.blogs.update', $blog) }}" method="patch" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Blog Title</label><span class="text-danger">*</span>
                        <x-input name="title" :value="$blog->title" data-target="#slug" />
                        <x-error field="title" />
                    </div>
                    <div class="form-group">
                        <label for="slug">Link</label><span class="text-danger">*</span>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">{{ url('/blogs') }}/</div>
                            </div>
                            <x-input name="slug" :value="$blog->slug" />
                            <button class="input-group-append align-items-center btn btn-secondary" type="button" onclick="window.open('/blogs/'+this.previousElementSibling.value, '_blank')">VISIT</button>
                        </div>
                        <x-error field="slug" />
                    </div>
                    <div class="form-group">
                        <label for="image">Featured Image</label>
                        @if($blog->image)
                            <div class="mb-2">
                                <img src="{{ $blog->image }}" alt="Current Image" style="max-width: 200px; max-height: 120px; object-fit: cover;" class="img-thumbnail">
                            </div>
                        @endif
                        <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror">
                        @error('image')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="content">Content</label><span class="text-danger">*</span>
                                <textarea editor name="content" id="content" cols="30" rows="10" class="form-control @error('content') is-invalid @enderror">{{ old('content', $blog->content) }}</textarea>
                                {!! $errors->first('content', '<span class="invalid-feedback">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <h4 class="mt-4 border-bottom pb-2">SEO Settings</h4>
                            <div class="form-group">
                                <label for="seo_title">SEO Title</label>
                                <input type="text" name="seo[title]" value="{{ old('seo.title', $blog->seo?->title ?? '') }}" id="seo_title" class="form-control @error('seo.title') is-invalid @enderror" placeholder="Leave empty to use blog title">
                                @error('seo.title')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Recommended: 50-60 characters. If empty, blog title will be used.</small>
                            </div>
                            <div class="form-group">
                                <label for="seo_description">SEO Description</label>
                                <textarea name="seo[description]" id="seo_description" rows="3" class="form-control @error('seo.description') is-invalid @enderror" placeholder="Leave empty to use content summary">{{ old('seo.description', $blog->seo?->description ?? '') }}</textarea>
                                @error('seo.description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Recommended: 150-160 characters. If empty, content summary will be used.</small>
                            </div>
                            <div class="form-group">
                                <label for="seo_image">SEO Image (Open Graph)</label>
                                <input type="text" name="seo[image]" value="{{ old('seo.image', $blog->seo?->image ?? '') }}" id="seo_image" class="form-control @error('seo.image') is-invalid @enderror" placeholder="Full URL to image">
                                @error('seo.image')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Optional: Full URL to an image for social media sharing. If empty, blog featured image will be used.</small>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-success">Save</button>
                            </div>
                        </div>
                    </div>
                </x-form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('js/tinymce.js') }}" defer></script>
@endpush
