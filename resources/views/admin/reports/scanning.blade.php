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
                                    <th>Name</th>
                                    <th>Phone</th>
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
                                    <th>Name</th>
                                    <th>Phone</th>
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
            var printContents = document.getElementById('section-to-print').innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

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
                couriers.add($(tr).find('td:nth-child(5)').text());
            });
            couriers = Array.from(couriers).filter((item) => item != 'N/A').join(', ').trim(', ');
            var courier = 'N/A';
            if (couriers.length) {
                courier = couriers;
            }
            var statuses = new Set();
            $('.card-body table tbody tr:not(:last-child)').each(function (index, tr) {
                statuses.add($(tr).find('td:nth-child(6)').text());
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
                        <td><a target="_blank" href="{{route('admin.orders.show', '')}}/${order.id}">${order.id}</a></td>
                        <td>${order.name}</td>
                        <td>${order.phone}</td>
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
                    <td><a target="_blank" href="{{route('admin.orders.show', '')}}/${order.id}">${order.id}</a></td>
                    <td>${order.name}</td>
                    <td>${order.phone}</td>
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
                $('.card-body table tbody').append('<tr class="summary"><th colspan="8" class="text-right">Total</th><th>'+subtotal+'</th><th>'+shipping+'</th><th>'+total+'</th></tr>');
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
                            <td><a target="_blank" href="{{route('products.show', '')}}/${product.slug}">${product.name}</a></td>
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
