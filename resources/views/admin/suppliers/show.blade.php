@extends('layouts.light.master')

@section('title', 'Supplier Ledger: ' . $supplier->name)

@section('breadcrumb-title')
    <h3>Supplier Ledger</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Purchases</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.suppliers.index') }}">Suppliers</a></li>
    <li class="breadcrumb-item active">{{ $supplier->name }}</li>
@endsection

@section('breadcrumb-right')
    <div class="d-flex align-items-center" style="gap: 8px;">
        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#paySupplierModal">
            <i class="fa fa-money mr-1"></i> Disburse Payment
        </button>
        <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-shopping-cart mr-1"></i> New Purchase
        </a>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fa fa-print mr-1"></i> Print Statement
        </button>
    </div>
@endsection

@section('content')
<div class="container-fluid mb-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <!-- Supplier Overview Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-left-primary">
                <div class="card-body p-3">
                    <span class="text-muted small">Supplier Details</span>
                    <h5 class="font-weight-bold mb-1">{{ $supplier->name }}</h5>
                    @if($supplier->company_name)
                        <div class="text-muted small"><i class="fa fa-building mr-1"></i>{{ $supplier->company_name }}</div>
                    @endif
                    @if($supplier->phone)
                        <div class="small"><i class="fa fa-phone mr-1"></i>{{ $supplier->phone }}</div>
                    @endif
                    @if($supplier->email)
                        <div class="small"><i class="fa fa-envelope mr-1"></i>{{ $supplier->email }}</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-left-info">
                <div class="card-body p-3">
                    <span class="text-muted small">Total Purchased</span>
                    <h4 class="font-roboto font-weight-bold mb-0">{!! theMoney($supplier->purchases->sum('total_amount')) !!}</h4>
                    <small class="text-muted">Total {{ count($supplier->purchases) }} purchase orders</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-left-danger">
                <div class="card-body p-3">
                    <span class="text-muted small">Current Outstanding Due</span>
                    <h4 class="font-roboto font-weight-bold mb-0 text-danger">{!! theMoney($supplier->current_due) !!}</h4>
                    <small class="text-muted">Opening: {!! theMoney($supplier->opening_balance) !!}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchases History Table -->
    <div class="card shadow-sm border mb-4" style="background: #ffffff;">
        <div class="card-header p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-shopping-bag text-primary mr-2"></i> Purchases from this Supplier</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-dark font-weight-bold" style="width: 110px;">Date</th>
                            <th class="text-dark font-weight-bold" style="width: 140px;">Invoice #</th>
                            <th class="text-dark font-weight-bold">Products Purchased</th>
                            <th class="text-right text-dark font-weight-bold" style="width: 130px;">Total Bill</th>
                            <th class="text-right text-dark font-weight-bold" style="width: 130px;">Paid</th>
                            <th class="text-right text-dark font-weight-bold" style="width: 130px;">Due</th>
                            <th class="text-center text-dark font-weight-bold" style="width: 100px;">Status</th>
                            <th class="text-center text-dark font-weight-bold" style="width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplier->purchases as $purchase)
                            <tr>
                                <td class="font-roboto font-weight-bold">{{ $purchase->purchase_date->format('M d, Y') }}</td>
                                <td class="font-roboto small text-primary">{{ $purchase->invoice_number ?: ('PO-#'.$purchase->id) }}</td>
                                <td>
                                    @foreach($purchase->products as $p)
                                        <div class="small">
                                            • <strong>{{ $p->name }}</strong> (Qty: {{ $p->pivot->quantity }}, Price: {!! theMoney($p->pivot->price) !!})
                                        </div>
                                    @endforeach
                                </td>
                                <td class="text-right font-roboto font-weight-bold">{!! theMoney($purchase->total_amount) !!}</td>
                                <td class="text-right font-roboto text-success">{!! theMoney($purchase->paid_amount) !!}</td>
                                <td class="text-right font-roboto font-weight-bold {{ $purchase->due_amount > 0 ? 'text-danger' : 'text-dark' }}">
                                    {!! theMoney($purchase->due_amount) !!}
                                </td>
                                <td class="text-center">
                                    @if($purchase->payment_status === 'paid')
                                        <span class="badge badge-success">Paid</span>
                                    @elseif($purchase->payment_status === 'partial')
                                        <span class="badge badge-warning">Partial</span>
                                    @else
                                        <span class="badge badge-danger">Unpaid</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.purchases.show', $purchase) }}" class="btn btn-outline-info btn-xs" title="View Purchase">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No purchases recorded for this supplier yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payments Made Table -->
    <div class="card shadow-sm border mb-4" style="background: #ffffff;">
        <div class="card-header p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-credit-card text-success mr-2"></i> Payment Records & Disbursements</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-dark font-weight-bold" style="width: 110px;">Payment Date</th>
                            <th class="text-dark font-weight-bold">Paid From Account</th>
                            <th class="text-dark font-weight-bold">Reference / Cheque</th>
                            <th class="text-dark font-weight-bold">Notes</th>
                            <th class="text-right text-dark font-weight-bold" style="width: 150px;">Amount Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplier->purchasePayments as $payment)
                            <tr>
                                <td class="font-roboto font-weight-bold">{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge badge-light-primary text-primary font-weight-bold">
                                        {{ $payment->account->name ?? 'Cash/Bank' }}
                                    </span>
                                </td>
                                <td>{{ $payment->reference ?? '—' }}</td>
                                <td>{{ $payment->notes ?? '—' }}</td>
                                <td class="text-right font-roboto font-weight-bold text-success">
                                    {!! theMoney($payment->amount) !!}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No payments recorded for this supplier yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Disburse Payment -->
<div class="modal fade" id="paySupplierModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.suppliers.payment', $supplier) }}" method="POST">
                @csrf
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title font-weight-bold">Disburse Payment to {{ $supplier->name }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 mb-3">
                        Current Outstanding Due: <strong>{!! theMoney($supplier->current_due) !!}</strong>
                    </div>

                    <div class="form-group">
                        <label for="amount_pay">Payment Amount (BDT) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" id="amount_pay" class="form-control h5 font-weight-bold" max="{{ $supplier->current_due > 0 ? $supplier->current_due : 1000000 }}" value="{{ $supplier->current_due > 0 ? $supplier->current_due : '' }}" required>
                    </div>

                    <div class="form-group">
                        <label for="account_id_pay">Paid From Account <span class="text-danger">*</span></label>
                        <select name="account_id" id="account_id_pay" class="form-control" required>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }} (Balance: {{ number_format($acc->current_balance, 2) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="payment_date_pay">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" id="payment_date_pay" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>

                    <div class="form-group">
                        <label for="purchase_id_pay">Specific Purchase (Optional)</label>
                        <select name="purchase_id" id="purchase_id_pay" class="form-control">
                            <option value="">-- General Supplier Due Payment --</option>
                            @foreach($supplier->purchases->where('due_amount', '>', 0) as $p)
                                <option value="{{ $p->id }}">{{ $p->invoice_number ?: ('Purchase #'.$p->id) }} - Due: {{ number_format($p->due_amount, 2) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="reference_pay">Reference / Cheque / Trx ID</label>
                        <input type="text" name="reference" id="reference_pay" class="form-control" placeholder="Optional reference">
                    </div>

                    <div class="form-group">
                        <label for="notes_pay">Notes</label>
                        <input type="text" name="notes" id="notes_pay" class="form-control" placeholder="Optional notes">
                    </div>
                </div>
                <div class="modal-footer p-3 bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4"><i class="fa fa-check mr-1"></i> Confirm Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .border-left-primary { border-left: 4px solid #6366f1 !important; }
    .border-left-info    { border-left: 4px solid #06b6d4 !important; }
    .border-left-danger  { border-left: 4px solid #ef4444 !important; }
    .badge-light-primary { background-color: rgba(99, 102, 241, 0.12); }

    @media print {
        .main-nav, .page-main-header, .footer, .breadcrumb, .breadcrumb-right, .modal, .btn {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
            margin-bottom: 15px !important;
            page-break-inside: avoid !important;
        }
    }
</style>
@endpush
