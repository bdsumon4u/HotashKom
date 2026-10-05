@extends('layouts.light.master')

@section('title', 'Investor Withdrawals')

@section('breadcrumb-title')
    <h3>Investor Withdrawals</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Investment Module</li>
    <li class="breadcrumb-item active">Withdrawals</li>
@endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold mb-0">Investor Withdrawal Requests</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable" id="withdrawals-table" style="width: 100%;">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Investor</th>
                                    <th>Payout Details</th>
                                    <th>Amount</th>
                                    <th>Current Balance</th>
                                    <th>Requested Date</th>
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

<!-- Confirm Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="confirmForm">
                @csrf
                <input type="hidden" id="confirm_transaction_id" name="transaction_id">
                <input type="hidden" id="confirm_investor_id" name="investor_id">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold">Confirm Investor Withdrawal</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Please provide the Transaction ID / Ref Number after sending payment:</p>
                    <div class="form-group">
                        <label for="trx_id" class="font-weight-600">Transaction ID (Trx ID) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="trx_id" name="trx_id" required placeholder="e.g. 9J28SKL892">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnConfirmSubmit">Confirm Payout</button>
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
    $(document).ready(function() {
        const table = $('#withdrawals-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.investment.withdrawals.data') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'investor', name: 'investor', orderable: false, searchable: false },
                { data: 'account_details', name: 'account_details', orderable: false, searchable: false },
                { data: 'amount', name: 'amount', searchable: false },
                { data: 'balance', name: 'balance', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at', searchable: false },
                { data: 'status', name: 'status', searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']]
        });

        // Open Confirm Modal
        $(document).on('click', '.confirm-withdraw', function() {
            const id = $(this).data('id');
            const investorId = $(this).data('investor-id');
            $('#confirm_transaction_id').val(id);
            $('#confirm_investor_id').val(investorId);
            $('#trx_id').val('');
            $('#confirmModal').modal('show');
        });

        // Handle Confirm Form Submit
        $('#confirmForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#btnConfirmSubmit');
            btn.prop('disabled', true).text('Confirming...');

            $.ajax({
                url: "{{ route('admin.investment.withdrawals.confirm') }}",
                method: "POST",
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    alert(res.message);
                    $('#confirmModal').modal('hide');
                    btn.prop('disabled', false).text('Confirm Payout');
                    table.ajax.reload();
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Confirm Payout');
                    alert(xhr.responseJSON ? xhr.responseJSON.message : 'Error confirming withdrawal.');
                }
            });
        });

        // Handle Delete / Reject
        $(document).on('click', '.delete-withdraw', function() {
            if (!confirm('Are you sure you want to delete/reject this withdrawal request? The amount will be restored to investor balance.')) {
                return;
            }

            const id = $(this).data('id');
            const investorId = $(this).data('investor-id');

            $.ajax({
                url: "{{ route('admin.investment.withdrawals.delete') }}",
                method: "POST",
                data: {
                    transaction_id: id,
                    investor_id: investorId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    alert(res.message);
                    table.ajax.reload();
                },
                error: function(xhr) {
                    alert(xhr.responseJSON ? xhr.responseJSON.message : 'Error rejecting withdrawal.');
                }
            });
        });
    });
</script>
@endpush
