@extends('layouts.light.master')

@section('title', 'Affiliate Network - ' . $reseller->name)

@section('breadcrumb-title')
    <h3>Affiliate Network: {{ $reseller->name }}</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('admin.resellers.index') }}">Resellers</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.resellers.edit', $reseller->id) }}">{{ $reseller->name }}</a></li>
    <li class="breadcrumb-item active">Affiliate Network</li>
@endsection

@section('breadcrumb-right')
    <div class="d-flex justify-content-lg-end align-items-center mt-2 mt-lg-0">
        <a href="{{ route('admin.resellers.edit', $reseller->id) }}" class="btn btn-primary btn-sm mr-2">
            <i class="fa fa-edit mr-1"></i> Edit Profile
        </a>
        <a href="{{ route('admin.orders.index', ['user_id' => $reseller->id, 'status' => '']) }}" class="btn btn-outline-primary btn-sm mr-2">
            <i class="fa fa-shopping-bag mr-1"></i> Orders
        </a>
        <a href="{{ route('admin.transactions.index', $reseller->id) }}" class="btn btn-outline-success btn-sm mr-2">
            <i class="fa fa-credit-card mr-1"></i> Ledger
        </a>
        <a href="{{ route('admin.resellers.index') }}" class="btn btn-light btn-sm border">
            <i class="fa fa-arrow-left mr-1"></i> All Resellers
        </a>
    </div>
@endsection

