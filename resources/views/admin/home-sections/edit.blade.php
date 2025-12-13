@extends('layouts.light.master')
@section('title', 'Edit Home Section')

@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/select2.css')}}">
@endpush

@push('styles')
<style>
    .select2 {
        width: 100% !important;
    }
    .select2-selection.select2-selection--multiple {
        display: flex;
        align-items: center;
    }
    .select2-container .select2-selection--single {
        border-color: #ced4da !important;
    }
</style>
@endpush

@section('breadcrumb-title')
<h3>Edit Home Section</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Edit Home Section</li>
@endsection

@section('content')
<div class="mb-5 row justify-content-center">
    <div class="col-md-8">
        <div class="shadow-sm card rounded-0">
            <div class="p-3 card-header">Edit <strong>Section</strong></div>
            <div class="p-3 card-body">
                <x-form :action="route('admin.home-sections.update', $section)" method="PATCH">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-label for="title" />
                                <x-input name="title" :value="$section->title" />
                                <x-error field="title" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-label for="type" />
                                <select selector name="type" id="type" class="form-control">
                                    <option value="pure-grid" {{ $section->type == 'pure-grid' ? 'selected' : '' }}>Pure Grid</option>
                                    <option value="carousel-grid" {{ $section->type == 'carousel-grid' ? 'selected' : '' }}>Carousel Grid</option>
                                </select>
                                <x-error field="type" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-label for="rows" />
                                <x-input type="number" name="data[rows]" :value="$section->data->rows ?? 2" />
                                <x-error field="data.rows" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-label for="cols" /><span>(4 or 5)</span>
                                <x-input type="number" name="data[cols]" :value="$section->data->cols ?? 5" />
                                <x-error field="data.cols" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pl-4 d-flex h-100 align-items-center">
                                <div class="radio radio-secondary mr-md-3">
                                    <input type="radio" id="available" class="d-none" name="data[source]" value="available"
                                        @if(old('data.source', $section->data->source??false)=='available') checked @endif />
                                    <label for="available" class="m-0">Show All Products</label>
                                </div>
                                <div class="radio radio-secondary ml-md-3">
                                    <input type="radio" id="specific" class="d-none" name="data[source]" value="specific"
                                        @if(old('data.source', $section->data->source??false)=='specific') checked @endif />
                                    <label for="specific" class="m-0">Show Products From Selected Categories</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <x-category-dropdown :categories="$categories" name="categories[]" placeholder="Select Categories" id="categories" multiple="true" :selected="old('categories', $section->categories->pluck('id')->toArray())" />
                            <x-error field="categories" class="d-block" />
                        </div>
                        <div class="col-md-12">
                            <livewire:section-product :selected-ids="$section->items ?? []" />
                        </div>
                        <div class="col-md-12">
                            <hr>
                            <h6 class="mb-2">SEO Settings <small class="text-muted">(Optional)</small></h6>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="seo-title">SEO Title</label>
                                        <input type="text" name="seo[title]"
                                            value="{{ old('seo.title', $section->seo?->title) }}"
                                            id="seo-title" class="form-control"
                                            placeholder="Leave empty to use section title">
                                        <small class="form-text text-muted">Recommended: 50-60 characters</small>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="seo-description">SEO Description</label>
                                        <textarea name="seo[description]" id="seo-description" rows="2"
                                            class="form-control" placeholder="Leave empty to use section title">{{ old('seo.description', $section->seo?->description) }}</textarea>
                                        <small class="form-text text-muted">Recommended: 150-160 characters</small>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="seo-image">SEO Image (Open Graph)</label>
                                        <input type="text" name="seo[image]"
                                            value="{{ old('seo.image', $section->seo?->image) }}"
                                            id="seo-image" class="form-control"
                                            placeholder="Full URL to image (optional)">
                                        <small class="form-text text-muted">Recommended: 1200x630px</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="mt-2 btn btn-success">
                        Submit
                    </button>
                </x-form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{asset('assets/js/select2/select2.full.min.js')}}" defer></script>
<script src="{{asset('assets/js/select2/select2-custom.js')}}" defer></script>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v1.x.x/dist/livewire-sortable.js"></script>
<script>
    $(document).ready(function(){
        $('[selector]').select2({
            // tags: true,
        });
    });
</script>
@endpush