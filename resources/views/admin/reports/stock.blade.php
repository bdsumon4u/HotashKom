@extends('layouts.light.master')
@section('title', 'Stock Report')

@section('breadcrumb-title')
<h3>Reports</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Reports</li>
<li class="breadcrumb-item active">Stock</li>
@endsection

@section('breadcrumb-right')
    @if (config('accounting.enabled', true) && auth('admin')->user()?->is('admin'))
        <div class="d-flex justify-content-end align-items-center">
            <a href="{{ route('admin.accounting.adjustments.create') }}" class="btn btn-warning btn-sm font-weight-bold text-dark shadow-sm">
                <i class="fa fa-sliders mr-1"></i> Stock Adjustment (Damage / Gift)
            </a>
        </div>
    @endif
@endsection

@push('styles')
<style>
.stock-tabs-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
}
.stock-group-nav {
    display: flex;
    flex-wrap: nowrap;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    gap: 8px;
    padding: 6px 4px 8px 4px;
    scrollbar-width: thin;
}
.stock-group-nav::-webkit-scrollbar {
    height: 4px;
}
.stock-group-nav::-webkit-scrollbar-track {
    background: #f1f5f9;
}
.stock-group-nav::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.stock-group-nav .nav-link {
    display: inline-flex;
    align-items: center;
    border-radius: 20px;
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 500;
    color: #475569;
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.2s ease-in-out;
    cursor: pointer;
}
.stock-group-nav .nav-link:hover {
    background-color: #f1f5f9;
    color: #1e293b;
    border-color: #cbd5e1;
    transform: translateY(-1px);
}
.stock-group-nav .nav-link.active {
    background: linear-gradient(135deg, #7366ff 0%, #5c50ee 100%) !important;
    color: #ffffff !important;
    border-color: #5c50ee !important;
    box-shadow: 0 3px 8px rgba(115, 102, 255, 0.35);
}
.stock-group-nav .nav-link .badge {
    margin-left: 6px;
    font-size: 11px;
    padding: 2px 7px;
    border-radius: 10px;
    transition: all 0.2s ease-in-out;
}
.stock-group-nav .nav-link.active .badge {
    background-color: #ffffff !important;
    color: #5c50ee !important;
    font-weight: 700;
}
.stock-group-nav .nav-link:not(.active) .badge {
    background-color: #e2e8f0;
    color: #475569;
}

@media print {
    html, body {
        margin: 0 !important;
        padding: 0 !important;
    }
    .main-nav, .stock-tabs-card, .no-print {
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
    <!-- Stock Summary Cards -->
    <div class="col-md-4">
        <div class="text-white rounded-sm shadow-sm card bg-primary">
            <div class="px-3 py-2 card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0" id="card-total-stock-count">{{ $totalStockCount ?? 0 }}</h6>
                        <small>Total Stock Count</small>
                    </div>
                    <div class="align-self-center">
                        <i data-feather="package" class="feather-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="text-white rounded-sm shadow-sm card bg-info">
            <div class="px-3 py-2 card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0" id="card-total-purchase-value">{!! $totalPurchaseValue !!}</h6>
                        <small>Total Purchase Value</small>
                    </div>
                    <div class="align-self-center">
                        <i data-feather="shopping-cart" class="feather-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="text-white rounded-sm shadow-sm card bg-success">
            <div class="px-3 py-2 card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0" id="card-total-sell-value">{!! $totalSellValue !!}</h6>
                        <small>Total Sell Value</small>
                    </div>
                    <div class="align-self-center">
                        <i data-feather="dollar-sign" class="feather-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Stock Summary Cards -->

    <!-- Stock Group Tabs Card -->
    <div class="mx-auto col-md-12 mb-3 no-print">
        <div class="stock-tabs-card shadow-sm p-2">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="d-flex align-items-center py-1 px-2">
                    <span class="font-weight-bold text-dark" style="font-size: 13px;">
                        <i class="fa fa-layer-group text-primary mr-1"></i> Stock Groups:
                    </span>
                </div>
                <div class="flex-grow-1 overflow-hidden ml-md-2">
                    <ul class="nav stock-group-nav" id="stockGroupTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ $selectedGroup === 'all' ? 'active' : '' }}" 
                               href="javascript:void(0)" 
                               data-group="all"
                               id="tab-group-all">
                                <span>All Stocks</span>
                                <span class="badge">{{ $products->count() }}</span>
                            </a>
                        </li>
                        @foreach($stockGroups as $group)
                            @php
                                $groupKey = strtolower($group);
                                $count = $groupCounts[$groupKey] ?? 0;
                                $isActive = (strtolower($selectedGroup) === $groupKey);
                            @endphp
                            <li class="nav-item">
                                <a class="nav-link {{ $isActive ? 'active' : '' }}" 
                                   href="javascript:void(0)" 
                                   data-group="{{ $group }}"
                                   id="tab-group-{{ Str::slug($group) }}">
                                    <span>{{ $group }}</span>
                                    <span class="badge">{{ $count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- End Stock Group Tabs Card -->

    <div class="mx-auto col-md-12">
        <div class="reports-table">
            <div class="shadow-sm card rounded-0">
                <div class="p-3 card-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Stock Report</strong>
                        <span class="badge badge-primary ml-2 px-2 py-1" id="active-group-indicator">
                            {{ $selectedGroup === 'all' ? 'All Stocks' : $selectedGroup }}
                        </span>
                    </div>
                    @if (config('accounting.enabled', true) && auth('admin')->user()?->is('admin'))
                        <a href="{{ route('admin.accounting.adjustments.create') }}" class="btn btn-warning btn-xs font-weight-bold text-dark no-print">
                            <i class="fa fa-sliders mr-1"></i> Adjust / Write-Off Stock
                        </a>
                    @endif
                </div>
                <div class="p-3 card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable" id="stock-report-table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="min-width: 50px;">ID</th>
                                    <th style="min-width: 50px;">Name</th>
                                    <th style="min-width: 50px;">Stock</th>
                                    <th style="min-width: 50px;">Buy</th>
                                    <th style="min-width: 50px;">Sell</th>
                                </tr>
                            </thead>
                            <tbody id="stock-report-tbody">
                                @foreach($products as $product)
                                    <tr class="stock-row"
                                        data-stock-group="{{ $product->stock_group }}"
                                        data-stock-count="{{ (int) $product->stock_count }}"
                                        data-buy-total="{{ (float) ($product->average_purchase_price ?? 0) * (int) ($product->stock_count ?? 0) }}"
                                        data-sell-total="{{ (float) ($product->selling_price ?? 0) * (int) ($product->stock_count ?? 0) }}">
                                        <td>{{ $product->id }}</td>
                                        <td>
                                            <a href="{{ route('admin.products.edit', $product->parent_id ?? $product->id) }}" target="_blank">{{ $product->var_name }}</a>
                                            <span class="badge badge-light border text-muted ml-1 font-weight-normal py-1 px-2" style="font-size: 0.75rem;">
                                                <i class="fa fa-tag text-primary mr-1"></i>{{ $product->stock_group }}
                                            </span>
                                            <div class="text-muted small">
                                                (Buy: {!! theMoney($product->average_purchase_price ?? 0) !!},
                                                Sell: {!! theMoney($product->selling_price ?? 0) !!})
                                            </div>
                                        </td>
                                        <td class="font-weight-bold">{{ $product->stock_count }}</td>
                                        <td>{!! theMoney((float) ($product->average_purchase_price ?? 0) * (int) ($product->stock_count ?? 0)) !!}</td>
                                        <td>{!! theMoney((float) ($product->selling_price ?? 0) * (int) ($product->stock_count ?? 0)) !!}</td>
                                    </tr>
                                @endforeach
                                <tr id="empty-stock-row" style="display: none;">
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fa fa-inbox fa-2x mb-2 d-block text-secondary"></i>
                                        <span>No products found in this stock group.</span>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold bg-light">
                                    <td colspan="3" class="text-right">
                                        Total (<span id="footer-group-name">{{ $selectedGroup === 'all' ? 'All Stocks' : $selectedGroup }}</span>)
                                    </td>
                                    <td id="footer-total-buy">{!! theMoney($products->sum(fn ($product) => (float) ($product->average_purchase_price ?? 0) * (int) ($product->stock_count ?? 0))) !!}</td>
                                    <td id="footer-total-sell">{!! theMoney($products->sum(fn ($product) => (float) ($product->selling_price ?? 0) * (int) ($product->stock_count ?? 0))) !!}</td>
                                </tr>
                            </tfoot>
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
runWhenJQueryReady(function ($) {
    function formatMoney(amount) {
        return 'TK&nbsp;<span>' + Number(Math.round(amount)).toLocaleString('en-US') + '</span>';
    }

    function filterByGroup(group) {
        const isAll = (!group || group.toLowerCase() === 'all');
        const targetGroup = isAll ? 'all' : group.trim().toLowerCase();

        // Update active tab in the tab bar
        $('#stockGroupTabs .nav-link').each(function () {
            const tabGroup = String($(this).data('group') || '');
            const match = isAll 
                ? (tabGroup.toLowerCase() === 'all') 
                : (tabGroup.toLowerCase() === targetGroup);

            if (match) {
                $(this).addClass('active');
            } else {
                $(this).removeClass('active');
            }
        });

        let visibleCount = 0;
        let sumStockCount = 0;
        let sumBuyTotal = 0;
        let sumSellTotal = 0;

        const $rows = $('#stock-report-tbody tr.stock-row');
        $rows.each(function () {
            const rowGroup = String($(this).data('stock-group') || 'uncategorized').toLowerCase();
            const showRow = isAll || (rowGroup === targetGroup);

            if (showRow) {
                $(this).show();
                visibleCount++;
                sumStockCount += parseInt($(this).data('stock-count') || 0, 10);
                sumBuyTotal += parseFloat($(this).data('buy-total') || 0);
                sumSellTotal += parseFloat($(this).data('sell-total') || 0);
            } else {
                $(this).hide();
            }
        });

        if (visibleCount === 0) {
            $('#empty-stock-row').show();
        } else {
            $('#empty-stock-row').hide();
        }

        // Update Top Summary Cards
        $('#card-total-stock-count').text(sumStockCount.toLocaleString());
        $('#card-total-purchase-value').html(formatMoney(sumBuyTotal));
        $('#card-total-sell-value').html(formatMoney(sumSellTotal));

        // Update Table Footer Totals
        const displayGroupName = isAll ? 'All Stocks' : group;
        $('#footer-group-name').text(displayGroupName);
        $('#active-group-indicator').text(displayGroupName);
        $('#footer-total-buy').html(formatMoney(sumBuyTotal));
        $('#footer-total-sell').html(formatMoney(sumSellTotal));

        // Sync with browser URL without reloading
        const url = new URL(window.location.href);
        if (isAll) {
            url.searchParams.delete('group');
        } else {
            url.searchParams.set('group', group);
        }
        window.history.replaceState({}, '', url);
    }

    // Tab click handler
    $('#stockGroupTabs').on('click', '.nav-link', function (e) {
        e.preventDefault();
        const selected = $(this).data('group');
        filterByGroup(selected);
    });

    // Handle initial state on page load from URL query parameter or server variable
    const urlParams = new URLSearchParams(window.location.search);
    const initialGroup = urlParams.get('group') || '{{ $selectedGroup }}';
    if (initialGroup && initialGroup.toLowerCase() !== 'all') {
        filterByGroup(initialGroup);
    }
});
</script>
@endpush
