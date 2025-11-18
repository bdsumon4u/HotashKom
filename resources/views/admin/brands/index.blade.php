@extends('layouts.light.master')
@section('title', 'Brands')

@section('breadcrumb-title')
    <h3>Brands</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Brands</li>
@endsection


@push('styles')
    <style>
        .nav-tabs .nav-item .nav-link {
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        .formatted-categories ul {
            list-style: none;
            padding: 0;
        }

        .formatted-categories ul li {
            background-color: #f3f3f3;
            padding: 5px 10px;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
        }

        .formatted-categories ul li button {
            margin-left: auto;
        }

        .formatted-categories ul li:hover {
            background-color: aliceblue;
        }

        .formatted-categories ul li:hover a {
            text-decoration: none;
        }

        .formatted-categories ul li:hover,
        .formatted-categories ul li.active,
        .formatted-categories ul li.active a {
            color: deeppink;
            text-decoration: none;
        }

        .select2 {
            width: 100% !important;
        }
    </style>
@endpush

@section('content')
    <div class="mb-3 row justify-content-center">
        <div class="col-sm-12">
            <div class="mb-5 shadow-sm card rounded-0">
                <div class="p-3 card-header"><strong>All</strong> <small><i>Brands</i></small></div>
                <div class="p-3 card-body">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="mb-3 formatted-categories">
                                @if ($brands->isEmpty())
                                    <div class="py-2 alert alert-danger"><strong>No Brands Found.</strong></div>
                                @else
                                    <x-categories.tree :categories="$brands" />
                                @endif
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="nav-tabs-boxed">
                                <div class="shadow-sm card rounded-0">
                                    <div class="p-3 card-header">
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link @unless (request('active_id')) active @endunless" data-toggle="tab" href="#create-brand"
                                                    role="tab" aria-controls="create-brand"
                                                    aria-selected="false">Create</a>
                                            </li>
                                            @if (request('active_id'))
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-toggle="tab" href="#edit-brand" role="tab"
                                                        aria-controls="edit-brand" aria-selected="false">Edit</a>
                                                </li>
                                                <li class="ml-auto nav-item">
                                                    <x-form
                                                        action="{{ route('admin.brands.destroy', request('active_id', 0)) }}"
                                                        method="delete"
                                                        onsubmit="return confirm('Are you sure to delete?');">
                                                        <button type="submit"
                                                            class="nav-link btn btn-danger btn-square delete-action">Delete</button>
                                                    </x-form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="p-3 card-body">
                                        @if ($message = Session::get('success'))
                                            <div class="py-2 alert alert-info"><strong>{{ $message }}</strong></div>
                                        @endif
                                        @php $active = \App\Models\Brand::find(request('active_id')) @endphp
                                        <div class="tab-content">
                                            <div class="tab-pane @unless (request('active_id')) active @endunless" id="create-brand" role="tabpanel">
                                                <p class="text-info">Create Brand</p>
                                                <form action="{{ route('admin.brands.store') }}" method="post">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label for="create-name">Name</label>
                                                        <input type="text" name="name" value="{{ old('name') }}"
                                                            id="create-name" data-target="#create-slug"
                                                            class="form-control @error('name') is-invalid @enderror">
                                                        @error('name')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="create-slug">Link</label><span class="text-danger">*</span>
                                                        <input type="text" name="slug" value="{{ old('slug') }}"
                                                            id="create-slug"
                                                            class="form-control @error('slug') is-invalid @enderror">
                                                        @error('slug')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <!-- Button to Open the Modal -->
                                                        <label for="base_image" class="mb-0 d-block">
                                                            <strong>Brand Image</strong>
                                                            <button type="button" class="px-2 btn single btn-light"
                                                                data-toggle="modal" data-target="#single-picker"
                                                                style="background: transparent; margin-left: 5px;">
                                                                <i class="mr-1 fa fa-image text-secondary"></i>
                                                                <span>Browse</span>
                                                            </button>
                                                        </label>
                                                        <div id="preview-image"
                                                            class="base_image-preview @unless (old('base_image')) d-none @endunless"
                                                            style="height: 150px; width: 150px; margin: 5px; margin-left: 0px;">
                                                            <img src="{{ old('base_image_src') }}" alt="Brand Image"
                                                                data-toggle="modal" data-target="#single-picker"
                                                                id="base_image-preview" class="img-thumbnail img-responsive"
                                                                style="display: {{ old('base_image_src') ? '' : 'none' }};">
                                                            <input type="hidden" name="base_image_src"
                                                                value="{{ old('base_image_src') }}">
                                                            <input type="hidden" name="base_image"
                                                                value="{{ old('base_image') }}" id="base-image"
                                                                class="form-control">
                                                        </div>
                                                        @error('base_image')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="checkbox checkbox-secondary">
                                                            <input type="hidden" name="is_enabled" value="0">
                                                            <x-checkbox id="create-is-enabled" name="is_enabled" value="1"
                                                                :checked="old('is_enabled', true)" />
                                                            <label for="create-is-enabled" class="m-0">Enable Brand</label>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="ml-auto btn btn-sm btn-success d-block"><i
                                                            class="fa fa-check"></i> Submit</button>
                                                </form>
                                            </div>
                                            @if (request('active_id'))
                                                <div class="tab-pane active" id="edit-brand" role="tabpanel">
                                                    <p class="text-info">Edit Brand</p>
                                                    <form
                                                        action="{{ route('admin.brands.update', request('active_id', 0)) }}"
                                                        method="post">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="form-group">
                                                            <label for="edit-name">Name</label><span
                                                                class="text-danger">*</span>
                                                            <input type="text" name="name"
                                                                value="{{ old('name', $active->name) }}" id="edit-name"
                                                                data-target="#edit-slug"
                                                                class="form-control @error('name') is-invalid @enderror">
                                                            @error('name')
                                                                <span class="invalid-feedback">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="edit-slug">Link</label><span
                                                                class="text-danger">*</span>
                                                            <input type="text" name="slug"
                                                                value="{{ old('slug', $active->slug) }}" id="edit-slug"
                                                                class="form-control @error('slug') is-invalid @enderror">
                                                            @error('slug')
                                                                <span class="invalid-feedback">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <!-- Button to Open the Modal -->
                                                            <label for="base_image" class="mb-0 d-block">
                                                                <strong>Brand Image</strong>
                                                                <button type="button" class="px-2 btn single btn-light"
                                                                    data-toggle="modal" data-target="#single-picker"
                                                                    style="background: transparent; margin-left: 5px;">
                                                                    <i class="mr-1 fa fa-image text-secondary"></i>
                                                                    <span>Browse</span>
                                                                </button>
                                                            </label>
                                                            <div id="preview-{{ $active->image_id }}"
                                                                class="base_image-preview @unless (old('base_image', $active->image_id)) d-none @endunless"
                                                                style="height: 150px; width: 150px; margin: 5px; margin-left: 0px;">
                                                                <img src="{{ old('base_image_src', asset(optional($active->image)->src)) }}"
                                                                    alt="Brand Image" data-toggle="modal"
                                                                    data-target="#single-picker" id="base_image-preview"
                                                                    class="img-thumbnail img-responsive"
                                                                    style="display: {{ old('base_image_src', optional($active->image)->src) ? '' : 'none' }};">
                                                                <input type="hidden" name="base_image_src"
                                                                    value="{{ old('base_image_src', asset(optional($active->image)->src)) }}">
                                                                <input type="hidden" name="base_image"
                                                                    value="{{ old('base_image', $active->image_id) }}"
                                                                    id="base-image" class="form-control">
                                                            </div>
                                                            @error('base_image')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-secondary">
                                                                <input type="hidden" name="is_enabled" value="0">
                                                                <x-checkbox id="edit-is-enabled" name="is_enabled" value="1"
                                                                    :checked="old('is_enabled', $active->is_enabled)" />
                                                                <label for="edit-is-enabled" class="m-0">Enable Brand</label>
                                                            </div>
                                                        </div>
                                                        <button type="submit"
                                                            class="ml-auto btn btn-sm btn-success d-block"><i
                                                                class="fa fa-check"></i> Submit</button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="shadow-sm card rounded-0">
                                <div class="p-3 card-header">
                                    <strong>Brand Settings</strong>
                                </div>
                                <div class="p-3 card-body">
                                    <x-form :action="route('admin.settings')" method="POST">
                                        <input type="hidden" name="tab" value="brands">
                                        @if ($errors->any())
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                @foreach ($errors->all() as $error)
                                                    <p class="mb-0">{{ $error }}</p>
                                                @endforeach
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                        @endif
                                        <div class="pl-3 form-group">
                                            @php $show_option = setting('show_option'); @endphp
                                            <div class="checkbox checkbox-secondary">
                                                <input type="hidden" name="show_option[brand_carousel]" value="0">
                                                <x-checkbox id="show-carousel" class="d-none"
                                                    name="show_option[brand_carousel]" value="1" :checked="!!old(
                                                        'show_option.brand_carousel',
                                                        $show_option->brand_carousel ?? false,
                                                    )" />
                                                <label for="show-carousel" class="m-0">Show Brand Carousel</label>
                                            </div>
                                        </div>

                                        <button class="btn btn-primary">Submit</button>
                                    </x-form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.images.single-picker', ['selected' => old('base_image', 0)])
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.delete-item', function(e) {
                e.preventDefault();

                if (!confirm('Are you sure to delete?')) {
                    return false;
                }

                $(e.target).addClass('disabled')
                var id = $(this).attr('data-id')
                $.ajax({
                    url: '{{route('admin.brands.destroy', ':id')}}'.replace(':id', id),
                    type: 'DELETE',
                    _method: 'DELETE',
                    complete: function() {
                        $(e.target).removeClass('disabled')
                        window.location.reload();
                    }
                })
            });

            $('[name="name"]').keyup(function() {
                $($(this).data('target')).val(slugify($(this).val()));
            });
        });
    </script>
@endpush
