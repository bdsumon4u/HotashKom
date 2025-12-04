@extends('layouts.light.master')
@section('title', 'Leads')

@push('css')
    <style>
        @media print {
            html,
            body {
                height: 100vh;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden;
            }

            .main-nav,
            .page-main-header,
            .footer,
            .card-header,
            .dt-buttons,
            .dataTables_paginate,
            .dataTables_info,
            .dataTables_filter,
            .dataTables_length,
            .no-print {
                display: none !important;
                width: 0 !important;
            }

            .page-body {
                font-size: 16px;
                margin-top: 0 !important;
                margin-left: 0 !important;
                page-break-after: always;
            }

            .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .card-body {
                padding: 10px 0 !important;
            }

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

            .table th:last-child,
            .table td:last-child {
                display: none !important;
            }

            /* Force-hide any card headers and their contents on print */
            .card-header,
            .card-header * {
                display: none !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }

        /* Slightly larger checkboxes for leads table */
        #select-all-leads,
        .lead-select {
            width: 1.1rem;
            height: 1.1rem;
        }

        .lead-header-action {
            min-width: 130px;
        }
    </style>
@endpush

@section('breadcrumb-title')
<h3>Leads</h3>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var selectAll = document.getElementById('select-all-leads');
            var checkboxes = document.querySelectorAll('.lead-select');
            var printButton = document.getElementById('printButton');
            var printHeader = document.querySelector('.print-header');
            var elementsToToggle = document.querySelectorAll('.main-nav, .page-main-header, .footer, .card-header, .dt-buttons, .dataTables_paginate, .dataTables_info, .dataTables_filter, .dataTables_length');

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checkboxes.forEach(function (checkbox) {
                        checkbox.checked = selectAll.checked;
                    });
                });
            }

            if (printButton && printHeader) {
                printButton.addEventListener('click', function () {
                    printHeader.style.display = 'block';
                    elementsToToggle.forEach(function (el) {
                        el?.classList?.add('no-print');
                    });

                    window.print();

                    setTimeout(function () {
                        printHeader.style.display = 'none';
                        elementsToToggle.forEach(function (el) {
                            el?.classList?.remove('no-print');
                        });
                    }, 500);
                });
            }
        });
    </script>
@endpush

@section('breadcrumb-items')
<li class="breadcrumb-item">Leads</li>
@endsection

@section('content')
<div class="mb-5 row">
    <div class="col-sm-12">
        <div class="shadow-sm card rounded-0">
            <div class="gap-2 card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between no-print">
                <div>
                    <strong>Lead</strong><small>Submissions</small>
                </div>
                <div class="gap-2 d-flex flex-column flex-md-row align-items-stretch w-100 w-md-auto" style="gap: 0.5rem;">
                    <form action="{{ route('admin.leads.index') }}" method="GET" class="flex-fill">
                        <div class="gap-2 d-flex flex-column flex-md-row" style="gap: 0.5rem;">
                            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search name, shop, email or phone">
                            <button class="btn btn-primary lead-header-action" type="submit">Search</button>
                        </div>
                    </form>
                    <button type="button" class="btn btn-outline-primary lead-header-action" id="printButton">
                        <i class="fa fa-print me-1"></i> Print
                    </button>
                </div>
            </div>
            <div class="p-3 card-body">
                <div class="mb-3 print-header" style="display: none;">
                    <h1 class="mb-1 h4">Leads Report</h1>
                    <div class="text-muted">Generated on: {{ now()->format('F j, Y \a\t g:i A') }}</div>
                </div>
                @if (session('status'))
                <div class="mb-3 alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
                @endif
                <form action="{{ route('admin.leads.bulk-destroy') }}" method="POST" id="bulk-delete-form">
                    @csrf
                    @method('DELETE')
                    <div class="table-responsive">
                        <table class="table align-middle table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="select-all-leads">
                                    </th>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Shop</th>
                                    <th>District</th>
                                    <th>Contact</th>
                                    <th>Submitted At</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leads as $lead)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="ids[]" value="{{ $lead->id }}" class="lead-select">
                                    </td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $lead->name }}</td>
                                    <td>{{ $lead->shop_name ?? '—' }}</td>
                                    <td>{{ $lead->district ?? '—' }}</td>
                                    <td>
                                        @if($lead->email)
                                            <div>{{ $lead->email }}</div>
                                        @endif
                                        @if($lead->phone)
                                            <div>{{ $lead->phone }}</div>
                                        @endif
                                        @if(! $lead->email && ! $lead->phone)
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $lead->created_at->format('d M Y') }}</div>
                                        <div>{{ $lead->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.leads.destroy', $lead) }}" method="POST" onsubmit="return confirm('Delete this lead?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-danger">No leads found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-start align-items-center">
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete selected leads?');">
                            Delete Selected
                        </button>
                    </div>
                </form>
            </div>
            <div class="p-3 card-footer">
                Lead Form: <a href="{{ route('leads.form') }}" target="_blank">
                    {{route('leads.form')}}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
