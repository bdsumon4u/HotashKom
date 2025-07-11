@extends('layouts.light.master')
@section('title', 'Home Sections')

@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/datatables.css')}}">
@endpush

@section('breadcrumb-title')
<h3>Home Sections</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Home Sections</li>
@endsection

@push('styles')
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
    <style>
        .footer {
            z-index: 1;
        }
        .route {position: relative;list-style-type: none;border: 0;margin: 0;padding: 0;top: 0px;margin-bottom: 1rem;max-height: 100% !important;width: 100%;background: #bcf;border-radius: 2px;z-index: -1;}
        .route span {position: absolute;top: 6px;left: 12px;-ms-transform: scale(2);z-index: 10;}
        .route .title {display: flex;align-items: center;justify-content: space-between;border: 0;margin: 0;padding: 0;padding-right: 2.5rem;font-size: 1rem;height: 30px;text-indent: 50px;background: #4af;border-radius: 2px;box-shadow: 0px 0px 0px 2px #29f;}
        .space{position: relative;list-style-type: none;border: 0;margin: 0;padding: 0;margin-left: 0;top: 0;height: 100%;z-index: 1;}
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
<div class="container-fluid">
   <div class="row">
      <div class="mx-auto col-sm-8">
         <div class="shadow-sm card rounded-0">
            <div class="p-3 card-header d-flex justify-content-between align-items-center">
               <strong>All Sections</strong>
               <div>
                    <a href="{{ route('admin.home-sections.create') }}" class="btn btn-primary">Product Section</a>
                    <a href="{{ route('admin.home-sections.create', ['banner' => true]) }}" class="btn btn-primary">Banner Section</a>
               </div>
           </div>
            <div class="p-3 card-body">
                <ul class="space" id="space-0" data-space="0">
                    @foreach($sections as $section)
                    <li id="space-item-{{ $section->id }}" data-id="{{$section->id}}" class="route space-item-{{ $section->id }}" data-parent="0" data-order="{{ $section->order }}">
                       <h5 class="title" id="title-{{ $section->id }}">
                          <a class="text-white text-underline" href="{{route('admin.home-sections.edit',$section)}}">{{ $section->title }}</a>
                          <small>({{$section->type}})</small>
                       </h5>
                       <span class="ui-icon ui-icon-arrow-4-diag"></span>
                       <button type="button" data-id="{{ $section->id }}" class="delete-item btn btn-sm btn-danger">x</button>
                    </li>
                    @endforeach
                </ul>
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
            var updated = [];
            var space = $('.space').sortable({
                connectWith:'.space',
// handle:'.title',
// placeholder: ....,
                tolerance:'intersect',
                create:function(event,ui){

                },
                over:function(event,ui){
                },
                receive:function(event, ui){

                },
                update:function (event, ui) {
                    $('#space-0 .route').each(function(idx, el) {
                        reorder(idx, $(el));
                    })

                    var orders = {};
                    $('.space .route').each(function(idx, el) {
                        if ($(el).data('id')) {
                            orders[$(el).data('id')] = idx+1;
                        }
                    });

                    $.ajax({
                        url: '{{route('admin.home-sections.index')}}',
                        type: 'GET',
                        data: {
                            orders: orders
                        },
                        success: function (data) {
                            $.notify(data.message, 'success');
                        }
                    });
                },
            });

            function reorder(idx, el) {
                var parent_id = el.parent().attr('data-space');
                if (el.attr('data-parent') != parent_id) {
                    el.attr('data-parent', parent_id);
                    ($.inArray(el.data('id'), updated) == -1) && updated.push(el.data('id'))
                }

                if (el.attr('data-order') != idx + 1) {
                    el.attr('data-order', idx + 1);
                    ($.inArray(el.data('id'), updated) == -1) && updated.push(el.data('id'))
                }

                el.find('.space .route').each(function (idx, cel) {
                    reorder(idx, $(cel));
                });
            }

            $('.space').disableSelection();

            $(document).on('click', '.delete-item', function(e) {
                e.preventDefault()

                if (confirm('Are you sure to delete?')) {
                    $(e.target).addClass('disabled')
                    var id = $(this).attr('data-id')
                    $.ajax({
                        url: '{{route('admin.home-sections.destroy', ':id')}}'.replace(':id', id),
                        type: 'DELETE',
                        _method: 'DELETE',
                        complete: function () {
                            $(e.target).removeClass('disabled')
                            window.location.reload();
                        }
                    })
                }
            });
        });
    </script>
@endpush
