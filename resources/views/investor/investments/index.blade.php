@extends('layouts.investor.master')

@section('title', 'My Investments')

@section('breadcrumb-title')
    <h3>My Investments</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('investor.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Investments</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold mb-0">Investment Portfolio</h5>
                    <span class="text-muted small">All investments run on a 36-month schedule yielding 2x total return</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Start Date</th>
                                    <th>Principal Invested</th>
                                    <th>Total Return (2x)</th>
                                    <th>Monthly Gross Return</th>
                                    <th>Completed / Total</th>
                                    <th>Paid Total</th>
                                    <th>Remaining Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($investments as $investment)
                                    <tr>
                                        <td><strong>#{{ $investment->id }}</strong></td>
                                        <td>{{ $investment->start_date->format('d M Y') }}</td>
                                        <td class="font-weight-bold">{{ number_format($investment->invested_amount, 2) }} TK</td>
                                        <td class="font-weight-bold text-success">{{ number_format($investment->total_return_amount, 2) }} TK</td>
                                        <td>{{ number_format($investment->monthly_installment_amount, 2) }} TK/mo</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 mr-2" style="height: 10px;">
                                                    <div class="progress-bar bg-success" style="width: {{ $investment->getProgressPercentage() }}%"></div>
                                                </div>
                                                <small class="font-weight-600">{{ $investment->installments_paid_count }}/{{ $investment->duration_months }}</small>
                                            </div>
                                        </td>
                                        <td class="text-success">{{ number_format($investment->total_paid_amount, 2) }} TK</td>
                                        <td class="text-muted">{{ number_format($investment->getRemainingReturnAmount(), 2) }} TK</td>
                                        <td>
                                            <span class="badge badge-{{ $investment->status === 'active' ? 'success' : ($investment->status === 'completed' ? 'primary' : 'danger') }}">
                                                {{ ucfirst($investment->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('investor.investments.show', $investment->id) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-list"></i> Schedule
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4 text-muted">
                                            No investments found in your account yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $investments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
