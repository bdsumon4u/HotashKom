@extends('layouts.light.master')

@section('title', 'Purchase Details')

@section('breadcrumb-title')
<h3>Purchases</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Purchase Details</li>
@endsection

@section('content')
<div class="mb-5 container-fluid">
    <div class="row">
        <div class="mb-4 col-lg-6">
            <div class="rounded-sm shadow-sm h-100 card">
                <div class="p-3 text-white card-header bg-primary">
                    <h5 class="mb-0">Purchase Summary <span class="ml-2 badge badge-light">#{{ $purchase->id }}</span></h5>
                </div>
                <div class="p-3 card-body">
                    <table class="table mb-0 table-borderless">
                        <tr>
                            <th>Purchase Date:</th>
                            <td>{{ $purchase->purchase_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th>Supplier:</th>
                            <td>{{ $purchase->supplier_name ?? 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <th>Supplier Phone:</th>
                            <td>{{ $purchase->supplier_phone ?? 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <th>Invoice Number:</th>
                            <td>{{ $purchase->invoice_number ?? 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <th>Created By:</th>
                            <td>{{ $purchase->admin->name }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $purchase->created_at->format('d M Y H:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Products:</th>
                            <td>
                                <span class="badge badge-info">{{ $purchase->productPurchases->count() }} items</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Amount:</th>
                            <td><strong class="text-success">{{ number_format($purchase->total_amount, 2) }} BDT</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="mb-4 col-lg-6">
            <div class="rounded-sm shadow-sm h-100 card">
                <div class="p-3 text-white card-header bg-secondary">
                    <h5 class="mb-0">Additional Information</h5>
                </div>
                <div class="p-3 card-body">
                    @if($purchase->notes)
                        <div class="mb-3">
                            <h6 class="font-weight-bold">Notes:</h6>
                            <div class="p-3 border bg-light">{{ $purchase->notes }}</div>
                        </div>
                    @else
                        <div class="text-muted">No additional notes for this purchase.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="rounded-sm shadow-sm card">
                <div class="p-3 text-white card-header bg-info d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Products in this Purchase</h5>
                    <span class="badge badge-light">Total: {{ $purchase->productPurchases->count() }}</span>
                </div>
                <div class="p-3 card-body">
                    <div class="mb-0 table-responsive">
                        <table class="table mb-0 table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Name</th>
                                    <th>SKU</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchase->productPurchases as $productPurchase)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.products.edit', $productPurchase->product) }}" target="_blank">
                                                {{ $productPurchase->product->name }}
                                            </a>
                                        </td>
                                        <td>{{ $productPurchase->product->sku }}</td>
                                        <td>{{ number_format($productPurchase->price, 2) }} BDT</td>
                                        <td>{{ $productPurchase->quantity }}</td>
                                        <td>{{ number_format($productPurchase->subtotal, 2) }} BDT</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light font-weight-bold">
                                    <td colspan="4" class="text-right">Total</td>
                                    <td>{{ number_format($purchase->total_amount, 2) }} BDT</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
