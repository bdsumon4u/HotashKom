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

                                /* Print styles */
        @media print {
            html, body {
                height: 100vh;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden;
            }

                        .main-nav {
                display: none !important;
                width: 0 !important;
            }

            .page-main-header {
                display: none !important;
                width: 0 !important;
            }

            .print-edit-buttons,
            .footer {
                display: none !important;
            }

            .page-body {
                font-size: 16px;
                margin-top: 0 !important;
                margin-left: 0 !important;
                page-break-after: always;
            }

            .page-body p {
                font-size: 14px !important;
            }

            /* Hide DataTable elements */
            .dt-buttons,
            .dataTables_paginate,
            .dataTables_info,
            .dataTables_filter,
            .dataTables_length,
            .card-header .d-flex,
            .no-print {
                display: none !important;
            }

            /* Hide Actions column */
            .table th:last-child,
            .table td:last-child,
            #money-requests-table th:last-child,
            #money-requests-table td:last-child,
            .dataTable th:last-child,
            .dataTable td:last-child {
                display: none !important;
            }

            /* Remove any horizontal lines or borders at the top */
            hr,
            .hr,
            [class*="border-top"],
            [class*="border-bottom"],
            .border-top,
            .border-bottom {
                display: none !important;
                border: none !important;
            }

            /* Reset page body for printing */
            .page-body {
                font-size: 16px;
                margin-top: 0 !important;
                margin-left: 0 !important;
                page-break-after: always;
            }

            .page-body p {
                font-size: 14px !important;
            }

            /* Container adjustments */
            .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }

            /* Style the card for printing */
            .card {
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .card-body {
                padding: 10px 0 !important;
            }

            /* Table styles for printing */
            .table {
                border-collapse: collapse !important;
                width: 100% !important;
            }

            .table th,
            .table td {
                border: 1px solid #000 !important;
                padding: 8px !important;
                text-align: left !important;
            }

            .table thead th {
                background-color: #f8f9fa !important;
                font-weight: bold !important;
            }

            /* Print header styles */
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
                padding-bottom: 10px;
            }

            .print-header h1 {
                margin: 0;
                font-size: 24px;
                font-weight: bold;
            }

            .print-header .date {
                font-size: 14px;
                color: #666;
                margin-top: 5px;
            }

                        /* Ensure only the card content is visible */
            .row {
                margin: 0 !important;
            }

            .col-sm-12 {
                padding: 0 !important;
            }

            /* Hide specific navigation and layout elements */
            .main-nav,
            .sidebar-wrapper,
            .sidebar,
            .main-header,
            .page-header,
            .page-title,
            .breadcrumb,
            footer,
            .footer,
            .main-footer,
            .dt-buttons,
            .dataTables_paginate,
            .dataTables_info,
            .dataTables_filter,
            .dataTables_length,
            .card-header .d-flex,
            .no-print,
            /* Additional header selectors */
            header,
            .header,
            .top-header,
            .navbar,
            .navbar-header,
            .navbar-nav,
            .nav-header,
            .page-header-wrapper,
            .main-header-wrapper,
            /* Hide Actions column */
            .table th:last-child,
            .table td:last-child {
                display: none !important;
                width: 0 !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="page-title no-print">
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
        <!-- Print Header (hidden on screen, visible when printing) -->
        <div class="print-header" style="display: none;">
            <h1>Money Requests Report</h1>
            <div class="date">Generated on: {{ now()->format('F j, Y \a\t g:i A') }}</div>
        </div>

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
                                <div class="ml-3">
                                    <button type="button" class="btn btn-primary btn-sm" id="printButton">
                                        <i class="fa fa-print"></i> Print Report
                                    </button>
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
                    {data: 'id', name: 'id', searchable: false},
                    {data: 'reseller', name: 'reseller', searchable: false},
                    {data: 'bkash', name: 'bkash', searchable: false},
                    {data: 'amount', name: 'amount', searchable: false},
                    {data: 'requested_at', name: 'requested_at', searchable: false},
                    {data: 'status', name: 'status', searchable: false},
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

                                                            // Handle print button click
            $('#printButton').on('click', function() {
                // Show print header
                $('.print-header').show();

                // Hide elements that shouldn't be printed (simplified like show.blade.php)
                $('.main-nav, .page-main-header, .footer, .card-header .d-flex, .dt-buttons, .dataTables_paginate, .dataTables_info, .dataTables_filter, .dataTables_length').addClass('no-print');

                // Hide Actions column
                $('.table th:last-child, .table td:last-child, #money-requests-table th:last-child, #money-requests-table td:last-child, .dataTable th:last-child, .dataTable td:last-child').addClass('no-print');

                // Print the page
                window.print();

                // Hide print header and remove no-print classes after printing
                setTimeout(function() {
                    $('.print-header').hide();
                    $('.main-nav, .page-main-header, .footer, .card-header .d-flex, .dt-buttons, .dataTables_paginate, .dataTables_info, .dataTables_filter, .dataTables_length').removeClass('no-print');
                    $('.table th:last-child, .table td:last-child, #money-requests-table th:last-child, #money-requests-table td:last-child, .dataTable th:last-child, .dataTable td:last-child').removeClass('no-print');
                }, 1000);
            });
        });
    </script>
@endpush
