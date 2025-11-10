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
    @page {
        margin: 0.3in !important;
        size: A4 !important;
    }

    html, body {
        margin: 0 !important;
        padding: 0 !important;
        font-size: 14px !important;
    }

    .main-nav {
        display: none !important;
        width: 0 !important;
    }

    .page-body {
        font-size: 14px !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .page-break {
        page-break-after: always;
        border-top: 2px dashed #000;
    }

    .page-main-header, .page-header, .footer-fix {
        display: none !important;
    }

    /* Table styling for print */
    .table {
        width: 100% !important;
        table-layout: fixed !important;
        font-size: 12px !important;
    }

    th, td {
        padding: 4px 6px !important;
        word-wrap: break-word !important;
        word-break: break-word !important;
        white-space: normal !important;
        vertical-align: top !important;
    }

    /* Column width adjustments for better fit */
    .table th:nth-child(1), .table td:nth-child(1) { width: 5% !important; } /* SI */
    .table th:nth-child(2), .table td:nth-child(2) { width: 7% !important; } /* ID */
    .table th:nth-child(3), .table td:nth-child(3) { width: 16% !important; } /* Customer */
    .table th:nth-child(4), .table td:nth-child(4) { width: 18% !important; } /* Address */
    .table th:nth-child(5), .table td:nth-child(5) { width: 12% !important; } /* Note */
    .table th:nth-child(6), .table td:nth-child(6) { width: 10% !important; } /* Courier */
    .table th:nth-child(7), .table td:nth-child(7) { width: 10% !important; } /* Status */
    .table th:nth-child(8), .table td:nth-child(8) { width: 8% !important; } /* Subtotal */
    .table th:nth-child(9), .table td:nth-child(9) { width: 8% !important; } /* Delivery Charge */
    .table th:nth-child(10), .table td:nth-child(10) { width: 8% !important; } /* Total */

    /* For duplicate orders table (has Action column) - hide Action column in print */
    .card-header .table th:nth-child(10), .card-header .table td:nth-child(10) {
        display: none !important;
    } /* Hide Action column */

    .card-header .table th:nth-child(1), .card-header .table td:nth-child(1) { width: 8% !important; } /* ID */
    .card-header .table th:nth-child(2), .card-header .table td:nth-child(2) { width: 15% !important; } /* Customer */
    .card-header .table th:nth-child(3), .card-header .table td:nth-child(3) { width: 18% !important; } /* Address */
    .card-header .table th:nth-child(4), .card-header .table td:nth-child(4) { width: 13% !important; } /* Note */
    .card-header .table th:nth-child(5), .card-header .table td:nth-child(5) { width: 9% !important; } /* Courier */
    .card-header .table th:nth-child(6), .card-header .table td:nth-child(6) { width: 9% !important; } /* Status */
    .card-header .table th:nth-child(7), .card-header .table td:nth-child(7) { width: 7% !important; } /* Subtotal */
    .card-header .table th:nth-child(8), .card-header .table td:nth-child(8) { width: 7% !important; } /* Delivery Charge */
    .card-header .table th:nth-child(9), .card-header .table td:nth-child(9) { width: 7% !important; } /* Total */

    /* Card styling */
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
        margin: 0 !important;
        page-break-inside: avoid;
    }

    .card-header {
        background-color: #f8f9fa !important;
        border-bottom: 1px solid #000 !important;
        padding: 5px !important;
    }

    .card-body, .card-footer {
        padding: 5px !important;
    }

    /* Hide print button and form elements */
    .btn, .form-control {
        display: none !important;
    }

    /* Show only the table content */
    .card-header .border {
        display: block !important;
    }

    a {
        text-decoration: none !important;
        color: #000 !important;
    }

    /* Ensure text wrapping for long content */
    .table td {
        max-width: 0 !important;
        overflow: hidden !important;
    }

    /* Summary row styling */
    .summary th {
        background-color: #f8f9fa !important;
        font-weight: bold !important;
        border-top: 2px solid #000 !important;
    }

    /* Print header styles */
    .print-header {
        display: block !important;
        text-align: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #000;
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
}
</style>
@endpush

