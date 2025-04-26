@extends('layouts.yellow.master')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
    <style>
        .dt-buttons.btn-group {
            margin: .25rem 1rem 1rem 1rem;
        }

        .dt-buttons.btn-group .btn {
            font-size: 12px;
        }

        th:focus {
            outline: none;
        }
    </style>
@endpush

@section('title', 'Transaction History')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Transaction History</h5>
                            <div>
                                <strong>Balance:</strong> {{ number_format(auth('user')->user()->balance, 2) }} tk
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>S.I.</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/buttons.bootstrap4.min.js') }}"></script>

    <script>
        var table = $('.datatable').DataTable({
            search: [{
                bRegex: true,
                bSmart: false,
            }],
            dom: 'lBftip',
            buttons: [{
                text: 'Export',
                className: 'px-1 py-1',
                action: function(e, dt, node, config) {
                    // Add export functionality if needed
                }
            }],
            processing: true,
            serverSide: true,
            ajax: "{{ route('user.transactions') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'meta',
                    name: 'meta',
                }
            ],
            order: [
                [3, 'desc']
            ],
            pageLength: 50,
        });
    </script>
@endpush
