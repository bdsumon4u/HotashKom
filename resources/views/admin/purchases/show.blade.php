@extends('layouts.light.master')

@section('title', 'Purchase Details')

@section('breadcrumb-title')
<h3>Purchases</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Purchase Details</li>
@endsection

@section('breadcrumb-right')
    <div class="theme-form m-t-10">
        <button type="button" class="btn btn-primary btn-sm" onclick="printPage()">
            <i class="fa fa-print"></i> Print Report
        </button>
    </div>
@endsection

@section('content')
<!-- Print Header (hidden on screen, visible when printing) -->
<div class="print-header" style="display: none;">
    <h1>Purchase Details Report</h1>
    <div class="date">Generated on: {{ now()->format('F j, Y \a\t g:i A') }}</div>
</div>

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
                                                {{ $productPurchase->product->var_name }}
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

@push('css')
    <style>
        /* Print styles */
        @media print {
            @page {
                margin: 0.5in !important;
            }

            html, body {
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
            }

            .main-nav {
                display: none !important;
                width: 0 !important;
            }

            .page-main-header {
                display: none !important;
                width: 0 !important;
            }

            .print-edit-buttons,
            .footer {
                display: none !important;
            }

            .page-body {
                font-size: 16px;
                margin: 0 !important;
                padding: 0 !important;
                page-break-after: avoid;
            }

            .page-body p {
                font-size: 14px !important;
            }

            /* Hide DataTable elements */
            .dt-buttons,
            .dataTables_paginate,
            .dataTables_info,
            .dataTables_filter,
            .dataTables_length,
            .no-print {
                display: none !important;
            }

            /* Hide breadcrumb right section during printing */
            .breadcrumb-right {
                display: none !important;
            }

            /* Remove any horizontal lines or borders at the top */
            hr,
            .hr,
            [class*="border-top"],
            [class*="border-bottom"],
            .border-top,
            .border-bottom {
                display: none !important;
                border: none !important;
            }


            /* Container adjustments */
            .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                min-height: auto !important;
            }

            /* Style the card for printing */
            .card {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
                margin: 0 0 10px 0 !important;
                padding: 0 !important;
                page-break-inside: avoid;
            }

            .card-body {
                padding: 15px !important;
            }

            /* Card header styling for print */
            .card-header {
                background-color: #f8f9fa !important;
                border-bottom: 1px solid #ddd !important;
                padding: 10px 15px !important;
            }

            .card-header h5 {
                margin: 0 !important;
                font-size: 16px !important;
                font-weight: bold !important;
            }

            /* Table styling for purchase details */
            .table-borderless th,
            .table-borderless td {
                border: none !important;
                padding: 5px 0 !important;
                vertical-align: top !important;
            }

            .table-borderless th {
                font-weight: bold !important;
                width: 40% !important;
            }

            .table-borderless td {
                width: 60% !important;
            }

            /* Table styles for printing */
            .table {
                border-collapse: collapse !important;
                width: 100% !important;
            }

            .table th,
            .table td {
                border: 1px solid #000 !important;
                padding: 8px !important;
                text-align: left !important;
            }

            .table thead th {
                background-color: #f8f9fa !important;
                font-weight: bold !important;
            }

            /* Products table specific styling */
            .table-bordered th,
            .table-bordered td {
                border: 1px solid #000 !important;
                padding: 8px !important;
                text-align: left !important;
            }

            .table-bordered thead th {
                background-color: #f8f9fa !important;
                font-weight: bold !important;
            }

            .table-bordered tfoot td {
                background-color: #f8f9fa !important;
                font-weight: bold !important;
            }

            /* Badge styling for print */
            .badge {
                border: 1px solid #000 !important;
                padding: 2px 6px !important;
                font-size: 12px !important;
            }

            .badge-light {
                background-color: #f8f9fa !important;
                color: #000 !important;
            }

            .badge-info {
                background-color: #d1ecf1 !important;
                color: #0c5460 !important;
            }

            /* Text color styling for print */
            .text-success {
                color: #000 !important;
                font-weight: bold !important;
            }

            .text-muted {
                color: #666 !important;
            }

            /* Print header styles */
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
                padding-bottom: 10px;
            }

            .print-header h1 {
                margin: 0;
                font-size: 24px;
                font-weight: bold;
            }

            .print-header .date {
                font-size: 14px;
                color: #666;
                margin-top: 5px;
            }

            /* Ensure only the card content is visible */
            .row {
                margin: 0 !important;
                display: flex !important;
                flex-wrap: wrap !important;
            }

            .col-sm-12 {
                padding: 0 !important;
            }

            /* Two-column layout for purchase details */
            .col-lg-6 {
                width: 50% !important;
                padding: 0 10px !important;
                margin-bottom: 15px !important;
                flex: 0 0 50% !important;
                max-width: 50% !important;
            }

            /* Ensure cards have equal height */
            .h-100 {
                height: auto !important;
                min-height: 300px !important;
            }

            /* Hide specific navigation and layout elements */
            .main-nav,
            .sidebar-wrapper,
            .sidebar,
            .main-header,
            .page-header,
            .page-title,
            .breadcrumb,
            footer,
            .footer,
            .main-footer,
            .dt-buttons,
            .dataTables_paginate,
            .dataTables_info,
            .dataTables_filter,
            .dataTables_length,
            .card-header .d-flex,
            .no-print,
            /* Additional header selectors */
            header,
            .header,
            .top-header,
            .navbar,
            .navbar-header,
            .navbar-nav,
            .nav-header,
            .page-header-wrapper,
            .main-header-wrapper {
                display: none !important;
                width: 0 !important;
            }

            /* Hide specific elements that might be showing */
            .page-wrapper,
            .page-body-wrapper,
            .page-body,
            .container-fluid {
                margin-left: 0 !important;
                padding-left: 0 !important;
            }

            /* Ensure content takes full width */
            .page-body {
                margin-left: 0 !important;
                padding-left: 0 !important;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        function printPage() {
            // Show print header
            $('.print-header').show();

            // Hide elements that shouldn't be printed
            $('.main-nav, .page-main-header, .footer, .dt-buttons, .dataTables_paginate, .dataTables_info, .dataTables_filter, .dataTables_length').addClass('no-print');

            // Print the page
            window.print();

            // Hide print header and remove no-print classes after printing
            setTimeout(function() {
                $('.print-header').hide();
                $('.main-nav, .page-main-header, .footer, .dt-buttons, .dataTables_paginate, .dataTables_info, .dataTables_filter, .dataTables_length').removeClass('no-print');
            }, 1000);
        }
    </script>
@endpush
