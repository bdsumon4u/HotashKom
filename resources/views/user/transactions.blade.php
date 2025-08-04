@extends('layouts.yellow.master')

@push('styles')
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

        table.dataTable thead .sorting:before, table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:before,
        table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_desc:before, table.dataTable thead
        .sorting_desc:after, table.dataTable thead .sorting_asc_disabled:before, table.dataTable thead
        .sorting_asc_disabled:after, table.dataTable thead .sorting_desc_disabled:before, table.dataTable thead
        .sorting_desc_disabled:after {
            bottom: 12px;
        }
    </style>
@endpush

@section('title', 'Transaction History')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            @include('user.layouts.sidebar')
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Transaction History</h5>
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <strong>Available Balance:</strong> {{ number_format(auth('user')->user()->getAvailableBalance(), 2) }} tk
                                    @if(auth('user')->user()->getPendingWithdrawalAmount() > 0)
                                        <br><small class="text-muted">Total Balance: {{ number_format(auth('user')->user()->balance, 2) }} tk</small>
                                        <br><small class="text-warning">Pending Withdrawals: {{ number_format(auth('user')->user()->getPendingWithdrawalAmount(), 2) }} tk</small>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#withdrawRequestModal">
                                    Request Withdraw
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>S.I.</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdraw Request Modal -->
    <div class="modal fade" id="withdrawRequestModal" tabindex="-1" role="dialog" aria-labelledby="withdrawRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="withdrawRequestModalLabel">Request Withdrawal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="withdrawRequestForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="amount">Amount (tk)</label>
                            <input type="number" class="form-control" id="amount" name="amount" min="1" max="{{ auth('user')->user()->getAvailableBalance() }}" step="0.01" required>
                            <small class="form-text text-muted">Available balance: {{ number_format(auth('user')->user()->getAvailableBalance(), 2) }} tk</small>
                            @if(auth('user')->user()->getPendingWithdrawalAmount() > 0)
                                <br><small class="form-text text-warning">You have {{ number_format(auth('user')->user()->getPendingWithdrawalAmount(), 2) }} tk in pending withdrawals</small>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/buttons.bootstrap4.min.js') }}"></script>

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
            ajax: "{{ route('user.transactions') }}",
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
                    name: 'meta',
                }
            ],
            order: [
                [3, 'desc']
            ],
            pageLength: 50,
        });

        // Handle withdraw request form submission
        $('#withdrawRequestForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('user.withdraw.request') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#withdrawRequestModal').modal('hide');
                    table.ajax.reload();
                    $.notify(response.message, 'success');
                    // Reload page to update balance
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                },
                error: function(xhr) {
                    $.notify(xhr.responseJSON.message || 'Error processing request', 'error');
                }
            });
        });
    </script>
@endpush
