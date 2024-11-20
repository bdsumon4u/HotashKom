@extends('layouts.light.master')
@section('title', 'Categories')

@section('breadcrumb-title')
    <h3>Categories</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Categories</li>
@endsection

@push('styles')
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
    <style>
        .footer {
            z-index: 1;
        }

        .route {
            position: relative;
            list-style-type: none;
            border: 0;
            margin: 0;
            padding: 0;
            top: 0px;
            margin-top: 0px;
            max-height: 100% !important;
            width: 100%;
            background: #bcf;
            border-radius: 2px;
            z-index: 1;
        }

        .route span {
            position: absolute;
            top: 11px;
            left: 17px;
            transform: scale(2);
            z-index: 10;
        }

        .route .title {
            position: absolute;
            border: 0;
            margin: 0;
            padding: 0;
            padding-top: 4px;
            font-size: 1rem;
            height: 30px;
            text-indent: 50px;
            border-radius: 2px;
            box-shadow: 0px 0px 0px 2px #29f;
        }

        .first-title {
            margin-bottom: 10px;
        }

        .space {
            background: white;
            position: relative;
            list-style-type: none;
            border: 1px dashed red;
            margin: 0;
            padding: 0;
            margin-left: 45px;
            top: 35px;
            padding-bottom: 15px;
            height: 100%;
            z-index: 1;
        }

        .first-space {
            margin-left: 0;
            margin-bottom: 10px;
            top: 0;
        }

        .space .space {
            min-height: 35px;
        }

        .space button[type="button"] {
            z-index: 9999;
            display: block;
            top: 1px;
            height: 35px;
            right: 1px;
            position: absolute;
            padding: 0.375rem 0.75rem;
        }

        .nav-tabs .nav-item .nav-link {
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        .formatted-categories ul {
            list-style: none;
        }

        .formatted-categories ul li {
            background-color: #f3f3f3;
            padding: 5px 0px 35px 5px;
            margin-bottom: 2px;
        }

        .formatted-categories ul li:hover {
            background-color: aliceblue;
        }

        .formatted-categories ul li:hover a {
            text-decoration: none;
        }

        .formatted-categories ul li>a:hover,
        .formatted-categories ul li.active>a {
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
                <div class="p-3 card-header"><strong>All</strong> <small><i>Categories</i></small></div>
                <div class="p-3 card-body">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="formatted-categories">
                                @if ($categories->isEmpty())
                                    <div class="py-2 alert alert-danger"><strong>No Categories Found.</strong></div>
                                @else
                                    <form action="">
                                        <x-categories.tree :categories="$categories" />
                                        <button type="submit" class="btn btn-primary">Save Order</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="nav-tabs-boxed">
                                <div class="shadow-sm card rounded-0">
                                    <div class="p-3 card-header">
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-toggle="tab" href="#create-category"
                                                    role="tab" aria-controls="create-category"
                                                    aria-selected="false">Create</a>
                                            </li>
                                            @if (request('active_id'))
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" href="#edit-category"
                                                        role="tab" aria-controls="edit-category"
                                                        aria-selected="false">Edit</a>
                                                </li>
                                                <li class="ml-auto nav-item">
                                                    <x-form
                                                        action="{{ route('admin.categories.destroy', request('active_id', 0)) }}"
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
                                        @php $active = \App\Models\Category::find(request('active_id')) @endphp
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="create-category" role="tabpanel">
                                                <p class="text-info">Create
                                                    <strong>{{ $active ? 'Child' : 'Root' }}</strong> Category</p>
                                                <form action="{{ route('admin.categories.store') }}" method="post">
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
                                                        <label for="create-slug">Slug</label>
                                                        <input type="text" name="slug" value="{{ old('slug') }}"
                                                            id="create-slug"
                                                            class="form-control @error('slug') is-invalid @enderror">
                                                        @error('slug')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="create-parent-id">Select Parent</label>
                                                        <x-category-dropdown :categories="$categories" name="parent_id"
                                                            placeholder="Select parent" id="create-parent-id"
                                                            :selected="request('active_id', 0)" />
                                                    </div>
                                                    <div class="form-group">
                                                        <!-- Button to Open the Modal -->
                                                        <label for="base_image" class="mb-0 d-block">
                                                            <strong>Category Image</strong>
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
                                                            <img src="{{ old('base_image_src') }}" alt="Category Image"
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
                                                    <button type="submit"
                                                        class="ml-auto btn btn-sm btn-success d-block"><i
                                                            class="fa fa-check"></i> Create</button>
                                                </form>
                                            </div>
                                            @if (request('active_id'))
                                                <div class="tab-pane" id="edit-category" role="tabpanel">
                                                    <p class="text-info">Edit Category</p>
                                                    <form
                                                        action="{{ route('admin.categories.update', request('active_id', 0)) }}"
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
                                                            <label for="edit-slug">Slug</label><span
                                                                class="text-danger">*</span>
                                                            <input type="text" name="slug"
                                                                value="{{ old('slug', $active->slug) }}" id="edit-slug"
                                                                class="form-control @error('slug') is-invalid @enderror">
                                                            @error('slug')
                                                                <span class="invalid-feedback">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="edit-parent-id">Select Parent</label>
                                                            <x-category-dropdown :categories="$categories" name="parent_id"
                                                                placeholder="Select parent" id="edit-parent-id"
                                                                :selected="$active->parent->id ?? 0" :disabled="$active->id" />
                                                        </div>
                                                        <div class="form-group">
                                                            <!-- Button to Open the Modal -->
                                                            <label for="base_image" class="mb-0 d-block">
                                                                <strong>Category Image</strong>
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
                                                                    alt="Category Image" data-toggle="modal"
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
                                                        <button type="submit"
                                                            class="ml-auto btn btn-sm btn-success d-block"><i
                                                                class="fa fa-check"></i> Edit</button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="shadow-sm card rounded-0">
                                <div class="p-3 card-header">
                                    <strong>Category Settings</strong>
                                </div>
                                <div class="p-3 card-body">
                                    <x-form :action="route('admin.settings')" method="POST">
                                        <input type="hidden" name="tab" value="categories">
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
                                                <input type="hidden" name="show_option[category_dropdown]"
                                                    value="0">
                                                <x-checkbox id="show-dropdown" class="d-none"
                                                    name="show_option[category_dropdown]" value="1"
                                                    :checked="!!old(
                                                        'show_option.category_dropdown',
                                                        $show_option->category_dropdown ?? false,
                                                    )" />
                                                <label for="show-dropdown" class="m-0">Show Category Dropdown</label>
                                            </div>
                                            <div class="checkbox checkbox-secondary">
                                                <input type="hidden" name="show_option[category_carousel]"
                                                    value="0">
                                                <x-checkbox id="show-carousel" class="d-none"
                                                    name="show_option[category_carousel]" value="1"
                                                    :checked="!!old(
                                                        'show_option.category_carousel',
                                                        $show_option->category_carousel ?? false,
                                                    )" />
                                                <label for="show-carousel" class="m-0">Show Category Carousel</label>
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
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function() {

            // calcWidth($('#title0'));

            window.onresize = function(event) {
                console.log("window resized");

                //method to execute one time after a timer

            };

            //recursively calculate the Width all titles
            function calcWidth(obj) {
                console.log('---- calcWidth -----');

                var titles =
                    $(obj).siblings('.space').children('.route').children('.title');

                $(titles).each(function(index, element) {
                    var pTitleWidth = parseInt($(obj).css('width'));
                    var leftOffset = parseInt($(obj).siblings('.space').css('margin-left'));

                    var newWidth = pTitleWidth - leftOffset;

                    if ($(obj).attr('id') == 'title0') {
                        console.log("called");

                        newWidth = newWidth - 10;
                    }

                    $(element).css({
                        'width': newWidth,
                    })

                    calcWidth(element);
                });

            }

            var updated = [];
            var space = $('.space').sortable({
                connectWith: '.space',
                // handle:'.title',
                // placeholder: ....,
                tolerance: 'intersect',
                create: function(event, ui) {
                    // calcWidth($('#title0'));
                },
                over: function(event, ui) {},
                receive: function(event, ui) {
                    // calcWidth($(this).siblings('.title'));
                },
                update: function(event, ui) {
                    $('#space-0 .route').each(function(idx, el) {
                        reorder(idx, $(el));
                    })

                    console.log(updated)
                },
            });

            function reorder(idx, el) {
                var parent_id = el.parent().attr('data-space');
                if (el.attr('data-parent') != parent_id) {
                    el.attr('data-parent', parent_id);
                    ($.inArray(el.attr('id'), updated) == -1) && updated.push(el.attr('id'))
                }

                if (el.attr('data-order') != idx + 1) {
                    el.attr('data-order', idx + 1);
                    ($.inArray(el.attr('id'), updated) == -1) && updated.push(el.attr('id'))
                }

                el.find('.space .route').each(function(idx, cel) {
                    reorder(idx, $(cel));
                })
            }

            $('.space').disableSelection();

            $(document).ready(function() {
                $(document).on('submit', '.formatted-categories form', function(e) {
                    e.preventDefault();

                    var arr = [],
                        uplen = updated.length;

                    $('li.route').each(function(idx, el) {
                        el = $(el);
                        var arri = $.inArray(el.attr('id'), updated);
                        if (arri != -1) {
                            arr.push(Object.assign({
                                id: el.attr('id').replace('space-item-', ''),
                                order: el.attr('data-order'),
                            }, el.attr('data-parent') == 0 ? {} : {
                                parent_id: el.attr('data-parent')
                            }))
                        }
                    });
                    // console.log(arr)

                    $(e.target).addClass('disabled')
                    $.ajax({
                        url: '{{route('admin.categories.store')}}',
                        type: 'POST',
                        data: {
                            categories: arr
                        },
                        success: function(response) {
                            $.notify('Categories are reordered successfully.',
                                'success');
                        },
                        error: function(err) {
                            // console.log(err)
                        },
                        complete: function() {
                            $(e.target).removeClass('disabled')
                        }
                    })
                })
            })

            $(document).on('click', '.delete-item', function(e) {
                e.preventDefault();

                if (!confirm('Are you sure to delete?')) {
                    return false;
                }

                $(e.target).addClass('disabled')
                var id = $(this).attr('data-id')
                $.ajax({
                    url: '{{route('admin.categories.destroy', 'id')}}'.replace('id', id),
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
