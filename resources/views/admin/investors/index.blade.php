@extends('layouts.light.master')

@section('title', 'Investors')

@section('breadcrumb-title')
    <h3>Investors</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Investment Module</li>
    <li class="breadcrumb-item active">Investors</li>
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
                    <h5 class="font-weight-bold mb-0">Investor Directory</h5>
                    <a href="{{ route('admin.investment.investors.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus-circle mr-1"></i> Register New Investor
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable" id="investors-table" style="width: 100%;">
                            <thead class="bg-light">
                                <tr>
                                    <th>S.I.</th>
                                    <th>Investor</th>
                                    <th>Referral Code</th>
                                    <th>Referred By</th>
                                    <th>Total Invested</th>
                                    <th>Total Received</th>
                                    <th>Available Balance</th>
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
@endsection

@push('js')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}" defer></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/dataTables.buttons.min.js') }}" defer></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/buttons.bootstrap4.min.js') }}" defer></script>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#investors-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.investment.investors.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'referral_code', name: 'referral_code' },
                { data: 'referred_by', name: 'referred_by', orderable: false, searchable: false },
                { data: 'total_invested', name: 'total_invested', orderable: false, searchable: false },
                { data: 'total_returned', name: 'total_returned', orderable: false, searchable: false },
                { data: 'balance', name: 'balance', orderable: false, searchable: false },
                { data: 'is_active', name: 'is_active' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
