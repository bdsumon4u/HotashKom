@extends('layouts.reseller.master')

@section('title', 'Affiliate Network')

@section('breadcrumb-title')
    <h3>Affiliate Network</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('reseller.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Affiliate Network</li>
@endsection

@section('breadcrumb-right')
    <div class="theme-form m-t-10">
        <div class="text-right">
            <span class="badge badge-primary px-3 py-2 font-roboto f-14">
                <i class="fa fa-qrcode mr-1"></i> Code: <strong>{{ $affiliate->referral_code }}</strong>
            </span>
        </div>
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
    </style>
@endpush

@section('content')
    <div class="container-fluid mb-5">
        <x-reseller-verification-alert />

        <!-- 1. Share Referral Link Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #f8faff 0%, #edf3ff 100%); border-left: 5px solid #0062cc !important;">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                            <div>
                                <h5 class="mb-1 text-primary font-weight-bold">
                                    <i class="fa fa-share-alt mr-2"></i> Your Exclusive Referral Link & Code
                                </h5>
                                <p class="mb-0 text-muted small">
                                    Share this link with potential resellers. Successful referrals are placed alternatingly into your <strong>Left</strong> and <strong>Right</strong> legs upon account verification.
                                </p>
                            </div>
                            <span class="badge badge-success px-3 py-2 mt-2 mt-md-0 font-roboto f-12">
                                <i class="fa fa-check-circle mr-1"></i> Active Affiliate
                            </span>
                        </div>

                        <div class="row">
                            <div class="col-lg-4 col-md-5 mb-3 mb-lg-0">
                                <label class="small text-muted font-weight-bold mb-1">Your Referral Code</label>
                                <div class="input-group">
                                    <input type="text" class="form-control font-weight-bold bg-white text-primary text-center" readonly value="{{ $affiliate->referral_code }}" id="refCodeInput">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" onclick="copyToClipboard('{{ $affiliate->referral_code }}', 'Referral Code')">
                                            <i class="fa fa-copy mr-1"></i> Copy Code
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-7">
                                <label class="small text-muted font-weight-bold mb-1">Your Referral Registration URL</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-white" readonly value="{{ $referralLink }}" id="refLinkInput">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" onclick="copyToClipboard('{{ $referralLink }}', 'Referral Link')">
                                            <i class="fa fa-link mr-1"></i> Copy Link
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
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card affiliate-stat-box border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small font-weight-bold text-uppercase">Total Direct Referred</span>
                                <h3 class="mb-0 mt-1 font-weight-bold">{{ $directTotal }}</h3>
                                <span class="text-muted small">All registrations via your code</span>
                            </div>
                            <div class="p-3 bg-light-primary text-primary rounded-circle">
                                <i class="fa fa-users f-20"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card affiliate-stat-box border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small font-weight-bold text-uppercase">Successful Referrals</span>
                                <h3 class="mb-0 mt-1 text-success font-weight-bold">{{ $directVerified }}</h3>
                                <span class="text-muted small">Verified accounts</span>
                            </div>
                            <div class="p-3 bg-light-success text-success rounded-circle">
                                <i class="fa fa-user-check f-20"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card affiliate-stat-box border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small font-weight-bold text-uppercase">Pending Referrals</span>
                                <h3 class="mb-0 mt-1 text-warning font-weight-bold">{{ $directPending }}</h3>
                                <span class="text-muted small">Waiting for verification</span>
                            </div>
                            <div class="p-3 bg-light-warning text-warning rounded-circle">
                                <i class="fa fa-user-clock f-20"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card affiliate-stat-box border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small font-weight-bold text-uppercase">Affiliate Commissions</span>
                                <h3 class="mb-0 mt-1 text-info font-weight-bold">{{ number_format((float) $totalCommissionEarned, 2) }} <small class="f-12">BDT</small></h3>
                                <span class="text-muted small">Deposited to wallet</span>
                            </div>
                            <div class="p-3 bg-light-info text-info rounded-circle">
                                <i class="fa fa-coins f-20"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Dual-Leg Balance & Network Totals -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-3 mb-lg-0">
                <div class="card leg-card shadow-sm h-100">
                    <div class="card-header leg-header-left p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 font-weight-bold text-white"><i class="fa fa-arrow-left mr-2"></i> Left Dataset / Sub-Network</h5>
                            <span class="badge badge-light text-primary font-roboto px-2 py-1">Leg: LEFT</span>
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
                <div class="card leg-card shadow-sm h-100">
                    <div class="card-header leg-header-right p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 font-weight-bold text-white">Right Dataset / Sub-Network <i class="fa fa-arrow-right ml-2"></i></h5>
                            <span class="badge badge-light text-success font-roboto px-2 py-1">Leg: RIGHT</span>
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

        <!-- 4. Matching Milestone Commission Progress -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white p-3 d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 font-weight-bold text-dark"><i class="fa fa-trophy text-warning mr-2"></i> Milestone Matching Commissions</h5>
                            <span class="text-muted small">Commissions trigger when an active leg reaches milestone <strong>x</strong> and the counter leg has <strong>&ge; x</strong> verified referrals.</span>
                        </div>
                        <span class="badge badge-primary px-3 py-2 font-roboto f-13 mt-2 mt-md-0">
                            Current Balanced Pairs: <strong>{{ $pairsCount }}</strong>
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Milestone (x Pairs)</th>
                                        <th>Commission Rate</th>
                                        <th>Base Calculation</th>
                                        <th>Reward Amount</th>
                                        <th>Progress / Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tiersProgress as $tp)
                                        <tr>
                                            <td class="font-weight-bold">
                                                <i class="fa fa-medal mr-1 {{ $tp['is_awarded'] ? 'text-warning' : 'text-muted' }}"></i>
                                                {{ $tp['milestone_x'] }} Left &amp; {{ $tp['milestone_x'] }} Right
                                            </td>
                                            <td><span class="badge badge-light border">{{ $tp['percentage'] }}%</span></td>
                                            <td class="text-muted small">{{ number_format($tp['base_amount'], 2) }} BDT</td>
                                            <td class="font-weight-bold text-success">{{ number_format($tp['commission'], 2) }} BDT</td>
                                            <td style="min-width: 220px;">
                                                @if ($tp['is_awarded'])
                                                    <span class="badge badge-success px-2 py-1">
                                                        <i class="fa fa-check mr-1"></i> Paid ({{ number_format($tp['awarded_amount'], 2) }} BDT)
                                                    </span>
                                                    <small class="text-muted ml-1">{{ $tp['awarded_at']?->format('d M Y') }}</small>
                                                @else
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress progress-thin flex-grow-1 mr-2 bg-light">
                                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $tp['progress_percent'] }}%"></div>
                                                        </div>
                                                        <small class="text-muted font-weight-bold">{{ $pairsCount }}/{{ $tp['milestone_x'] }}</small>
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
                            <span class="text-muted small">List of all reseller accounts registered using your referral link</span>
                        </div>
                        <div class="mt-2 mt-md-0">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('reseller.affiliate') }}" class="btn btn-outline-primary {{ !request('leg') && !request('status') ? 'active' : '' }}">All ({{ $directTotal }})</a>
                                <a href="{{ route('reseller.affiliate', ['leg' => 'left']) }}" class="btn btn-outline-primary {{ request('leg') == 'left' ? 'active' : '' }}">Left Leg ({{ $affiliate->direct_left_count }})</a>
                                <a href="{{ route('reseller.affiliate', ['leg' => 'right']) }}" class="btn btn-outline-primary {{ request('leg') == 'right' ? 'active' : '' }}">Right Leg ({{ $affiliate->direct_right_count }})</a>
                                <a href="{{ route('reseller.affiliate', ['status' => 'pending']) }}" class="btn btn-outline-primary {{ request('status') == 'pending' ? 'active' : '' }}">Pending ({{ $directPending }})</a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        @if ($directReferrals->isEmpty())
                            <div class="p-5 text-center text-muted">
                                <i class="fa fa-users f-36 mb-2 text-muted"></i>
                                <h6>No referrals found in this view.</h6>
                                <p class="small">Share your referral link with others to start growing your reseller network!</p>
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($directReferrals as $index => $item)
                                            <tr>
                                                <td>{{ $directReferrals->firstItem() + $index }}</td>
                                                <td class="font-weight-bold text-dark">{{ $item->user?->name ?? 'User #'.$item->user_id }}</td>
                                                <td>{{ $item->user?->shop_name ?? '-' }}</td>
                                                <td class="small">{{ $item->user?->phone_number ?? '-' }}</td>
                                                <td>
                                                    @if ($item->direct_leg === 'left')
                                                        <span class="badge badge-primary px-2 py-1"><i class="fa fa-arrow-left mr-1"></i> LEFT</span>
                                                    @elseif ($item->direct_leg === 'right')
                                                        <span class="badge badge-success px-2 py-1">RIGHT <i class="fa fa-arrow-right ml-1"></i></span>
                                                    @else
                                                        <span class="badge badge-secondary px-2 py-1">Pending Assignment</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($item->verified_at)
                                                        <span class="badge badge-success px-2 py-1"><i class="fa fa-check mr-1"></i> Verified</span>
                                                    @else
                                                        <span class="badge badge-warning px-2 py-1"><i class="fa fa-clock mr-1"></i> Pending Verification</span>
                                                    @endif
                                                </td>
                                                <td class="text-muted small">{{ $item->created_at?->format('d M Y, h:i A') }}</td>
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

        <!-- 6. Commissions History (if any) -->
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
                                                <td>{{ $comm->triggeredBy?->name ?? 'Downline Reseller #'.$comm->triggered_by_user_id }}</td>
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
