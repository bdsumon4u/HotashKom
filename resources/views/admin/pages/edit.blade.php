@extends('layouts.light.master')
@section('title', 'Edit page')

@section('breadcrumb-title')
<h3>Edit page</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Edit page</li>
@endsection

@section('content')
<div class="row mb-5">
    <div class="col-sm-12">
        <div class="card rounded-0 shadow-sm">
            <div class="card-header p-3">Edit <strong>Page</strong></div>
            <div class="card-body p-3">
                <x-form action="{{ route('admin.pages.update', $page) }}" method="patch">
                    <div class="form-group">
                        <label for="title">Page Title</label><span class="text-danger">*</span>
                        <x-input name="title" :value="$page->title" data-target="#slug" />
                        <x-error field="title" />
                    </div>
                    <div class="form-group">
                        <label for="slug">Link</label><span class="text-danger">*</span>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">{{ url('/') }}/</div>
                            </div>
                            <x-input name="slug" :value="$page->slug" />
                            <button class="input-group-append align-items-center btn btn-secondary" type="button" onclick="window.open('/'+this.previousElementSibling.value, '_blank')">VISIT</button>
                        </div>
                        <x-error field="slug" />
                    </div>
                    <!-- Page SEO Fields -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="seo_title">SEO Title</label>
                                <x-input
                                    name="seo_title"
                                    id="seo_title"
                                    :value="old('seo_title', $page->seo_title)"
                                    maxlength="255"
                                    placeholder="Enter the SEO title"
                                />
                                <small class="form-text text-muted">
                                    Recommended length: approximately 50–60 characters.
                                </small>
                                <x-error field="seo_title" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="meta_description">Meta Description</label>
                                <x-textarea
                                    rows="4"
                                    name="meta_description"
                                    id="meta_description"
                                    maxlength="500"
                                    placeholder="Enter a concise page description"
                                >{{ old('meta_description', $page->meta_description) }}</x-textarea>
                                <small class="form-text text-muted">
                                    Recommended length: approximately 150–160 characters.
                                </small>
                                <x-error field="meta_description" />
                            </div>
                        </div>
                    </div>
                    <!-- Page SEO Fields / end -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="content">Content</label><span class="text-danger">*</span>
                                <textarea editor name="content" id="content" cols="30" rows="10" class="form-control @error('content') is-invalid @enderror">{{ old('content', $page->content) }}</textarea>
                                {!! $errors->first('content', '<span class="invalid-feedback">:message</span>') !!}
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

@push('scripts')
<script>
$(document).ready(function () {
    // $('[name="title"]').keyup(function () {
    //     $($(this).data('target')).val(slugify($(this).val()));
    // });
});
</script>
@endpush