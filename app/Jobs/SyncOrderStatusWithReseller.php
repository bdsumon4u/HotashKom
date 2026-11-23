<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SyncOrderStatusWithReseller implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int $orderId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (! isOninda()) {
            return;
        }

        Order::query()
            ->whereKey($this->orderId)
            ->toBase()
            ->update([
                'reseller_status_sync_token' => (string) Str::uuid(),
            ]);
    }
}
