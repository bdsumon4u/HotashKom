<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use App\Services\ProductReportService;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $_start = \Illuminate\Support\Facades\Date::parse(request('start_d'));
        $start = $_start->format('Y-m-d');
        $_end = \Illuminate\Support\Facades\Date::parse(request('end_d'));
        $end = $_end->format('Y-m-d');

        $totalSQL = 'COUNT(*) as order_count, SUM(JSON_UNQUOTE(JSON_EXTRACT(data, "$.subtotal"))) + SUM(JSON_UNQUOTE(JSON_EXTRACT(data, "$.shipping_cost"))) - COALESCE(SUM(JSON_UNQUOTE(JSON_EXTRACT(data, "$.discount"))), 0) as total_amount';

        $orderQ = Order::query()
            ->whereBetween(request('date_type', 'status_at'), [
                $_start->startOfDay()->toDateTimeString(),
                $_end->endOfDay()->toDateTimeString(),
            ]);

        if (request('staff_id')) {
            $orderQ->where('admin_id', request('staff_id'));
        }

        // Use the service to generate products report
        $productsData = (new ProductReportService)->generateProductsReport(
            $_start,
            $_end,
            ['CONFIRMED', 'INVOICED', 'SHIPPING'],
            request('date_type', 'status_at'),
            request('staff_id')
        );

        $products = $productsData['products'];
        $productInOrders = $productsData['productInOrders'];

        $data = (clone $orderQ)
            ->selectRaw($totalSQL)
            ->first();
        $orders['Total'] = $data->order_count;
        $amounts['Total'] = (float) ($data->total_amount ?? 0);

        $data = (clone $orderQ)->where('type', Order::ONLINE)
            ->selectRaw($totalSQL)
            ->first();
        $orders['Online'] = $data->order_count;
        $amounts['Online'] = (float) ($data->total_amount ?? 0);

        $data = (clone $orderQ)->where('type', Order::MANUAL)
            ->selectRaw($totalSQL)
            ->first();
        $orders['Manual'] = $data->order_count;
        $amounts['Manual'] = (float) ($data->total_amount ?? 0);

        foreach (config('app.orders', []) as $status) {
            $data = (clone $orderQ)->where('status', $status)
                ->selectRaw($totalSQL)
                ->first();
            $orders[$status] = $data->order_count ?? 0;
            $amounts[$status] = (float) ($data->total_amount ?? 0);
        }

        // If retail pricing is enabled, recalculate amounts using retail pricing
        if (isOninda() && ! config('app.resell')) {
            $this->recalculateAmountsWithRetailPricing($orderQ, $amounts);
        }

        $query = DB::table('admins')
            ->select('admins.id', 'admins.name', 'admins.email', 'admins.role_id', 'admins.is_active', DB::raw('MAX(sessions.last_activity) as last_activity'))
            ->leftJoin('sessions', 'sessions.userable_id', '=', 'admins.id')
            ->where('sessions.userable_type', Admin::class)
            ->groupBy('admins.id', 'admins.name', 'admins.email', 'admins.role_id', 'admins.is_active'); // Add all selected non-aggregated columns to GROUP BY

        // Get online admins
        $online = $query->having('last_activity', '>=', now()->subMinutes(5)->timestamp)->get();

        // Get offline admins
        $offline = DB::table('admins')->whereNotIn('email', $online->pluck('email'))->get();
        $staffs = compact('online', 'offline');

        $productsCount = Product::whereNull('parent_id')->count();
        $inactiveProducts = Product::whereIsActive(0)->whereNull('parent_id')->get();
        $lowStockProducts = Product::whereShouldTrack(1)->where('stock_count', '<', 10)->get();

        // Get total pending withdrawal amount
        $pendingWithdrawalAmount = cacheMemo()->remember('pending_withdrawal_amount', 300, fn (): float|int => abs(\Bavix\Wallet\Models\Transaction::where('type', 'withdraw')
            ->where('confirmed', false)
            ->sum('amount')));

        return view('admin.dashboard', compact('staffs', 'products', 'productInOrders', 'productsCount', 'orders', 'amounts', 'inactiveProducts', 'lowStockProducts', 'start', 'end', 'pendingWithdrawalAmount'));
    }

    /**
     * Recalculate order amounts using retail pricing when retail pricing is enabled
     */
    private function recalculateAmountsWithRetailPricing($orderQ, &$amounts): void
    {
        // Get all orders for recalculation
        $allOrders = (clone $orderQ)->get();

        // Reset amounts
        $amounts = array_fill_keys(array_keys($amounts), 0);

        foreach ($allOrders as $order) {
            $retailAmounts = $order->getRetailAmounts();
            // Fallback: if retail_total is not available, use wholesale total
            $totalAmount = $retailAmounts['retail_total'] ?? (float) ($order->data['subtotal'] ?? 0) + (float) ($order->data['shipping_cost'] ?? 0) - (float) ($order->data['discount'] ?? 0);

            // Ensure totalAmount is numeric
            $totalAmount = (float) $totalAmount;

            // Add to total
            $amounts['Total'] += $totalAmount;

            // Add to type-specific totals
            if ($order->type === Order::ONLINE) {
                $amounts['Online'] += $totalAmount;
            } elseif ($order->type === Order::MANUAL) {
                $amounts['Manual'] += $totalAmount;
            }

            // Add to status-specific totals
            $amounts[$order->status] += $totalAmount;
        }
    }
}
