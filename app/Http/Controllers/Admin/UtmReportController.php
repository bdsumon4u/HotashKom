<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class UtmReportController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_if(request()->user()->is(['salesman', 'uploader']), 403, 'You do not have permission.');

        $startD = $request->query('start_d') ? Date::parse((string) $request->query('start_d')) : now()->startOfMonth();
        $endD = $request->query('end_d') ? Date::parse((string) $request->query('end_d')) : now();
        $dateType = $request->query('date_type', 'created_at') === 'status_at' ? 'status_at' : 'created_at';
        $selectedSource = $request->query('source');

        $startStr = $startD->format('Y-m-d');
        $endStr = $endD->format('Y-m-d');

        $ordersQuery = Order::query()
            ->whereBetween($dateType, [
                $startD->startOfDay()->toDateTimeString(),
                $endD->endOfDay()->toDateTimeString(),
            ]);

        $allOrders = $ordersQuery->get();

        if ($selectedSource) {
            $allOrders = $allOrders->filter(fn (Order $order): bool => strtolower((string) $order->utm_source) === strtolower($selectedSource));
        }

        $totalOrdersCount = $allOrders->count();
        $utmOrders = $allOrders->filter(fn (Order $order): bool => ! empty($order->utm_source));
        $totalUtmOrdersCount = $utmOrders->count();

        // Group orders by Campaign + Source + Medium
        $campaigns = [];
        $sourcesCount = [];
        $campaignsCount = [];
        $utmDeliveredRevenue = 0.0;

        foreach ($utmOrders as $order) {
            $source = strtolower(trim((string) ($order->utm_source ?? 'unknown')));
            $defaultCampaign = match ($source) {
                'google' => 'Google Ads',
                'facebook', 'fb' => 'Facebook Ads',
                'tiktok' => 'TikTok Ads',
                default => 'None / Unnamed',
            };
            $campaign = trim((string) ($order->utm_campaign ?: $defaultCampaign));
            $medium = strtolower(trim((string) ($order->utm_medium ?: 'cpc')));

            $key = $campaign.'|'.$source.'|'.$medium;

            if (! isset($campaigns[$key])) {
                $campaigns[$key] = [
                    'campaign' => $campaign,
                    'source' => $source,
                    'medium' => $medium,
                    'total' => 0,
                    'pending' => 0,
                    'confirmed' => 0,
                    'packaging' => 0,
                    'shipping' => 0,
                    'delivered' => 0,
                    'returned' => 0,
                    'cancelled' => 0,
                    'revenue' => 0.0,
                ];
            }

            $campaigns[$key]['total']++;

            $status = (string) $order->status;
            if ($status === 'DELIVERED') {
                $campaigns[$key]['delivered']++;
                $orderTotal = (float) $order->condition;
                $campaigns[$key]['revenue'] += $orderTotal;
                $utmDeliveredRevenue += $orderTotal;
            } elseif (in_array($status, ['RETURNED', 'PAID_RETURN'])) {
                $campaigns[$key]['returned']++;
            } elseif ($status === 'CANCELLED') {
                $campaigns[$key]['cancelled']++;
            } elseif ($status === 'CONFIRMED') {
                $campaigns[$key]['confirmed']++;
            } elseif ($status === 'PACKAGING') {
                $campaigns[$key]['packaging']++;
            } elseif ($status === 'SHIPPING') {
                $campaigns[$key]['shipping']++;
            } else {
                $campaigns[$key]['pending']++;
            }

            $sourcesCount[$source] = ($sourcesCount[$source] ?? 0) + 1;
            if ($campaign !== 'None / Unnamed') {
                $campaignsCount[$campaign] = ($campaignsCount[$campaign] ?? 0) + 1;
            }
        }

        arsort($sourcesCount);
        arsort($campaignsCount);

        // Sort campaigns by total orders descending
        uasort($campaigns, fn ($a, $b): int => $b['total'] <=> $a['total']);

        // Summary calculations
        $topSource = ! empty($sourcesCount) ? array_key_first($sourcesCount) : 'N/A';
        $topSourceCount = ! empty($sourcesCount) ? reset($sourcesCount) : 0;

        $topCampaign = ! empty($campaignsCount) ? array_key_first($campaignsCount) : 'N/A';
        $topCampaignCount = ! empty($campaignsCount) ? reset($campaignsCount) : 0;

        $totalDelivered = $utmOrders->where('status', 'DELIVERED')->count();
        $overallDeliveryRate = $totalUtmOrdersCount > 0 ? round(($totalDelivered / $totalUtmOrdersCount) * 100, 1) : 0;

        $availableSources = $allOrders
            ->map(fn (Order $o) => $o->utm_source)
            ->filter()
            ->unique()
            ->values();

        return view('admin.reports.utm', [
            'start' => $startStr,
            'end' => $endStr,
            'dateType' => $dateType,
            'selectedSource' => $selectedSource,
            'availableSources' => $availableSources,
            'totalOrdersCount' => $totalOrdersCount,
            'totalUtmOrdersCount' => $totalUtmOrdersCount,
            'utmDeliveredRevenue' => $utmDeliveredRevenue,
            'topSource' => $topSource,
            'topSourceCount' => $topSourceCount,
            'topCampaign' => $topCampaign,
            'topCampaignCount' => $topCampaignCount,
            'overallDeliveryRate' => $overallDeliveryRate,
            'campaigns' => $campaigns,
        ]);
    }
}
