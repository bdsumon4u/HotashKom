@extends('layouts.light.master')

@section('title', 'Company Investment Earnings')

@section('breadcrumb-title')
    <h3>Company Investment Earnings</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Investment Module</li>
    <li class="breadcrumb-item active">Company Earnings</li>
@endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card h-100">
                <div class="card-body">
                    <span class="text-muted small text-uppercase font-weight-600 d-block">Total Company Revenue</span>
                    <h3 class="font-weight-bold mb-0 text-success">+{{ number_format($totalEarnings, 2) }} TK</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card h-100">
                <div class="card-body">
                    <span class="text-muted small text-uppercase font-weight-600 d-block">Initial Fee Revenue</span>
                    <h3 class="font-weight-bold mb-0 text-primary">+{{ number_format($initialEarnings, 2) }} TK</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="text-muted small text-uppercase font-weight-600 d-block">Monthly Fee Revenue</span>
                    <h3 class="font-weight-bold mb-0 text-info">+{{ number_format($monthlyEarnings, 2) }} TK</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings Table Card -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="font-weight-bold mb-0">Platform Fee Income Ledger</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable" id="earnings-table" style="width: 100%;">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Fee Type</th>
                            <th>Investor</th>
                            <th>Investment</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                </table>
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
        $('#earnings-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.investment.earnings.index') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'type', name: 'type' },
                { data: 'investor', name: 'investor.name' },
                { data: 'investment', name: 'investment.id' },
                { data: 'amount', name: 'amount' },
                { data: 'description', name: 'description' },
                { data: 'created_at', name: 'created_at' }
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
