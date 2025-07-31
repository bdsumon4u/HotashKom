@extends('layouts.light.master')
@section('title', 'Reports')

@section('breadcrumb-title')
<h3>Reports</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Reports</li>
@endsection

@push('styles')
<style>
@media print {
    html, body {
        /* height:100vh; */
        margin: 0 !important;
        padding: 0 !important;
        /* overflow: hidden; */
    }
    .main-nav {
        display: none !important;
        width: 0 !important;
    }
    .page-body {
        font-size: 14px;
        margin-top: 0 !important;
        margin-left: 0 !important;
    }
    .page-break {
        page-break-after: always;
        border-top: 2px dashed #000;
    }

    .page-main-header, .page-header, .card-header, .footer-fix {
        display: none !important;
    }

    th, td {
        padding: 0.25rem !important;
    }

    a {
        text-decoration: none !important;
    }
}
</style>
@endpush

@section('content')
<div class="mb-5 row">
    <!-- Stock Summary Cards -->
    <div class="col-md-4">
        <div class="text-white rounded-sm shadow-sm card bg-primary">
            <div class="px-3 py-2 card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">{{ $totalStockCount ?? 0 }}</h6>
                        <small>Total Stock Count</small>
                    </div>
                    <div class="align-self-center">
                        <i data-feather="package" class="feather-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="text-white rounded-sm shadow-sm card bg-info">
            <div class="px-3 py-2 card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">{!!$totalPurchaseValue!!}</h6>
                        <small>Total Purchase Value</small>
                    </div>
                    <div class="align-self-center">
                        <i data-feather="shopping-cart" class="feather-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="text-white rounded-sm shadow-sm card bg-success">
            <div class="px-3 py-2 card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">{!!$totalSellValue!!}</h6>
                        <small>Total Sell Value</small>
                    </div>
                    <div class="align-self-center">
                        <i data-feather="dollar-sign" class="feather-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Stock Summary Cards -->
    <div class="mx-auto col-md-12">
        <div class="reports-table">
            <div class="shadow-sm card rounded-0">
                <div class="p-3 card-header d-flex justify-content-between align-items-center">
                    <strong>Stock Report</strong>
                </div>
                <div class="p-3 card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="min-width: 50px;">ID</th>
                                    <th style="min-width: 50px;">Name</th>
                                    <th style="min-width: 50px;">Stock</th>
                                    <th style="min-width: 50px;">Buy</th>
                                    <th style="min-width: 50px;">Sell</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>{{$product->id}}</td>
                                        <td>
                                            <a href="{{ route('admin.products.edit', $product->parent_id ?? $product->id) }}" target="_blank">{{$product->var_name}}</a>
                                            <div class="text-muted small">
                                                (Buy: {!! theMoney($product->average_purchase_price ?? 0) !!},
                                                Sell: {!! theMoney($product->selling_price ?? 0) !!})
                                            </div>
                                        </td>
                                        <td>{{$product->stock_count}}</td>
                                        <td>{!!theMoney($product->average_purchase_price*$product->stock_count)!!}</td>
                                        <td>{!!theMoney($product->selling_price*$product->stock_count)!!}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tbody>
                                <tr>
                                    <td colspan="3" class="text-right">Total</td>
                                    <td>{!!theMoney($products->sum(fn ($product) => $product->average_purchase_price * $product->stock_count))!!}</td>
                                    <td>{!!theMoney($products->sum(fn ($product) => $product->selling_price * $product->stock_count))!!}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
