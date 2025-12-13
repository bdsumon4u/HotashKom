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
    <div class="col-md-8">
        <x-form :action="route('admin.home-sections.store', ['banner' => true])" method="POST" class="shadow-sm card rounded-0">
            <div class="p-3 card-header d-flex justify-content-between align-items-center">
                <div>Add New <strong>Section</strong></div>
                <button type="submit" class="btn btn-primary">Save Section</button>
            </div>
            <div class="p-3 card-body">
                <livewire:banner-section :$categories />
            </div>
        </x-form>
    </div>
</div>

@include('admin.images.single-picker', ['selected' => old('base_image', 0), 'resize' => false])
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