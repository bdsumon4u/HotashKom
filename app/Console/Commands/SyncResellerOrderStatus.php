<?php

namespace App\Console\Commands;

use App\Jobs\SyncResellerOrderStatuses;
use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class SyncResellerOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:reseller-order-status
                            {--status=CONFIRMED : Manually mark orders with this status for sync (defaults to CONFIRMED)}
                            {--pending : Sync only pending changes across all statuses}';

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

        $pendingOnly = (bool) $this->option('pending');
        $status = $pendingOnly ? null : $this->option('status');

        if ($status !== null) {
            $status = strtoupper(trim($status));

            if (! in_array($status, config('app.orders'), true)) {
                $this->error('Invalid status. Valid statuses: '.implode(', ', config('app.orders')));

                return self::FAILURE;
            }
        }

        $eligibleResellers = static function (Builder $query): void {
            $query->where('is_active', true)
                ->whereNotNull('db_name')
                ->whereRaw("TRIM(db_name) != ''")
                ->whereNotNull('db_username')
                ->whereRaw("TRIM(db_username) != ''");
        };

        if (! $pendingOnly) {
            $orders = Order::query()->whereHas('user', $eligibleResellers);

            if ($status !== null) {
                $orders->where('status', $status);
            }

            $markedCount = $orders->toBase()->update([
                'reseller_status_sync_token' => (string) Str::uuid(),
            ]);

            $this->info("Marked {$markedCount} orders for status sync.");
        }

        $resellers = User::query()
            ->where($eligibleResellers)
            ->whereHas('orders', function (Builder $query) use ($status): void {
                $query->whereNotNull('reseller_status_sync_token');

                if ($status !== null) {
                    $query->where('status', $status);
                }
            });

        $requestedCount = 0;

        $resellers->select('id')->chunkById(100, function (Collection $resellers) use ($status, &$requestedCount): void {
            foreach ($resellers as $reseller) {
                SyncResellerOrderStatuses::dispatch($reseller->id, $status);
                $requestedCount++;
            }
        });

        $this->info("Requested {$requestedCount} reseller order status sync jobs; already queued resellers are deduplicated.");

        return self::SUCCESS;
    }
}
