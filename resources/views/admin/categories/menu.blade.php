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
        .route {position: relative;list-style-type: none;border: 0;margin: 0;padding: 0;top: 0px;margin-top: 0px;max-height: 100% !important;width: 100%;background: #bcf;border-radius: 2px;z-index: -1;}
        .route span {position: absolute;top: 6px;left: 12px;-ms-transform: scale(2);z-index: 10;}
        .route .title {position: absolute;border: 0;margin: 0;padding: 0;padding-top: 4px;font-size: 1rem;height: 30px;text-indent: 50px;background: #4af;border-radius: 2px;box-shadow: 0px 0px 0px 2px #29f;pointer-events: none;}
        .first-title { margin-left: 10px; }
        .space{background:white;position: relative;list-style-type: none;border: 0;margin: 0;padding: 0;margin-left: 45px;top: 35px;padding-bottom: 35px;height: 100%;z-index: 1;}
        .first-space {margin-left: 10px; top: 0;}
        .space .space{min-height: 4rem;}
        .space button[type="button"] {
            z-index: 9999;
            display: block;
            top: -3px;
            height: 35px;
            right: 3px;
            position: absolute;
            padding: 0.375rem 0.75rem;
        }
    </style>
@endpush

@section('content')
    <div id="app" class="mb-3 row justify-content-center">
        <div class="col-sm-12">
            <div class="mb-5 shadow-sm card rounded-0">
                <div class="p-3 card-header"><strong>All</strong> <small><i>Categories</i></small></div>
                <div class="p-3 card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="p-2 border rounded-sm shadow-md card">
                                <form action="{{ route('admin.category-menus.store') }}" method="post">
                                    @csrf
                                    <div class="p-2 card-header">
                                        <button type="submit" class="ml-auto btn btn-sm btn-success d-block">Add To List</button>
                                    </div>
                                    <div class="p-2 card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm table-striped">
                                                <thead>
                                                <tr>
                                                    <th class="text-center">
                                                        Select
                                                    </th>
                                                    <th>Category</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($remaining_cats as $category)
                                                    <tr>
                                                        <td class="text-center">
                                                            <input type="checkbox" name="category[{{ $category->id }}]" id="{{ $category->id }}" style="width: 15px; height: 15px;">
                                                        </td>
                                                        <td>{{ $category->name }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="p-2 card-footer">
                                        <button type="submit" class="ml-auto btn btn-sm btn-success d-block">Add To List</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="p-2 card">
                                <form id="category-menu--form" action="" method="post">
                                    @csrf
                                    <div class="p-2 card-header">
                                        <button type="submit" class="btn btn-sm btn-success">Update Menu</button>
                                    </div>
                                    <div class="p-2 card-body">
                                        <x-category-menu :categories="$selected_cats" />
                                    </div>
                                    <div class="p-2 card-footer">
                                        <button type="submit" class="btn btn-sm btn-success">Update Menu</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function(){

            // calcWidth($('#title0'));

            window.onresize = function(event) {
                console.log("window resized");

                //method to execute one time after a timer

            };

//recursively calculate the Width all titles
            function calcWidth(obj){
                console.log('---- calcWidth -----');

                var titles =
                    $(obj).siblings('.space').children('.route').children('.title');

                $(titles).each(function(index, element){
                    var pTitleWidth = parseInt($(obj).css('width'));
                    var leftOffset = parseInt($(obj).siblings('.space').css('margin-left'));

                    var newWidth = pTitleWidth - leftOffset;

                    if ($(obj).attr('id') == 'title0'){
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
                connectWith:'.space',
// handle:'.title',
// placeholder: ....,
                tolerance:'intersect',
                create:function(event,ui){
                    // calcWidth($('#title0'));
                },
                over:function(event,ui){
                },
                receive:function(event, ui){
                    // calcWidth($(this).siblings('.title'));
                },
                update:function (event, ui) {
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

                el.find('.space .route').each(function (idx, cel) {
                    reorder(idx, $(cel));
                })
            }

            $('.space').disableSelection();

            $(document).ready(function () {
                $(document).on('submit', '#category-menu--form', function (e) {
                    e.preventDefault();

                    var arr = [], uplen = updated.length;

                    $('li.route').each(function (idx, el) {
                        el = $(el);
                        var arri = $.inArray(el.attr('id'), updated);
                        if (arri != -1) {
                            arr.push(Object.assign({
                                id: el.attr('id').replace('space-item-', ''),
                                order: el.attr('data-order'),
                            }, el.attr('data-parent') == 0 ? {} : {parent_id: el.attr('data-parent')}))
                        }
                    });
                    // console.log(arr)

                    $(e.target).addClass('disabled')
                    $.ajax({
                        url: '{{route('admin.category-menus.store')}}',
                        type: 'POST',
                        data: {categories: arr},
                        success: function(response) {
                            // console.log(response)
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
                e.preventDefault()

                $(e.target).addClass('disabled')
                var id = $(this).attr('data-id')
                $.ajax({
                    url: '{{route('admin.category-menus.destroy', 'id')}}'.replace('id', id),
                    type: 'DELETE',
                    _method: 'DELETE',
                    complete: function () {
                        $(e.target).removeClass('disabled')
                        window.location.reload();
                    }
                })
            })
        });
    </script>
@endpush
