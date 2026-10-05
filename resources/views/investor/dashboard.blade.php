@extends('layouts.investor.master')

@section('title', 'Investor Dashboard')

@section('breadcrumb-title')
    <h3>Investor Dashboard</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Dashboard</li>
@endsection

@push('css')
    <style>
        .investor-stat-card {
            border-radius: 8px;
            border: 1px solid #edf2f7;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .investor-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07);
        }
        .stat-icon-wrapper {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        .referral-banner {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border-radius: 8px;
            color: #ffffff;
            padding: 16px 20px;
        }
        .referral-input-group {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 6px;
            padding: 3px;
            display: flex;
            align-items: center;
        }
        .referral-input-group input {
            background: transparent;
            border: none;
            color: #ffffff;
            font-weight: 500;
            padding: 6px 10px;
            font-size: 13px;
            width: 100%;
        }
        .referral-input-group input:focus {
            outline: none;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Referral Header Banner -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="referral-banner d-md-flex justify-content-between align-items-center">
                <div class="mb-2 mb-md-0">
                    <h5 class="font-weight-bold mb-1"><i class="fa fa-gem mr-2"></i> Welcome, {{ $investor->name }}!</h5>
                    <p class="mb-0 text-white-50 small">Grow your returns with our 3-year 2x investment plan & earn {{ (float) config('investment.monthly_referrer_bonus_rate', 5.0) }}% recurring monthly bonuses on your direct referrals.</p>
                </div>
                <div>
                    <span class="small text-white-50 d-block mb-1">Your Referral Code: <strong class="text-white">{{ $investor->referral_code }}</strong></span>
                    <div class="referral-input-group">
                        <input type="text" id="refLinkInput" value="{{ $investor->getReferralLink() }}" readonly>
                        <button class="btn btn-light btn-sm font-weight-600 py-1 px-2" onclick="copyReferralLink()">
                            <i class="fa fa-copy"></i> Copy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row mb-2">
        <!-- Total Invested -->
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card investor-stat-card h-100 mb-0">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase small font-weight-600 d-block mb-1">Total Invested</span>
                        <h4 class="font-weight-bold text-dark mb-0">{{ number_format($totalInvested, 2) }} <small>TK</small></h4>
                    </div>
                    <div class="stat-icon-wrapper bg-light-primary text-primary">
                        <i class="fa fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Expected Return (2x) -->
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card investor-stat-card h-100 mb-0">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase small font-weight-600 d-block mb-1">Expected Return (2x)</span>
                        <h4 class="font-weight-bold text-info mb-0">{{ number_format($totalExpectedReturn, 2) }} <small>TK</small></h4>
                    </div>
                    <div class="stat-icon-wrapper bg-light-info text-info">
                        <i class="fa fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Received Return -->
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card investor-stat-card h-100 mb-0">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase small font-weight-600 d-block mb-1">Received Return</span>
                        <h4 class="font-weight-bold text-success mb-0">{{ number_format($totalReceivedReturn, 2) }} <small>TK</small></h4>
                    </div>
                    <div class="stat-icon-wrapper bg-light-success text-success">
                        <i class="fa fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Balance -->
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card investor-stat-card h-100 mb-0 border-primary">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase small font-weight-600 d-block mb-1">Available Balance</span>
                        <h4 class="font-weight-bold text-primary mb-0">{{ number_format($availableBalance, 2) }} <small>TK</small></h4>
                        @if($pendingWithdrawals > 0)
                            <small class="text-warning d-block mt-1">Pending: {{ number_format($pendingWithdrawals, 2) }} TK</small>
                        @endif
                    </div>
                    <div class="stat-icon-wrapper bg-primary text-white">
                        <i class="fa fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Row: Referral Stats -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3 mb-md-0">
            <div class="card investor-stat-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase font-weight-600 d-block">Referral Bonus Earned</span>
                        <h4 class="font-weight-bold text-success mb-0">+{{ number_format($totalReferralBonus, 2) }} <small>TK</small></h4>
                    </div>
                    <a href="{{ route('investor.referrals.index') }}" class="btn btn-outline-success btn-sm">
                        View Referrals ({{ $referralsCount }}) <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card investor-stat-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase font-weight-600 d-block">Ready to Withdraw?</span>
                        <span class="font-weight-500 text-dark">Withdraw straight to your bKash or Bank Account</span>
                    </div>
                    <a href="{{ route('investor.transactions.index') }}" class="btn btn-primary btn-sm ml-1">
                        <i class="fa fa-paper-plane mr-1"></i> Request Withdraw
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row">
        <!-- Active Investments -->
        <div class="col-xl-7 mb-4">
            <div class="card investor-stat-card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="font-weight-bold mb-0 text-dark"><i class="fa fa-layer-group text-primary mr-2"></i> My Investments</h5>
                    <a href="{{ route('investor.investments.index') }}" class="btn btn-light btn-sm text-primary font-weight-600">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Investment ID</th>
                                    <th>Invested</th>
                                    <th>2x Return</th>
                                    <th>Progress (36 Mo.)</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($investments as $inv)
                                    <tr>
                                        <td><strong>#{{ $inv->id }}</strong><br><small class="text-muted">{{ $inv->start_date->format('d M Y') }}</small></td>
                                        <td>{{ number_format($inv->invested_amount, 2) }} TK</td>
                                        <td class="text-success font-weight-600">{{ number_format($inv->total_return_amount, 2) }} TK</td>
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
                                            <a href="{{ route('investor.investments.show', $inv->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            No investments recorded yet. Please contact admin to activate your initial investment.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Installments -->
        <div class="col-xl-5 mb-4">
            <div class="card investor-stat-card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="font-weight-bold mb-0 text-dark"><i class="fa fa-calendar-alt text-info mr-2"></i> Upcoming Returns</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Installment</th>
                                    <th>Due Date</th>
                                    <th>Net Payout</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingInstallments as $inst)
                                    <tr>
                                        <td>
                                            <strong>#{{ $inst->installment_number }}</strong>
                                            <small class="text-muted d-block">Inv #{{ $inst->investment_id }}</small>
                                        </td>
                                        <td>
                                            {{ $inst->due_date->format('d M Y') }}
                                            @if($inst->due_date->isToday())
                                                <span class="badge badge-warning ml-1">Today</span>
                                            @endif
                                        </td>
                                        <td class="font-weight-bold text-success">
                                            +{{ number_format($inst->net_investor_amount, 2) }} TK
                                        </td>
                                        <td>
                                            <span class="badge badge-warning">Pending</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            No pending upcoming installments.
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
</div>
@endsection

@push('js')
<script>
    function copyReferralLink() {
        const copyText = document.getElementById("refLinkInput");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        alert("Referral link copied to clipboard: " + copyText.value);
    }
</script>
@endpush
