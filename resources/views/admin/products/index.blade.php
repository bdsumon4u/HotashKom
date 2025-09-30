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
    <!-- Stock Summary Cards -->
    <div class="row">
        <div class="col-md-4">
            <div class="text-white rounded-sm shadow-sm card bg-primary">
                <div class="px-3 py-2 card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">{{ $totalStockCount ?? 0 }}</h6>
                            <small>Total Stock Count</small>
                        </div>
                        <div class="align-self-center">
                            <i data-feather="package" class="feather-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="text-white rounded-sm shadow-sm card bg-info">
                <div class="px-3 py-2 card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">{!!$totalPurchaseValue!!}</h6>
                            <small>Total Purchase Value</small>
                        </div>
                        <div class="align-self-center">
                            <i data-feather="shopping-cart" class="feather-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="text-white rounded-sm shadow-sm card bg-success">
                <div class="px-3 py-2 card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">{!!$totalSellValue!!}</h6>
                            <small>Total Sell Value</small>
                        </div>
                        <div class="align-self-center">
                            <i data-feather="dollar-sign" class="feather-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Stock Summary Cards -->
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
                  <table class="display" id="product-table" data-url="{{ route('api.products', ['admin_id' => auth('admin')->id()]) }}">
                     <thead>
                        <tr>
                           <th width="100">Image</th>
                           <th>Name</th>
                           <th>Price</th>
                           <th>Stock</th>
                           <th>Status</th>
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
        {data: 'price', name: 'price', orderable: true, searchable: false},
        {data: 'stock', name: 'stock', orderable: true, searchable: false},
        {data: 'status', name: 'status', orderable: true, searchable: false},
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
