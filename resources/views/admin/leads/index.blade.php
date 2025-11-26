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
    </style>
@endpush

@section('breadcrumb-title')
<h3>Leads</h3>
@endsection

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
                <form action="{{ route('admin.leads.index') }}" method="GET" class="w-100 w-md-auto">
                    <div class="input-group">
                        <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search name, shop, email or phone">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </form>
            </div>
            <div class="p-3 card-body">
                @if (session('status'))
                <div class="mb-3 alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table align-middle table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Shop</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Message</th>
                                <th>Submitted At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leads as $lead)
                            <tr>
                                <td>{{ $leads->firstItem() + $loop->index }}</td>
                                <td>{{ $lead->name }}</td>
                                <td>{{ $lead->shop_name ?? '—' }}</td>
                                <td>{{ $lead->email ?? '—' }}</td>
                                <td>{{ $lead->phone }}</td>
                                <td style="max-width: 320px;">{{ $lead->message }}</td>
                                <td>{{ $lead->created_at->format('d M Y h:i A') }}</td>
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
                                <td colspan="8" class="text-center text-danger">No leads found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $leads->links() }}
                </div>
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

