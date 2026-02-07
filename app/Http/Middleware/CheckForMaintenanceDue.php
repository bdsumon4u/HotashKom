<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class CheckForMaintenanceDue
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return $next($request);
        }

        if (Cache::get('ignore_maintenance_due_check') || (! $serviceId = $this->getServiceId($request))) {
            return $next($request);
        }

        if (Cache::has('unpaid_or_overdue_invoice')) {
            return redirect()->route('maintenance.payment');
        }

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::asForm()
            ->acceptJson()
            ->post('https://hotash.tech/includes/api/checkunpaidservice.php', [
                'serviceid' => $serviceId,
            ]);

        $data = $response->json();
        if ($data['has_unpaid_or_overdue'] ?? false) {
            Cache::put('unpaid_or_overdue_invoice', $data['data'][0], now()->addHour());

            return redirect()->route('maintenance.payment');
        }

        Cache::put('ignore_maintenance_due_check', true, now()->addDay());

        return $next($request);
    }

    private function getServiceId($request)
    {
        if (Cache::has($key = 'maintenance_service_id')) {
            return Cache::get($key);
        }

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::asForm()
            ->acceptJson()
            ->get('https://sites.hotash.tech/api/get-service-id/'.$request->getHost());

        if ($serviceId = $response->json('service_id')) {
            Cache::put($key, $serviceId, now()->addWeek());
        } else {
            Cache::put($key, 0, now()->addDay());
        }
    }
}
