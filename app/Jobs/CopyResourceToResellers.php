<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyResourceToResellers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;
    protected $uniqueColumn;
    protected $table;

    /**
     * Create a new job instance.
     */
    public function __construct(Model $model, string $uniqueColumn)
    {
        $this->model = $model;
        $this->uniqueColumn = $uniqueColumn;
        $this->table = $model->getTable();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all active resellers
        $resellers = User::where('is_active', true)->get();

        foreach ($resellers as $reseller) {
            try {
                // Connect to reseller's database using their config
                config(['database.connections.reseller' => $reseller->getDatabaseConfig()]);

                // Get raw data from the model
                $data = $this->model->getRawOriginal();

                // Check if resource exists in reseller's database
                $existing = DB::connection('reseller')
                    ->table($this->table)
                    ->where($this->uniqueColumn, $data[$this->uniqueColumn])
                    ->first();

                if ($existing) {
                    // Update source_id in oninda database
                    DB::table($this->table)
                        ->where('id', $this->model->id)
                        ->update(['source_id' => $existing->id]);

                    Log::info("Resource {$this->model->id} from {$this->table} already exists in reseller {$reseller->id}'s database. Updated source_id.");
                } else {
                    // Copy resource to reseller's database
                    $insertData = $data;
                    $insertData['source_id'] = $insertData['id'];
                    unset($insertData['id']);

                    DB::connection('reseller')->table($this->table)->insert($insertData);
                    Log::info("Copied resource {$this->model->id} from {$this->table} to reseller {$reseller->id}'s database.");
                }

            } catch (\Exception $e) {
                Log::error("Failed to copy resource {$this->model->id} from {$this->table} to reseller {$reseller->id}: " . $e->getMessage());
                continue;
            }
        }
    }
}
