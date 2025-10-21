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

class CleanupDuplicateRelationships implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [10, 30, 60];

    public $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(protected ?int $productId = null) {}

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Cleanup duplicate relationships job failed: '.$exception->getMessage());
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting CleanupDuplicateRelationships job', [
            'productId' => $this->productId,
        ]);

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
                $config = $reseller->getDatabaseConfig();
                config(['database.connections.reseller' => $config]);

                // Purge and reconnect to ensure fresh connection
                DB::purge('reseller');
                DB::reconnect('reseller');

                // Test connection before proceeding
                try {
                    DB::connection('reseller')->getPdo();
                } catch (\Exception $e) {
                    Log::error('Failed to connect to reseller database', [
                        'resellerId' => $reseller->id,
                        'domain' => $reseller->domain,
                        'error' => $e->getMessage(),
                    ]);

                    continue;
                }

                // Clean up duplicates for this reseller
                $this->cleanupResellerDuplicates($reseller);

                Log::info("Successfully cleaned up duplicates for reseller {$reseller->id}");

            } catch (\PDOException $e) {
                Log::error("Database connection failed for reseller {$reseller->id}: ".$e->getMessage());

                continue;
            } catch (\Exception $e) {
                Log::error("Failed to cleanup duplicates for reseller {$reseller->id}: ".$e->getMessage());

                continue;
            } finally {
                // Always purge the connection to free up resources
                DB::purge('reseller');
            }
        }
    }

    /**
     * Clean up duplicate relationships for a specific reseller
     */
    private function cleanupResellerDuplicates(User $reseller): void
    {
        $totalCleaned = 0;

        // Clean up duplicate category relationships
        $duplicateCategories = DB::connection('reseller')
            ->table('category_product')
            ->select('product_id', 'category_id')
            ->groupBy('product_id', 'category_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicateCategories as $duplicate) {
            $recordsToDelete = DB::connection('reseller')
                ->table('category_product')
                ->where('product_id', $duplicate->product_id)
                ->where('category_id', $duplicate->category_id)
                ->orderBy('id')
                ->skip(1)
                ->get(['id']);

            if ($recordsToDelete->isNotEmpty()) {
                DB::connection('reseller')
                    ->table('category_product')
                    ->whereIn('id', $recordsToDelete->pluck('id'))
                    ->delete();

                $totalCleaned += $recordsToDelete->count();

                Log::info('Deleted duplicate category relationships', [
                    'resellerId' => $reseller->id,
                    'productId' => $duplicate->product_id,
                    'categoryId' => $duplicate->category_id,
                    'deletedCount' => $recordsToDelete->count(),
                ]);
            }
        }

        // Clean up duplicate image relationships
        $duplicateImages = DB::connection('reseller')
            ->table('image_product')
            ->select('product_id', 'image_id', 'img_type')
            ->groupBy('product_id', 'image_id', 'img_type')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicateImages as $duplicate) {
            $recordsToDelete = DB::connection('reseller')
                ->table('image_product')
                ->where('product_id', $duplicate->product_id)
                ->where('image_id', $duplicate->image_id)
                ->where('img_type', $duplicate->img_type)
                ->orderBy('id')
                ->skip(1)
                ->get(['id']);

            if ($recordsToDelete->isNotEmpty()) {
                DB::connection('reseller')
                    ->table('image_product')
                    ->whereIn('id', $recordsToDelete->pluck('id'))
                    ->delete();

                $totalCleaned += $recordsToDelete->count();

                Log::info('Deleted duplicate image relationships', [
                    'resellerId' => $reseller->id,
                    'productId' => $duplicate->product_id,
                    'imageId' => $duplicate->image_id,
                    'imgType' => $duplicate->img_type,
                    'deletedCount' => $recordsToDelete->count(),
                ]);
            }
        }

        // Clean up duplicate option relationships
        $duplicateOptions = DB::connection('reseller')
            ->table('option_product')
            ->select('product_id', 'option_id')
            ->groupBy('product_id', 'option_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicateOptions as $duplicate) {
            $recordsToDelete = DB::connection('reseller')
                ->table('option_product')
                ->where('product_id', $duplicate->product_id)
                ->where('option_id', $duplicate->option_id)
                ->orderBy('id')
                ->skip(1)
                ->get(['id']);

            if ($recordsToDelete->isNotEmpty()) {
                DB::connection('reseller')
                    ->table('option_product')
                    ->whereIn('id', $recordsToDelete->pluck('id'))
                    ->delete();

                $totalCleaned += $recordsToDelete->count();

                Log::info('Deleted duplicate option relationships', [
                    'resellerId' => $reseller->id,
                    'productId' => $duplicate->product_id,
                    'optionId' => $duplicate->option_id,
                    'deletedCount' => $recordsToDelete->count(),
                ]);
            }
        }

        Log::info('Cleanup completed for reseller', [
            'resellerId' => $reseller->id,
            'resellerDomain' => $reseller->domain,
            'totalCleaned' => $totalCleaned,
            'duplicateCategories' => $duplicateCategories->count(),
            'duplicateImages' => $duplicateImages->count(),
            'duplicateOptions' => $duplicateOptions->count(),
        ]);
    }
}
