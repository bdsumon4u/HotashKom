@extends('layouts.light.master')

@section('title', 'Investment #' . $investment->id . ' Details')

@section('breadcrumb-title')
    <h3>Investment #{{ $investment->id }}</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.investment.investments.index') }}">Investments</a></li>
    <li class="breadcrumb-item active">#{{ $investment->id }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Investment Summary Card -->
    <div class="card mb-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
                <h5 class="font-weight-bold mb-1">
                    Investment #{{ $investment->id }} - Investor:
                    <a href="{{ route('admin.investment.investors.show', $investment->investor->id) }}">
                        {{ $investment->investor->name }}
                    </a>
                </h5>
                <small class="text-muted">
                    Referral: {{ $investment->investor->referrer ? 'Referred by ' . $investment->investor->referrer->name : 'Direct (No Referrer)' }}
                    | Start Date: {{ $investment->start_date->format('d M Y') }}
                </small>
            </div>
            <div>
                <span class="badge badge-{{ $investment->status === 'active' ? 'success' : 'primary' }} px-3 py-2 font-weight-bold" style="font-size: 14px;">
                    {{ strtoupper($investment->status) }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 border-right mb-3 mb-md-0">
                    <span class="text-muted small text-uppercase font-weight-600 d-block">Principal Invested</span>
                    <h4 class="font-weight-bold text-dark">{{ number_format($investment->invested_amount, 2) }} TK</h4>
                    <small class="text-muted">Multiplier: {{ $investment->multiplier }}x</small>
                </div>
                <div class="col-md-3 border-right mb-3 mb-md-0">
                    <span class="text-muted small text-uppercase font-weight-600 d-block">Total Return (2x)</span>
                    <h4 class="font-weight-bold text-success">{{ number_format($investment->total_return_amount, 2) }} TK</h4>
                    <small class="text-muted">Over {{ $investment->duration_months }} Months</small>
                </div>
                <div class="col-md-3 border-right mb-3 mb-md-0">
                    <span class="text-muted small text-uppercase font-weight-600 d-block">Initial {{ (float) $investment->initial_deduction_rate }}% Deduction</span>
                    <h4 class="font-weight-bold text-info">{{ number_format($investment->initial_deduction_amount, 2) }} TK</h4>
                    <small class="text-muted">
                        Ref: {{ number_format($investment->initial_referrer_bonus_amount, 2) }} TK |
                        Co: {{ number_format($investment->initial_company_amount, 2) }} TK
                    </small>
                </div>
                <div class="col-md-3">
                    <span class="text-muted small text-uppercase font-weight-600 d-block">Disbursed Progress</span>
                    <h4 class="font-weight-bold text-primary">{{ $investment->installments_paid_count }} / {{ $investment->duration_months }}</h4>
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: {{ $investment->getProgressPercentage() }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 36 Installments Schedule -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="font-weight-bold mb-0">36-Month Installment Schedule & Financial Distribution</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover text-center">
                    <thead class="bg-light">
                        @php
                            $hasReferrer = (bool) $investment->investor->referrer;
                            $monthlyFeeRate = (float) $investment->monthly_deduction_rate;
                            $referrerRate = $hasReferrer ? (float) $investment->monthly_referrer_bonus_rate : 0;
                            $companyRate = $hasReferrer ? max(0, $monthlyFeeRate - $referrerRate) : $monthlyFeeRate;
                            $netInvestorRate = 100 - $monthlyFeeRate;
                        @endphp
                        <tr>
                            <th>#</th>
                            <th>Due Date</th>
                            <th>Gross Amount</th>
                            <th>Total {{ (int) $monthlyFeeRate }}% Fee</th>
                            <th>Referrer Share ({{ (int) $referrerRate }}%)</th>
                            <th>Company Share ({{ (int) $companyRate }}%)</th>
                            <th>Net Investor Payout ({{ (int) $netInvestorRate }}%)</th>
                            <th>Status</th>
                            <th>Processed At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($investment->installments as $installment)
                            <tr>
                                <td><strong>{{ $installment->installment_number }}</strong></td>
                                <td>{{ $installment->due_date->format('d M Y') }}</td>
                                <td class="font-weight-bold">{{ number_format($installment->gross_amount, 2) }} TK</td>
                                <td class="text-danger">-{{ number_format($installment->deduction_amount, 2) }} TK</td>
                                <td class="text-info">{{ number_format($installment->referrer_bonus_amount, 2) }} TK</td>
                                <td class="text-primary font-weight-500">{{ number_format($installment->company_amount, 2) }} TK</td>
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
