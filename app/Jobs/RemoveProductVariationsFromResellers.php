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

class RemoveProductVariationsFromResellers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $variationIds;

    /**
     * Create a new job instance.
     */
    public function __construct(array $variationIds)
    {
        $this->variationIds = $variationIds;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Get all active resellers
            $resellers = User::where('is_active', true)->get();

            foreach ($resellers as $reseller) {
                try {
                    // Set up reseller database connection
                    config(['database.connections.reseller.database' => $reseller->database]);
                    DB::purge('reseller');
                    DB::reconnect('reseller');

                    // Delete each variation
                    foreach ($this->variationIds as $variationId) {
                        // First remove any relationships
                        DB::connection('reseller')
                            ->table('image_product')
                            ->whereIn('product_id', function($query) use ($variationId) {
                                $query->select('id')
                                    ->from('products')
                                    ->where('source_id', $variationId);
                            })
                            ->delete();

                        DB::connection('reseller')
                            ->table('option_product')
                            ->whereIn('product_id', function($query) use ($variationId) {
                                $query->select('id')
                                    ->from('products')
                                    ->where('source_id', $variationId);
                            })
                            ->delete();

                        DB::connection('reseller')
                            ->table('category_product')
                            ->whereIn('product_id', function($query) use ($variationId) {
                                $query->select('id')
                                    ->from('products')
                                    ->where('source_id', $variationId);
                            })
                            ->delete();

                        // Then delete the variation
                        DB::connection('reseller')
                            ->table('products')
                            ->where('source_id', $variationId)
                            ->delete();
                    }

                    Log::info("Removed variations " . implode(', ', $this->variationIds) . " from reseller database {$reseller->database}");
                } catch (\Exception $e) {
                    Log::error("Failed to remove variations from reseller {$reseller->database}: " . $e->getMessage());
                    // Continue with next reseller instead of failing the entire job
                    continue;
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to process resellers: " . $e->getMessage());
            throw $e;
        }
    }
}
