@extends('layouts.light.master')

@section('title', $menu->name)

@push('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush

@section('breadcrumb-title')
<h3>{{ $menu->name }}</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">{{ $menu->name }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card rounded-0 shadow-sm">
            <div class="card-header p-3 d-flex align-items-center">
                Menu Items&nbsp;<strong>[{{$menu->name}}]</strong>
                <div class="card-header-actions ml-auto">
                    <a href="#create-menu-item" data-toggle="modal" class="card-header-action btn btn-sm btn-primary text-light">Add New</a>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="card-title text-info">Drag To Sort..</div>
                <div class="alert-box" style="display: none;">
                    <div class="alert alert-success">Sort Successfull.</div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th width="10">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sortable">
                            @foreach($menu->menuItems as $item)
                            <tr data-index="{{ $item->id }}" data-order="{{ $item->order }}">
                                <td>
                                    <a href="{{ url($item->href) }}" target="_blank">{{ $item->name }}</a>
                                </td>
                                <td>
                                    <form action="{{ route('admin.menu-items.destroy', $item->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <div class="btn-group btn-group-sm btn-group-inline d-flex justify-content-between">
                                            <a href="#edit-menu-item" data-toggle="modal" data-route="{{ route('admin.menu-items.update', $item) }}" data-name="{{ $item->name }}" data-href="{{ $item->href }}" class="btn btn-success rounded-0 mr-1">Edit</a>
                                            <button type="submit" class="btn btn-danger rounded-0 ml-1">Delete</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="create-menu-item">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <x-form action="{{ route('admin.menu-items.store') }}" method="post">
                <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                <div class="modal-header p-2">Create Menu Item</div>
                <div class="modal-body">
                    <div class="form-group">
                        <x-label for="name" /><span class="text-danger">*</span>
                        <x-input name="name" data-target="#href" />
                    </div>
                    <div class="form-group">
                        <label for="href">Link</label><span class="text-danger">*</span>
                        <x-input name="href" />
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </x-form>
        </div>
    </div>
</div>
<div class="modal" id="edit-menu-item">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <x-form action="" method="patch">
                <div class="modal-header p-2">Edit Menu Item</div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Title</label><span class="text-danger">*</span>
                        <x-input name="name" data-target="#url" />
                    </div>
                    <div class="form-group">
                        <label for="url">Link</label><span class="text-danger">*</span>
                        <input type="text" name="href" id="url" class="form-control">
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </x-form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endpush

@push('scripts')
<script>
    $(function() {
        // $('[name="name"]').keyup(function () {
        //     $($(this).data('target')).val(slugify($(this).val()));
        // });

        $('[href="#edit-menu-item"]').click(function () {
            $('#edit-menu-item').find('form').attr('action', $(this).data('route'));
            $('#edit-menu-item').find('[name="name"]').val($(this).data('name'));
            $('#edit-menu-item').find('[name="href"]').val($(this).data('href'));
        });

        $("#sortable").sortable({
            helper: function (e, ui) {
                ui.children().each(function () {
                    $(this).width($(this).width());
                });
                return ui;
            },
            update: function (event, ui) {
                $(this).children().each(function (index) {
                    if ($(this).attr('data-order') != index + 1) {
                        $(this).attr('data-order', index + 1).addClass('updated');
                    }
                });
                saveNewPositions();
            },
        });
        function saveNewPositions () {
            var positions = [];
            $('tr.updated').each(function () {
                positions.push([$(this).data('index'), $(this).attr('data-order')]);
            });
            $.ajax({
                url: "{!! route('api.menu-items.sort', $menu) !!}",
                method: 'POST',
                dataType: 'text',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    positions: positions,
                },
                success: function (response) {
                    $('.alert-box').show();
                    setTimeout(function () {
                        $('.alert-box').hide();
                    }, 3000);
                    $('tr.updated').each(function () {
                        $(this).removeClass('updated');
                    });
                },
                error : function (error) {
                    console.log(error);
                },
            });
        }
        $("#sortable").disableSelection();
    });
</script>
@endpush