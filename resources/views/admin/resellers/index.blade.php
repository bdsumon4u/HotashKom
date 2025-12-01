@extends('layouts.light.master')
@section('title', 'Resellers')

@push('css')
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

        /* Hide sort icons for ID column */
        .datatable thead th.sorting_asc,
        .datatable thead th.sorting_desc,
        .datatable thead th.sorting {
            background-image: none !important;
        }
    </style>
@endpush

@section('breadcrumb-title')
    <h3>Resellers</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Resellers</li>
    @if(isset($status) && $status === 'pending')
    <li class="breadcrumb-item active">Pending</li>
    @endif
@endsection

@section('content')
    <div class="mb-5 row">
        <div class="col-sm-12">
            <div class="orders-table">
                <div class="shadow-sm card rounded-0">
                    <div class="p-3 card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @if(isset($status) && $status === 'pending')
                                    <strong>Pending</strong>&nbsp;<small>Resellers</small>
                                    <small class="ml-2 text-muted">(Non-verified resellers)</small>
                                @else
                                    <strong>All</strong>&nbsp;<small>Resellers</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="p-3 card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Shop</th>
                                        <th>Phone</th>
                                        <th>bKash</th>
                                        <th style="white-space: nowrap;">Reg. Date</th>
                                        @if(!isset($status) || $status !== 'pending')
                                        <th>Balance</th>
                                        <th>Orders</th>
                                        <th>Verified?</th>
                                        @endif
                                        <th>Actions</th>
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

@push('js')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatable-extension/buttons.bootstrap4.min.js') }}"></script>
@endpush

@push('scripts')
    <script>
        var table = $('.datatable').DataTable({
            search: [{
                bRegex: true,
                bSmart: false,
            }, ],
            dom: 'lBftip',
            buttons: [{
                text: 'Export',
                className: 'px-1 py-1',
                action: function(e, dt, node, config) {
                    // Add export functionality if needed
                }
            }],
            columnDefs: [

            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('api.resellers') }}",
                data: function(d) {
                    @if(isset($status) && $status === 'pending')
                    d.status = 'pending';
                    @endif
                }
            },
            columns: [
                {
                    data: 'id',
                    name: 'id',
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'shop_name',
                    name: 'shop_name'
                },
                {
                    data: 'phone_number',
                    name: 'phone_number'
                },
                {
                    data: 'bkash_number',
                    name: 'bkash_number'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    sortable: true,
                    searchable: true,
                },
                @if(!isset($status) || $status !== 'pending')
                {
                    data: 'balance',
                    name: 'balance',
                    sortable: true,
                    searchable: false,
                },
                {
                    data: 'orders_count',
                    name: 'orders_count',
                    sortable: true,
                    searchable: false,
                },
                {
                    data: 'is_verified',
                    name: 'is_verified'
                },
                @endif
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ],
            initComplete: function(settings, json) {
                var tr = $(this.api().table().header()).children('tr').clone();
                tr.find('th').each(function(i, item) {
                    $(this).removeClass('sorting').addClass('p-1');
                });
                tr.appendTo($(this.api().table().header()));
                this.api().columns().every(function(i) {
                    var th = $(this.header()).parents('thead').find('tr').eq(1).find('th').eq(i);
                    $(th).empty();

                    @if(!isset($status) || $status !== 'pending')
                    // For all resellers view - exclude balance, orders, verified, and actions columns
                    if ($.inArray(i, [6, 7, 8, 9]) === -1) {
                    @else
                    // For pending resellers view - exclude actions column only
                    if ($.inArray(i, [6]) === -1) {
                    @endif
                        var column = this;
                        var input = document.createElement("input");
                        input.classList.add('form-control', 'border-primary');
                        $(input).appendTo($(th))
                            .on('change', function() {
                                if (i) {
                                    column.search($(this).val(), false, false, true).draw();
                                } else {
                                    column.search('^' + (this.value.length ? this.value : '.*') +
                                        '$', true, false).draw();
                                }
                            });
                    }
                });
            },
            order: [
                @if(isset($status) && $status === 'pending')
                [0, 'desc'] // Sort by ID column (index 0) in descending order for pending resellers
                @else
                [6, 'desc'] // Sort by balance column (index 6) in descending order for all resellers
                @endif
            ],
            pageLength: 50,
            lengthMenu: [[10, 25, 50, 100, 250, 500], [10, 25, 50, 100, 250, 500]],
        });

        // Toggle Verification
        $(document).on('click', '.toggle-verify', function() {
            var id = $(this).data('id');
            var isVerified = $(this).data('verified');
            var button = $(this);
            var action = isVerified ? 'unverify' : 'verify';

            if (confirm('Are you sure you want to ' + action + ' this reseller?')) {
                $.ajax({
                    url: '/api/resellers/' + id + '/toggle-verify',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        table.ajax.reload();
                        $.notify('Verification status updated successfully', 'success');
                    },
                    error: function(xhr) {
                        $.notify('Error updating verification status', 'error');
                    }
                });
            }
        });

        // Delete Reseller (only shown for unverified/pending view)
        $(document).on('click', '.delete-reseller', function() {
            var id = $(this).data('id');

            if (confirm('Are you sure you want to delete this unverified reseller? This action cannot be undone.')) {
                $.ajax({
                    url: '/api/resellers/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        table.ajax.reload();
                        $.notify(response.message || 'Reseller deleted successfully', 'success');
                    },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to delete reseller';
                        $.notify(msg, 'error');
                    }
                });
            }
        });
    </script>
@endpush
