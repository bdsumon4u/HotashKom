<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CallOnindaOrderApi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $orderId
    ) {}

    public function handle(): void
    {
        $domain = parse_url(config('app.url'), PHP_URL_HOST);
        $endpoint = config('app.oninda_url').'/api/reseller/orders/place';

        info('Calling Oninda order API: '.$endpoint, $data = [
            'order_id' => [$this->orderId],
            'domain' => $domain,
        ]);

        try {
            Http::post($endpoint, $data)->throw();
        } catch (\Exception $e) {
            DB::table('orders')->whereIntegerInRaw('id', [$this->orderId])->update(['source_id' => null]);
        }
    }
}
