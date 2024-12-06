@extends('layouts.light.master')
@section('title', 'Create Banner Section')

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
<h3>Create Banner Section</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Create Banner Section</li>
@endsection

@section('content')
<div class="mb-5 row justify-content-center">
    <div class="col-md-12">
        <div class="shadow-sm card rounded-0">
            <div class="p-3 card-header d-flex justify-content-between align-items-center">
                <div>Add New <strong>Section</strong></div>
                <button type="button" data-toggle="modal" data-target="#new-col" class="btn btn-primary">New Column</button>
            </div>
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
<!-- Modal -->
<div class="modal fade" id="new-col" tabindex="-1" aria-labelledby="new-colLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="new-colLabel">Banner Column</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="banner-image" class="d-block">
                        <div>Image</div>
                        <img src="" alt="Image" class="img-responsive d-block" style="max-width: 100%;">
                    </label>
                    <input type="file" name="image" id="banner-image" class="mb-1 form-control">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="banner-width">Width</label>
                    <x-input name="width" id="banner-width" :value="$company->dev_name ?? null" />
                    <x-error field="width" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="banner-animation">Animation</label>
                    <x-input name="animation" id="banner-animation" :value="$company->dev_name ?? null" />
                    <x-error field="animation" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="banner-link">Link</label>
                    <x-input name="link" id="banner-link" :value="$company->dev_name ?? null" />
                    <x-error field="link" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="dev-name">Categories</label>
                    <x-input name="company[dev_name]" id="dev-name" :value="$company->dev_name ?? null" />
                    <x-error field="company[dev_name]" />
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
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
<script>
    $(document).ready(function() {
        $('input[type="file"]').change(function() {
            var $img = $(this).parent().find('img');
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $img.attr('src', e.target.result);
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endpush