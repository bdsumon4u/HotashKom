@extends('layouts.light.master')
@section('title', 'Create Home Section')

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
<h3>Create Home Section</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Create Home Section</li>
@endsection

@section('content')
<div class="mb-5 row justify-content-center">
    <div class="col-md-8">
        <div class="shadow-sm card rounded-0">
            <div class="p-3 card-header">Add New <strong>Section</strong></div>
            <div class="p-3 card-body">
                <x-form :action="route('admin.home-sections.store')" method="POST">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-label for="title" />
                                <x-input name="title" />
                                <x-error field="title" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-label for="type" />
                                <select selector name="type" id="type" class="form-control">
                                    <option value="pure-grid" {{ old('type') == 'pure-grid' ? 'selected' : '' }}>Pure Grid</option>
                                    <option value="carousel-grid" {{ old('type') == 'carousel-grid' ? 'selected' : '' }}>Carousel Grid</option>
                                </select>
                                <x-error field="type" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-label for="rows" />
                                <x-input type="number" name="data[rows]" />
                                <x-error field="data.rows" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-label for="cols" /><span>(4 or 5)</span>
                                <x-input type="number" name="data[cols]" />
                                <x-error field="data.cols" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pl-4 d-flex h-100 align-items-center">
                                <div class="radio radio-secondary mr-md-3">
                                    <input type="radio" id="available" class="d-none" name="data[source]" value="available"
                                        @if(old('data.source')=='available') checked @endif />
                                    <label for="available" class="m-0">Show All Products</label>
                                </div>
                                <div class="radio radio-secondary ml-md-3">
                                    <input type="radio" id="specific" class="d-none" name="data[source]" value="specific"
                                        @if(old('data.source')=='specific') checked @endif />
                                    <label for="specific" class="m-0">Show Products From Selected Categories</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <x-category-dropdown :categories="$categories" name="categories[]" placeholder="Select Categories" id="categories" multiple="true" :selected="old('categories')" />
                            <x-error field="categories" class="d-block" />
                        </div>
                        <div class="col-md-12">
                            <livewire:section-product />
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
<script src="{{asset('assets/js/select2/select2.full.min.js')}}"></script>
<script src="{{asset('assets/js/select2/select2-custom.js')}}"></script>
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