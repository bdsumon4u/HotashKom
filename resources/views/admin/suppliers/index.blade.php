@extends('layouts.light.master')

@section('title', 'Suppliers List')

@section('breadcrumb-title')
    <h3>Suppliers</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Purchases</li>
    <li class="breadcrumb-item active">Suppliers</li>
@endsection

@section('breadcrumb-right')
    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary btn-sm">
        <i class="fa fa-plus"></i> New Supplier
    </a>
@endsection

@section('content')
<div class="container-fluid mb-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif
    @if(session('danger'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('danger') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <!-- Supplier KPI Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-left-primary">
                <div class="card-body p-3">
                    <span class="text-muted small">Total Suppliers</span>
                    <h4 class="font-roboto font-weight-bold mb-0">{{ count($suppliers) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-left-info">
                <div class="card-body p-3">
                    <span class="text-muted small">Total Purchases</span>
                    <h4 class="font-roboto font-weight-bold mb-0">{!! theMoney($suppliers->sum('purchases_sum_total_amount')) !!}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-left-danger">
                <div class="card-body p-3">
                    <span class="text-muted small">Total Outstanding Due</span>
                    <h4 class="font-roboto font-weight-bold mb-0 text-danger">{!! theMoney($suppliers->sum('current_due')) !!}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Suppliers Table -->
    <div class="card shadow-sm border mb-4" style="background: #ffffff;">
        <div class="card-header p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-users text-primary mr-2"></i> Suppliers List</h5>
            <span class="text-muted small font-weight-bold">Total: {{ count($suppliers) }} Suppliers</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-dark font-weight-bold">Supplier / Company</th>
                            <th class="text-dark font-weight-bold">Phone</th>
                            <th class="text-dark font-weight-bold">Email</th>
                            <th class="text-center text-dark font-weight-bold" style="width: 100px;">Purchases</th>
                            <th class="text-right text-dark font-weight-bold" style="width: 140px;">Total Bought</th>
                            <th class="text-right text-dark font-weight-bold" style="width: 140px;">Total Paid</th>
                            <th class="text-right text-dark font-weight-bold" style="width: 140px;">Current Due</th>
                            <th class="text-center text-dark font-weight-bold" style="width: 170px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.suppliers.show', $supplier) }}" class="font-weight-bold text-primary">
                                        {{ $supplier->name }}
                                    </a>
                                    @if($supplier->company_name)
                                        <div class="small text-muted">{{ $supplier->company_name }}</div>
                                    @endif
                                </td>
                                <td>{{ $supplier->phone ?? '—' }}</td>
                                <td>{{ $supplier->email ?? '—' }}</td>
                                <td class="text-center font-roboto">{{ $supplier->purchases_count }}</td>
                                <td class="text-right font-roboto">{!! theMoney($supplier->purchases_sum_total_amount ?? 0) !!}</td>
                                <td class="text-right font-roboto text-success">{!! theMoney($supplier->purchases_sum_paid_amount ?? 0) !!}</td>
                                <td class="text-right font-roboto font-weight-bold {{ $supplier->current_due > 0 ? 'text-danger' : 'text-success' }}">
                                    {!! theMoney($supplier->current_due) !!}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-outline-info btn-xs mr-1" title="View Ledger">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-success btn-xs mr-1" data-toggle="modal" data-target="#payModal{{ $supplier->id }}" title="Make Payment">
                                        <i class="fa fa-money"></i> Pay
                                    </button>
                                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-outline-primary btn-xs mr-1" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this supplier?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-xs" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>

                                    <!-- Quick Payment Modal -->
                                    <div class="modal fade text-left" id="payModal{{ $supplier->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.suppliers.payment', $supplier) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-light p-3">
                                                        <h5 class="modal-title font-weight-bold">Pay Supplier: {{ $supplier->name }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    </div>
                                                    <div class="modal-body p-3">
                                                        <div class="alert alert-info py-2">
                                                            Current Outstanding Due: <strong>{!! theMoney($supplier->current_due) !!}</strong>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Payment Amount (BDT) <span class="text-danger">*</span></label>
                                                            <input type="number" step="0.01" name="amount" class="form-control font-weight-bold" max="{{ $supplier->current_due > 0 ? $supplier->current_due : 1000000 }}" value="{{ $supplier->current_due > 0 ? $supplier->current_due : '' }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Paid From Account <span class="text-danger">*</span></label>
                                                            <select name="account_id" class="form-control" required>
                                                                @foreach($accounts as $acc)
                                                                    <option value="{{ $acc->id }}">{{ $acc->name }} (Balance: {{ number_format($acc->current_balance, 2) }})</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Payment Date <span class="text-danger">*</span></label>
                                                            <input type="date" name="payment_date" class="form-control" value="{{ now()->toDateString() }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Reference / Cheque #</label>
                                                            <input type="text" name="reference" class="form-control" placeholder="Optional transaction ID">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Notes</label>
                                                            <input type="text" name="notes" class="form-control" placeholder="Optional notes">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer p-2 bg-light">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check mr-1"></i> Disburse Payment</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No suppliers created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .border-left-primary { border-left: 4px solid #6366f1 !important; }
    .border-left-info    { border-left: 4px solid #06b6d4 !important; }
    .border-left-danger  { border-left: 4px solid #ef4444 !important; }
</style>
@endpush
