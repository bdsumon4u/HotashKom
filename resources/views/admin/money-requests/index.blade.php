@extends('layouts.light.master')

@section('title', 'Money Requests')

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
    <style>
        .dt-buttons.btn-group {
            margin: .25rem 1rem 1rem 1rem;
        }

        .dt-buttons.btn-group .btn {
            font-size: 12px;
        }

        th:focus {
            outline: none;
        }

        /* Hide sort icons for ID column */
        .datatable thead th.sorting_asc,
        .datatable thead th.sorting_desc,
        .datatable thead th.sorting {
            background-image: none !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Money Requests</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.home') }}">
                                <i data-feather="home"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Money Requests</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="shadow-sm card rounded-0">
                    <div class="p-3 card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Pending Withdrawal Requests</h5>
                            <div class="d-flex align-items-center">
                                <div class="mr-3 text-right">
                                    <div class="mb-0 h4 text-warning" id="total-pending">0 tk</div>
                                    <small class="text-muted">Total Pending</small>
                                </div>
                                <div class="mr-3 text-right">
                                    <div class="mb-0 h5 text-info" id="total-requests">0</div>
                                    <small class="text-muted">Total Requests</small>
                                </div>
                                <div class="text-right">
                                    <div class="mb-0 h5 text-success" id="today-requests">0</div>
                                    <small class="text-muted">Today</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 card-body">
                        <div class="table-responsive">
                            <table class="display" id="money-requests-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Reseller</th>
                                        <th>Phone</th>
                                        <th>bKash</th>
                                        <th>Amount</th>
                                        <th>Requested At</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Withdrawal Modal -->
    <div class="modal fade" id="confirmWithdrawalModal" tabindex="-1" role="dialog" aria-labelledby="confirmWithdrawalModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmWithdrawalModalLabel">Confirm Withdrawal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="confirmWithdrawalForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Amount:</strong> <span id="modal-amount">0</span> tk
                        </div>
                        <div class="form-group">
                            <label for="trx_id">Transaction ID</label>
                            <input type="text" class="form-control" id="trx_id" name="trx_id" required placeholder="Enter transaction ID">
                            <small class="form-text text-muted">Enter the transaction ID from your payment system</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Confirm Withdrawal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Withdrawal Modal -->
    <div class="modal fade" id="deleteWithdrawalModal" tabindex="-1" role="dialog" aria-labelledby="deleteWithdrawalModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteWithdrawalModalLabel">Delete Withdrawal Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this withdrawal request?</p>
                    <div class="alert alert-warning">
                        <strong>Amount:</strong> <span id="delete-modal-amount">0</span> tk
                    </div>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete Request</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/buttons.bootstrap4.min.js') }}"></script>
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/datatable/datatable-extension/custom.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#money-requests-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.money-requests.data') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'reseller', name: 'reseller'},
                    {data: 'phone', name: 'phone'},
                    {data: 'bkash', name: 'bkash'},
                    {data: 'amount', name: 'amount'},
                    {data: 'requested_at', name: 'requested_at'},
                    {data: 'status', name: 'status'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ],
                order: [[0, 'desc']],
                pageLength: 25,
                responsive: true
            });

            // Load summary data
            function loadSummary() {
                $.get("{{ route('admin.money-requests.summary') }}")
                    .done(function(data) {
                        $('#total-pending').html(data.total_pending);
                        $('#total-requests').text(data.total_requests);
                        $('#today-requests').text(data.today_requests);
                    })
                    .fail(function() {
                        console.error('Failed to load summary data');
                    });
            }

            // Load summary on page load
            loadSummary();

            // Refresh summary every 30 seconds
            setInterval(loadSummary, 30000);

            // Handle confirm withdrawal button click
            $(document).on('click', '.confirm-withdraw', function() {
                var transactionId = $(this).data('id');
                var userId = $(this).data('user-id');
                var amount = $(this).data('amount');

                $('#modal-amount').text(amount.toLocaleString());
                $('#confirmWithdrawalForm').data('transaction-id', transactionId);
                $('#confirmWithdrawalForm').data('user-id', userId);
                $('#confirmWithdrawalModal').modal('show');
            });

            // Handle confirm withdrawal form submission
            $('#confirmWithdrawalForm').on('submit', function(e) {
                e.preventDefault();

                var transactionId = $(this).data('transaction-id');
                var userId = $(this).data('user-id');
                var trxId = $('#trx_id').val();

                $.ajax({
                    url: "{{ route('admin.money-requests.confirm') }}",
                    type: 'POST',
                    data: {
                        transaction_id: transactionId,
                        user_id: userId,
                        trx_id: trxId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#confirmWithdrawalModal').modal('hide');
                        $('#trx_id').val('');
                        table.ajax.reload();
                        loadSummary();
                        $.notify(response.message, 'success');
                    },
                    error: function(xhr) {
                        $.notify(xhr.responseJSON?.message || 'Error confirming withdrawal', 'error');
                    }
                });
            });

            // Handle delete withdrawal button click
            $(document).on('click', '.delete-withdraw', function() {
                var transactionId = $(this).data('id');
                var userId = $(this).data('user-id');
                var amount = $(this).data('amount');

                $('#delete-modal-amount').text(amount.toLocaleString());
                $('#confirmDelete').data('transaction-id', transactionId);
                $('#confirmDelete').data('user-id', userId);
                $('#deleteWithdrawalModal').modal('show');
            });

            // Handle delete confirmation
            $('#confirmDelete').on('click', function() {
                var transactionId = $(this).data('transaction-id');
                var userId = $(this).data('user-id');

                $.ajax({
                    url: "{{ route('admin.money-requests.delete') }}",
                    type: 'POST',
                    data: {
                        transaction_id: transactionId,
                        user_id: userId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#deleteWithdrawalModal').modal('hide');
                        table.ajax.reload();
                        loadSummary();
                        $.notify(response.message, 'success');
                    },
                    error: function(xhr) {
                        $.notify(xhr.responseJSON?.message || 'Error deleting withdrawal request', 'error');
                    }
                });
            });

            // Refresh table every 60 seconds
            setInterval(function() {
                table.ajax.reload(null, false);
            }, 60000);
        });
    </script>
@endpush
