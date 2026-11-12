@extends('layouts.light.master')

@section('title', 'Purchases & Stock Report')

@section('breadcrumb-title')
<h3>Purchases & Stock Report</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Purchases & Stock Report</li>
@endsection

@section('breadcrumb-right')
<div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
        <i class="fa fa-print mr-1"></i> Print Report
    </button>
    <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary btn-sm">
        <i class="fa fa-plus mr-1"></i> Add Purchase
    </a>
</div>
@endsection

@section('content')
<!-- Print Header (Visible on print) -->
<div id="print-purchases-header" style="display: none;">
    <div style="display: flex; justify-content-between; align-items: flex-start; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px;">
        <div>
            <h2 style="margin: 0; font-size: 22px; font-weight: bold; color: #1e293b;">Purchases & Stock Monthly Summary</h2>
            <div style="font-size: 13px; color: #64748b; margin-top: 4px;">Procurement overview, supplier bills, payment status & stock statistics</div>
        </div>
        <div style="text-align: right; font-size: 12px; color: #475569;">
            <div><strong>Generated:</strong> {{ now()->format('M d, Y h:i A') }}</div>
            <div><strong>Total Orders:</strong> {{ $purchaseTotals['orders'] ?? 0 }}</div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Stock Summary Cards -->
    <div class="row">
        <div class="col-md-4">
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
        </div><div class="col-md-4">
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

    <!-- Purchases Table -->
    <div class="mb-5 rounded-sm shadow-sm card border">
        <div class="p-3 card-header bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
            <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-shopping-bag text-primary mr-1"></i> Purchase History</h5>
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <label for="filter-purchase-month" class="mb-0 text-muted small font-weight-bold">Month:</label>
                    <input type="month" id="filter-purchase-month" class="form-control form-control-sm" style="width: 155px; height: 32px;">
                    <button type="button" id="clear-month-btn" class="btn btn-outline-secondary btn-xs" title="Show All Purchases">
                        <i class="fa fa-times mr-1"></i> All
                    </button>
                </div>
            </div>
            <div class="d-flex align-items-center" style="gap: 8px;">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                    <i class="fa fa-print mr-1"></i> Print Table
                </button>
                <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary btn-sm d-flex align-items-center justify-content-center">
                    <i data-feather="plus"></i> Add Purchase
                </a>
            </div>
        </div>
        <div class="p-3 card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0" id="purchases-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th style="width: 120px;">Date</th>
                            <th style="width: 100px;">Products</th>
                            <th style="width: 170px;">Total Bill / Due</th>
                            <th>Supplier</th>
                            <th style="width: 140px;">Admin</th>
                            <th style="width: 110px;">Actions</th>
                        </tr>
                    </thead>
                    <tfoot style="background-color: #f8fafc; border-top: 2px solid #cbd5e1;">
                        <tr>
                            <th colspan="2" id="footer-count" class="font-weight-bold text-dark">Total: —</th>
                            <th id="footer-products" class="font-weight-bold text-dark">—</th>
                            <th id="footer-amount" class="font-weight-bold text-dark font-roboto">—</th>
                            <th colspan="3" id="footer-summary" class="font-roboto text-muted small">—</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this purchase record? This action will:</p>
                <ul>
                    <li>Remove the purchase from the system</li>
                    <li>Revert stock changes for all products in this purchase</li>
                    <li>Recalculate average purchase prices</li>
                </ul>
                <p class="text-danger"><strong>This action cannot be undone!</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Purchase</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link href="{{ cdnAsset('datatables.css', 'assets/css/datatables.css') }}" rel="stylesheet" crossorigin="anonymous" referrerpolicy="no-referrer">
<link href="{{ cdnAsset('select2.css', 'assets/css/select2.css') }}" rel="stylesheet" crossorigin="anonymous" referrerpolicy="no-referrer">
<style>
@media print {
    .page-main-header,
    .main-nav,
    .sidebar-wrapper,
    .breadcrumb,
    .breadcrumb-right,
    .btn,
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate,
    footer {
        display: none !important;
    }
    .page-body, .container-fluid {
        padding: 0 !important;
        margin: 0 !important;
    }
    #print-purchases-header {
        display: block !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .card-header {
        background: #f8fafc !important;
        border-bottom: 2px solid #000 !important;
    }
    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    th, td {
        border: 1px solid #ddd !important;
        color: #000 !important;
    }
}
</style>
@endpush

@push('scripts')
<script src="{{ cdnAsset('datatables.js', 'assets/js/datatables.js') }}" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ cdnAsset('datatables.js-bootstrap5', 'assets/js/datatables-bootstrap5.js') }}" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ cdnAsset('select2.js', 'assets/js/select2.js') }}" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
function confirmDelete(purchaseId) {
    if (confirm('Are you sure you want to delete this purchase record? This will revert stock changes and cannot be undone.')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.purchases.index") }}/' + purchaseId;

        var csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        var methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

$(document).ready(function() {
    // Initialize DataTable
    var table = $('#purchases-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("api.purchases") }}',
            data: function(d) {
                d.month = $('#filter-purchase-month').val();
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'formatted_date', name: 'purchase_date'},
            {data: 'products_count', name: 'products_count'},
            {data: 'formatted_amount', name: 'total_amount'},
            {data: 'supplier_display', name: 'supplier_name'},
            {data: 'admin_display', name: 'admin.name'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false}
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, 250, 500], [10, 25, 50, 100, 250, 500]],
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        },
        drawCallback: function() {
            var json = table.ajax.json();
            if (json && json.aggregation) {
                var agg = json.aggregation;
                var countText = 'Total: ' + agg.total_records + ' POs';
                var selectedMonth = $('#filter-purchase-month').val();
                if (selectedMonth) {
                    countText += ' (' + selectedMonth + ')';
                }
                $('#footer-count').html('<strong>' + countText + '</strong>');

                var amountText = '<span class="text-dark font-weight-bold">৳' + Number(agg.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span>';
                $('#footer-amount').html(amountText);

                var summaryHtml = '<span class="text-success font-weight-bold mr-2">Paid: ৳' + Number(agg.total_paid).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span>';
                if (agg.total_due > 0) {
                    summaryHtml += '<span class="text-danger font-weight-bold">Due: ৳' + Number(agg.total_due).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span>';
                }
                $('#footer-summary').html(summaryHtml);
            }
        }
    });

    $('#filter-purchase-month').on('change', function() {
        table.ajax.reload();
    });

    $('#clear-month-btn').on('click', function() {
        $('#filter-purchase-month').val('');
        table.ajax.reload();
    });
});
</script>
@endpush
