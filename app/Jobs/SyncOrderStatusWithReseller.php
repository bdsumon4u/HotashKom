<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

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
        info('Syncing order status with resellers', [
            'orderId' => $this->orderId,
        ]);

        // Get the order and its reseller
        if (! $order = Order::find($this->orderId)) {
            info('Order not found', ['orderId' => $this->orderId]);

            return;
        }

        if (! $reseller = $order->user) {
            info('Order has no reseller', [
                'orderId' => $this->orderId,
                'userId' => $order->user_id,
            ]);

            return;
        }

        try {
            // Configure reseller database connection
            config(['database.connections.reseller' => $reseller->getDatabaseConfig()]);

            // Purge and reconnect to ensure fresh connection
            DB::purge('reseller');
            DB::reconnect('reseller');

            // Update status in reseller's database
            DB::connection('reseller')
                ->table('orders')
                ->where('source_id', $this->orderId)
                ->update(['status' => $order->status]);

            info('Order status synced with reseller', [
                'orderId' => $this->orderId,
                'status' => $order->status,
                'resellerId' => $reseller->id,
            ]);
        } catch (\Exception $e) {
            info('Failed to sync order status with reseller', [
                'orderId' => $this->orderId,
                'status' => $order->status,
                'resellerId' => $reseller->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
