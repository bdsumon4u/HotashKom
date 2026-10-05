@extends('layouts.light.master')

@section('title', 'Investor Profile - ' . $investor->name)

@section('breadcrumb-title')
    <h3>Investor: {{ $investor->name }}</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.investment.investors.index') }}">Investors</a></li>
    <li class="breadcrumb-item active">{{ $investor->name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Row -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <span class="badge badge-info px-3 py-1 font-weight-bold" style="font-size: 13px;">
                    Referral Code: {{ $investor->referral_code }}
                </span>
                @if($investor->referrer)
                    <span class="badge badge-light border text-dark ml-2 px-3 py-1">
                        Referred by: <strong>{{ $investor->referrer->name }}</strong>
                    </span>
                @endif
            </div>

            <div>
                <a href="{{ route('admin.investment.investors.edit', $investor->id) }}" class="btn btn-primary btn-sm mr-2">
                    <i class="fa fa-edit"></i> Edit Profile
                </a>
                <button type="button" class="btn btn-success btn-sm font-weight-bold" data-toggle="modal" data-target="#recordInvestmentModal">
                    <i class="fa fa-plus-circle mr-1"></i> Record New Investment
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-1">
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card h-100 mb-0">
                <div class="card-body p-3">
                    <span class="text-muted small text-uppercase font-weight-600 d-block mb-1">Total Invested</span>
                    <h4 class="font-weight-bold text-dark mb-0">{{ number_format($totalInvested, 2) }} TK</h4>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card h-100 mb-0">
                <div class="card-body p-3">
                    <span class="text-muted small text-uppercase font-weight-600 d-block mb-1">Expected Return (2x)</span>
                    <h4 class="font-weight-bold text-info mb-0">{{ number_format($totalExpected, 2) }} TK</h4>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card h-100 mb-0">
                <div class="card-body p-3">
                    <span class="text-muted small text-uppercase font-weight-600 d-block mb-1">Total Received</span>
                    <h4 class="font-weight-bold text-success mb-0">{{ number_format($totalReceived, 2) }} TK</h4>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card h-100 mb-0">
                <div class="card-body p-3">
                    <span class="text-muted small text-uppercase font-weight-600 d-block mb-1">Available Balance</span>
                    <h4 class="font-weight-bold text-primary mb-0">{{ number_format($availableBalance, 2) }} TK</h4>
                    @if($pendingWithdrawal > 0)
                        <small class="text-warning d-block">Pending: {{ number_format($pendingWithdrawal, 2) }} TK</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Details & Investments -->
    <div class="row">
        <!-- Investor Info -->
        <div class="col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="font-weight-bold mb-0">Investor Details</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3"><strong>Name:</strong> {{ $investor->name }}</li>
                        <li class="mb-3"><strong>Email:</strong> {{ $investor->email }}</li>
                        <li class="mb-3"><strong>Phone:</strong> {{ $investor->phone_number ?: 'N/A' }}</li>
                        <li class="mb-3"><strong>bKash Number:</strong> {{ $investor->bkash_number ?: 'N/A' }}</li>
                        <li class="mb-3"><strong>Status:</strong>
                            <span class="badge badge-{{ $investor->is_active ? 'success' : 'danger' }}">
                                {{ $investor->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </li>
                        <li class="mb-3"><strong>Joined Date:</strong> {{ $investor->created_at->format('d M Y, h:i A') }}</li>
                        <li class="mb-3"><strong>Bank Details:</strong><br><span class="text-muted">{{ $investor->bank_details ?: 'N/A' }}</span></li>
                        <li><strong>Address:</strong><br><span class="text-muted">{{ $investor->address ?: 'N/A' }}</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Investments List -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold mb-0">Investments</h5>
                    <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#recordInvestmentModal">
                        <i class="fa fa-plus"></i> Add Investment
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Start Date</th>
                                    <th>Invested</th>
                                    <th>2x Return</th>
                                    <th>Monthly Gross</th>
                                    <th>Progress (36 Mo.)</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($investor->investments as $inv)
                                    <tr>
                                        <td><strong>#{{ $inv->id }}</strong></td>
                                        <td>{{ $inv->start_date->format('d M Y') }}</td>
                                        <td class="font-weight-bold">{{ number_format($inv->invested_amount, 2) }} TK</td>
                                        <td class="text-success font-weight-bold">{{ number_format($inv->total_return_amount, 2) }} TK</td>
                                        <td>{{ number_format($inv->monthly_installment_amount, 2) }} TK</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 mr-2" style="height: 8px;">
                                                    <div class="progress-bar bg-success" style="width: {{ $inv->getProgressPercentage() }}%"></div>
                                                </div>
                                                <small class="font-weight-600">{{ $inv->installments_paid_count }}/{{ $inv->duration_months }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $inv->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($inv->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.investment.investments.show', $inv->id) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i> Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            No investments recorded yet for this investor.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions History -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="font-weight-bold mb-0">Recent Wallet Transactions</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                            <tr>
                                <td>#{{ $trx->id }}</td>
                                <td>
                                    @if($trx->type === 'deposit')
                                        <span class="badge badge-success">Deposit</span>
                                    @else
                                        <span class="badge badge-danger">Withdraw</span>
                                    @endif
                                </td>
                                <td class="font-weight-bold {{ $trx->type === 'deposit' ? 'text-success' : 'text-danger' }}">
                                    {{ $trx->type === 'deposit' ? '+' : '-' }}{{ number_format(abs((float) $trx->amount), 2) }} TK
                                </td>
                                <td>
                                    {{ $trx->meta['reason'] ?? 'N/A' }}
                                    @if(isset($trx->meta['trx_id']))
                                        <br><small class="text-muted">Trx ID: {{ $trx->meta['trx_id'] }}</small>
                                    @endif
                                </td>
                                <td>{{ $trx->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    @if($trx->confirmed)
                                        <span class="badge badge-success">Confirmed</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">No transactions recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Record Investment Modal -->
<div class="modal fade" id="recordInvestmentModal" tabindex="-1" role="dialog" aria-labelledby="recordInvestmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.investment.investors.investments.store', $investor->id) }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="recordInvestmentModalLabel">
                        <i class="fa fa-hand-holding-usd mr-1"></i> Record Investment for {{ $investor->name }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 small">
                        <strong>Terms:</strong> 3-Year term (36 monthly installments), 2x total return.
                        @php
                            $initDeductRate = (float) config('investment.initial_deduction_rate', 0.0);
                            $initRefRate = (float) config('investment.initial_referrer_bonus_rate', 0.0);
                        @endphp
                        @if($initDeductRate > 0)
                            <br>Immediate {{ $initDeductRate }}% deduction applied ({{ $investor->referrer && $initRefRate > 0 ? $initRefRate.'% to referrer '.$investor->referrer->name.' + '.max(0, $initDeductRate - $initRefRate).'% to company' : $initDeductRate.'% to company' }}).
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="amount" class="font-weight-600">Invested Amount (TK) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="1000" class="form-control" id="amount" name="amount" required placeholder="e.g. 100000">
                    </div>

                    <div class="form-group">
                        <label for="start_date" class="font-weight-600">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="form-group">
                        <label for="notes" class="font-weight-600">Notes / Remarks</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success font-weight-bold">
                        <i class="fa fa-check"></i> Activate Investment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
