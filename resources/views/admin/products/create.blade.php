@extends('layouts.light.master')
@section('title', 'Create Product')

@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/select2.css')}}">
@endpush

@section('breadcrumb-title')
<h3>Create Product</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">
    <a href="{{ route('admin.products.index') }}">Products</a>
</li>
<li class="breadcrumb-item">Create Product</li>
@endsection


@push('styles')
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<style>
    .nav-tabs {
        border: 2px solid #ddd;
    }
    .nav-tabs li:hover a,
    .nav-tabs li a.active {
        border-radius: 0;
        border-bottom-color: #ddd !important;
    }
    .nav-tabs li a.active {
        background-color: #f0f0f0 !important;
    }
    .nav-tabs li a:hover {
        border-bottom: 1px solid #ddd;
        background-color: #f7f7f7;
    }

    .is-invalid + .SumoSelect + .invalid-feedback {
        display: block;
    }
</style>
<style>
    .dropzone {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .previewer {
        display: inline-block;
        position: relative;
        margin-left: 3px;
        margin-right: 7px;
    }
    .previewer i {
        position: absolute;
        top: 0;
        color: red;
        right: 0;
        background: #ddd;
        padding: 2px;
        border-radius: 3px;
        cursor: pointer;
    }
    .dataTables_scrollHeadInner {
        width: 100% !important;
    }
    th,
    td {
        vertical-align: middle !important;
    }
    table.dataTable tbody td.select-checkbox:before,
    table.dataTable tbody td.select-checkbox:after,
    table.dataTable tbody th.select-checkbox:before,
    table.dataTable tbody th.select-checkbox:after {
        top: 50%;
    }
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

@section('content')
<div class="row mb-5">
    <div class="col-md-8">
        <div class="card rounded-0 shadow-sm">
            <div class="card-header p-3">Add New <strong>Product</strong></div>
            <div class="card-body p-3">
                <div class="row justify-content-center">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col">
                                <x-form action="{{ route('admin.products.store') }}" method="post">
                                    @include('admin.products.form')
                                </x-form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="shadow-sm card rounded-0">
            <div class="px-3 py-2 card-header">
                <strong>SEO Settings</strong>
            </div>
            <div class="p-2 card-body">
                <small class="text-muted d-block mb-3">These fields are optional. If left empty, the system will use default values based on the product name and description.</small>
                <p class="text-info small">Note: SEO settings can be updated after creating the product.</p>
            </div>
        </div>
    </div>
</div>

@include('admin.images.single-picker', ['selected' => old('base_image', 0)])
@include('admin.images.multi-picker', ['selected' => old('additional_images', [])])
@endsection

@push('js')
<script src="{{ asset('js/tinymce.js') }}"></script>
<script src="{{asset('assets/js/select2/select2.full.min.js')}}"></script>
<script src="{{asset('assets/js/select2/select2-custom.js')}}"></script>
@endpush

@push('scripts')
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script>
    $(document).ready(function(){
        $('.add-wholesale').click(function (e) {
            e.preventDefault();
            
            $(this).closest('.card').find('.card-body').append(`
                <div class="form-group mb-1">
                    <div class="input-group">
                        <x-input name="wholesale[quantity][]" placeholder="Quantity" />
                        <x-input name="wholesale[price][]" placeholder="Price" />
                        <div class="input-group-append">
                            <button type="button" class="btn btn-danger btn-sm remove-wholesale">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `);
        });
        $(document).on('click', '.remove-wholesale', function (e) {
            e.preventDefault();

            $(this).closest('.form-group').remove();
        });
        $('.additional_images-previews').sortable();
        $('[name="name"]').keyup(function () {
            $($(this).data('target')).val(slugify($(this).val()));
        });

        $('#should_track').change(function() {
            if($(this).is(':checked')) {
                $('.stock-count').show();
            } else {
                $('.stock-count').hide();
            }
        });

        $('[selector]').select2({
            // tags: true,
        });
    });
</script>
@endpush