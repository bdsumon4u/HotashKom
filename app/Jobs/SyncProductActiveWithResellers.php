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

class SyncProductActiveWithResellers implements ShouldQueue
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
        $resellers = User::where('is_active', true)
            ->whereNotNull('db_name')
            ->where('db_name', '!=', '')
            ->whereNotNull('db_username')
            ->where('db_username', '!=', '')
            ->inRandomOrder()
            ->get();

        foreach ($resellers as $reseller) {
            try {
                config(['database.connections.reseller' => $reseller->getDatabaseConfig()]);

                DB::purge('reseller');
                DB::reconnect('reseller');

                DB::connection('reseller')
                    ->table('products')
                    ->where('source_id', $this->product->id)
                    ->update([
                        'is_active' => (bool) $this->product->is_active,
                        'updated_at' => now(),
                    ]);

                $reseller->clearResellerCache('products');

                Log::info("Successfully synced product {$this->product->id} active status with reseller {$reseller->id}");
            } catch (\Exception $e) {
                Log::error("Failed to sync product {$this->product->id} active status with reseller {$reseller->id}: ".$e->getMessage());

                continue;
            }
        }
    }
}
