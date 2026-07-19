<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use App\Services\ProductReportService;
use Bavix\Wallet\Models\Transaction;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        $_start = Date::parse(request('start_d'));
        $start = $_start->format('Y-m-d');
        $_end = Date::parse(request('end_d'));
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
            ['CONFIRMED', 'PACKAGING', 'SHIPPING'],
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

        $staffs = cacheMemo()->remember('admin_staffs_online_offline', now()->addMinutes(1), function () {
            // Online/offline tracking requires the custom database session driver.
            // When SESSION_DRIVER is set to 'file', this feature is disabled.
            if (config('session.driver') !== 'custom') {
                return ['online' => collect(), 'offline' => collect()];
            }

            $query = DB::table('admins')
                ->select('admins.id', 'admins.name', 'admins.email', 'admins.role_id', 'admins.is_active', DB::raw('MAX(sessions.last_activity) as last_activity'))
                ->leftJoin('sessions', 'sessions.userable_id', '=', 'admins.id')
                ->where('sessions.userable_type', Admin::class)
                ->groupBy('admins.id', 'admins.name', 'admins.email', 'admins.role_id', 'admins.is_active');

            // Get online admins
            $online = $query->having('last_activity', '>=', now()->subMinutes(5)->timestamp)->get();

            // Get offline admins
            $offline = DB::table('admins')->whereNotIn('email', $online->pluck('email'))->get();

            return compact('online', 'offline');
        });

        $productsCount = cacheMemo()->remember('admin_products_count', now()->addMinutes(5), fn () => Product::whereNull('parent_id')->count());

        $inactiveProductsQuery = Product::whereIsActive(0)->whereNull('parent_id');
        $inactiveProductsCount = (clone $inactiveProductsQuery)->count();
        // $inactiveProducts = $inactiveProductsCount > 15
        //     ? $inactiveProductsQuery->get()
        //     : cacheMemo()->remember('admin_inactive_products', now()->addMinutes(5), fn () => $inactiveProductsQuery->get());

        $lowStockProductsQuery = Product::whereShouldTrack(1)->where('stock_count', '<', 10)->whereNull('parent_id');
        $lowStockProductsCount = (clone $lowStockProductsQuery)->count();
        // $lowStockProducts = $lowStockProductsCount > 20
        //     ? $lowStockProductsQuery->get()
        //     : cacheMemo()->remember('admin_low_stock_products', now()->addMinutes(5), fn () => $lowStockProductsQuery->get());

        // Get total pending withdrawal amount
        $pendingWithdrawalAmount = isOninda() && config('app.resell') ? cacheMemo()->remember('pending_withdrawal_amount', 300, fn (): float|int => abs(Transaction::where('type', 'withdraw')
            ->where('confirmed', false)
            ->sum('amount'))) : 0;

        $serverInfo = $this->getServerInfo();

        return view('admin.dashboard', compact('staffs', 'products', 'productInOrders', 'productsCount', 'orders', 'amounts', 'inactiveProductsCount', 'lowStockProductsCount', 'start', 'end', 'pendingWithdrawalAmount', 'serverInfo'));
    }

    /**
     * Get server information.
     */
    private function getServerInfo(): array
    {
        $info = [
            'os' => PHP_OS_FAMILY.' ('.PHP_OS.')',
            'php_version' => PHP_VERSION,
            'ip' => request()->server('SERVER_ADDR') ?? gethostbyname(gethostname()) ?? 'Unknown',
            'server_software' => request()->server('SERVER_SOFTWARE') ?? 'Unknown',
            'cpu_model' => 'Unknown',
            'cpu_cores' => 'Unknown',
            'ram_total' => 'Unknown',
            'ram_free' => 'Unknown',
            'ram_used' => 'Unknown',
            'ram_percentage' => 0,
            'disk_total' => 'Unknown',
            'disk_free' => 'Unknown',
            'disk_used' => 'Unknown',
            'disk_percentage' => 0,
            'db_version' => 'Unknown',
        ];

        // CPU & RAM details
        try {
            if (PHP_OS_FAMILY === 'Linux') {
                // Read /proc/cpuinfo
                if (@is_readable('/proc/cpuinfo')) {
                    $cpuinfo = @file_get_contents('/proc/cpuinfo');
                    if ($cpuinfo) {
                        $cores = substr_count($cpuinfo, 'processor');
                        if ($cores > 0) {
                            $info['cpu_cores'] = $cores;
                        }
                        if (preg_match('/model name\s+:\s+(.+)$/m', $cpuinfo, $matches)) {
                            $info['cpu_model'] = trim($matches[1]);
                        }
                    }
                }

                // Read /proc/meminfo
                if (@is_readable('/proc/meminfo')) {
                    $lines = @file('/proc/meminfo');
                    if ($lines) {
                        $totalRam = 0;
                        $freeRam = 0;
                        foreach ($lines as $line) {
                            if (preg_match('/^MemTotal:\s+(\d+)\s+kB/i', $line, $matches)) {
                                $totalRam = (int) $matches[1] * 1024;
                            }
                            if (preg_match('/^MemAvailable:\s+(\d+)\s+kB/i', $line, $matches)) {
                                $freeRam = (int) $matches[1] * 1024;
                            } elseif (preg_match('/^MemFree:\s+(\d+)\s+kB/i', $line, $matches)) {
                                if ($freeRam === 0) {
                                    $freeRam = (int) $matches[1] * 1024;
                                }
                            }
                        }
                        if ($totalRam > 0) {
                            $usedRam = $totalRam - $freeRam;
                            $info['ram_total'] = $this->formatBytes($totalRam);
                            $info['ram_free'] = $this->formatBytes($freeRam);
                            $info['ram_used'] = $this->formatBytes($usedRam);
                            $info['ram_percentage'] = round(($usedRam / $totalRam) * 100, 1);
                        }
                    }
                }
            } elseif (PHP_OS_FAMILY === 'Darwin') {
                // macOS helper commands if shell_exec is allowed
                if (function_exists('shell_exec')) {
                    $cpuModel = @shell_exec('sysctl -n machdep.cpu.brand_string');
                    $cpuCores = @shell_exec('sysctl -n hw.ncpu');
                    $totalRam = @shell_exec('sysctl -n hw.memsize');

                    if ($cpuModel) {
                        $info['cpu_model'] = trim($cpuModel);
                    }
                    if ($cpuCores) {
                        $info['cpu_cores'] = trim($cpuCores);
                    }
                    if ($totalRam) {
                        $totalRamBytes = (int) trim($totalRam);
                        $info['ram_total'] = $this->formatBytes($totalRamBytes);

                        // Try parsing vm_stat for free memory
                        $vmStat = @shell_exec('vm_stat');
                        if ($vmStat) {
                            $pageSize = 4096;
                            if (preg_match('/page size of (\d+) bytes/', $vmStat, $pageMatches)) {
                                $pageSize = (int) $pageMatches[1];
                            }
                            if (preg_match('/Pages free:\s+(\d+)/', $vmStat, $freeMatches) &&
                                preg_match('/Pages inactive:\s+(\d+)/', $vmStat, $inactiveMatches)) {
                                $freePages = (int) $freeMatches[1] + (int) $inactiveMatches[1];
                                $freeRamBytes = $freePages * $pageSize;
                                $usedRamBytes = $totalRamBytes - $freeRamBytes;
                                $info['ram_free'] = $this->formatBytes($freeRamBytes);
                                $info['ram_used'] = $this->formatBytes($usedRamBytes);
                                $info['ram_percentage'] = round(($usedRamBytes / $totalRamBytes) * 100, 1);
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore system parsing exceptions to prevent crash
        }

        // Disk Usage
        try {
            $diskPath = '/';
            if (PHP_OS_FAMILY === 'Windows') {
                $diskPath = 'C:';
            }
            $totalDisk = @disk_total_space($diskPath);
            $freeDisk = @disk_free_space($diskPath);
            if ($totalDisk !== false && $freeDisk !== false) {
                $usedDisk = $totalDisk - $freeDisk;
                $info['disk_total'] = $this->formatBytes($totalDisk);
                $info['disk_free'] = $this->formatBytes($freeDisk);
                $info['disk_used'] = $this->formatBytes($usedDisk);
                $info['disk_percentage'] = round(($usedDisk / $totalDisk) * 100, 1);
            }
        } catch (\Throwable $e) {
            // Ignore disk usage exception
        }

        // Database Version
        try {
            $dbVersion = DB::select('select version() as version');
            if (! empty($dbVersion) && isset($dbVersion[0]->version)) {
                $info['db_version'] = $dbVersion[0]->version;
            }
        } catch (\Throwable $e) {
            // Ignore DB exception
        }

        return $info;
    }

    /**
     * Format bytes into readable format
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
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
