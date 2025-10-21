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

class RemoveResourceFromResellers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected string $table, protected int $id) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
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

                // Set source_id to null for the resource
                DB::connection('reseller')
                    ->table($this->table)
                    ->where('source_id', $this->id)
                    ->update(['source_id' => null]);

                // Clear reseller's cache
                $reseller->clearResellerCache($this->table);

                Log::info("Successfully removed {$this->table} {$this->id} from reseller {$reseller->id}");

            } catch (\Exception $e) {
                Log::error("Failed to remove {$this->table} {$this->id} from reseller {$reseller->id}: ".$e->getMessage());

                continue;
            }
        }
    }
}
