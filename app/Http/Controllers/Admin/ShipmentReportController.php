<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ShipmentReportController extends Controller
{
    /**
     * Show the shipment report page
     */
    public function index(Request $request)
    {
        $start = Carbon::parse($request->get('start_d', now()))->format('Y-m-d');
        $end = Carbon::parse($request->get('end_d', now()))->format('Y-m-d');

        $report = $this->generateReport($start, $end);

        return view('admin.reports.shipment', compact('report', 'start', 'end'));
    }

    /**
     * Generate shipment report for the given date range
     */
    private function generateReport($startDate, $endDate)
    {
        $orders = Order::whereNotNull('shipped_at')
            ->whereBetween(DB::raw('DATE(shipped_at)'), [$startDate, $endDate])
            ->get();

        $totalShipped = $orders->count();

        $statusBreakdown = $orders->groupBy('status')->map(function ($group) {
            $totalSubtotal = $group->sum(function ($order) {
                return $order->data['subtotal'] ?? 0;
            });

            $totalPurchaseCost = $group->sum(function ($order) {
                return $order->data['purchase_cost'] ?? $order->data['subtotal'] ?? 0;
            });

            return [
                'count' => $group->count(),
                'total_subtotal' => $totalSubtotal,
                'total_purchase_cost' => $totalPurchaseCost,
            ];
        })->all();

        // Ensure keys for SHIPPING, DELIVERED, RETURNED always exist
        foreach (['SHIPPING', 'DELIVERED', 'RETURNED'] as $status) {
            if (! isset($statusBreakdown[$status])) {
                $statusBreakdown[$status] = [
                    'count' => 0,
                    'total_subtotal' => 0,
                    'total_purchase_cost' => 0,
                ];
            }
        }

        $dailyBreakdown = $orders->groupBy(function ($order) {
            return $order->shipped_at->format('Y-m-d');
        })->map(function ($group) {
            $totalSubtotal = $group->sum(function ($order) {
                return $order->data['subtotal'] ?? 0;
            });

            $totalPurchaseCost = $group->sum(function ($order) {
                return $order->data['purchase_cost'] ?? $order->data['subtotal'] ?? 0;
            });

            return [
                'total' => $group->count(),
                'shipping' => $group->where('status', 'SHIPPING')->count(),
                'delivered' => $group->where('status', 'DELIVERED')->count(),
                'returned' => $group->where('status', 'RETURNED')->count(),
                'total_subtotal' => $totalSubtotal,
                'total_purchase_cost' => $totalPurchaseCost,
            ];
        });

        $courierBreakdown = $orders->groupBy(function ($order) {
            return $order->data['courier'] ?? 'Other';
        })->map(function ($group) {
            $totalSubtotal = $group->sum(function ($order) {
                return $order->data['subtotal'] ?? 0;
            });

            $totalPurchaseCost = $group->sum(function ($order) {
                return $order->data['purchase_cost'] ?? $order->data['subtotal'] ?? 0;
            });

            return [
                'total' => $group->count(),
                'shipping' => $group->where('status', 'SHIPPING')->count(),
                'delivered' => $group->where('status', 'DELIVERED')->count(),
                'returned' => $group->where('status', 'RETURNED')->count(),
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
