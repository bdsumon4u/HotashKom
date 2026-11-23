<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use stdClass;
use Throwable;

class SyncResellerOrderStatuses implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(protected int $resellerId, protected ?string $status = null)
    {
        $this->afterCommit();
    }

    public function uniqueId(): string
    {
        return (string) $this->resellerId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (! isOninda()) {
            return;
        }

        $reseller = User::query()->find($this->resellerId);

        if (! $reseller || ! $reseller->is_active || blank($reseller->db_name) || blank($reseller->db_username)) {
            return;
        }

        $orders = Order::query()
            ->select(['id', 'status', 'reseller_status_sync_token'])
            ->where('user_id', $reseller->id)
            ->whereNotNull('reseller_status_sync_token');

        if ($this->status !== null) {
            $orders->where('status', $this->status);
        }

        $syncedCount = 0;
        $missingCount = 0;

        try {
            config(['database.connections.reseller' => $reseller->getDatabaseConfig()]);
            DB::purge('reseller');
            $connection = DB::connection('reseller');

            $orders->toBase()->chunkById(200, function (Collection $orders) use ($connection, &$syncedCount, &$missingCount): void {
                $existingIds = $connection->table('orders')
                    ->whereIn('source_id', $orders->pluck('id'))
                    ->pluck('source_id')
                    ->map(fn ($id): int => (int) $id)
                    ->flip();

                $matchedOrders = $orders->filter(fn (stdClass $order): bool => $existingIds->has((int) $order->id));
                $missingCount += $orders->count() - $matchedOrders->count();

                if ($matchedOrders->isEmpty()) {
                    return;
                }

                $cases = [];
                $bindings = [];

                foreach ($matchedOrders as $order) {
                    $cases[] = 'WHEN ? THEN ?';
                    $bindings[] = (int) $order->id;
                    $bindings[] = $order->status;
                }

                $grammar = $connection->getQueryGrammar();
                $table = $grammar->wrapTable('orders');
                $statusColumn = $grammar->wrap('status');
                $sourceColumn = $grammar->wrap('source_id');
                $placeholders = implode(', ', array_fill(0, $matchedOrders->count(), '?'));

                $connection->update(
                    "UPDATE {$table} SET {$statusColumn} = CASE {$sourceColumn} ".implode(' ', $cases).
                    " ELSE {$statusColumn} END WHERE {$sourceColumn} IN ({$placeholders})",
                    array_merge($bindings, $matchedOrders->pluck('id')->all()),
                );

                Order::query()
                    ->where('user_id', $this->resellerId)
                    ->toBase()
                    ->where(function (Builder $query) use ($matchedOrders): void {
                        foreach ($matchedOrders as $order) {
                            $query->orWhere(function (Builder $query) use ($order): void {
                                $query->where('id', $order->id)
                                    ->where('reseller_status_sync_token', $order->reseller_status_sync_token);
                            });
                        }
                    })
                    ->update(['reseller_status_sync_token' => null]);

                $syncedCount += $matchedOrders->count();
            });

            Log::info('Synced pending order statuses with reseller', [
                'resellerId' => $reseller->id,
                'syncedCount' => $syncedCount,
                'missingCount' => $missingCount,
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to sync pending order statuses with reseller', [
                'resellerId' => $reseller->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        } finally {
            DB::purge('reseller');
        }
    }
}
