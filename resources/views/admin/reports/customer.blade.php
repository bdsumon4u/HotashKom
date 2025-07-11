@extends('layouts.light.master')
@section('title', 'Reports')

@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/animate.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/daterange-picker.css')}}">
<style>
    .daterangepicker {
        border: 2px solid #d7d7d7 !important;
    }
</style>
@endpush

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
        font-size: 20px;
        margin-top: 0 !important;
        margin-left: 0 !important;
    }
    .page-break {
        page-break-after: always;
        border-top: 2px dashed #000;
    }
}
</style>
@endpush

@section('content')
<div class="row mb-5">
    <div class="col-md-8 mx-auto">
        <div class="reports-table">
            <div class="card rounded-0 shadow-sm">
                <div class="card-header p-3">
                    <form action="">
                        <div class="row">
                            {{-- <div class="col-auto pr-1">
                                <select name="date_type" id="datetype" class="form-control">
                                    <option value="created_at" @if(request('date_type') == 'created_at') selected @endif>ORDER DATE</option>
                                    <option value="status_at" @if(request('date_type', 'status_at') == 'status_at') selected @endif>UPDATE DATE</option>
                                </select>
                            </div> --}}
                            <div class="col-auto pr-1">
                                <input class="form-control" id="reportrange" type="text">
                                <input type="hidden" name="start_d" value="{{ $start }}">
                                <input type="hidden" name="end_d" value="{{ $end }}">
                            </div>
                            <div class="col-auto px-1">
                                <select name="top_by" id="topby" class="form-control">
                                    <option value="order_amount" @if(request('top_by') == 'order_amount') selected @endif>Top by Order-Amount</option>
                                    <option value="order_count" @if(request('top_by') == 'order_count') selected @endif>Top by Order-Count</option>
                                </select>
                            </div>
                            <div class="col pl-1">
                                <button class="btn btn-primary" type="submit">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="min-width: 50px;">S.I.</th>
                                    <th style="min-width: 50px;">ID</th>
                                    <th style="min-width: 120px;">Name</th>
                                    <th style="min-width: 100px;">Phone</th>
                                    <th style="min-width: 100px;">Order Count</th>
                                    <th style="min-width: 100px;">Order Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->id }}</td>
                                        <td>
                                            <a href="{{ route('admin.orders.index', ['user_id' => $user->id, 'status' => 'DELIVERED', 'start_d' => $start, 'end_d' => $end]) }}" target="_blank">{{ $user->name }}</a>
                                        </td>
                                        <td>{{ $user->phone_number }}</td>
                                        <td>{{ $user->order_count }}</td>
                                        <td>{!!theMoney($user->order_amount)!!}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script src="{{ asset('assets/js/datepicker/daterange-picker/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/daterange-picker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/daterange-picker/daterange-picker.custom.js') }}"></script>
    <script>
        window._start = moment('{{ $start }}');
        window._end = moment('{{ $end }}');
        window.reportRangeCB = function (start, end) {
            $('input[name="start_d"]').val(start.format('YYYY-MM-DD'));
            $('input[name="end_d"]').val(end.format('YYYY-MM-DD'));
        }
    </script>
@endpush
