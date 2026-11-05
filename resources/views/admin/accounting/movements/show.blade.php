@extends('layouts.light.master')

@php
    $isShipment = $journalEntry->source_type === 'Shipment';
    $details = $details ?? [];
    $hasStructuredDetails = $journalEntry->details !== null;
    $quantityTotal = collect($details)->sum(fn ($detail) => (int) ($detail['quantity'] ?? 0));
    $purchaseCostTotal = collect($details)->sum(fn ($detail) => (float) ($detail['line_purchase_total'] ?? ((float) ($detail['purchase_price'] ?? 0) * (int) ($detail['quantity'] ?? 0))));
    $inventoryMovement = $journalEntry->items->first(fn ($item) => $item->account?->code === '1004');
@endphp

@section('title', $isShipment ? 'Shipment Details' : 'Courier Return Details')

@section('breadcrumb-title')
    <h3>{{ $isShipment ? 'Shipment Details' : 'Courier Return Details' }}</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Accounting</li>
    <li class="breadcrumb-item active">{{ $isShipment ? 'Shipment Details' : 'Courier Return Details' }}</li>
@endsection

@section('content')
<div class="container-fluid mb-5">
    <div class="card shadow-sm border">
        <div class="card-header p-3 bg-light d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1 font-weight-bold">{{ $isShipment ? 'Shipment' : 'Courier Return' }} · {{ $journalEntry->entry_number }}</h5>
                <div class="text-muted">{{ $journalEntry->entry_date->format('d M Y') }} · {{ $journalEntry->description }}</div>
                @if($journalEntry->reference)<div class="small text-muted">Tracking / Reference: {{ $journalEntry->reference }}</div>@endif
                @if($inventoryMovement)<div class="small font-weight-bold text-primary">Inventory value moved: {!! theMoney((float) ($inventoryMovement->debit ?: $inventoryMovement->credit)) !!}</div>@endif
            </div>
            <a href="{{ $isShipment ? route('admin.accounting.shipments.index') : route('admin.accounting.returns.index') }}" class="btn btn-outline-secondary btn-sm">Back to history</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th class="text-right">Quantity</th>
                        <th class="text-right">Purchase Price</th>
                        @if($isShipment)<th class="text-right">Sell Price</th>@endif
                        <th class="text-right">Purchase Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($details as $detail)
                        @php
                            $quantity = (int) ($detail['quantity'] ?? 0);
                            $purchasePrice = (float) ($detail['purchase_price'] ?? 0);
                        @endphp
                        <tr>
                            <td>{{ $detail['product_name'] ?? 'Product #'.($detail['product_id'] ?? '') }}</td>
                            <td>{{ $detail['sku'] ?? '—' }}</td>
                            <td class="text-right">{{ $quantity }}</td>
                            <td class="text-right">
                                @if(array_key_exists('purchase_price', $detail)){!! theMoney($purchasePrice) !!}@else Not recorded @endif
                            </td>
                            @if($isShipment)
                                <td class="text-right">
                                    @if(array_key_exists('selling_price', $detail)){!! theMoney((float) $detail['selling_price']) !!}@else Not recorded @endif
                                </td>
                            @endif
                            <td class="text-right">
                                @if(array_key_exists('line_purchase_total', $detail)){!! theMoney((float) $detail['line_purchase_total']) !!}@else — @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $isShipment ? 6 : 5 }}" class="text-center text-muted py-4">This older record does not have structured product details.</td></tr>
                    @endforelse
                </tbody>
                @if($details && $hasStructuredDetails)
                    <tfoot class="bg-light font-weight-bold">
                        <tr>
                            <td colspan="2">Total</td>
                            <td class="text-right">{{ $quantityTotal }}</td>
                            <td @if($isShipment) colspan="2" @endif></td>
                            <td class="text-right">{!! theMoney($purchaseCostTotal) !!}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
        @if($journalEntry->details === null && $details)
            <div class="card-body text-muted small">Product names and quantities were recovered from the older journal notes. Individual purchase and selling prices were not stored on that record.</div>
        @elseif($journalEntry->details === null)
            <div class="card-body text-muted small">This entry predates structured history. Its saved journal notes remain visible in the transaction and ledger records.</div>
        @endif
    </div>
</div>
@endsection
