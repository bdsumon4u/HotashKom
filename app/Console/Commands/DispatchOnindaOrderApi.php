<?php

namespace App\Console\Commands;

use App\Jobs\CallOnindaOrderApi;
use App\Models\Order;
use Illuminate\Console\Command;

class DispatchOnindaOrderApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oninda:dispatch-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch CallOnindaOrderApi job for orders placed within the last hour with null or 0 source_id';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting to process orders for Oninda API dispatch...');

        // Find orders placed within the last hour with null or 0 source_id
        $orders = Order::where('created_at', '>=', now()->subHour())
            ->where(function ($query) {
                $query->whereNull('source_id')
                    ->orWhere('source_id', 0);
            })
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No orders found that meet the criteria.');

            return self::SUCCESS;
        }

        $this->info("Found {$orders->count()} orders to process.");

        $dispatchedCount = 0;
        foreach ($orders as $order) {
            try {
                CallOnindaOrderApi::dispatch($order->id);
                $dispatchedCount++;
                $this->line("Dispatched job for order ID: {$order->id}");
            } catch (\Exception $e) {
                $this->error("Failed to dispatch job for order ID {$order->id}: {$e->getMessage()}");
            }
        }

        $this->info("Successfully dispatched {$dispatchedCount} jobs out of {$orders->count()} orders.");

        return self::SUCCESS;
    }
}
