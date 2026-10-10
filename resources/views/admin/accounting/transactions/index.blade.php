@extends('layouts.light.master')

@section('title', 'Transactions & Journal Entries')

@section('breadcrumb-title')
    <h3>Accounting Transactions</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Accounting</li>
    <li class="breadcrumb-item active">Transactions</li>
@endsection

@section('breadcrumb-right')
    <a href="{{ route('admin.accounting.transactions.create') }}" class="btn btn-primary btn-sm">
        <i class="fa fa-plus mr-1"></i> Record Transaction
    </a>
@endsection

@section('content')
<div class="container-fluid mb-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="card shadow-sm border mb-4" style="background: #ffffff;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.accounting.transactions.index') }}" class="form-row align-items-end" id="transactionsFilterForm">
                <div class="form-group col-lg-2 col-md-3 mb-2">
                    <label class="small font-weight-bold text-dark"><i class="fa fa-calendar mr-1 text-primary"></i> Start Date</label>
                    <input type="date" name="start_d" class="form-control form-control-sm text-dark font-weight-bold" value="{{ $startDate }}">
                </div>
                <div class="form-group col-lg-2 col-md-3 mb-2">
                    <label class="small font-weight-bold text-dark"><i class="fa fa-calendar mr-1 text-primary"></i> End Date</label>
                    <input type="date" name="end_d" class="form-control form-control-sm text-dark font-weight-bold" value="{{ $endDate }}">
                </div>
                <div class="form-group col-lg-5 col-md-6 mb-2">
                    <label class="small font-weight-bold text-dark"><i class="fa fa-filter mr-1 text-primary"></i> Filter by Accounts (Multi-Select)</label>
                    <select name="account_ids[]" id="account_ids_select" class="form-control form-control-sm text-dark font-weight-bold select2" multiple="multiple" data-placeholder="-- All Accounts (Select to filter) --">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ in_array($acc->id, $selectedAccountIds ?? []) ? 'selected' : '' }}>
                                [{{ strtoupper($acc->type) }}] {{ $acc->name }} ({{ $acc->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-12 mb-2 d-flex">
                    <button type="submit" class="btn btn-primary btn-sm mr-2 flex-grow-1 font-weight-bold"><i class="fa fa-filter mr-1"></i> Filter</button>
                    <a href="{{ route('admin.accounting.transactions.index') }}" class="btn btn-light btn-sm border text-dark font-weight-bold" title="Reset all filters">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="card shadow-sm border" style="background: #ffffff;">
        <div class="card-header p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-list text-primary mr-1"></i> Journal Entries</h5>
            <span class="text-muted small font-weight-bold">Showing {{ $entries->total() }} entries</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-dark font-weight-bold" style="width: 110px;">Date</th>
                            <th class="text-dark font-weight-bold" style="width: 140px;">Entry #</th>
                            <th class="text-dark font-weight-bold">Description / Reference</th>
                            <th class="text-dark font-weight-bold">Debit Details (Dr)</th>
                            <th class="text-dark font-weight-bold">Credit Details (Cr)</th>
                            <th class="text-right text-dark font-weight-bold" style="width: 140px;">Total Amount</th>
                            <th class="text-center text-dark font-weight-bold" style="width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                            <tr>
                                <td class="font-roboto font-weight-bold text-dark">{{ $entry->entry_date->format('M d, Y') }}</td>
                                <td class="font-roboto small text-primary font-weight-bold">{{ $entry->entry_number }}</td>
                                <td>
                                    <div class="text-dark font-weight-500">{{ $entry->description ?? '—' }}</div>
                                    <div class="d-flex align-items-center flex-wrap mt-1" style="gap: 4px;">
                                        @if($entry->reference)
                                            <span class="badge badge-light border text-muted">Ref: {{ $entry->reference }}</span>
                                        @endif
                                        @if($entry->source_type === \App\Models\Purchase::class && $entry->source_id)
                                            <a href="{{ route('admin.purchases.edit', $entry->source_id) }}" class="badge badge-light-info text-info border">
                                                <i class="fa fa-shopping-cart mr-1"></i> Purchase #{{ $entry->source_id }}
                                            </a>
                                        @elseif($entry->source_type === \App\Models\Supplier::class || $entry->source_type === \App\Models\PurchasePayment::class)
                                            <a href="{{ route('admin.suppliers.index') }}" class="badge badge-light-warning text-warning border">
                                                <i class="fa fa-user mr-1"></i> Supplier Payment
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @foreach($entry->items->where('debit', '>', 0) as $item)
                                        @php
                                            $isDebitNature = in_array($item->account?->type, ['asset', 'expense'], true);
                                            $drSign = $isDebitNature ? '(+)' : '(-)';
                                        @endphp
                                        <div class="d-flex justify-content-between align-items-center small mb-1">
                                            <span>
                                                <strong class="text-dark">{{ $item->account->name ?? 'Unknown' }}</strong>
                                                @if($item->account?->type)
                                                    <span class="badge badge-light border text-muted ml-1" style="font-size: 10px;">{{ strtoupper($item->account->type) }} {{ $drSign }}</span>
                                                @endif
                                                @if($item->category)
                                                    <span class="badge badge-light-primary text-primary border ml-1">#{{ $item->category->name }}</span>
                                                @endif
                                            </span>
                                            <span class="font-roboto text-danger font-weight-bold ml-2">
                                                {!! theMoney($item->debit) !!}
                                            </span>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($entry->items->where('credit', '>', 0) as $item)
                                        @php
                                            $isDebitNature = in_array($item->account?->type, ['asset', 'expense'], true);
                                            $crSign = $isDebitNature ? '(-)' : '(+)';
                                        @endphp
                                        <div class="d-flex justify-content-between align-items-center small mb-1">
                                            <span>
                                                <strong class="text-dark">{{ $item->account->name ?? 'Unknown' }}</strong>
                                                @if($item->account?->type)
                                                    <span class="badge badge-light border text-muted ml-1" style="font-size: 10px;">{{ strtoupper($item->account->type) }} {{ $crSign }}</span>
                                                @endif
                                                @if($item->category)
                                                    <span class="badge badge-light-secondary text-secondary border ml-1">#{{ $item->category->name }}</span>
                                                @endif
                                            </span>
                                            <span class="font-roboto text-success font-weight-bold ml-2">
                                               {!! theMoney($item->credit) !!}
                                            </span>
                                        </div>
                                    @endforeach
                                </td>
                                <td class="text-right font-roboto font-weight-bold text-dark" style="font-size: 1.05rem;">
                                    {!! theMoney($entry->totalDebit()) !!}
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center" style="gap: 4px;">
                                        @if($entry->source_type === \App\Models\Purchase::class && $entry->source_id)
                                            <a href="{{ route('admin.purchases.edit', $entry->source_id) }}" class="btn btn-outline-info btn-xs" title="Edit Source Purchase">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                        @elseif($entry->source_type === \App\Models\Supplier::class || $entry->source_type === \App\Models\PurchasePayment::class)
                                            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-warning btn-xs" title="View Supplier Payments">
                                                <i class="fa fa-external-link"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.accounting.transactions.edit', $entry) }}" class="btn btn-outline-primary btn-xs" title="Edit Transaction">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                        @endif
                                        <form action="{{ route('admin.accounting.transactions.destroy', $entry) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this journal entry? Account balances will be recalculated.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-xs" title="Delete Entry">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No transactions found for the selected filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($entries->hasPages())
            <div class="card-footer p-2 bg-light border-top">
                {{ $entries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/select2.css') }}">
<style>
    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
        min-height: 33px !important;
        padding: 2px 4px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25) !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #eff6ff !important;
        border: 1px solid #bfdbfe !important;
        color: #1e40af !important;
        font-weight: 600 !important;
        font-size: 12px !important;
        padding: 2px 8px !important;
        border-radius: 3px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #ef4444 !important;
        margin-right: 5px !important;
        font-weight: bold !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #b91c1c !important;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('assets/js/select2/select2.full.min.js') }}"></script>
<script>
    $(document).ready(function() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('#account_ids_select').select2({
                placeholder: '-- All Accounts (Select to filter) --',
                allowClear: true,
                width: '100%'
            });
        }
    });
</script>
@endpush
