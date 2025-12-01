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
