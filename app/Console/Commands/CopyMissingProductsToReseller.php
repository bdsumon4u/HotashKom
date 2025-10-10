<?php

namespace App\Console\Commands;

use App\Jobs\CopyProductToResellers;
use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CopyMissingProductsToReseller extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:copy-missing-to-reseller
                            {reseller_id : The ID of the reseller to copy products to}
                            {--limit=50 : Maximum number of products to copy in one run}
                            {--dry-run : Show what would be copied without actually copying}
                            {--queue=default : Queue name to dispatch jobs to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy missing products from Oninda to a specific reseller using queue jobs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $resellerId = $this->argument('reseller_id');
        $limit = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');
        $queue = $this->option('queue');

        // Validate reseller exists and has database configuration
        $reseller = User::where('id', $resellerId)
            ->where('is_active', true)
            ->whereNotNull('db_name')
            ->where('db_name', '!=', '')
            ->whereNotNull('db_username')
            ->where('db_username', '!=', '')
            ->first();

        if (! $reseller) {
            $this->error("Reseller with ID {$resellerId} not found or doesn't have proper database configuration.");

            return 1;
        }

        $this->info("Targeting reseller: {$reseller->name} (ID: {$reseller->id})");
        $this->info("Database: {$reseller->db_name}");

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No actual copying will be performed');
        }

        // Configure reseller database connection for finding missing products
        config(['database.connections.reseller' => $reseller->getDatabaseConfig()]);
        DB::purge('reseller');
        DB::reconnect('reseller');

        // Test connection
        try {
            DB::connection('reseller')->getPdo();
            $this->info('✓ Successfully connected to reseller database');
        } catch (\Exception $e) {
            $this->error("Failed to connect to reseller database: {$e->getMessage()}");

            return 1;
        }

        // Find missing products
        $missingProducts = $this->findMissingProducts($limit);

        if ($missingProducts->isEmpty()) {
            $this->info('✓ No missing products found. All products are already synced.');

            return 0;
        }

        $this->info("Found {$missingProducts->count()} missing products to copy");

        if ($dryRun) {
            $this->displayMissingProducts($missingProducts);

            return 0;
        }

        // Dispatch jobs for missing products
        $dispatchedCount = 0;
        $progressBar = $this->output->createProgressBar($missingProducts->count());
        $progressBar->start();

        foreach ($missingProducts as $product) {
            try {
                CopyProductToResellers::dispatch($product)->onQueue($queue);
                $dispatchedCount++;
                $progressBar->advance();
            } catch (\Exception $e) {
                $this->newLine();
                $this->warn("Failed to dispatch job for product: {$product->name} (ID: {$product->id}) - {$e->getMessage()}");
            }
        }

        $progressBar->finish();
        $this->newLine();

        $this->info('✓ Jobs dispatched successfully!');
        $this->info("  - Dispatched {$dispatchedCount} copy jobs to '{$queue}' queue");
        $this->info('  - Jobs will process products individually for better reliability');
        $this->info('  - Monitor queue workers to see job progress');

        return 0;
    }

    /**
     * Find products that are missing in the reseller's database
     */
    private function findMissingProducts(int $limit)
    {
        // Get all product IDs that exist in Oninda
        $onindaProductIds = Product::whereNull('parent_id')->pluck('id')->toArray();

        // Get product IDs that already exist in reseller's database
        $existingProductIds = DB::connection('reseller')
            ->table('products')
            ->whereIn('source_id', $onindaProductIds)
            ->pluck('source_id')
            ->toArray();

        // Find missing product IDs
        $missingProductIds = array_diff($onindaProductIds, $existingProductIds);

        if (empty($missingProductIds)) {
            return collect();
        }

        // Get the actual product models for missing products
        return Product::whereIn('id', $missingProductIds)->limit($limit)->get();
    }

    /**
     * Display missing products in dry run mode
     */
    private function displayMissingProducts($products): void
    {
        $this->newLine();
        $this->info('Missing products that would be copied:');
        $this->newLine();

        $headers = ['ID', 'Name', 'SKU', 'Price', 'Active'];
        $rows = [];

        foreach ($products as $product) {
            $rows[] = [
                $product->id,
                $product->name,
                $product->sku,
                '৳'.number_format($product->selling_price),
                $product->is_active ? 'Yes' : 'No',
            ];
        }

        $this->table($headers, $rows);
    }
}
