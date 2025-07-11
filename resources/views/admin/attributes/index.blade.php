@extends('layouts.light.master')
@section('title', 'Attributes')

@section('breadcrumb-title')
    <h3>Attributes</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Attributes</li>
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
                <div class="p-3 card-header"><strong>All</strong> <small><i>Attributes</i></small></div>
                <div class="p-3 card-body">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="mb-3 formatted-categories">
                                @if ($attributes->isEmpty())
                                    <div class="py-2 alert alert-danger"><strong>No Attributes Found.</strong></div>
                                @else
                                    <x-categories.tree :categories="$attributes" />
                                @endif
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="nav-tabs-boxed">
                                <div class="shadow-sm card rounded-0">
                                    <div class="p-3 card-header">
                                        <ul class="nav nav-tabs" role="tablist">
                                            @if (request('active_id'))
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-toggle="tab" href="#options"
                                                        role="tab" aria-controls="options"
                                                        aria-selected="false">Options</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" href="#edit-category"
                                                        role="tab" aria-controls="edit-category"
                                                        aria-selected="false">Edit</a>
                                                </li>
                                                <li class="ml-auto nav-item">
                                                    <x-form
                                                        action="{{ route('admin.attributes.destroy', request('active_id', 0)) }}"
                                                        method="delete">
                                                        <button type="submit"
                                                            class="nav-link btn btn-danger btn-square delete-action">Delete</button>
                                                    </x-form>
                                                </li>
                                            @else
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-toggle="tab" href="#create-category"
                                                        role="tab" aria-controls="create-category"
                                                        aria-selected="false">Create</a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                    @php $active = \App\Models\Attribute::with('options')->find(request('active_id')) @endphp
                                    <div class="p-3 card-body">
                                        @if ($message = Session::get('success'))
                                            <div class="py-2 alert alert-info"><strong>{{ $message }}</strong></div>
                                        @endif
                                        <div class="tab-content">
                                            @if (request('active_id'))
                                                <div class="tab-pane active" id="options" role="tabpanel">
                                                    <form action="{{ route('admin.attributes.options.store', $active) }}"
                                                        method="POST">
                                                        @csrf
                                                        <div class="d-flex align-items-center">
                                                            <h6 class="mb-0 mr-2">{{ $active->name }}</h6>
                                                            <input type="text" class="mr-1 form-control" name="name"
                                                                placeholder="Option Name">
                                                            @if ($active->name == 'Color')
                                                                <input type="color" class="mr-1 form-control"
                                                                    name="value">
                                                            @endif
                                                            <button class="ml-1 btn btn-light"
                                                                style="white-space: nowrap;">Add Option</button>
                                                        </div>
                                                    </form>

                                                    <div class="my-3 formatted-categories">
                                                        @if ($active->options->isEmpty())
                                                            <div class="py-2 alert alert-danger"><strong>No Options
                                                                    Found.</strong></div>
                                                        @else
                                                            <ul>
                                                                @foreach ($active->options as $option)
                                                                    <li class="d-flex justify-content-between">
                                                                        <a href="" data-toggle="modal"
                                                                            data-target="#edit-option-{{ $option->id }}">{{ $option->name }}</a>
                                                                        <x-form :action="route(
                                                                            'admin.attributes.options.destroy',
                                                                            [$active, $option],
                                                                        )" method="DELETE">
                                                                            <button type="submit"
                                                                                class="btn btn-danger btn-xs btn-square delete-action">Delete</button>
                                                                        </x-form>
                                                                        <div class="modal"
                                                                            id="edit-option-{{ $option->id }}">
                                                                            <div class="modal-dialog">
                                                                                <div class="modal-content">

                                                                                    <!-- Modal Header -->
                                                                                    <div class="modal-header">
                                                                                        <h4 class="modal-title">Edit Option:
                                                                                            {{ $option->name }}</h4>
                                                                                        <button type="button"
                                                                                            class="close"
                                                                                            data-dismiss="modal">&times;</button>
                                                                                    </div>

                                                                                    <!-- Modal body -->
                                                                                    <div class="modal-body">
                                                                                        <x-form :action="route(
                                                                                            'admin.attributes.options.update',
                                                                                            [$active, $option],
                                                                                        )"
                                                                                            method="PATCH">
                                                                                            <div
                                                                                                class="d-flex align-items-center">
                                                                                                <h6 class="mb-0 mr-2">
                                                                                                    {{ $active->name }}</h6>
                                                                                                <input type="text"
                                                                                                    class="mr-1 form-control"
                                                                                                    name="name"
                                                                                                    placeholder="Option Name"
                                                                                                    value="{{ old('name', $option->name) }}">
                                                                                                @if ($active->name == 'Color')
                                                                                                    <input type="color"
                                                                                                        class="mr-1 form-control"
                                                                                                        name="value"
                                                                                                        value="{{ old('value', $option->value) }}">
                                                                                                @endif
                                                                                                <button type="submit"
                                                                                                    class="ml-1 btn btn-light"
                                                                                                    style="white-space: nowrap;">Update
                                                                                                    Option</button>
                                                                                            </div>
                                                                                        </x-form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="edit-category" role="tabpanel">
                                                    <p class="text-info">Edit Attribute</p>
                                                    <form
                                                        action="{{ route('admin.attributes.update', request('active_id', 0)) }}"
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
                                                        <button type="submit"
                                                            class="ml-auto btn btn-sm btn-success d-block"><i
                                                                class="fa fa-check"></i> Submit</button>
                                                    </form>
                                                </div>
                                            @else
                                                <div class="tab-pane active" id="create-category" role="tabpanel">
                                                    <p class="text-info">Create Attribute</p>
                                                    <form action="{{ route('admin.attributes.store') }}" method="post">
                                                        @csrf
                                                        <div class="form-group">
                                                            <label for="create-name">Name</label>
                                                            <input type="text" name="name"
                                                                value="{{ old('name') }}" id="create-name"
                                                                data-target="#create-slug"
                                                                class="form-control @error('name') is-invalid @enderror">
                                                            @error('name')
                                                                <span class="invalid-feedback">{{ $message }}</span>
                                                            @enderror
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                    url: '{{route('admin.attributes.destroy', ':id')}}'.replace(':id', id),
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
