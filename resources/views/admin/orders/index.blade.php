@extends('layouts.light.master')
@section('title', 'Orders')

@push('css')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/animate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/daterange-picker.css')}}">
    <style>
        .daterangepicker {
            border: 2px solid #d7d7d7 !important;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/datatables.css')}}">
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

@section('breadcrumb-title')
    <h3>Orders</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Orders</li>
@endsection

@section('content')
    <div class="row mb-5">
        <div class="col-sm-12">
            <div class="orders-table">
                <div class="card rounded-0 shadow-sm">
                    <div class="card-header p-2">
                        <div class="row px-3 justify-content-between align-items-center">
                            <div>All Orders</div>
                            <div>
                                <a href="{{route('admin.orders.create')}}" class="btn btn-sm btn-primary">New Order</a>
                                <a href="{{ route('admin.orders.pathao-csv') }}" class="btn btn-sm btn-primary ml-1">Pathao CSV</a>
                            </div>
                        </div>
                        <div class="row d-none" style="row-gap: .25rem;">
                            <div class="col-auto pr-0 d-flex align-items-center" check-count></div>
                            @unless(false && in_array(request('status'), ['CONFIRMED', 'INVOICED']))
                            <div class="col-auto px-1">
                                <select name="status" id="status" onchange="changeStatus()" class="form-control form-control-sm bg-primary text-white">
                                    <option value="">Change Status</option>
                                    @foreach(config('app.orders', []) as $status)
                                        @php $show = false @endphp
                                        @switch($status)
                                            @case('WAITING')
                                                @php $show = in_array(request('status'), ['PENDING', 'CANCELLED']) @endphp
                                                @break
                                        
                                            @case('CONFIRMED')
                                                @php $show = in_array(request('status'), ['PENDING', 'WAITING', 'CANCELLED']) @endphp
                                                @break
                                        
                                            @case('CANCELLED')
                                                @php $show = in_array(request('status'), ['PENDING', 'WAITING']) @endphp
                                                @break
                                        
                                            @case('COMPLETED')
                                            @case('RETURNED')
                                            @case('LOST')
                                                @php $show = in_array(request('status'), ['SHIPPING']) @endphp
                                                @break
                                        
                                            @default
                                                
                                        @endswitch
                                        @if($show || true)
                                        <option value="{{ $status }}">{{ $status }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            @endunless
                            @unless(request('status') == 'SHIPPING')
                            <div class="col-auto px-1">
                                <select name="courier" id="courier" onchange="changeCourier()" class="form-control form-control-sm bg-primary text-white">
                                    <option value="">Change Courier</option>
                                    @foreach(['Pathao', 'SteadFast', 'Other'] as $provider)
                                    <option value="{{ $provider }}">{{ $provider }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endunless
                            <div class="col-auto pl-0 ml-auto">
                                @if(request('status') == 'CONFIRMED')
                                <button onclick="printInvoice()" id="invoice" class="btn btn-sm btn-primary ml-1">Print Invoice</button>
                                @elseif(request('status') == 'INVOICED')
                                <button onclick="courier()" id="courier" class="btn btn-sm btn-primary ml-1">Send to Courier</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                <thead>
                                <tr>
                                    @if($bulk = true || request('status') && !in_array(request('status'), ['COMPLETED', 'RETURNED', 'LOST']))
                                    <th style="max-width: 5%">
                                        <input type="checkbox" class="form-control" name="check_all" style="min-height: 20px;min-width: 20px;max-height: 20px;max-width: 20px;">
                                    </th>
                                    @endif
                                    <th width="80">ID</th>
                                    <th>Customer</th>
                                    <th>Products</th>
                                    <th width="10">Amount</th>
                                    <th width="10">Status</th>
                                    <th>Courier</th>
                                    <th>Staff</th>
                                    <th width="150">DateTime</th>
                                    @if(auth()->user()->is('admin'))
                                    <th width="10">Action</th>
                                    @endif
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
    <script src="{{asset('assets/js/datatable/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/datatable/datatable-extension/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/js/datatable/datatable-extension/buttons.bootstrap4.min.js')}}"></script>
    <script src="{{asset('assets/js/product-list-custom.js')}}"></script>
@endpush

@push('scripts')
    <script>
        var checklist = new Set();
        function updateBulkMenu() {
            $('[name="check_all"]').prop('checked', true);
            $(document).find('[name="order_id[]"]').each(function () {
                if (checklist.has($(this).val())) {
                    $(this).prop('checked', true);
                } else {
                    $('[name="check_all"]').prop('checked', false);
                }
            });

            if (checklist.size > 0) {
                $('[check-count]').text(checklist.size + 'x');
                $('.card-header > .row:last-child').removeClass('d-none');
                $('.card-header > .row:first-child').addClass('d-none');
            } else {
                $('[check-count]').text('');
                $('.card-header > .row:last-child').addClass('d-none');
                $('.card-header > .row:first-child').removeClass('d-none');
            }
        }
        $('[name="check_all"]').on('change', function () {
            if ($(this).prop('checked')) {
                $(document).find('[name="order_id[]"]').each(function () {
                    checklist.add($(this).val());
                });
            } else {
                $(document).find('[name="order_id[]"]').each(function () {
                    checklist.delete($(this).val());
                });
            }
            $('[name="order_id[]"]').prop('checked', $(this).prop('checked'));
            updateBulkMenu();
        });

        var table = $('.datatable').DataTable({
            search: [
                {
                    bRegex: true,
                    bSmart: false,
                },
            ],
            // aoColumns: [{ "bSortable": false }, null, null, { "sType": "numeric" }, { "sType": "date" }, null, { "bSortable": false}],
            dom: 'lBftip',
            buttons: [
                    @foreach(config('app.orders', []) as $status)
                {
                    text: '{{ $status }}',
                    className: 'px-1 py-1 {{ request('status') == $status ? 'btn-secondary' : '' }}',
                    action: function ( e, dt, node, config ) {
                        window.location = '{!! request()->fullUrlWithQuery(['status' => $status]) !!}'
                    }
                },@endforeach
                {
                    text: 'All',
                    className: 'px-1 py-1 {{ request('status') == '' ? 'btn-secondary' : '' }}',
                    action: function ( e, dt, node, config ) {
                        window.location = '{!! request()->fullUrlWithQuery(['status' => '']) !!}'
                    }
                },
            ],
            columnDefs: [
                {
                    type: 'num',
                    orderable: false,
                    searchable: false,
                    targets: @if(auth()->user()->is('admin')) -5 @else -6 @endif
                },
            ],
            processing: true,
            serverSide: true,
            ajax: "{!! route('api.orders', request()->query()) !!}",
            columns: [
                @if($bulk)
                { data: 'checkbox', name: 'checkbox', sortable: false, searchable: false},
                @endif
                { data: 'id', name: 'id' },
                { data: 'customer', name: 'customer', sortable: false },
                { data: 'products', name: 'products', sortable: false },
                { data: 'amount', name: 'amount', sortable: false },
                { data: 'status', name: 'status', sortable: false },
                { data: 'courier', name: 'courier', sortable: false },
                { data: 'staff', name: 'admin.name', sortable: false },
                { data: 'created_at', name: 'created_at' },
                @if(auth()->user()->is('admin'))
                { data: 'actions' },
                @endif
            ],
            initComplete: function (settings, json) {
                window.ordersTotal = json.recordsTotal;
                var tr = $(this.api().table().header()).children('tr').clone();
                tr.find('th').each(function (i, item) {
                    $(this).removeClass('sorting').addClass('p-1');
                });
                tr.appendTo($(this.api().table().header()));
                this.api().columns().every(function (i) {
                    var th = $(this.header()).parents('thead').find('tr').eq(1).find('th').eq(i);
                    $(th).empty();

                    if ($.inArray(i, [0, 4, 7, 9]) === -1) {
                        var column = this;
                        var input = document.createElement("input");
                        input.classList.add('form-control', 'border-primary');
                        if (i === 8) {
                            $(input).appendTo($(th)).on('apply.daterangepicker', function (ev, picker) {
                                column.search(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD')).draw();
                            }).daterangepicker({
                                startDate: window._start,
                                endDate: window._end,
                                ranges: {
                                    'Today': [moment(), moment()],
                                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                                },
                            });

                            // clear the input when _start and _end are empty
                            if (!window._start && !window._end) {
                                $(input).val('');
                            }
                        } else {
                            $(input).appendTo($(th))
                                .on('change', function () {
                                    if (i) {
                                        column.search($(this).val(), false, false, true).draw();
                                    } else {
                                        column.search('^'+ (this.value.length ? this.value : '.*') +'$', true, false).draw();
                                    }
                                });
                        }
                    }
                });
            },
            drawCallback: function () {
                updateBulkMenu();
                $(document).on('change', '[name="order_id[]"]', function () {
                    if ($(this).prop('checked')) {
                        checklist.add($(this).val());
                    } else {
                        checklist.delete($(this).val());
                    }
                    updateBulkMenu();
                });
            },
            order: [
                // [1, 'desc']
            ],
            pageLength: 50,
        });

        $(document).on('change', '.status-column', changeStatus);

        function changeStatus() {
            $('[name="status"]').prop('disabled', true);

            var order_id = Array.from(checklist);
            var status = $('[name="status"]').val();
            if ($(this).data('id')) {
                order_id = [$(this).data('id')];
                status = $(this).val();
            }

            $.post({
                url: '{{ route('admin.orders.status') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_id: order_id,
                    status: status,
                },
                success: function (response) {
                    checklist.clear();
                    updateBulkMenu();
                    table.draw();

                    $.notify('Status updated successfully', 'success');
                },
                complete: function () {
                    $('[name="status"]').prop('disabled', false);
                    $('[name="status"]').val('');
                }
            });
        }

        $(document).on('change', '.courier-column', changeCourier);

        function changeCourier() {
            $('[name="courier"]').prop('disabled', true);

            var order_id = Array.from(checklist);
            var courier = $('[name="courier"]').val();
            if ($(this).data('id')) {
                order_id = [$(this).data('id')];
                courier = $(this).val();
            }

            $.post({
                url: '{{ route('admin.orders.courier') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_id: order_id,
                    courier: courier,
                },
                success: function (response) {
                    // checklist.clear();
                    // updateBulkMenu();
                    table.draw();

                    $.notify('Courier updated successfully', 'success');
                },
                complete: function () {
                    $('[name="courier"]').prop('disabled', false);
                    $('[name="courier"]').val('');
                }
            });
        }

        $(document).on('change', '.staff-column', changeStaff);

        function changeStaff() {
            $('[name="staff"]').prop('disabled', true);

            var order_id = Array.from(checklist);
            var staff = $('[name="staff"]').val();
            if ($(this).data('id')) {
                order_id = [$(this).data('id')];
                staff = $(this).val();
            }

            $.post({
                url: '{{ route('admin.orders.staff') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_id: order_id,
                    admin_id: staff,
                },
                success: function (response) {
                    checklist.clear();
                    updateBulkMenu();
                    table.draw();

                    $.notify('Staff updated successfully', 'success');
                },
                complete: function () {
                    $('[name="staff"]').prop('disabled', false);
                    $('[name="staff"]').val('');
                },
                error: function (response) {
                    $.notify(response?.responseJSON?.message || 'Staff update failed.', {type: 'danger'});
                },
            });
        }

        setInterval(function () {
            $('.datatable').DataTable().ajax.reload(function (res) {
                if (res.recordsTotal > window.ordersTotal) {
                    window.ordersTotal = res.recordsTotal;
                    $.notify('New orders found', 'success');
                }
            }, false);
        }, 60*1000);

        function printInvoice() {
            window.open('{{ route('admin.orders.invoices') }}?order_id=' + $('[name="order_id[]"]:checked').map(function () {
                return $(this).val();
            }).get().join(','), '_blank');
        }
        function courier() {
            window.open('{{ route('admin.orders.booking') }}?order_id=' + $('[name="order_id[]"]:checked').map(function () {
                return $(this).val();
            }).get().join(','), '_self');
        }
    </script>

    <script src="{{ asset('assets/js/datepicker/daterange-picker/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/daterange-picker/daterangepicker.js') }}"></script>
@endpush

