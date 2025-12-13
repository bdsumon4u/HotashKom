@extends('layouts.light.master')
@section('title', 'Create Content Section')

@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/select2.css')}}">
@endpush

@push('styles')
<style>
    .select2 {
        width: 100% !important;
    }
    .select2-selection.select2-selection--single {
        border-color: #ced4da !important;
    }
</style>
@endpush

@section('breadcrumb-title')
<h3>Create Content Section</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Create Content Section</li>
@endsection

@section('content')
<div class="mb-5 row justify-content-center">
    <div class="col-md-8">
        <div class="shadow-sm card rounded-0">
            <div class="p-3 card-header">Add New <strong>Content Section</strong></div>
            <div class="p-3 card-body">
                <x-form :action="route('admin.home-sections.store')" method="POST">
                    <input type="hidden" name="type" value="content">
                    <input type="hidden" name="content" value="1">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-label for="title" />
                                <x-input name="title" placeholder="Enter section title" />
                                <x-error field="title" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="page_id">Select Page</label>
                                <select name="data[page_id]" id="page_id" class="form-control" required>
                                    <option value="">Select a page</option>
                                    @foreach($pages as $page)
                                        <option value="{{ $page->id }}" {{ old('data.page_id') == $page->id ? 'selected' : '' }}>
                                            {{ $page->title }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-error field="data.page_id" />
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="mt-2 btn btn-success">
                        Create Content Section
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
<script>
    $(document).ready(function(){
        $('#page_id').select2({
            placeholder: "Select a page",
            allowClear: true
        });
    });
</script>
@endpush
