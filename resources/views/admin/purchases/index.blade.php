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

    <!-- Monthly Purchase Summary Table -->
    <div class="mb-4 rounded-sm shadow-sm card border">
        <div class="p-3 card-header bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
            <h5 class="mb-0 font-weight-bold text-dark">
                <i class="fa fa-calendar text-primary mr-2"></i> Monthly Purchase Summary
            </h5>
            <span class="text-muted small">Aggregated purchase bills, payments, and dues by month</span>
        </div>
        <div class="p-0 card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="min-width: 140px;">Month</th>
                            <th class="text-center" style="width: 130px;">PO Count</th>
                            <th class="text-right" style="width: 170px;">Total Bill Amount</th>
                            <th class="text-right" style="width: 170px;">Total Paid Amount</th>
                            <th class="text-right" style="width: 170px;">Total Due Amount</th>
                            <th class="text-center" style="min-width: 220px;">Payment Status Breakdown</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlyPurchases as $row)
                            <tr>
                                <td class="font-roboto font-weight-bold text-dark">
                                    <i class="fa fa-calendar-check-o text-muted mr-1"></i> {{ $row->month_label }}
                                </td>
                                <td class="text-center font-roboto">
                                    <span class="badge badge-light border px-2 py-1 font-weight-bold">{{ number_format($row->total_orders) }}</span>
                                </td>
                                <td class="text-right font-roboto font-weight-bold text-dark">{!! theMoney($row->total_amount) !!}</td>
                                <td class="text-right font-roboto font-weight-bold text-success">{!! theMoney($row->total_paid) !!}</td>
                                <td class="text-right font-roboto font-weight-bold {{ $row->total_due > 0 ? 'text-danger' : 'text-muted' }}">
                                    {!! theMoney($row->total_due) !!}
                                </td>
                                <td class="text-center">
                                    @if($row->paid_count > 0)
                                        <span class="badge badge-success px-2 py-1 mr-1" title="Paid POs">Paid: {{ $row->paid_count }}</span>
                                    @endif
                                    @if($row->partial_count > 0)
                                        <span class="badge badge-warning px-2 py-1 mr-1 text-white" title="Partial Paid POs">Partial: {{ $row->partial_count }}</span>
                                    @endif
                                    @if($row->unpaid_count > 0)
                                        <span class="badge badge-danger px-2 py-1 mr-1" title="Unpaid POs">Unpaid: {{ $row->unpaid_count }}</span>
                                    @endif
                                    @if($row->paid_count == 0 && $row->partial_count == 0 && $row->unpaid_count == 0)
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No purchases recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot style="background-color: #f1f5f9; border-top: 2px solid #94a3b8;">
                        <tr>
                            <th class="font-weight-bold text-dark">Grand Total Summation:</th>
                            <th class="text-center font-roboto font-weight-bold text-dark">{{ number_format($purchaseTotals['orders']) }}</th>
                            <th class="text-right font-roboto font-weight-bold text-dark">{!! theMoney($purchaseTotals['amount']) !!}</th>
                            <th class="text-right font-roboto font-weight-bold text-success">{!! theMoney($purchaseTotals['paid']) !!}</th>
                            <th class="text-right font-roboto font-weight-bold {{ $purchaseTotals['due'] > 0 ? 'text-danger' : 'text-dark' }}">{!! theMoney($purchaseTotals['due']) !!}</th>
                            <th class="text-center font-roboto">
                                <span class="badge badge-success px-2 py-1 mr-1">Paid: {{ $purchaseTotals['paid_count'] }}</span>
                                <span class="badge badge-warning px-2 py-1 mr-1 text-white">Partial: {{ $purchaseTotals['partial_count'] }}</span>
                                <span class="badge badge-danger px-2 py-1 mr-1">Unpaid: {{ $purchaseTotals['unpaid_count'] }}</span>
                            </th>
                        </tr>
                    </tfoot>
                </table>
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
                            <th>ID</th>
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
        pageLength: 15,
        lengthMenu: [[10, 25, 50, 100, 250, 500], [10, 25, 50, 100, 250, 500]],
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        }
    });
});
</script>
@endpush
