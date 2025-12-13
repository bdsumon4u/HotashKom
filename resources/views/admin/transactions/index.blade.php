@extends('layouts.light.master')
@section('title', 'Wallet Transactions')

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
    </style>
@endpush

@section('breadcrumb-title')
    <h3>Wallet Transactions</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Resellers</li>
    <li class="breadcrumb-item">Transactions</li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('content')
    <div class="mb-5 row">
        <div class="col-sm-12">
            <div class="card">
                <div class="p-3 card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Wallet Transactions</strong>&nbsp;<small>for {{ $user->name }}</small>
                            @if($user->bkash_number)
                                <br><span class="text-info"><strong>bKash:</strong> {{ $user->bkash_number }}</span>
                            @endif
                        </div>
                        <div>
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <strong>Available Balance:</strong> {{ number_format($user->getAvailableBalance(), 2) }} tk
                                    @if($user->getPendingWithdrawalAmount() > 0)
                                        <br><small class="text-muted">Total Balance: {{ number_format($user->balance, 2) }} tk</small>
                                        <br><small class="text-warning">Pending Withdrawals: {{ number_format($user->getPendingWithdrawalAmount(), 2) }} tk</small>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                    data-target="#withdrawModal">
                                    Withdraw
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-3 card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>S.I.</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Meta</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdraw Modal -->
    <div class="modal fade" id="withdrawModal" tabindex="-1" role="dialog" aria-labelledby="withdrawModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="withdrawModalLabel">Withdraw from {{ $user->name }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="withdrawForm" method="POST" action="{{ route('admin.transactions.withdraw', $user) }}">
                    @csrf
                    <div class="modal-body">
                        @if($user->bkash_number)
                            <div class="alert alert-info">
                                <strong>bKash Number:</strong> {{ $user->bkash_number }}
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="amount">Amount (tk)</label>
                            <input type="number" class="form-control" id="amount" name="amount" min="1"
                                max="{{ $user->getAvailableBalance() }}" step="0.01" required>
                            <small class="form-text text-muted">Available balance: {{ number_format($user->getAvailableBalance(), 2) }} tk</small>
                            @if($user->getPendingWithdrawalAmount() > 0)
                                <br><small class="form-text text-warning">User has {{ number_format($user->getPendingWithdrawalAmount(), 2) }} tk in pending withdrawals</small>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="trx-id">Trx ID</label>
                            <input type="text" class="form-control" id="trx-id" name="trx_id" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Withdraw</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm Withdraw Modal -->
    <div class="modal fade" id="confirmWithdrawModal" tabindex="-1" role="dialog" aria-labelledby="confirmWithdrawModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmWithdrawModalLabel">Confirm Withdrawal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="confirmWithdrawForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        @if($user->bkash_number)
                            <div class="alert alert-info">
                                <strong>bKash Number:</strong> {{ $user->bkash_number }}
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="confirm-amount">Amount (tk)</label>
                            <input type="number" class="form-control" id="confirm-amount" readonly>
                        </div>
                        <div class="form-group">
                            <label for="confirm-trx-id">Trx ID</label>
                            <input type="text" class="form-control" id="confirm-trx-id" name="trx_id" required>
                        </div>
                        <input type="hidden" id="transaction-id" name="transaction_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Confirm Withdrawal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}" defer></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/dataTables.buttons.min.js') }}" defer></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/buttons.bootstrap4.min.js') }}" defer></script>
@endpush

@push('scripts')
    <script>
        var table = $('.datatable').DataTable({
            search: [{
                bRegex: true,
                bSmart: false,
            }],
            dom: 'lBftip',
            buttons: [{
                text: 'Export',
                className: 'px-1 py-1',
                action: function(e, dt, node, config) {
                    // Add export functionality if needed
                }
            }],
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.transactions.index', $user) }}",
                data: function(d) {
                    // No need to pass user_id since we're using route-model binding
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'meta',
                    name: 'meta'
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [3, 'desc']
            ],
            pageLength: 50,
            lengthMenu: [[10, 25, 50, 100, 250, 500], [10, 25, 50, 100, 250, 500]],
        });

        // Handle withdraw form submission
        $('#withdrawForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#withdrawModal').modal('hide');
                    table.ajax.reload();
                    $.notify('Withdrawal successful', 'success');
                    // Reload page to update balance
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                },
                error: function(xhr) {
                    $.notify(xhr.responseJSON.message || 'Error processing withdrawal', 'error');
                }
            });
        });

        // Handle confirm withdraw button click
        $(document).on('click', '.confirm-withdraw', function() {
            var transactionId = $(this).data('id');
            var amount = $(this).data('amount');

            $('#transaction-id').val(transactionId);
            $('#confirm-amount').val(amount);
            $('#confirmWithdrawModal').modal('show');
        });

        // Handle delete withdraw button click
        $(document).on('click', '.delete-withdraw', function() {
            var transactionId = $(this).data('id');
            var amount = $(this).data('amount');

            if (confirm('Are you sure you want to delete this withdrawal request for ' + amount + ' tk?')) {
                $.ajax({
                    url: "{{ route('admin.transactions.delete-withdraw', $user) }}",
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        transaction_id: transactionId
                    },
                    success: function(response) {
                        table.ajax.reload();
                        $.notify(response.message, 'success');
                        // Reload page to update balance
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        $.notify(xhr.responseJSON.message || 'Error deleting withdrawal request', 'error');
                    }
                });
            }
        });

        // Handle confirm withdraw form submission
        $('#confirmWithdrawForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('admin.transactions.confirm-withdraw', $user) }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#confirmWithdrawModal').modal('hide');
                    table.ajax.reload();
                    $.notify(response.message, 'success');
                    // Reload page to update balance
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                },
                error: function(xhr) {
                    $.notify(xhr.responseJSON.message || 'Error confirming withdrawal', 'error');
                }
            });
        });
    </script>
@endpush
