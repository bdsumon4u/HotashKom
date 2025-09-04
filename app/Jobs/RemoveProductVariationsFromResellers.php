<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RemoveProductVariationsFromResellers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $productId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $productId)
    {
        $this->productId = $productId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
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
                    // Configure reseller database connection using getDatabaseConfig
                    config(['database.connections.reseller' => $reseller->getDatabaseConfig()]);

                    // Purge and reconnect to ensure fresh connection
                    DB::purge('reseller');
                    DB::reconnect('reseller');

                    // Get the parent product ID in reseller database
                    $parentProductId = DB::connection('reseller')
                        ->table('products')
                        ->where('source_id', $this->productId)
                        ->value('id');

                    if ($parentProductId) {
                        // Get all variation IDs for this product
                        $variationIds = DB::connection('reseller')
                            ->table('products')
                            ->where('parent_id', $parentProductId)
                            ->pluck('id');

                        if ($variationIds->isNotEmpty()) {
                            // Delete all relationships and variations in a single query
                            // DB::connection('reseller')
                            //     ->table('image_product')
                            //     ->whereIn('product_id', $variationIds)
                            //     ->delete();

                            DB::connection('reseller')
                                ->table('option_product')
                                ->whereIn('product_id', $variationIds)
                                ->delete();

                            DB::connection('reseller')
                                ->table('products')
                                ->whereIn('id', $variationIds)
                                ->delete();
                        }
                    }

                    Log::info("Removed variations for product {$this->productId} from reseller database {$reseller->database}");
                } catch (\Exception $e) {
                    Log::error("Failed to remove variations from reseller {$reseller->database}: ".$e->getMessage());

                    continue;
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to process resellers: '.$e->getMessage());
            throw $e;
        }
    }
}
