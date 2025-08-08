<?php

namespace App\Console\Commands;

use App\Jobs\CleanupDuplicateRelationships as CleanupDuplicateRelationshipsJob;
use Illuminate\Console\Command;

class CleanupDuplicateRelationshipsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resellers:cleanup-duplicates {--product-id= : Clean up duplicates for a specific product ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up duplicate relationships in reseller databases';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $productId = $this->option('product-id');

        $this->info('Starting cleanup of duplicate relationships...');

        if ($productId) {
            $this->info("Cleaning up duplicates for product ID: {$productId}");
            CleanupDuplicateRelationshipsJob::dispatch((int) $productId);
        } else {
            $this->info('Cleaning up duplicates for all products...');
            CleanupDuplicateRelationshipsJob::dispatch();
        }

        $this->info('Cleanup job dispatched successfully!');
        $this->info('Check the logs for progress and results.');

        return Command::SUCCESS;
    }
}
