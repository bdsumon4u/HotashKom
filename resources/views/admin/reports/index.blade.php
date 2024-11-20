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
                    <strong>Saved Reports</strong>
                    <a href="{{ route('admin.reports.create') }}" class="btn btn-primary">New Report</a>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="min-width: 50px;">ID</th>
                                    <th style="min-width: 50px;">DateTime</th>
                                    <th style="min-width: 50px;">Orders</th>
                                    <th style="min-width: 50px;">Products</th>
                                    <th style="min-width: 80px;">Courier</th>
                                    <th style="min-width: 80px;">Status</th>
                                    <th style="min-width: 80px;">Amount</th>
                                    <th style="width: 100px;">DELETE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports as $report)
                                    <tr onclick="window.location='{{route('admin.reports.edit', $report->id)}}'">
                                        <td>{{$report->id}}</td>
                                        <td>{{$report->created_at->format('d-M-Y h:i A')}}</td>
                                        <td>{{$report->orders}}</td>
                                        <td>{{$report->products}}</td>
                                        <td>{{$report->courier}}</td>
                                        <td>{{$report->status}}</td>
                                        <td>{{$report->total}}</td>
                                        <td>
                                            <form action="{{route('admin.reports.destroy', $report->id)}}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" data-action="delete">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-center mt-3">
                            {{$reports->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
