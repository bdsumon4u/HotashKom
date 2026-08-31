<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ProductReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class ShipmentReportController extends Controller
{
    private const array REPORT_STATUSES = [
        'SHIPPING',
        'DELIVERED',
        'RETURNED',
        'PAID_RETURN',
        'RETURN_RECEIVED',
        'PAID_RETURN_RCV',
    ];

    /**
     * Show the shipment report page
     */
    public function index(Request $request)
    {
        $start = Date::parse($request->get('start_d', now()));
        $end = Date::parse($request->get('end_d', now()));

        $report = $this->generateReport($start->format('Y-m-d'), $end->format('Y-m-d'));

        // Generate shipped products report for the selected date range
        $productStatus = $request->get('product_status', 'ALL');
        $statuses = $productStatus === 'ALL' ? self::REPORT_STATUSES : [$productStatus];

        $shippedProductsData = (new ProductReportService)->generateProductsReport(
            $start,
            $end,
            $statuses,
            'shipped_at'
        );

        return view('admin.reports.shipment', compact(
            'report',
            'start',
            'end',
            'shippedProductsData'
        ));
    }

    /**
     * Generate shipment report for the given date range
     */
    private function generateReport($startDate, $endDate): array
    {
        $orders = Order::whereNotNull('shipped_at')
            ->whereBetween(DB::raw('DATE(shipped_at)'), [$startDate, $endDate])
            ->get();

        $totalShipped = $orders->count();

        $statusBreakdown = $orders->groupBy('status')->map(function ($group) {
            $totalSubtotal = $group->sum(fn ($order) => $order->data['subtotal'] ?? 0);

            $totalPurchaseCost = $group->sum(fn ($order) => (isset($order->data['purchase_cost']) && $order->data['purchase_cost']) ? $order->data['purchase_cost'] : ($order->data['subtotal'] ?? 0));

            return [
                'count' => $group->count(),
                'total_subtotal' => $totalSubtotal,
                'total_purchase_cost' => $totalPurchaseCost,
            ];
        })->all();

        // Ensure keys for all report statuses always exist
        foreach (self::REPORT_STATUSES as $status) {
            if (! isset($statusBreakdown[$status])) {
                $statusBreakdown[$status] = [
                    'count' => 0,
                    'total_subtotal' => 0,
                    'total_purchase_cost' => 0,
                ];
            }
        }

        $dailyBreakdown = $orders->groupBy(fn ($order) => $order->shipped_at->format('Y-m-d'))->map(function ($group) {
            $totalSubtotal = $group->sum(fn ($order) => $order->data['subtotal'] ?? 0);

            $totalPurchaseCost = $group->sum(fn ($order) => (isset($order->data['purchase_cost']) && $order->data['purchase_cost']) ? $order->data['purchase_cost'] : ($order->data['subtotal'] ?? 0));

            return [
                'total' => $group->count(),
                'shipping' => $group->where('status', 'SHIPPING')->count(),
                'delivered' => $group->where('status', 'DELIVERED')->count(),
                'returned' => $group->where('status', 'RETURNED')->count(),
                'paid_return' => $group->where('status', 'PAID_RETURN')->count(),
                'return_received' => $group->where('status', 'RETURN_RECEIVED')->count(),
                'paid_return_rcv' => $group->where('status', 'PAID_RETURN_RCV')->count(),
                'total_subtotal' => $totalSubtotal,
                'total_purchase_cost' => $totalPurchaseCost,
            ];
        });

        $courierBreakdown = $orders->groupBy(fn ($order) => $order->data['courier'] ?? 'Other')->map(function ($group) {
            $totalSubtotal = $group->sum(fn ($order) => $order->data['subtotal'] ?? 0);

            $totalPurchaseCost = $group->sum(fn ($order) => (isset($order->data['purchase_cost']) && $order->data['purchase_cost']) ? $order->data['purchase_cost'] : ($order->data['subtotal'] ?? 0));

            return [
                'total' => $group->count(),
                'shipping' => $group->where('status', 'SHIPPING')->count(),
                'delivered' => $group->where('status', 'DELIVERED')->count(),
                'returned' => $group->where('status', 'RETURNED')->count(),
                'paid_return' => $group->where('status', 'PAID_RETURN')->count(),
                'return_received' => $group->where('status', 'RETURN_RECEIVED')->count(),
                'paid_return_rcv' => $group->where('status', 'PAID_RETURN_RCV')->count(),
                'total_subtotal' => $totalSubtotal,
                'total_purchase_cost' => $totalPurchaseCost,
            ];
        });

        return [
            'total_shipped' => $totalShipped,
            'status_breakdown' => $statusBreakdown,
            'daily_breakdown' => $dailyBreakdown,
            'courier_breakdown' => $courierBreakdown,
        ];
    }
}
