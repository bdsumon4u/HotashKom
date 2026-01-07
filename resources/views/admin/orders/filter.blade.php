@extends('layouts.light.master')
@section('title', 'Reports')

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/daterange-picker.css') }}">
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

            html,
            body {
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

            .page-main-header,
            .page-header,
            .footer-fix {
                display: none !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="mb-5 row">
        <div class="mx-auto col-md-11">
            <div class="reports-table">
                <div class="shadow-sm card rounded-0">
                    <div class="p-3 card-header">
                        <form action="">
                            <div class="row">
                                <div class="col-auto pr-1">
                                    <select name="date_type" id="datetype" class="form-control">
                                        <option value="created_at" @if (request('date_type') == 'created_at') selected @endif>ORDER
                                            DATE</option>
                                        <option value="status_at" @if (request('date_type', 'status_at') == 'status_at') selected @endif>UPDATE
                                            DATE</option>
                                    </select>
                                </div>
                                <div class="col-auto px-1">
                                    <input class="form-control" id="reportrange" type="text">
                                    <input type="hidden" name="start_d" value="{{ $start }}">
                                    <input type="hidden" name="end_d" value="{{ $end }}">
                                </div>
                                @if (request('status') == 'SHIPPING' && request()->has('courier'))
                                    <div class="col-auto px-1">
                                        <select name="courier" id="courier" class="form-control">
                                            <option value="">Courier</option>
                                            @foreach (couriers() as $courier)
                                                <option value="{{ $courier }}"
                                                    @if (request()->get('courier') == $courier) selected @endif>{{ $courier }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div class="col-auto px-1">
                                        <select name="status" id="status" class="form-control">
                                            <option value="">Delivery Status</option>
                                            @foreach (config('app.orders', []) as $status)
                                                <option value="{{ $status }}"
                                                    @if (request()->get('status') == $status) selected @endif>{{ $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="col-auto px-1">
                                    <select name="staff_id" id="staff-id" class="form-control">
                                        <option value="">Select Salesman</option>
                                        @foreach (\App\Models\Admin::where('role_id', \App\Models\Admin::SALESMAN)->get() as $admin)
                                            <option value="{{ $admin->id }}"
                                                @if (request()->get('staff_id') == $admin->id) selected @endif>{{ $admin->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="pl-1 col">
                                    <button class="btn btn-primary" type="submit">Filter</button>
                                </div>
                                <div class="pl-1 col">
                                    <button class="ml-auto btn btn-primary d-block" onclick="window.print()">Print</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="p-3 card-body d-print-none">
                        <div class="row">
                            @foreach ($orders as $status => $count)
                                <div class="col-xl-3 box-col- col-lg-3 col-md-3">
                                    <div class="rounded-sm card o-hidden">
                                        <div class="p-3 card-body">
                                            <div class="media">
                                                <div class="media-body">
                                                    @php
                                                        $statData = compact('status');
                                                        if ($loop->index == 0) {
                                                            $statData = ['status' => ''];
                                                        } elseif ($loop->index < 3) {
                                                            $statData = ['status' => '', 'type' => $status];
                                                        }
                                                    @endphp
                                                    <a
                                                        href="{{ route(
                                                            'admin.orders.index',
                                                            array_merge(
                                                                array_merge(
                                                                    [
                                                                        'start_d' => date('Y-m-d'),
                                                                        'end_d' => date('Y-m-d'),
                                                                    ],
                                                                    request()->query(),
                                                                ),
                                                                $statData,
                                                            ),
                                                        ) }}">
                                                        <p class="mb-2 f-w-500 font-roboto">{{ $status }} Orders</p>
                                                        <h4 class="mb-0 f-w-500 f-26"><span
                                                                class="-counter-">{{ $count }}</span></h4>
                                                        <span class="-counter-">Taka: {{ $amounts[$status] }}</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="p-3 card-footer">
                        @include('admin.reports.filtered')
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
        window.reportRangeCB = function(start, end) {
            $('input[name="start_d"]').val(start.format('YYYY-MM-DD'));
            $('input[name="end_d"]').val(end.format('YYYY-MM-DD'));
        }
    </script>
@endpush
