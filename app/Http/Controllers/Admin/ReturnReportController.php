<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ProductReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class ReturnReportController extends Controller
{
    private const array RETURN_STATUSES = [
        'RETURNED',
        'PAID_RETURN',
        'RETURN_RECEIVED',
        'PAID_RETURN_RCV',
    ];

    /**
     * Show the return report page based on returned_at date.
     */
    public function index(Request $request)
    {
        $start = Date::parse($request->get('start_d', now()));
        $end = Date::parse($request->get('end_d', now()));

        $report = $this->generateReturnReport($start->format('Y-m-d'), $end->format('Y-m-d'));

        // Generate returned products report for the selected date range
        $productStatus = $request->get('product_status', 'ALL');
        $statuses = $productStatus === 'ALL' ? self::RETURN_STATUSES : [$productStatus];

        $returnedProductsData = (new ProductReportService)->generateProductsReport(
            $start,
            $end,
            $statuses,
            'returned_at'
        );

        return view('admin.reports.return', compact(
            'report',
            'start',
            'end',
            'returnedProductsData'
        ));
    }

    /**
     * Generate return report for the given date range based on returned_at
     */
    private function generateReturnReport(string $startDate, string $endDate): array
    {
        $orders = Order::whereNotNull('returned_at')
            ->whereIn('status', self::RETURN_STATUSES)
            ->whereBetween(DB::raw('DATE(returned_at)'), [$startDate, $endDate])
            ->get();

        $totalReturned = $orders->count();

        $statusBreakdown = $orders->groupBy('status')->map(function ($group) {
            $totalSubtotal = $group->sum(fn ($order) => $order->data['subtotal'] ?? 0);

            $totalPurchaseCost = $group->sum(fn ($order) => (isset($order->data['purchase_cost']) && $order->data['purchase_cost']) ? $order->data['purchase_cost'] : ($order->data['subtotal'] ?? 0));

            return [
                'count' => $group->count(),
                'total_subtotal' => $totalSubtotal,
                'total_purchase_cost' => $totalPurchaseCost,
            ];
        })->all();

        // Ensure keys for all return statuses always exist
        foreach (self::RETURN_STATUSES as $status) {
            if (! isset($statusBreakdown[$status])) {
                $statusBreakdown[$status] = [
                    'count' => 0,
                    'total_subtotal' => 0,
                    'total_purchase_cost' => 0,
                ];
            }
        }

        $dailyBreakdown = $orders->groupBy(fn ($order) => $order->returned_at ? Date::parse($order->returned_at)->format('Y-m-d') : '')
            ->reject(fn ($group, $key) => empty($key))
            ->map(function ($group) {
                $totalSubtotal = $group->sum(fn ($order) => $order->data['subtotal'] ?? 0);

                $totalPurchaseCost = $group->sum(fn ($order) => (isset($order->data['purchase_cost']) && $order->data['purchase_cost']) ? $order->data['purchase_cost'] : ($order->data['subtotal'] ?? 0));

                return [
                    'total' => $group->count(),
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
                'returned' => $group->where('status', 'RETURNED')->count(),
                'paid_return' => $group->where('status', 'PAID_RETURN')->count(),
                'return_received' => $group->where('status', 'RETURN_RECEIVED')->count(),
                'paid_return_rcv' => $group->where('status', 'PAID_RETURN_RCV')->count(),
                'total_subtotal' => $totalSubtotal,
                'total_purchase_cost' => $totalPurchaseCost,
            ];
        });

        return [
            'total_returned' => $totalReturned,
            'status_breakdown' => $statusBreakdown,
            'daily_breakdown' => $dailyBreakdown,
            'courier_breakdown' => $courierBreakdown,
        ];
    }
}