@push('css')
    <style>
        .affiliate-stat-box {
            border-radius: 8px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .affiliate-stat-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        .leg-card {
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.06);
        }
        .leg-header-left {
            background: linear-gradient(135deg, #0062cc 0%, #007bff 100%) !important;
            color: #fff !important;
        }
        .leg-header-right {
            background: linear-gradient(135deg, #1e7e34 0%, #28a745 100%) !important;
            color: #fff !important;
        }
        .leg-header-left h1, .leg-header-left h2, .leg-header-left h3, .leg-header-left h4, .leg-header-left h5, .leg-header-left h6,
        .leg-header-right h1, .leg-header-right h2, .leg-header-right h3, .leg-header-right h4, .leg-header-right h5, .leg-header-right h6 {
            color: #ffffff !important;
        }
        .progress-thin {
            height: 8px;
            border-radius: 4px;
        }
        .rank-card {
            border-radius: 12px;
            transition: all 0.25s ease;
            border: 1px solid rgba(0,0,0,0.06);
            overflow: hidden;
            position: relative;
        }
        .rank-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }
        .rank-card.rank-silver {
            border-top: 4px solid #757F9A;
        }
        .rank-card.rank-gold {
            border-top: 4px solid #f39c12;
        }
        .rank-card.rank-diamon, .rank-card.rank-diamond {
            border-top: 4px solid #00bcd4;
        }
        .rank-badge-silver {
            background: linear-gradient(135deg, #757F9A 0%, #D7DDE8 100%);
            color: #2c3e50;
            font-weight: 700;
        }
        .rank-badge-gold {
            background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
            color: #ffffff;
            font-weight: 700;
        }
        .rank-badge-diamon, .rank-badge-diamond {
            background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
            color: #ffffff;
            font-weight: 700;
        }
        .rank-prize-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px 14px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid mb-5">
        <!-- 0. Reseller Info Top Bar -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-light-primary text-primary d-flex align-items-center justify-content-center mr-3" style="width: 52px; height: 52px; min-width: 52px; font-size: 22px;">
                                <i class="fa fa-user-tie"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center mb-1">
                                    <h5 class="mb-0 font-weight-bold mr-2 text-dark">{{ $reseller->name }}</h5>
                                    <span class="badge badge-light border text-muted mr-2">ID: #{{ $reseller->id }}</span>
                                    @if ($reseller->is_verified)
                                        <span class="badge badge-success"><i class="fa fa-check mr-1"></i> Verified Reseller</span>
                                    @else
                                        <span class="badge badge-warning"><i class="fa fa-clock mr-1"></i> Pending Verification</span>
                                    @endif
                                </div>
                                <div class="text-muted small d-flex flex-wrap align-items-center">
                                    <span class="mr-3 my-1"><i class="fa fa-store text-muted mr-1"></i> {{ $reseller->shop_name ?? '-' }}</span>
                                    <span class="mr-3 my-1"><i class="fa fa-phone text-muted mr-1"></i> {{ $reseller->phone_number ?? '-' }}</span>
                                    <span class="mr-3 my-1"><i class="fa fa-wallet text-muted mr-1"></i> Wallet: <strong class="text-dark">{{ number_format((float) $reseller->balance, 2) }} BDT</strong></span>
                                    <span class="my-1">
                                        @if ($affiliate->referrer)
                                            <i class="fa fa-link text-muted mr-1"></i> Referred by: <a href="{{ route('admin.resellers.affiliate', $affiliate->referrer_id) }}"><strong>{{ $affiliate->referrer->name }}</strong></a>
                                        @else
                                            <i class="fa fa-star text-warning mr-1"></i> <strong>Root Reseller</strong>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1. Share Referral Link Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #f8faff 0%, #edf3ff 100%); border-left: 5px solid #0062cc !important;">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                            <div>
                                <h5 class="mb-1 text-primary font-weight-bold">
                                    <i class="fa fa-share-alt mr-2"></i> Referral Code & Registration Link
                                </h5>
                                <p class="mb-0 text-muted small">
                                    New resellers registered with this link will be placed alternatingly into this reseller's <strong>Left</strong> and <strong>Right</strong> datasets upon verification.
                                </p>
                            </div>
                            <span class="badge badge-primary px-3 py-2 font-roboto f-13 mt-2 mt-md-0">
                                Referral Code: <strong>{{ $affiliate->referral_code }}</strong>
                            </span>
                        </div>

                        <div class="row">
                            <div class="col-lg-4 col-md-5 mb-3 mb-lg-0">
                                <label class="small text-muted font-weight-bold mb-1">Referral Code</label>
                                <div class="input-group">
                                    <input type="text" class="form-control font-weight-bold bg-white text-primary text-center" readonly value="{{ $affiliate->referral_code }}" id="adminRefCodeInput">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" onclick="copyToClipboard('{{ $affiliate->referral_code }}', 'Referral Code')">
                                            <i class="fa fa-copy mr-1"></i> Copy
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-7">
                                <label class="small text-muted font-weight-bold mb-1">Referral URL</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-white" readonly value="{{ $referralLink }}" id="adminRefLinkInput">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" onclick="copyToClipboard('{{ $referralLink }}', 'Referral Link')">
                                            <i class="fa fa-link mr-1"></i> Copy URL
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Top Metric Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card affiliate-stat-box border-0 shadow-sm h-100 mb-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small font-weight-bold text-uppercase">Total Direct Referred</span>
                                <h3 class="mb-0 mt-1 font-weight-bold text-dark">{{ $directTotal }}</h3>
                                <span class="text-muted small">Registrations with code</span>
                            </div>
                            <div class="rounded-circle bg-light-primary text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px; font-size: 20px;">
                                <i class="fa fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card affiliate-stat-box border-0 shadow-sm h-100 mb-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small font-weight-bold text-uppercase">Successful Referrals</span>
                                <h3 class="mb-0 mt-1 text-success font-weight-bold">{{ $directVerified }}</h3>
                                <span class="text-muted small">Verified accounts</span>
                            </div>
                            <div class="rounded-circle bg-light-success text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px; font-size: 20px;">
                                <i class="fa fa-user-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card affiliate-stat-box border-0 shadow-sm h-100 mb-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small font-weight-bold text-uppercase">Pending Referrals</span>
                                <h3 class="mb-0 mt-1 text-warning font-weight-bold">{{ $directPending }}</h3>
                                <span class="text-muted small">Unverified accounts</span>
                            </div>
                            <div class="rounded-circle bg-light-warning text-warning d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px; font-size: 20px;">
                                <i class="fa fa-user-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card affiliate-stat-box border-0 shadow-sm h-100 mb-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small font-weight-bold text-uppercase">Affiliate Commissions</span>
                                <h3 class="mb-0 mt-1 text-info font-weight-bold">{{ number_format((float) $totalCommissionEarned, 2) }} <small class="f-12">BDT</small></h3>
                                <span class="text-muted small">Credited to wallet</span>
                            </div>
                            <div class="rounded-circle bg-light-info text-info d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px; font-size: 20px;">
                                <i class="fa fa-coins"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Dual-Leg Balance & Network Totals -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-3 mb-lg-0">
                <div class="card leg-card shadow-sm h-100 mb-0">
                    <div class="card-header leg-header-left p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 font-weight-bold text-white"><i class="fa fa-arrow-left mr-2"></i> Left Dataset / Sub-Network</h5>
                            <span class="badge badge-light text-primary font-roboto px-2 py-1">LEFT</span>
                        </div>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="row align-items-center">
                            <div class="col-6 border-right">
                                <h1 class="display-4 font-weight-bold text-primary mb-0">{{ $affiliate->left_total }}</h1>
                                <p class="text-muted font-weight-bold mb-0">Total Left Network</p>
                                <span class="small text-muted">(Direct + Downlines)</span>
                            </div>
                            <div class="col-6">
                                <h2 class="font-weight-bold text-secondary mb-0">{{ $affiliate->direct_left_count }}</h2>
                                <p class="text-muted font-weight-bold mb-0">Direct Left Referrals</p>
                                <span class="small text-muted">Personally sponsored</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card leg-card shadow-sm h-100 mb-0">
                    <div class="card-header leg-header-right p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 font-weight-bold text-white">Right Dataset / Sub-Network <i class="fa fa-arrow-right ml-2"></i></h5>
                            <span class="badge badge-light text-success font-roboto px-2 py-1">RIGHT</span>
                        </div>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="row align-items-center">
                            <div class="col-6 border-right">
                                <h1 class="display-4 font-weight-bold text-success mb-0">{{ $affiliate->right_total }}</h1>
                                <p class="text-muted font-weight-bold mb-0">Total Right Network</p>
                                <span class="small text-muted">(Direct + Downlines)</span>
                            </div>
                            <div class="col-6">
                                <h2 class="font-weight-bold text-secondary mb-0">{{ $affiliate->direct_right_count }}</h2>
                                <p class="text-muted font-weight-bold mb-0">Direct Right Referrals</p>
                                <span class="small text-muted">Personally sponsored</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Special Rank & Leadership Rewards Showcase -->
        @if (!empty($rankRewards))
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white p-3 d-flex flex-wrap justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 font-weight-bold text-dark">
                                    <i class="fa fa-crown text-warning mr-2"></i> Special Rank &amp; Leadership Rewards
                                </h5>
                                <span class="text-muted small">Special milestone achievements awarded with exclusive honors. Disbursed manually by administration upon verification.</span>
                            </div>
                            <span class="badge badge-light border text-muted px-3 py-2 font-roboto f-12 mt-2 mt-md-0">
                                <i class="fa fa-hand-holding-usd mr-1"></i> Manual Disbursement
                            </span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                @foreach ($rankRewards as $rr)
                                    @php
                                        $labelLower = strtolower($rr['label'] ?? '');
                                        $isSilver = str_contains($labelLower, 'silver');
                                        $isGold = str_contains($labelLower, 'gold');
                                        $isDiamond = str_contains($labelLower, 'diamon');
                                        $rankClass = $isSilver ? 'rank-silver' : ($isGold ? 'rank-gold' : ($isDiamond ? 'rank-diamond' : ''));
                                        $badgeClass = $isSilver ? 'rank-badge-silver' : ($isGold ? 'rank-badge-gold' : ($isDiamond ? 'rank-badge-diamond' : 'badge-primary text-white'));
                                        $iconClass = $isSilver ? 'fa-award' : ($isGold ? 'fa-crown' : ($isDiamond ? 'fa-gem' : 'fa-trophy'));
                                    @endphp
                                    <div class="col-lg-4 col-md-6 mb-3">
                                        <div class="card rank-card {{ $rankClass }} h-100 shadow-sm bg-white mb-0">
                                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                                <div>
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <span class="badge {{ $badgeClass }} px-3 py-2 text-uppercase f-12">
                                                            <i class="fa {{ $iconClass }} mr-1"></i> {{ $rr['label'] }} Rank
                                                        </span>
                                                        <span class="small font-weight-bold text-muted">
                                                            {{ number_format($rr['milestone_x']) }} Pairs
                                                        </span>
                                                    </div>

                                                    <div class="rank-prize-box mb-3 text-center">
                                                        <span class="text-muted small text-uppercase font-weight-bold d-block mb-1">Benefit / Reward</span>
                                                        <h5 class="mb-0 font-weight-bold text-dark">
                                                            <i class="fa fa-gift text-warning mr-1"></i> {{ $rr['benefit'] }}
                                                        </h5>
                                                    </div>

                                                    <p class="text-muted small mb-3">
                                                        Requires reaching <strong>{{ number_format($rr['milestone_x']) }}</strong> verified resellers on both Left &amp; Right teams.
                                                    </p>
                                                </div>

                                                <div>
                                                    <div class="d-flex align-items-center justify-content-between small text-muted font-weight-bold mb-1">
                                                        <span>Progress</span>
                                                        <span>{{ number_format($pairsCount) }} / {{ number_format($rr['milestone_x']) }} pairs ({{ $rr['progress_percent'] }}%)</span>
                                                    </div>
                                                    <div class="progress progress-thin mb-3 bg-light">
                                                        <div class="progress-bar {{ $rr['is_qualified'] ? 'bg-success' : 'bg-primary' }}"
                                                             role="progressbar" style="width: {{ $rr['progress_percent'] }}%"></div>
                                                    </div>

                                                    @if ($rr['is_qualified'])
                                                        <div class="text-center">
                                                            <span class="badge badge-success px-3 py-2 w-100 font-roboto f-13">
                                                                <i class="fa fa-check-circle mr-1"></i> Qualified for Award
                                                            </span>
                                                        </div>
                                                    @else
                                                        <div class="text-center">
                                                            <span class="badge badge-light border text-muted px-3 py-2 w-100 font-roboto f-12">
                                                                <i class="fa fa-clock mr-1"></i> {{ number_format(max(0, $rr['milestone_x'] - $pairsCount)) }} pairs remaining
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- 5. Matching Milestone Commission Progress -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white p-3 d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 font-weight-bold text-dark"><i class="fa fa-trophy text-warning mr-2"></i> Milestone Matching Commissions</h5>
                            <span class="text-muted small">Commissions trigger when an active leg reaches milestone <strong>x</strong> and counter leg has <strong>&ge; x</strong> verified referrals.</span>
                        </div>
                        <span class="badge badge-primary px-3 py-2 font-roboto f-13 mt-2 mt-md-0">
                            Current Balanced Pairs: <strong>{{ number_format($pairsCount) }}</strong>
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Milestone (x Pairs)</th>
                                        <th>Commission / Type</th>
                                        <th>Base Calculation</th>
                                        <th>Reward / Benefit</th>
                                        <th>Status / Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tiersProgress as $tp)
                                        <tr>
                                            <td class="font-weight-bold">
                                                <i class="fa fa-medal mr-1 {{ $tp['is_awarded'] || $tp['is_qualified'] ? 'text-warning' : 'text-muted' }}"></i>
                                                {{ number_format($tp['milestone_x']) }} Left &amp; {{ number_format($tp['milestone_x']) }} Right
                                                @if (! empty($tp['label']))
                                                    @php
                                                        $lbl = strtolower($tp['label']);
                                                        $bColor = str_contains($lbl, 'gold') ? 'badge-warning text-dark' : (str_contains($lbl, 'silver') ? 'badge-secondary' : 'badge-info');
                                                    @endphp
                                                    <span class="badge {{ $bColor }} ml-1 px-2 py-1"><i class="fa fa-crown mr-1"></i>{{ $tp['label'] }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($tp['has_percentage'])
                                                    <span class="badge badge-light border">{{ $tp['percentage'] }}%</span>
                                                @else
                                                    <span class="badge badge-warning text-dark"><i class="fa fa-gift mr-1"></i> Special Reward</span>
                                                @endif
                                            </td>
                                            <td class="text-muted small">
                                                @if ($tp['has_percentage'])
                                                    {{ number_format($tp['base_amount'], 2) }} BDT
                                                @else
                                                    <span class="text-muted font-italic">Manual Disbursement</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($tp['has_percentage'])
                                                    <span class="font-weight-bold text-success">{{ number_format($tp['commission'], 2) }} BDT</span>
                                                @else
                                                    <span class="font-weight-bold text-dark"><i class="fa fa-gift text-warning mr-1"></i> {{ $tp['benefit'] }}</span>
                                                @endif
                                            </td>
                                            <td style="min-width: 220px;">
                                                @if ($tp['is_awarded'])
                                                    <span class="badge badge-success px-2 py-1">
                                                        <i class="fa fa-check mr-1"></i> Paid ({{ number_format($tp['awarded_amount'], 2) }} BDT)
                                                    </span>
                                                    <small class="text-muted ml-1">{{ $tp['awarded_at']?->format('d M Y') }}</small>
                                                @elseif ($tp['is_manual'] && $tp['is_qualified'])
                                                    <span class="badge badge-success px-2 py-1">
                                                        <i class="fa fa-check-circle mr-1"></i> Qualified
                                                    </span>
                                                @else
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress progress-thin flex-grow-1 mr-2 bg-light">
                                                            <div class="progress-bar {{ $tp['is_qualified'] ? 'bg-success' : 'bg-primary' }}" role="progressbar" style="width: {{ $tp['progress_percent'] }}%"></div>
                                                        </div>
                                                        <small class="text-muted font-weight-bold">{{ number_format($pairsCount) }}/{{ number_format($tp['milestone_x']) }}</small>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Direct Referrals Breakdown Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white p-3 d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-1 font-weight-bold text-dark"><i class="fa fa-list-ul mr-2"></i> Direct Referred Resellers</h5>
                            <span class="text-muted small">Reseller accounts registered with this reseller's code</span>
                        </div>
                        <div class="mt-2 mt-md-0">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.resellers.affiliate', $reseller->id) }}" class="btn btn-outline-primary {{ !request('leg') && !request('status') ? 'active' : '' }}">All ({{ $directTotal }})</a>
                                <a href="{{ route('admin.resellers.affiliate', ['reseller' => $reseller->id, 'leg' => 'left']) }}" class="btn btn-outline-primary {{ request('leg') == 'left' ? 'active' : '' }}">Left Leg ({{ $affiliate->direct_left_count }})</a>
                                <a href="{{ route('admin.resellers.affiliate', ['reseller' => $reseller->id, 'leg' => 'right']) }}" class="btn btn-outline-primary {{ request('leg') == 'right' ? 'active' : '' }}">Right Leg ({{ $affiliate->direct_right_count }})</a>
                                <a href="{{ route('admin.resellers.affiliate', ['reseller' => $reseller->id, 'status' => 'pending']) }}" class="btn btn-outline-primary {{ request('status') == 'pending' ? 'active' : '' }}">Pending ({{ $directPending }})</a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        @if ($directReferrals->isEmpty())
                            <div class="p-5 text-center text-muted">
                                <i class="fa fa-users f-36 mb-2 text-muted"></i>
                                <h6>No direct referrals found for this filter.</h6>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Reseller Name</th>
                                            <th>Shop Name</th>
                                            <th>Contact</th>
                                            <th>Assigned Leg</th>
                                            <th>Status</th>
                                            <th>Registered At</th>
                                            <th>Admin Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($directReferrals as $index => $item)
                                            <tr>
                                                <td>{{ $directReferrals->firstItem() + $index }}</td>
                                                <td class="font-weight-bold text-dark">
                                                    <a href="{{ route('admin.resellers.affiliate', $item->user_id) }}" title="View Affiliate Network">
                                                        {{ $item->user?->name ?? 'User #'.$item->user_id }}
                                                    </a>
                                                </td>
                                                <td>{{ $item->user?->shop_name ?? '-' }}</td>
                                                <td class="small">{{ $item->user?->phone_number ?? '-' }}</td>
                                                <td>
                                                    @if ($item->direct_leg === 'left')
                                                        <span class="badge badge-primary px-2 py-1"><i class="fa fa-arrow-left mr-1"></i> LEFT</span>
                                                    @elseif ($item->direct_leg === 'right')
                                                        <span class="badge badge-success px-2 py-1">RIGHT <i class="fa fa-arrow-right ml-1"></i></span>
                                                    @else
                                                        <span class="badge badge-secondary px-2 py-1">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($item->verified_at)
                                                        <span class="badge badge-success px-2 py-1"><i class="fa fa-check mr-1"></i> Verified</span>
                                                    @else
                                                        <span class="badge badge-warning px-2 py-1"><i class="fa fa-clock mr-1"></i> Pending</span>
                                                    @endif
                                                </td>
                                                <td class="text-muted small">{{ $item->created_at?->format('d M Y, h:i A') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.resellers.affiliate', $item->user_id) }}" class="btn btn-xs btn-outline-info mr-1" title="View Downline's Affiliate Network">
                                                        <i class="fa fa-users"></i>
                                                    </a>
                                                    <a href="{{ route('admin.resellers.edit', $item->user_id) }}" class="btn btn-xs btn-outline-primary" title="Edit Profile">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if ($directReferrals->hasPages())
                                <div class="p-3 border-top d-flex justify-content-end">
                                    {{ $directReferrals->links() }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Commissions History -->
        @if ($commissions->isNotEmpty())
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white p-3">
                            <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-history mr-2"></i> Commission Payment History</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Milestone</th>
                                            <th>Rate</th>
                                            <th>Base Calculation</th>
                                            <th>Commission Received</th>
                                            <th>Triggered Leg</th>
                                            <th>Triggered By</th>
                                            <th>Date Paid</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($commissions as $comm)
                                            <tr>
                                                <td class="font-weight-bold">{{ $comm->milestone_x }} Pairs</td>
                                                <td><span class="badge badge-light border">{{ $comm->percentage }}%</span></td>
                                                <td class="text-muted small">{{ number_format($comm->base_amount, 2) }} BDT</td>
                                                <td class="font-weight-bold text-success">{{ number_format($comm->amount, 2) }} BDT</td>
                                                <td>
                                                    <span class="badge badge-{{ $comm->trigger_leg === 'left' ? 'primary' : 'success' }} px-2 py-1">
                                                        {{ strtoupper($comm->trigger_leg) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.resellers.affiliate', $comm->triggered_by_user_id) }}">
                                                        {{ $comm->triggeredBy?->name ?? 'User #'.$comm->triggered_by_user_id }}
                                                    </a>
                                                </td>
                                                <td class="text-muted small">{{ $comm->created_at?->format('d M Y, h:i A') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function copyToClipboard(text, label) {
            navigator.clipboard.writeText(text).then(function() {
                alert(label + ' copied to clipboard!');
            }).catch(function() {
                var temp = document.createElement('input');
                document.body.appendChild(temp);
                temp.value = text;
                temp.select();
                document.execCommand('copy');
                document.body.removeChild(temp);
                alert(label + ' copied to clipboard!');
            });
        }
    </script>
@endpush
