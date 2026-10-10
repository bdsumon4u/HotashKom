@extends('layouts.investor.master')

@section('title', 'Wallet & Transactions')

@section('breadcrumb-title')
    <h3>Wallet & Withdrawals</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('investor.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Wallet</li>
@endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Balance Card -->
    <div class="card mb-3">
        <div class="card-body p-3">
            <div class="d-md-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="p-2 bg-light-primary rounded-circle text-primary mr-3">
                        <i class="fa fa-wallet fa-lg"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase font-weight-600 d-block">Available for Withdrawal</span>
                        <h4 class="font-weight-bold mb-0 text-primary">{{ number_format($availableBalance, 2) }} TK</h4>
                        @if($pendingWithdrawal > 0)
                            <small class="text-warning">
                                <i class="fa fa-clock"></i> Pending: {{ number_format($pendingWithdrawal, 2) }} TK (Total: {{ number_format($totalBalance, 2) }} TK)
                            </small>
                        @endif
                    </div>
                </div>

                <div>
                    <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#withdrawModal" {{ $availableBalance < 100 ? 'disabled' : '' }}>
                        <i class="fa fa-paper-plane mr-1"></i> Request Withdrawal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="font-weight-bold mb-0">Transaction History</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable" id="transactions-table" style="width: 100%;">
                    <thead class="bg-light">
                        <tr>
                            <th>S.I.</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1" role="dialog" aria-labelledby="withdrawModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="withdrawForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="withdrawModalLabel"><i class="fa fa-money-bill-wave mr-2"></i> Request Withdrawal</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 small">
                        <strong>Available Balance:</strong> {{ number_format($availableBalance, 2) }} TK
                    </div>

                    <div class="form-group">
                        <label for="amount" class="font-weight-600">Withdraw Amount (TK) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="100" max="{{ $availableBalance }}" class="form-control" id="amount" name="amount" required placeholder="e.g. 5000">
                    </div>

                    <div class="form-group">
                        <label for="payment_method" class="font-weight-600">Payout Method <span class="text-danger">*</span></label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="bkash">bKash</option>
                            <option value="nagad">Nagad</option>
                            <option value="rocket">Rocket</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="account_number" class="font-weight-600">Account Number / Bank Details <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="account_number" name="account_number" rows="2" required placeholder="Enter mobile wallet number or Bank name, A/C No., Branch & Routing Number">{{ $investor->bkash_number ?: $investor->phone_number }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitWithdraw">Submit Request</button>
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
        const table = $('#transactions-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('investor.transactions.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'type', name: 'type', searchable: false },
                { data: 'amount', name: 'amount', searchable: false },
                { data: 'reason', name: 'reason', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at', searchable: false },
                { data: 'status', name: 'status', searchable: false }
            ],
            order: [[4, 'desc']]
        });

        $('#withdrawForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#btnSubmitWithdraw');
            btn.prop('disabled', true).text('Submitting...');

            $.ajax({
                url: "{{ route('investor.withdraw.request') }}",
                method: "POST",
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    alert(res.message);
                    $('#withdrawModal').modal('hide');
                    window.location.reload();
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Submit Request');
                    const msg = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred. Please try again.';
                    alert(msg);
                }
            });
        });
    });
</script>
@endpush
