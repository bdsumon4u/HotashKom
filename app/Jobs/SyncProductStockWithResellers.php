<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncProductStockWithResellers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param  Product|array<int, int>  $product
     */
    public function __construct(protected Product|array $product)
    {
        $this->afterCommit();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $productIds = $this->product instanceof Product ? [$this->product->id] : $this->product;
        $products = Product::query()->whereKey($productIds)->get(['id', 'should_track', 'stock_count']);

        if ($products->isEmpty()) {
            return;
        }

        $stockCases = [];
        $trackingCases = [];

        foreach ($products as $product) {
            $stockCases[] = sprintf('WHEN %d THEN %d', $product->id, $product->stock_count);
            $trackingCases[] = sprintf('WHEN %d THEN %d', $product->id, (int) $product->should_track);
        }

        // Get all active resellers
        $resellers = User::where('is_active', true)
            ->whereNotNull('db_name')
            ->where('db_name', '!=', '')
            ->whereNotNull('db_username')
            ->where('db_username', '!=', '')
            ->inRandomOrder()
            ->get();

        foreach ($resellers as $reseller) {
            try {
                // Configure reseller database connection
                config(['database.connections.reseller' => $reseller->getDatabaseConfig()]);

                // Purge and reconnect to ensure fresh connection
                DB::purge('reseller');
                DB::reconnect('reseller');

                $connection = DB::connection('reseller');

                $connection
                    ->table('products')
                    ->whereIn('source_id', $products->modelKeys())
                    ->update([
                        'should_track' => $connection->raw('CASE source_id '.implode(' ', $trackingCases).' ELSE should_track END'),
                        'stock_count' => $connection->raw('CASE source_id '.implode(' ', $stockCases).' ELSE stock_count END'),
                        'updated_at' => now(),
                    ]);

                // Clear reseller's cache
                $reseller->clearResellerCache('products');

                Log::info("Successfully synced product stock with reseller {$reseller->id}", [
                    'productIds' => $products->modelKeys(),
                ]);

            } catch (\Exception $e) {
                Log::error("Failed to sync product stock with reseller {$reseller->id}: ".$e->getMessage(), [
                    'productIds' => $products->modelKeys(),
                ]);

                continue;
            }
        }
    }
}
