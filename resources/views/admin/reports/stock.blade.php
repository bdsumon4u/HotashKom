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
<div class="row mb-5">
    <div class="col-md-12 mx-auto">
        <div class="reports-table">
            <div class="card rounded-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center p-3">
                    <strong>Stock Report</strong>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="min-width: 50px;">ID</th>
                                    <th style="min-width: 50px;">Name</th>
                                    <th style="min-width: 50px;">Stock</th>
                                    <th style="min-width: 50px;">Price</th>
                                    <th style="min-width: 50px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>{{$product->id}}</td>
                                        <td>
                                            <a href="{{ route('admin.products.edit', $product->parent_id ?? $product->id) }}" target="_blank">{{$product->var_name}}</a>
                                        </td>
                                        <td>{{$product->stock_count}}</td>
                                        <td>{{$product->selling_price}}</td>
                                        <td>{!!theMoney($product->selling_price*$product->stock_count)!!}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-right">Total</td>
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
