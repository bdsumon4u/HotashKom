@extends('layouts.investor.master')

@section('title', 'Investment #' . $investment->id . ' Schedule')

@section('breadcrumb-title')
    <h3>Investment #{{ $investment->id }} Installments</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('investor.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('investor.investments.index') }}">Investments</a></li>
    <li class="breadcrumb-item active">#{{ $investment->id }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Overview Card -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row text-center text-md-left">
                <div class="col-md-3 border-right mb-3 mb-md-0">
                    <span class="text-muted small text-uppercase font-weight-600 d-block">Principal Invested</span>
                    <h4 class="font-weight-bold mb-0 text-dark">{{ number_format($investment->invested_amount, 2) }} TK</h4>
                    <small class="text-muted">Started: {{ $investment->start_date->format('d M Y') }}</small>
                </div>
                <div class="col-md-3 border-right mb-3 mb-md-0">
                    <span class="text-muted small text-uppercase font-weight-600 d-block">Total Return (2x)</span>
                    <h4 class="font-weight-bold mb-0 text-success">{{ number_format($investment->total_return_amount, 2) }} TK</h4>
                    <small class="text-muted">36 Monthly Installments</small>
                </div>
                <div class="col-md-3 border-right mb-3 mb-md-0">
                    <span class="text-muted small text-uppercase font-weight-600 d-block">Monthly Gross Return</span>
                    <h4 class="font-weight-bold mb-0 text-info">{{ number_format($investment->monthly_installment_amount, 2) }} TK</h4>
                    <small class="text-muted">Net After 10% Fee: {{ number_format($investment->monthly_installment_amount * 0.9, 2) }} TK</small>
                </div>
                <div class="col-md-3">
                    <span class="text-muted small text-uppercase font-weight-600 d-block">Overall Progress</span>
                    <h4 class="font-weight-bold mb-0 text-primary">{{ $investment->installments_paid_count }} / {{ $investment->duration_months }}</h4>
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: {{ $investment->getProgressPercentage() }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 36 Installments Schedule Table -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="font-weight-bold mb-0">36-Month Installment Payout Timeline</h5>
            <span class="badge badge-{{ $investment->status === 'active' ? 'success' : 'primary' }} px-3 py-2">
                {{ strtoupper($investment->status) }}
            </span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover text-center">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Due Date</th>
                            <th>Gross Amount</th>
                            <th>Platform Fee (10%)</th>
                            <th>Net Payout to You</th>
                            <th>Status</th>
                            <th>Credited At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($installments as $installment)
                            <tr class="{{ $installment->status === 'processed' ? 'table-success-light' : '' }}">
                                <td><strong>{{ $installment->installment_number }}</strong></td>
                                <td>{{ $installment->due_date->format('d M Y') }}</td>
                                <td>{{ number_format($installment->gross_amount, 2) }} TK</td>
                                <td class="text-danger">-{{ number_format($installment->deduction_amount, 2) }} TK</td>
                                <td class="font-weight-bold text-success">+{{ number_format($installment->net_investor_amount, 2) }} TK</td>
                                <td>
                                    @if($installment->status === 'processed')
                                        <span class="badge badge-success"><i class="fa fa-check"></i> Paid</span>
                                    @elseif($installment->due_date->isPast())
                                        <span class="badge badge-warning">Due</span>
                                    @else
                                        <span class="badge badge-secondary">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $installment->paid_at ? $installment->paid_at->format('d M Y, h:i A') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
