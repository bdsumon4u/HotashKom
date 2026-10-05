@extends('layouts.light.master')

@section('title', 'Investments Portfolio')

@section('breadcrumb-title')
    <h3>Investments</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Investment Module</li>
    <li class="breadcrumb-item active">Investments</li>
@endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Stat Cards & Actions -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card h-100">
                <div class="card-body">
                    <span class="text-muted small text-uppercase font-weight-600 d-block">Active Investments</span>
                    <h3 class="font-weight-bold mb-0 text-success">{{ $totalActive }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card h-100">
                <div class="card-body">
                    <span class="text-muted small text-uppercase font-weight-600 d-block">Total Principal Invested</span>
                    <h3 class="font-weight-bold mb-0 text-primary">{{ number_format($totalInvested, 2) }} TK</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="text-muted small text-uppercase font-weight-600 d-block">Total Returns Disbursed</span>
                    <h3 class="font-weight-bold mb-0 text-info">{{ number_format($totalReturned, 2) }} TK</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Investments Table Card -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="font-weight-bold mb-0">All Investment Contracts</h5>
            <form method="POST" action="{{ route('admin.investment.investments.process-due') }}" onsubmit="return confirm('Process all due monthly installments as of today?');">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-sync-alt mr-1"></i> Process Due Installments Now
                </button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable" id="investments-table" style="width: 100%;">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Investor</th>
                            <th>Invested</th>
                            <th>2x Return</th>
                            <th>Progress (36 Mo.)</th>
                            <th>Start Date</th>
                            <th>Status</th>
                            <th>Actions</th>
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
        $('#investments-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.investment.investments.index') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'investor', name: 'investor.name' },
                { data: 'invested_amount', name: 'invested_amount' },
                { data: 'total_return', name: 'total_return_amount' },
                { data: 'progress', name: 'progress', orderable: false, searchable: false },
                { data: 'start_date', name: 'start_date' },
                { data: 'status', name: 'status' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
