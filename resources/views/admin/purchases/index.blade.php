@extends('layouts.light.master')

@section('title', 'Purchases & Stock Report')

@section('breadcrumb-title')
<h3>Purchases & Stock Report</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Purchases & Stock Report</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Stock Summary Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="text-white rounded-sm shadow-sm card bg-primary">
                <div class="px-3 py-2 card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">{{ number_format($totalStockCount) }}</h6>
                            <small>Total Stock Count</small>
                        </div>
                        <div class="align-self-center">
                            <i data-feather="package" class="feather-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-white rounded-sm shadow-sm card bg-success">
                <div class="px-3 py-2 card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">{{ number_format($totalStockValue) }}</h6>
                            <small>Total Stock Value</small>
                        </div>
                        <div class="align-self-center">
                            <i data-feather="dollar-sign" class="feather-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-white rounded-sm shadow-sm card bg-info">
                <div class="px-3 py-2 card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">{{ number_format($totalPurchaseValue) }}</h6>
                            <small>Total Purchase Value</small>
                        </div>
                        <div class="align-self-center">
                            <i data-feather="shopping-cart" class="feather-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-white rounded-sm shadow-sm card bg-warning">
                <div class="px-3 py-2 card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">{{ number_format($totalPurchaseRecords) }}</h6>
                            <small>Total Purchase Records</small>
                        </div>
                        <div class="align-self-center">
                            <i data-feather="list" class="feather-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="mb-5 rounded-sm shadow-sm card">
        <div class="p-3 card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Purchase History</h5>
                <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary btn-sm d-flex align-items-center justify-content-center">
                    <i data-feather="plus"></i> Add Purchase
                </a>
            </div>
        </div>
        <div class="p-3 card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="purchases-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Products</th>
                            <th>Subtotal</th>
                            <th>Supplier</th>
                            <th>Admin</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#purchases-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("api.purchases") }}',
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'formatted_date', name: 'purchase_date'},
            {data: 'products_count', name: 'products_count'},
            {data: 'formatted_amount', name: 'total_amount'},
            {data: 'supplier_display', name: 'supplier_name'},
            {data: 'admin_display', name: 'admin.name'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false}
        ],
        order: [[0, 'desc']],
        pageLength: 15,
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        }
    });
});
</script>
@endpush