@section('content')
<!-- Print Header (hidden on screen, visible when printing) -->
<div class="print-header" style="display: none;">
    <h1>Scanning Report</h1>
    <div class="date">Generated on: {{ now()->format('F j, Y \a\t g:i A') }}</div>
</div>

<div class="mb-5 row">
    <div class="mx-auto col-md-12">
        <div class="reports-table">
            <div id="section-to-print" class="shadow-sm card rounded-0">
                <div class="p-3 card-header">
                    <div class="border table-responsive border-danger" style="display: none;">
                        <strong class="p-2 text-danger">Duplicate Orders</strong>
                        <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="min-width: 50px;">ID</th>
                                    <th>Customer</th>
                                    <th>Address</th>
                                    <th>Note</th>
                                    <th style="min-width: 80px;">Courier</th>
                                    <th style="min-width: 80px;">Status</th>
                                    <th style="min-width: 80px;">Subtotal</th>
                                    <th style="min-width: 80px;">Delivery Charge</th>
                                    <th style="min-width: 80px;">Total</th>
                                    <th style="max-width: 225px; text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <form id="search-form" action="" class="mt-2">
                        <div class="row">
                            <div class="pr-1 col">
                                <input type="text" name="code" id="search" class="form-control">
                            </div>
                            <div class="col-auto px-1">
                                <button type="button" onclick="window.print()" class="btn btn-primary">Print</button>
                            </div>
                            <div class="col-auto pl-1">
                                <button type="button" onclick="saveThis()" class="btn btn-primary">{{isset($report)?'Update':'Save'}}</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="p-1 card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="min-width: 50px;">SI</th>
                                    <th style="min-width: 50px;">ID</th>
                                    <th>Customer</th>
                                    <th>Address</th>
                                    <th>Note</th>
                                    <th style="min-width: 80px;">Courier</th>
                                    <th style="min-width: 80px;">Status</th>
                                    <th style="min-width: 80px;">Subtotal</th>
                                    <th style="min-width: 80px;">Delivery Charge</th>
                                    <th style="min-width: 80px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="p-1 card-footer">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="min-width: 50px;">Product Name</th>
                                    <th style="width: 80px;">Quantity</th>
                                    <th style="width: 120px;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
                        // Configuration for pricing logic
        var isOninda = {{ isOninda() ? 'true' : 'false' }};

        console.log('Scanning report initialized - isOninda:', isOninda);

        function cardPrint() {
            // Show print header
            $('.print-header').show();

            var printContents = document.getElementById('section-to-print').innerHTML;
            var printHeader = document.querySelector('.print-header').outerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printHeader + printContents;

            setTimeout(() => {
                window.print();
                document.body.innerHTML = originalContents;
            }, 1500);
        }

        var phones = [];
        var products = {};
        var uniqueness = [];
        var duplicates = [];
        var subtotal = shipping = total = quantity = amount = 0;

        function getOrderAmount(order, field) {
            // Use retail amounts when available (retail pricing is enabled)
            // Otherwise fall back to wholesale amounts (original behavior)
            if (order.retail_amounts && order.retail_amounts.retail_subtotal !== undefined) {
                switch(field) {
                    case 'subtotal':
                        return order.retail_amounts.retail_subtotal || order.data.subtotal;
                    case 'shipping_cost':
                        return order.retail_amounts.retail_delivery_fee || order.data.shipping_cost;
                    default:
                        return order.data[field];
                }
            }
            // Use wholesale amounts when retail amounts not available
            return order.data[field];
        }
        $('#search-form').on('submit', function (ev) {
            ev.preventDefault();
            var code = $('#search').blur().val();

            $.get('{{route('admin.reports.create')}}', {code:code})
                .done(function(response) {
                    console.log('Order data received:', response);
                    scanned(response);
                })
                .fail(function(xhr, status, error) {
                    console.error('Error fetching order:', error);
                    if (xhr.status === 404) {
                        alert('Order not found with code: ' + code);
                    } else {
                        alert('Error fetching order: ' + error);
                    }
                });

            return false;
        });

        function saveThis() {
            var codes = uniqueness.concat(duplicates.map(order => order.id)).join(',');
            var url = '{{route('admin.reports.store')}}';
            var method = 'POST';
            if ({{isset($report)?1:0}}) {
                url = '{{route('admin.reports.update', $report->id??0)}}';
                method = 'PUT';
            }
            var couriers = new Set();
            $('.card-body table tbody tr:not(:last-child)').each(function (index, tr) {
                couriers.add($(tr).find('td:nth-child(6)').text());
            });
            couriers = Array.from(couriers).filter((item) => item != 'N/A').join(', ').trim(', ');
            var courier = 'N/A';
            if (couriers.length) {
                courier = couriers;
            }
            var statuses = new Set();
            $('.card-body table tbody tr:not(:last-child)').each(function (index, tr) {
                statuses.add($(tr).find('td:nth-child(7)').text());
            });
            statuses = Array.from(statuses).filter((item) => item != 'N/A').join(', ').trim(', ');
            var status = 'N/A';
            if (statuses.length) {
                status = statuses;
            }
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _method: method,
                    _token: '{{csrf_token()}}',
                    codes: codes,
                    orders: uniqueness.length+duplicates.length,
                    products: quantity,
                    courier: courier,
                    status: status,
                    total: amount,
                },
                success: function (response, status, xhr) {
                    if ({{isset($report)?1:0}})
                        $.notify('Report updated successfully', 'success');
                    else
                        $.notify('Report saved successfully', 'success');

                    window.location.href = '{{route('admin.reports.index')}}';
                },
            });
        }

        function scanned(order) {
            console.log('scanned function called with:', order);
            $('#search').focus().val('');
            if (! order || uniqueness.includes(order.id)) {
                console.log('Order not found or already exists');
                return;
            }
            uniqueness.push(order.id);
            if (phones.includes(order.phone)) {
                duplicates.push(order);

                var tr = `
                    <tr data-id="${order.id}">
                        <td><a target="_blank" href="/admin/orders/${order.id}">${order.id}</a></td>
                        <td><strong>${order.name}</strong><br><small class="text-muted">${order.phone}</small></td>
                        <td>${order.address}</td>
                        <td>${order.note ?? 'N/A'}</td>
                        <td>${order.data.courier ?? 'N/A'}</td>
                        <td>${order.status}</td>
                                            <td>${getOrderAmount(order, 'subtotal')}</td>
                    <td>${getOrderAmount(order, 'shipping_cost')}</td>
                    <td>${parseInt(getOrderAmount(order, 'subtotal'))+parseInt(getOrderAmount(order, 'shipping_cost'))}</td>
                        <td style="width: 225px;">
                            <div class="d-flex justify-content-center">
                                <button type="button" onclick="keep(${order.id})" class="mr-1 btn btn-primary btn-sm">Keep</button>
                                <button type="button" onclick="remove(${order.id})" class="ml-1 d-none btn btn-danger btn-sm">Remove</button>
                            </div>
                        </td>
                    </tr>
                `;

                $('.card-header table tbody').prepend(tr);
            } else manageOrder(order);
            phones.push(order.phone);

            if (duplicates.length) {
                $('.card-header .table-responsive').show();
            } else {
                $('.card-header .table-responsive').hide();
            }
        }

        @foreach($orders ?? [] as $order)
            scanned({!! json_encode($order) !!});
        @endforeach

        function keep(id) {
            var order = duplicates.find(order => order.id == id);
            remove(id);
            manageOrder(order);
        }

        function remove(id) {
            var order = duplicates.find(order => order.id == id);
            duplicates.splice(duplicates.indexOf(order), 1);
            $('.card-header table tbody tr[data-id="'+id+'"]').remove();
            // uniqueness.splice(uniqueness.indexOf(order.id), 1);

            if (duplicates.length) {
                $('.card-header .table-responsive').show();
            } else {
                $('.card-header .table-responsive').hide();
            }
        }

        function manageOrder(order) {
            subtotal += parseInt(getOrderAmount(order, 'subtotal'));
            shipping += parseInt(getOrderAmount(order, 'shipping_cost'));
            total += parseInt(getOrderAmount(order, 'subtotal'))+parseInt(getOrderAmount(order, 'shipping_cost'));

            var tr = `
                <tr data-id="${order.id}" class="${phones.includes(order.phone) ? 'border border-danger' : ''}">
                    <td>${1+$('.card-body table tbody tr').length}</td>
                    <td><a target="_blank" href="/admin/orders/${order.id}">${order.id}</a></td>
                    <td><strong>${order.name}</strong><br><small class="text-muted">${order.phone}</small></td>
                    <td>${order.address}</td>
                    <td>${order.note ?? 'N/A'}</td>
                    <td>${order.data.courier ?? 'N/A'}</td>
                    <td>${order.status}</td>
                    <td>${getOrderAmount(order, 'subtotal')}</td>
                    <td>${getOrderAmount(order, 'shipping_cost')}</td>
                    <td>${parseInt(getOrderAmount(order, 'subtotal'))+parseInt(getOrderAmount(order, 'shipping_cost'))}</td>
                </tr>
            `;
            $('.card-body table tbody').prepend(tr);

            $('.card-body table tbody tr:not(:last-child)').each(function (index, tr) {
                $(tr).find('td:first-child').text(index + 1);
            });

            if (! $('.card-body table tbody tr:last-child').hasClass('summary')) {
                $('.card-body table tbody').append('<tr class="summary"><th colspan="7" class="text-right">Total</th><th>'+subtotal+'</th><th>'+shipping+'</th><th>'+total+'</th></tr>');
            } else {
                $('.card-body table tbody tr:last-child').find('th:nth-child(2)').text(subtotal);
                $('.card-body table tbody tr:last-child').find('th:nth-child(3)').text(shipping);
                $('.card-body table tbody tr:last-child').find('th:nth-child(4)').text(total);
            }

            // ## //
            if ($('.card-footer table tbody tr:last-child').hasClass('summary')) {
                $('.card-footer table tbody tr:last-child').remove();
            }

            for (var key in order.products) {
                var product = order.products[key];
                var tr = $('.card-footer table tbody tr[data-id="'+product.id+'"]');

                quantity += parseInt(product.quantity);
                // Use retail price when available (retail pricing is enabled), otherwise use wholesale price
                var productTotal = (isOninda && product.retail_price && product.retail_price > 0) ?
                    (product.retail_price * (product.quantity || 1)) :
                    (product.total || 0);
                amount += parseInt(productTotal);

                if (tr.length) {
                    tr.find('td:nth-child(2)').text(parseInt(tr.find('td:nth-child(2)').text()) + parseInt(product.quantity));
                    tr.find('td:nth-child(3)').text(parseInt(tr.find('td:nth-child(3)').text()) + parseInt(productTotal));
                } else {
                    var tr = `
                        <tr data-id="${product.id}">
                            <td><a target="_blank" href="/products/${product.slug}">${product.name}</a></td>
                            <td>${product.quantity}</td>
                            <td>${productTotal}</td>
                        </tr>
                    `;

                    $('.card-footer table tbody').append(tr);
                }
            }

            if (! $('.card-footer table tbody tr:last-child').hasClass('summary')) {
                $('.card-footer table tbody').append('<tr class="summary"><th class="text-right">Total</th><th>'+quantity+'</th><th>'+amount+'</th></tr>');
            }
        }
    </script>
@endpush
