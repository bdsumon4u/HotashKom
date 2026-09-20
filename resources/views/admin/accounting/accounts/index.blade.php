@extends('layouts.light.master')

@section('title', 'Chart of Accounts')

@section('breadcrumb-title')
    <h3>Chart of Accounts</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Accounting</li>
    <li class="breadcrumb-item active">Accounts</li>
@endsection

@section('breadcrumb-right')
    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
        <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#accountingGuideModal">
            <i class="fa fa-graduation-cap mr-1"></i> Accounting Guide & Tutorial
        </button>
        <a href="{{ route('admin.accounting.accounts.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus mr-1"></i> New Account
        </a>
    </div>
@endsection

@section('content')
<div class="container-fluid mb-5">
    <!-- Accounting Guide Callout Banner -->
    <div class="card border mb-4 shadow-sm" style="background: linear-gradient(to right, #f8fafc, #ffffff); border-left: 4px solid #3b82f6 !important;">
        <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
            <div class="d-flex align-items-center">
                <div class="mr-3 p-2 bg-light-primary text-primary rounded-circle">
                    <i class="fa fa-book fa-lg"></i>
                </div>
                <div>
                    <strong class="text-dark d-block" style="font-size: 14px;">How Accounts Work in HotashKom</strong>
                    <span class="text-muted small">Learn the difference between <strong>Inventory Assets</strong> and <strong>Product Purchase Costs</strong>, and how double-entry records your transactions.</span>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-primary font-weight-bold" data-toggle="modal" data-target="#accountingGuideModal">
                <i class="fa fa-graduation-cap mr-1"></i> Open Accounting Guide & Tutorial
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session('danger'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa fa-exclamation-triangle mr-1"></i> {{ session('danger') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        @foreach(['asset' => 'Assets', 'liability' => 'Liabilities', 'equity' => 'Equity', 'income' => 'Income / Revenue', 'expense' => 'Expenses'] as $typeKey => $typeLabel)
            @php
                $typeAccounts = $groupedAccounts[$typeKey] ?? collect();
                $totalBalance = $typeAccounts->sum('current_balance');
                $badgeColor = match($typeKey) {
                    'asset' => 'primary',
                    'liability' => 'danger',
                    'equity' => 'info',
                    'income' => 'success',
                    'expense' => 'warning',
                    default => 'secondary'
                };
            @endphp
            <div class="col-12 mb-4">
                <div class="card shadow-sm border" style="background: #ffffff;">
                    <div class="card-header p-3 d-flex justify-content-between align-items-center bg-light border-bottom">
                        <div class="d-flex align-items-center">
                            <span class="badge badge-{{ $badgeColor }} mr-2 px-2 py-1 font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">{{ strtoupper($typeKey) }}</span>
                            <h5 class="mb-0 font-weight-bold text-dark">{{ $typeLabel }}</h5>
                        </div>
                        <div class="text-dark font-weight-bold">
                            Total: <span class="h6 font-roboto text-primary font-weight-bold">{!! theMoney($totalBalance) !!}</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-dark font-weight-bold" style="width: 90px;">Code</th>
                                        <th class="text-dark font-weight-bold">Account Name</th>
                                        <th class="text-dark font-weight-bold">Description</th>
                                        <th class="text-right text-dark font-weight-bold" style="width: 160px;">Opening Balance</th>
                                        <th class="text-right text-dark font-weight-bold" style="width: 180px;">Current Balance</th>
                                        <th class="text-center text-dark font-weight-bold" style="width: 100px;">Status</th>
                                        <th class="text-center text-dark font-weight-bold" style="width: 110px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($typeAccounts as $account)
                                        <tr>
                                            <td class="font-roboto font-weight-bold text-muted">{{ $account->code ?? '—' }}</td>
                                            <td class="font-weight-bold text-dark">
                                                {{ $account->name }}
                                                @if($account->is_system)
                                                    <span class="badge badge-light border text-muted small ml-1">System</span>
                                                @endif
                                            </td>
                                            <td class="text-muted small">{{ $account->description ?? '—' }}</td>
                                            <td class="text-right font-roboto text-dark">{!! theMoney($account->opening_balance) !!}</td>
                                            <td class="text-right font-roboto font-weight-bold {{ $account->current_balance < 0 ? 'text-danger' : 'text-primary' }}" style="font-size: 1.05rem;">
                                                {!! theMoney($account->current_balance) !!}
                                            </td>
                                            <td class="text-center">
                                                @if($account->is_active)
                                                    <span class="badge badge-success px-2 py-1">Active</span>
                                                @else
                                                    <span class="badge badge-secondary px-2 py-1">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.accounting.accounts.edit', $account) }}" class="btn btn-outline-primary btn-xs mr-1" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.accounting.accounts.destroy', $account) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this account?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-xs" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-3">No {{ strtolower($typeLabel) }} accounts created yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@include('admin.accounting.partials.tutorial-modal')
@endsection
