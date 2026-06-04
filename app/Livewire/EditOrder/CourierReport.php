<?php

namespace App\Livewire\EditOrder;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class CourierReport extends Component
{
    public Order $order;

    public bool $loaded = false;

    public array|string|null $report = null;

    public function mount(Order $order): void
    {
        $this->order = $order;
    }

    public function load(): void
    {
        if ($this->loaded) {
            return;
        }

        $expires = config('services.courier_report.expires');
        if (! $expires || Date::parse($expires)->isPast()) {
            $this->report = 'API Expired';
            $this->loaded = true;

            return;
        }

        $report = cacheMemo()->remember(
            'courier:'.($this->order->phone ?? ''),
            now()->addWeek(),
            fn () => $this->fetchCourierReport(),
        );

        if (is_string($report)) {
            cacheMemo()->forget('courier:'.($this->order->phone ?? ''));
        }

        $this->report = $report;
        $this->loaded = true;
    }

    /**
     * @return array<string, mixed>|string
     */
    private function fetchCourierReport(): array|string
    {
        try {
            $nextToken = function () {
                $cacheKey = 'bdcourier:token:rotating';
                $apiKeys = Cache::pull($cacheKey, explode('|', config('services.courier_report.key')));
                $ignoredKeys = Cache::get($cacheKey.':ignored', []);
                $effectiveKeys = array_diff($apiKeys, $ignoredKeys);
                if (empty($effectiveKeys)) {
                    Cache::forget($cacheKey.':ignored');
                    $effectiveKeys = $apiKeys;
                }
                $current = array_shift($effectiveKeys);
                array_push($effectiveKeys, $current);
                Cache::put($cacheKey, $effectiveKeys, now()->addDay());

                return $current;
            };

            $token = $nextToken();
            $response = Http::retry(3, 100)
                ->withToken($token)
                ->post(config('services.courier_report.url'), [
                    'phone' => $this->order->phone ?? '',
                ]);

            if ($response->status() === 429) {
                $ignoredCacheKey = 'bdcourier:token:rotating:ignored';
                $ignoredKeys = Cache::get($ignoredCacheKey, []);
                if (! in_array($token, $ignoredKeys)) {
                    $ignoredKeys[] = $token;
                }
                Cache::put($ignoredCacheKey, $ignoredKeys, now()->endOfDay());
            }

            return $response->json();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.edit-order.courier-report');
    }
}
