<?php

namespace App\Console\Commands;

use App\Jobs\SyncOrderStatusWithReseller;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SyncResellerOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:reseller-order-status
                            {--status=CONFIRMED : Only sync master orders with this status (omit to sync all statuses)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue order status syncs from the master site to reseller sites';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! isOninda()) {
            $this->error('This command must be run on the master site.');

            return self::FAILURE;
        }

        $status = $this->option('status');

        if ($status !== null) {
            $status = strtoupper(trim($status));

            if (! in_array($status, config('app.orders'), true)) {
                $this->error('Invalid status. Valid statuses: '.implode(', ', config('app.orders')));

                return self::FAILURE;
            }
        }

        $orders = Order::query()
            ->select('id')
            ->where('created_at', '>=', now()->subDays(3))
            ->whereHas('user', function (Builder $query): void {
                $query->where('is_active', true)
                    ->whereNotNull('db_name')
                    ->where('db_name', '!=', '')
                    ->whereNotNull('db_username')
                    ->where('db_username', '!=', '');
            });

        if ($status !== null) {
            $orders->where('status', $status);
        }

        $dispatchedCount = 0;

        $orders->chunkById(100, function (Collection $orders) use (&$dispatchedCount): void {
            foreach ($orders as $order) {
                SyncOrderStatusWithReseller::dispatch($order->id);
                $dispatchedCount++;
            }
        });

        $this->info("Dispatched {$dispatchedCount} order status sync jobs to reseller sites.");

        return self::SUCCESS;
    }
}
