<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Carbon;
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
        $_start = Carbon::parse(request('start_d'));
        $start = $_start->format('Y-m-d');
        $_end = Carbon::parse(request('end_d'));
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

        $productInOrders[] = [];

        $products = (clone $orderQ)->get()
            ->whereIn('status', ['CONFIRMED', 'INVOICED', 'SHIPPING'])
            ->flatMap(function ($order) use (&$productInOrders) {
                $products = json_decode(json_encode($order->products, JSON_UNESCAPED_UNICODE), true);

                foreach ($products as $product) {
                    $productInOrders[$product['name']][$order->id] = 1 + ($productInOrders[$product['name']][$order->id] ?? 0);
                }

                return $products;
            })
            ->groupBy('id')->mapWithKeys(fn($item, $id) => [$id => [
                'name' => $item->random()['name'],
                'slug' => $item->random()['slug'],
                'quantity' => $item->sum('quantity'),
                'total' => $item->sum('total'),
            ]])->sortByDesc('quantity')->toArray();

        $data = (clone $orderQ)
            ->selectRaw($totalSQL)
            ->first();
        $orders['Total'] = $data->order_count;
        $amounts['Total'] = $data->total_amount;

        $data = (clone $orderQ)->where('type', Order::ONLINE)
            ->selectRaw($totalSQL)
            ->first();
        $orders['Online'] = $data->order_count;
        $amounts['Online'] = $data->total_amount;

        $data = (clone $orderQ)->where('type', Order::MANUAL)
            ->selectRaw($totalSQL)
            ->first();
        $orders['Manual'] = $data->order_count;
        $amounts['Manual'] = $data->total_amount;

        foreach (config('app.orders', []) as $status) {
            $data = (clone $orderQ)->where('status', $status)
                ->selectRaw($totalSQL)
                ->first();
            $orders[$status] = $data->order_count ?? 0;
            $amounts[$status] = $data->total_amount ?? 0;
        }

        $query = DB::table('admins')
            ->select('admins.id', 'admins.name', 'admins.email', 'admins.role_id', DB::raw('MAX(sessions.last_activity) as last_activity'))
            ->leftJoin('sessions', 'sessions.userable_id', '=', 'admins.id')
            ->where('sessions.userable_type', Admin::class)
            ->groupBy('admins.id', 'admins.name', 'admins.email', 'admins.role_id'); // Add all selected non-aggregated columns to GROUP BY

        // Get online admins
        $online = $query->having('last_activity', '>=', now()->subMinutes(5)->timestamp)->get();

        // Get offline admins
        $offline = DB::table('admins')->whereNotIn('email', $online->pluck('email'))->get();
        $staffs = compact('online', 'offline');

        $productsCount = Product::whereNull('parent_id')->count();
        $inactiveProducts = Product::whereIsActive(0)->whereNull('parent_id')->get();
        $lowStockProducts = Product::whereShouldTrack(1)->where('stock_count', '<', 10)->get();

        return view('admin.dashboard', compact('staffs', 'products', 'productInOrders', 'productsCount', 'orders', 'amounts', 'inactiveProducts', 'lowStockProducts', 'start', 'end'));
    }
}
