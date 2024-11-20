@extends('layouts.light.master')
@section('title', 'Create Menu')

@section('breadcrumb-title')
<h3>Create Menu</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Create Menu</li>
@endsection

@section('content')
<div class="row mb-5 justify-content-center">
    <div class="col-md-8">
        <div class="card rounded-0 shadow-sm">
            <div class="card-header p-3">Add New <strong>Menu</strong></div>
            <div class="card-body p-3">
                <x-form :action="route('admin.menus.store')" method="POST">
                    <div class="form-group">
                        <x-label for="name" />
                        <x-input name="name" data-target="#slug" />
                        <x-error field="name" />
                    </div>
                    <div class="form-group">
                        <x-label for="slug" />
                        <x-input name="slug" />
                        <x-error field="slug" />
                    </div>
                    <button type="submit" class="btn btn-success">
                        Submit
                    </button>
                </x-form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function(){
        $('[name="name"]').keyup(function () {
            $($(this).data('target')).val(slugify($(this).val()));
        });
    });
</script>
@endpush