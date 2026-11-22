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
                  <div class="d-flex align-items-center flex-wrap" style="gap: .75rem;">
                     <h5 class="mb-0 font-weight-bold">Products</h5>
                     @php
                        $currentFilter = request('filter') ?? request('only') ?? '';
                     @endphp
                     <div class="btn-group" role="group" id="product-filter-group" aria-label="Product Filter">
                        <a href="{{ route('admin.products.index') }}"
                           data-filter=""
                           data-active-class="btn-primary"
                           data-outline-class="btn-outline-primary"
                           class="btn btn-xs product-filter-btn {{ $currentFilter === '' ? 'btn-primary active' : 'btn-outline-primary' }}">
                           All ({{ number_format($productCounts['all'] ?? 0) }})
                        </a>
                        <a href="{{ route('admin.products.index', ['filter' => 'active']) }}"
                           data-filter="active"
                           data-active-class="btn-success"
                           data-outline-class="btn-outline-success"
                           class="btn btn-xs product-filter-btn {{ $currentFilter === 'active' ? 'btn-success active' : 'btn-outline-success' }}">
                           Active ({{ number_format($productCounts['active'] ?? 0) }})
                        </a>
                        <a href="{{ route('admin.products.index', ['filter' => 'inactive']) }}"
                           data-filter="inactive"
                           data-active-class="btn-danger"
                           data-outline-class="btn-outline-danger"
                           class="btn btn-xs product-filter-btn {{ $currentFilter === 'inactive' ? 'btn-danger active' : 'btn-outline-danger' }}">
                           Inactive ({{ number_format($productCounts['inactive'] ?? 0) }})
                        </a>
                        <a href="{{ route('admin.products.index', ['filter' => 'variational']) }}"
                           data-filter="variational"
                           data-active-class="btn-info"
                           data-outline-class="btn-outline-info"
                           class="btn btn-xs product-filter-btn {{ in_array($currentFilter, ['variational', 'variation', 'variations', 'has-variations']) ? 'btn-info active' : 'btn-outline-info' }}">
                           Variational ({{ number_format($productCounts['variational'] ?? 0) }})
                        </a>
                        @if(isset($productCounts['low_stock']) || $currentFilter === 'low-stock')
                        <a href="{{ route('admin.products.index', ['filter' => 'low-stock']) }}"
                           data-filter="low-stock"
                           data-active-class="btn-warning"
                           data-outline-class="btn-outline-warning"
                           class="btn btn-xs product-filter-btn {{ $currentFilter === 'low-stock' ? 'btn-warning active' : 'btn-outline-warning' }}">
                           Low Stock ({{ number_format($productCounts['low_stock'] ?? 0) }})
                        </a>
                        @endif
                     </div>
                  </div>
                  <div>
                     <a href="{{route('admin.products.create')}}" class="btn btn-sm btn-primary">
                        <i class="fa fa-plus mr-1"></i> New Product
                     </a>
                  </div>
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
<script src="{{asset('assets/js/datatable/datatables/jquery.dataTables.min.js')}}" defer></script>
<script>
$(function() {
    var currentFilter = '{{ $currentFilter }}';
    var columns = [
        {data: 'image', name: 'image', orderable: false, searchable: false},
        {data: 'name', name: 'name'},
        {data: 'price', name: 'price', orderable: true, searchable: false},
        {data: 'stock', name: 'stock', orderable: true, searchable: false},
        {data: 'status', name: 'status', orderable: true, searchable: false},
        {data: 'actions', name: 'actions', orderable: false, searchable: false}
    ];

    var table = $('#product-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('api.products') }}',
            data: function(d) {
                d.admin_id = '{{ auth('admin')->id() }}';
                if (currentFilter) {
                    d.filter = currentFilter;
                }
            }
        },
        columns: columns,
        lengthMenu: [[10, 25, 50, 100, 250, 500], [10, 25, 50, 100, 250, 500]],
        // No default order, use backend or DataTable default
    });

    $('.product-filter-btn').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var filter = $btn.data('filter') || '';

        if (currentFilter === filter) {
            return;
        }

        currentFilter = filter;

        // Reset all buttons to outline classes
        $('.product-filter-btn').each(function() {
            var $b = $(this);
            $b.removeClass('active ' + $b.data('active-class'))
              .addClass($b.data('outline-class'));
        });

        // Set active button
        $btn.removeClass($btn.data('outline-class'))
            .addClass('active ' + $btn.data('active-class'));

        // Reload DataTable
        table.ajax.reload();

        // Update URL in browser address bar without reloading page
        var newUrl = new URL(window.location.href);
        if (currentFilter) {
            newUrl.searchParams.set('filter', currentFilter);
            newUrl.searchParams.delete('only');
        } else {
            newUrl.searchParams.delete('filter');
            newUrl.searchParams.delete('only');
        }
        window.history.pushState({ filter: currentFilter }, '', newUrl.toString());
    });

    window.addEventListener('popstate', function() {
        var params = new URLSearchParams(window.location.search);
        var filterFromUrl = params.get('filter') || params.get('only') || '';

        if (currentFilter !== filterFromUrl) {
            currentFilter = filterFromUrl;
            $('.product-filter-btn').each(function() {
                var $b = $(this);
                var f = $b.data('filter') || '';
                var isActive = (f === currentFilter) ||
                    (f === 'variational' && ['variational', 'variation', 'variations', 'has-variations'].indexOf(currentFilter) !== -1);
                if (isActive) {
                    $b.removeClass($b.data('outline-class')).addClass('active ' + $b.data('active-class'));
                } else {
                    $b.removeClass('active ' + $b.data('active-class')).addClass($b.data('outline-class'));
                }
            });
            table.ajax.reload();
        }
    });
});
</script>
@endpush
