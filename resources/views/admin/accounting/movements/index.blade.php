@extends('layouts.light.master')

@section('title', $title)

@section('breadcrumb-title')
    <h3>{{ $title }}</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Accounting</li>
    <li class="breadcrumb-item active">{{ $title }}</li>
@endsection

@section('content')
<div class="container-fluid mb-5">
    <div class="card shadow-sm border">
        <div class="card-header p-3 bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bold">{{ $title }}</h5>
            <a href="{{ route('admin.accounting.transactions.index') }}" class="btn btn-outline-secondary btn-sm">Transactions</a>
        </div>
        <div class="card-body border-bottom">
            <form method="GET" class="form-row align-items-end">
                <div class="form-group col-md-3 mb-2">
                    <label for="start_d">From</label>
                    <input id="start_d" type="date" name="start_d" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="form-group col-md-3 mb-2">
                    <label for="end_d">To</label>
                    <input id="end_d" type="date" name="end_d" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="form-group col-md-3 mb-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ $sourceType === 'Shipment' ? route('admin.accounting.shipments.index') : route('admin.accounting.returns.index') }}" class="btn btn-light border">Reset</a>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Date</th>
                        <th>Entry</th>
                        <th>Courier / Description</th>
                        <th>Products</th>
                        <th class="text-right">Quantity</th>
                        <th class="text-right">Purchase Cost</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $entry)
                        @php
                            $details = $entry->details ?? [];
                            $inventoryItem = $entry->items->first(fn ($item) => $item->account?->code === '1004');
                            $purchaseCost = $inventoryItem
                                ? (float) ($inventoryItem->debit ?: $inventoryItem->credit)
                                : collect($details)->sum(fn ($detail) => (float) ($detail['line_purchase_total'] ?? ((float) ($detail['purchase_price'] ?? 0) * (int) ($detail['quantity'] ?? 0))));
                        @endphp
                        <tr>
                            <td>{{ $entry->entry_date->format('d M Y') }}</td>
                            <td>{{ $entry->entry_number }}</td>
                            <td>{{ $entry->description ?: '—' }}</td>
                            <td>{{ count($details) ?: 'Details unavailable for this older entry' }}</td>
                            <td class="text-right">{{ collect($details)->sum(fn ($detail) => (int) ($detail['quantity'] ?? 0)) ?: '—' }}</td>
                            <td class="text-right">{!! theMoney($purchaseCost) !!}</td>
                            <td><a href="{{ route('admin.accounting.movements.show', $entry) }}" class="btn btn-outline-primary btn-xs">View details</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No records found for this date range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($entries->hasPages())
            <div class="card-footer">{{ $entries->links() }}</div>
        @endif
    </div>
</div>
@endsection
