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
            $allOrders = $allOrders->filter(function (Order $order) use ($selectedSource): bool {
                $src = strtolower(trim((string) $order->utm_source));
                if ($src === strtolower($selectedSource)) {
                    return true;
                }
                $platform = self::resolvePlatformGroup($src);

                return $platform['key'] === strtolower($selectedSource);
            });
        }

        $totalOrdersCount = $allOrders->count();
        $utmOrders = $allOrders->filter(fn (Order $order): bool => ! empty($order->utm_source));
        $totalUtmOrdersCount = $utmOrders->count();

        // Group orders by Campaign + Source + Medium, and by Unified Platform Group
        $campaigns = [];
        $platforms = [];
        $platformsCount = [];
        $campaignsCount = [];
        $utmDeliveredRevenue = 0.0;

        foreach ($utmOrders as $order) {
            $source = strtolower(trim((string) ($order->utm_source ?? 'unknown')));
            $platformMeta = self::resolvePlatformGroup($source);
            $platformKey = $platformMeta['key'];

            $defaultCampaign = match ($platformKey) {
                'google' => 'Google Ads',
                'meta' => 'Meta Ads',
                'tiktok' => 'TikTok Ads',
                default => 'None / Unnamed',
            };
            $campaign = trim((string) ($order->utm_campaign ?: $defaultCampaign));
            $medium = strtolower(trim((string) ($order->utm_medium ?: 'cpc')));

            $campaignKey = $campaign.'|'.$source.'|'.$medium;

            if (! isset($campaigns[$campaignKey])) {
                $campaigns[$campaignKey] = [
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

            if (! isset($platforms[$platformKey])) {
                $platforms[$platformKey] = [
                    'key' => $platformKey,
                    'name' => $platformMeta['name'],
                    'label' => $platformMeta['label'],
                    'badge' => $platformMeta['badge'],
                    'sources' => [],
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

            $campaigns[$campaignKey]['total']++;
            $platforms[$platformKey]['total']++;
            $platforms[$platformKey]['sources'][$source] = ($platforms[$platformKey]['sources'][$source] ?? 0) + 1;

            $status = (string) $order->status;
            if ($status === 'DELIVERED') {
                $campaigns[$campaignKey]['delivered']++;
                $platforms[$platformKey]['delivered']++;
                $orderTotal = (float) $order->condition;
                $campaigns[$campaignKey]['revenue'] += $orderTotal;
                $platforms[$platformKey]['revenue'] += $orderTotal;
                $utmDeliveredRevenue += $orderTotal;
            } elseif (in_array($status, ['RETURNED', 'PAID_RETURN'])) {
                $campaigns[$campaignKey]['returned']++;
                $platforms[$platformKey]['returned']++;
            } elseif ($status === 'CANCELLED') {
                $campaigns[$campaignKey]['cancelled']++;
                $platforms[$platformKey]['cancelled']++;
            } elseif ($status === 'CONFIRMED') {
                $campaigns[$campaignKey]['confirmed']++;
                $platforms[$platformKey]['confirmed']++;
            } elseif ($status === 'PACKAGING') {
                $campaigns[$campaignKey]['packaging']++;
                $platforms[$platformKey]['packaging']++;
            } elseif ($status === 'SHIPPING') {
                $campaigns[$campaignKey]['shipping']++;
                $platforms[$platformKey]['shipping']++;
            } else {
                $campaigns[$campaignKey]['pending']++;
                $platforms[$platformKey]['pending']++;
            }

            $platformsCount[$platformKey] = ($platformsCount[$platformKey] ?? 0) + 1;
            if ($campaign !== 'None / Unnamed') {
                $campaignsCount[$campaign] = ($campaignsCount[$campaign] ?? 0) + 1;
            }
        }

        arsort($platformsCount);
        arsort($campaignsCount);

        // Sort campaigns and platforms by total orders descending
        uasort($campaigns, fn ($a, $b): int => $b['total'] <=> $a['total']);
        uasort($platforms, fn ($a, $b): int => $b['total'] <=> $a['total']);

        // Summary calculations
        $topPlatformKey = ! empty($platformsCount) ? array_key_first($platformsCount) : 'N/A';
        $topPlatformName = isset($platforms[$topPlatformKey]) ? $platforms[$topPlatformKey]['name'] : 'N/A';
        $topPlatformCount = ! empty($platformsCount) ? reset($platformsCount) : 0;

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
            'topSource' => $topPlatformName,
            'topSourceCount' => $topPlatformCount,
            'topCampaign' => $topCampaign,
            'topCampaignCount' => $topCampaignCount,
            'overallDeliveryRate' => $overallDeliveryRate,
            'campaigns' => $campaigns,
            'platforms' => $platforms,
        ]);
    }

    /**
     * Resolve raw traffic source into a unified platform group.
     *
     * @return array{key: string, name: string, label: string, badge: string}
     */
    public static function resolvePlatformGroup(string $source): array
    {
        $sourceLower = strtolower(trim($source));

        // Meta (Facebook, Instagram, Messenger, WhatsApp, Threads, etc.)
        if (
            in_array($sourceLower, ['facebook', 'fb', 'fb-sitelink', 'fb_sitelink', 'fb-ads', 'fbads', 'facebook_ads', 'instagram', 'ig', 'ig-story', 'meta', 'messenger', 'whatsapp', 'threads', 'an'])
            || str_starts_with($sourceLower, 'fb-')
            || str_starts_with($sourceLower, 'fb_')
            || str_starts_with($sourceLower, 'ig-')
            || str_contains($sourceLower, 'facebook')
            || str_contains($sourceLower, 'instagram')
        ) {
            return [
                'key' => 'meta',
                'name' => 'Meta',
                'label' => 'META',
                'badge' => 'badge-primary',
            ];
        }

        // Google (Search, Ads, YouTube, Display, Shopping, etc.)
        if (
            in_array($sourceLower, ['google', 'google_ads', 'googleads', 'gads', 'adwords', 'youtube', 'yt', 'gmail', 'google-shopping'])
            || str_contains($sourceLower, 'google')
            || str_contains($sourceLower, 'youtube')
        ) {
            return [
                'key' => 'google',
                'name' => 'Google',
                'label' => 'GOOGLE',
                'badge' => 'badge-danger',
            ];
        }

        // TikTok
        if (
            in_array($sourceLower, ['tiktok', 'tt', 'tiktok_ads', 'tt_ads', 'bytedance', 'douyin'])
            || str_contains($sourceLower, 'tiktok')
        ) {
            return [
                'key' => 'tiktok',
                'name' => 'TikTok',
                'label' => 'TIKTOK',
                'badge' => 'badge-dark',
            ];
        }

        // Snapchat
        if (in_array($sourceLower, ['snapchat', 'snap']) || str_contains($sourceLower, 'snap')) {
            return [
                'key' => 'snapchat',
                'name' => 'Snapchat',
                'label' => 'SNAPCHAT',
                'badge' => 'badge-warning',
            ];
        }

        // Twitter / X
        if (in_array($sourceLower, ['twitter', 'x', 't.co']) || str_contains($sourceLower, 'twitter')) {
            return [
                'key' => 'twitter',
                'name' => 'X / Twitter',
                'label' => 'X / TWITTER',
                'badge' => 'badge-dark',
            ];
        }

        // Pinterest
        if (in_array($sourceLower, ['pinterest', 'pin']) || str_contains($sourceLower, 'pinterest')) {
            return [
                'key' => 'pinterest',
                'name' => 'Pinterest',
                'label' => 'PINTEREST',
                'badge' => 'badge-danger',
            ];
        }

        return [
            'key' => $sourceLower,
            'name' => ucfirst($source),
            'label' => strtoupper($source),
            'badge' => 'badge-secondary',
        ];
    }
}
