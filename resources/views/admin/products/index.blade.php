@extends('layouts.light.master')
@section('title', 'Products')

@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/datatables.css')}}">
@endpush

@section('breadcrumb-title')
<h3>Products</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Products</li>
@endsection

@section('content')
<div class="container-fluid">
   <div class="mb-5 row">
      <div class="col-sm-12">
         <div class="shadow-sm card rounded-0">
            <div class="p-3 card-header">
               <div class="px-3 row justify-content-between align-items-center">
                  <div>All Products</div>
                  <a href="{{route('admin.products.create')}}" class="btn btn-sm btn-primary">New Product</a>
               </div>
            </div>
            <div class="p-3 card-body">
               <div class="table-responsive product-table">
                  @php $isAdmin = auth('admin')->check() && auth('admin')->user()->is('admin'); @endphp
                  <table class="display" id="product-table" data-url="{{ route('api.products') }}">
                     <thead>
                        <tr>
                           <th width="100">Image</th>
                           <th>Name</th>
                           @if($isAdmin)
                           <th>Purchase</th>
                           @endif
                           <th>Price</th>
                           <th>Stock</th>
                           <th width="10">Action</th>
                        </tr>
                     </thead>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection

@push('js')
<script src="{{asset('assets/js/datatable/datatables/jquery.dataTables.min.js')}}"></script>
<script>
$(function() {
    var columns = [
        {data: 'image', name: 'image', orderable: false, searchable: false},
        {data: 'name', name: 'name'},
        @if($isAdmin)
        {data: 'average_purchase_price', name: 'average_purchase_price', orderable: true, searchable: false, render: function(data, type, row) {
            if (type === 'display' && data && !isNaN(data)) {
                return '<span>' + parseFloat(data).toLocaleString('en-US', { style: 'currency', currency: 'BDT', minimumFractionDigits: 2 }) + '</span>';
            }
            return data;
        }},
        @endif
        {data: 'price', name: 'price', orderable: true, searchable: false},
        {data: 'stock', name: 'stock', orderable: true, searchable: false},
        {data: 'actions', name: 'actions', orderable: false, searchable: false}
    ];
    $('#product-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#product-table').data('url'),
        columns: columns,
        // No default order, use backend or DataTable default
    });
});
</script>
@endpush
