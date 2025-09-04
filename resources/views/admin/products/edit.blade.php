@extends('layouts.light.master')
@section('title', 'Edit Product')

@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/select2.css')}}">
@endpush

@section('breadcrumb-title')
<h3>Edit Product</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">
    <a href="{{ route('admin.products.index') }}">Products</a>
</li>
<li class="breadcrumb-item">Edit Product</li>
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
<div class="mb-5 row">
    @if($errors->any())
    <div class="col-12">
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
    <div class="@if ($product->parent_id) col-md-12 @else col-md-8 @endif">
        <div class="shadow-sm card rounded-0">
            <div class="p-3 card-header">Edit <strong>Product</strong></div>
            <div class="p-3 card-body">
                <div class="row justify-content-center">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col">
                                <x-form action="{{ route('admin.products.update', $product) }}" method="patch">
                                    @include('admin.products.form')
                                </x-form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @unless ($product->parent_id)
    <div class="col-md-4">
        <div class="shadow-sm card rounded-0">
            <div class="px-3 py-2 card-header">
                <strong>Attributes</strong>
            </div>
            <div class="p-2 card-body">
                <x-form method="POST" action="{{ route('admin.products.variations.store', $product) }}">
                    <div id="attributes">
                        @php $options = $product->variations->pluck('options')->flatten()->unique('id')->pluck('id'); @endphp
                        @foreach ($attributes as $attribute)
                        <div class="mb-3 shadow-sm card rounded-0">
                            <div class="px-3 py-2 card-header">
                                <a class="card-link" data-toggle="collapse" href="#collapse-{{$attribute->id}}">
                                    {{ $attribute->name }}
                                </a>
                            </div>
                            <div id="collapse-{{$attribute->id}}" class="collapse" data-parent="#attributes">
                                <div class="px-3 py-2 card-body">
                                    <div class="flex-wrap d-flex" style="column-gap: 3rem;">
                                        @foreach ($attribute->options as $option)
                                            <div class="checkbox checkbox-secondary">
                                                <x-checkbox :id="$option->name" name="attributes[{{$attribute->id}}][]" value="{{ $option->id }}" :checked="$options->contains($option->id)" />
                                                <x-label :for="$option->name" />
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-block btn-success">Generate Variations</button>
                </x-form>
            </div>
        </div>

        <div class="shadow-sm card rounded-0">
            <div class="px-3 py-2 card-header">
                <strong>Variations</strong>
            </div>
            <div class="p-2 card-body">
                <div id="variations">
                    @foreach ($product->variations as $variation)
                    <div class="mb-3 shadow-sm card rounded-0">
                        <div class="px-3 py-2 card-header">
                            <a class="card-link" data-toggle="collapse" href="#collapse-{{$variation->id}}">
                                [#{{$variation->id}}] {{ $variation->name }}
                            </a>
                        </div>
                        <div id="collapse-{{$variation->id}}" class="collapse" data-parent="#variations">
                            <div class="px-3 py-2 card-body">
                                <x-form method="PATCH" action="{{ route('admin.products.variations.update', [$product, $variation]) }}">
                                    <div class="flex-wrap d-flex" style="column-gap: 3rem;">
                                        <div class="tab-pane active" id="var-price-{{$variation->id}}" role="tabpanel">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h4><small class="mb-1 border-bottom">Price</small></h4>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="price-{{$variation->id}}">Price <span class="text-danger">*</span></label>
                                                        <x-input id="price-{{$variation->id}}" name="price" :value="$variation->price" />
                                                        <x-error field="price" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="selling-price-{{$variation->id}}">Selling Price <span class="text-danger">*</span></label>
                                                        <x-input id="selling-price-{{$variation->id}}" name="selling_price" :value="$variation->selling_price" />
                                                        <x-error field="selling_price" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="purchase-price-{{$variation->id}}">Average Purchase Price</label>
                                                        <x-input id="purchase-price-{{$variation->id}}" name="purchase_price" :value="$variation->purchase_price" />
                                                        <x-error field="purchase_price" />
                                                    </div>
                                                </div>
                                                @if(isOninda() && config('app.resell'))
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="suggested-price-{{$variation->id}}">Suggested Retail Price</label>
                                                        <x-input id="suggested-price-{{$variation->id}}" name="suggested_price" :value="$variation->suggested_price" />
                                                        <x-error field="suggested_price" />
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="shadow-sm card rounded-0">
                                                <div class="p-1 card-header">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <strong>Wholesale (Quantity|Price)</strong>
                                                        <button type="button" class="btn btn-primary btn-sm add-wholesale">+</button>
                                                    </div>
                                                </div>
                                                <div class="p-1 card-body">
                                                    @foreach (old('wholesale.price', $variation->wholesale['price'] ?? []) as $price)
                                                        <div class="mb-1 form-group">
                                                            <div class="input-group">
                                                                <x-input name="wholesale[quantity][]" placeholder="Quantity" value="{{old('wholesale.quantity', $variation->wholesale['quantity'] ?? [])[$loop->index]}}" />
                                                                <x-input name="wholesale[price][]" placeholder="Price" value="{{$price}}" />
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-danger btn-sm remove-wholesale">x</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    <ul>
                                                        @foreach ([$errors->first('wholesale.price.*'), $errors->first('wholesale.quantity.*')] as $error)
                                                            <li class="text-danger">{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane active" id="var-invent-{{$variation->id}}" role="tabpanel">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h4><small class="mb-1 border-bottom">Inventory</small></h4>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="hidden" name="should_track" value="0" />
                                                            <x-checkbox id="should-track-{{$variation->id}}" name="should_track" value="1" :checked="$variation->should_track" class="should_track custom-control-input" />
                                                            <label for="should-track-{{$variation->id}}" class="custom-control-label">Should Track</label>
                                                            <x-error field="should_track" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="sku-{{$variation->id}}">Product Code</label><span class="text-danger">*</span>
                                                        <x-input id="sku-{{$variation->id}}" name="sku" :value="$variation->sku" />
                                                        <x-error field="sku" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group stock-count" @if(!old('should_track', $variation->should_track)) style="display: none;" @endif>
                                                        <label for="stock-count-{{$variation->id}}">Stock Count <span class="text-danger">*</span></label>
                                                        <x-input id="stock-count-{{$variation->id}}" name="stock_count" :value="$variation->stock_count" />
                                                        <x-error field="stock_count" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <button type="submit" class="btn btn-block btn-success">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </x-form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endunless
</div>

@include('admin.images.single-picker', ['selected' => old('base_image', optional($product->base_image)->id)])
@include('admin.images.multi-picker', ['selected' => old('additional_images', $product->additional_images->pluck('id')->toArray())])
@endsection

@push('js')
<script src="{{ asset('js/tinymce.js') }}"></script>
<script src="{{asset('assets/js/select2/select2.full.min.js')}}"></script>
<script src="{{asset('assets/js/select2/select2-custom.js')}}"></script>
@endpush

@push('scripts')
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script>
    $(document).ready(function () {
        $('.add-wholesale').click(function (e) {
            e.preventDefault();

            $(this).closest('.card').find('.card-body').append(`
                <div class="mb-1 form-group">
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
        // $('[name="name"]').keyup(function () {
        //     $($(this).data('target')).val(slugify($(this).val()));
        // });

        $('.should_track').change(function() {
            if($(this).is(':checked')) {
                $(this).closest('.row').find('.stock-count').show();
            } else {
                $(this).closest('.row').find('.stock-count').hide();
            }
        });

        $('[selector]').select2({
            // tags: true,
        });
    });
</script>
@endpush
