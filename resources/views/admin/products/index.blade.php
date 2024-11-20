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
   <div class="row">
      <div class="col-sm-12">
         <div class="card rounded-0 shadow-sm">
            <div class="card-header p-3">
               <div class="row px-3 justify-content-between align-items-center">
                  <div>All Products</div>
                  <a href="{{route('admin.products.create')}}" class="btn btn-sm btn-primary">New Product</a>
               </div>
            </div>
            <div class="card-body p-3">
               <div class="table-responsive product-table">
                  <table class="display" id="product-table" data-url="{{ route('api.products') }}">
                     <thead>
                        <tr>
                           <th width="100">Image</th>
                           <th>Name</th>
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
<script src="{{asset('assets/js/product-list-custom.js')}}"></script>
@endpush
