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

    protected Product $product;

    /**
     * Create a new job instance.
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all active resellers
        $resellers = User::where('is_active', true)
            ->whereNotNull('db_password')
            ->where('db_password', '!=', '')
            ->inRandomOrder()
            ->get();

        foreach ($resellers as $reseller) {
            try {
                // Configure reseller database connection
                config(['database.connections.reseller' => $reseller->getDatabaseConfig()]);

                // Purge and reconnect to ensure fresh connection
                DB::purge('reseller');
                DB::reconnect('reseller');

                // Update product in reseller database
                DB::connection('reseller')
                    ->table('products')
                    ->where('source_id', $this->product->id)
                    ->update([
                        'should_track' => $this->product->should_track,
                        'stock_count' => $this->product->stock_count,
                        'updated_at' => now(),
                    ]);

                // Clear reseller's cache
                $reseller->clearResellerCache('products');

                Log::info("Successfully synced product {$this->product->id} stock with reseller {$reseller->id}");

            } catch (\Exception $e) {
                Log::error("Failed to sync product {$this->product->id} stock with reseller {$reseller->id}: ".$e->getMessage());

                continue;
            }
        }
    }
}
