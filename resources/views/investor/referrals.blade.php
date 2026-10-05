@extends('layouts.investor.master')

@section('title', 'Referral Program')

@section('breadcrumb-title')
    <h3>Referral Program</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('investor.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Referral Program</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Referral Header Card -->
    <div class="card bg-primary text-white mb-3">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-lg-7 mb-3 mb-lg-0">
                    <h4 class="font-weight-bold mb-1"><i class="fa fa-users mr-2"></i> Direct Referral Program</h4>
                    <p class="mb-0 text-white-50 small">
                        Share your unique referral link with prospective investors.
                        @if($initialRate > 0)
                            Earn <strong>{{ $initialRate }}%</strong> immediate bonus on their investment amount plus <strong>{{ $monthlyRate }}%</strong> monthly recurring bonus on all 36 return installments!
                        @else
                            Earn <strong>{{ $monthlyRate }}%</strong> monthly recurring bonus on all 36 return installments from your direct referrals!
                        @endif
                    </p>
                </div>
                <div class="col-lg-5">
                    <div class="bg-white rounded p-2 text-dark">
                        <label class="small text-muted font-weight-bold mb-1">YOUR REFERRAL LINK</label>
                        <div class="input-group">
                            <input type="text" id="refLinkMain" class="form-control form-control-sm font-weight-bold" value="{{ $referralLink }}" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-dark btn-sm" onclick="copyMainRefLink()">
                                    <i class="fa fa-copy"></i> Copy
                                </button>
                            </div>
                        </div>
                        <small class="text-muted mt-1 d-block">Referral Code: <strong class="text-primary">{{ $referralCode }}</strong></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-3">
        <div class="col-md-6 mb-3 mb-md-0">
            <div class="card h-100 mb-0">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase font-weight-600 d-block">Total Referred Members</span>
                        <h4 class="font-weight-bold mb-0 text-primary">{{ $totalReferrals }}</h4>
                    </div>
                    <div class="p-2 bg-light-primary rounded-circle text-primary">
                        <i class="fa fa-user-friends fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100 mb-0">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase font-weight-600 d-block">Total Referral Bonuses Earned</span>
                        <h4 class="font-weight-bold mb-0 text-success">+{{ number_format($totalBonusEarned, 2) }} TK</h4>
                    </div>
                    <div class="p-2 bg-light-success rounded-circle text-success">
                        <i class="fa fa-hand-holding-usd fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Referred Investors Table -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="font-weight-bold mb-0">Referred Investors</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Investor Name</th>
                            <th>Email / Phone</th>
                            <th>Joined Date</th>
                            <th>Active Investments</th>
                            <th>Total Invested</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($referrals as $index => $ref)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-weight-bold">{{ $ref->name }}</td>
                                <td>
                                    {{ $ref->email }}
                                    @if($ref->phone_number)
                                        <br><small class="text-muted">{{ $ref->phone_number }}</small>
                                    @endif
                                </td>
                                <td>{{ $ref->created_at->format('d M Y') }}</td>
                                <td>{{ $ref->investments->where('status', 'active')->count() }}</td>
                                <td class="font-weight-bold text-primary">{{ number_format($ref->getTotalInvestedAmount(), 2) }} TK</td>
                                <td>
                                    <span class="badge badge-{{ $ref->is_active ? 'success' : 'danger' }}">
                                        {{ $ref->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    You haven't referred any investors yet. Share your referral link to start earning direct referral bonuses!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $referrals->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    function copyMainRefLink() {
        const copyText = document.getElementById("refLinkMain");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        alert("Referral link copied to clipboard: " + copyText.value);
    }
</script>
@endpush
