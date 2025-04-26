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
                        </div>
                        <div>
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <strong>Balance:</strong> {{ number_format($user->balance, 2) }} tk
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
                                    <th>Meta</th>
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
                <form id="withdrawForm" method="POST" action="{{ route('admin.transactions.withdraw', $user->id) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="amount">Amount (tk)</label>
                            <input type="number" class="form-control" id="amount" name="amount" min="1"
                                max="{{ $user->balance }}" step="0.01" required>
                            <small class="form-text text-muted">Available balance: {{ number_format($user->balance, 2) }}
                                tk</small>
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
@endsection

@push('js')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/buttons.bootstrap4.min.js') }}"></script>
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
                url: "{{ route('admin.transactions.index') }}",
                data: function(d) {
                    d.user_id = "{{ $user->id }}";
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
                    data: 'meta',
                    name: 'meta'
                }
            ],
            order: [
                [3, 'desc']
            ],
            pageLength: 50,
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
    </script>
@endpush
