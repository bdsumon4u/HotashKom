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
    protected $idMap = [];

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
     * Get or create a resource in reseller's database
     */
    protected function getOrCreateResource(): int
    {
        // If we already have the ID mapping, return it
        if (isset($this->idMap[$this->table][$this->model->id])) {
            return $this->idMap[$this->table][$this->model->id];
        }

        $data = $this->model->getRawOriginal();

        // First check if the resource's ID exists in source_id column
        $existingBySourceId = DB::connection('reseller')
            ->table($this->table)
            ->where('source_id', $this->model->id)
            ->first();

        if ($existingBySourceId) {
            // Resource already exists with this source_id, store mapping and return
            $this->idMap[$this->table][$this->model->id] = $existingBySourceId->id;
            return $existingBySourceId->id;
        }

        // If not found by source_id, check if unique column exists
        $existingByUnique = DB::connection('reseller')
            ->table($this->table)
            ->where($this->uniqueColumn, $data[$this->uniqueColumn])
            ->first();

        if ($existingByUnique) {
            // Update source_id in reseller's database to match original resource's ID
            DB::connection('reseller')
                ->table($this->table)
                ->where($this->uniqueColumn, $data[$this->uniqueColumn])
                ->update(['source_id' => $this->model->id]);

            // Store the ID mapping
            $this->idMap[$this->table][$this->model->id] = $existingByUnique->id;
            return $existingByUnique->id;
        }

        // Handle foreign keys in the data
        $foreignKeys = [];
        foreach ($data as $key => $value) {
            if (str_ends_with($key, '_id') && $value) {
                $foreignKeys[$key] = $value;
            }
        }

        // If we have foreign keys, get all related IDs in one query per table
        if (!empty($foreignKeys)) {
            $relatedIds = [];
            foreach ($foreignKeys as $key => $value) {
                // Get the relationship method name by removing '_id' from the foreign key
                $relationName = str_replace('_id', '', $key);

                // Check if the relationship method exists
                if (method_exists($this->model, $relationName)) {
                    $relatedModel = $this->model->{$relationName}()->getRelated();
                    $relatedTable = $relatedModel->getTable();

                    if (!isset($relatedIds[$relatedTable])) {
                        $relatedIds[$relatedTable] = [];
                    }
                    $relatedIds[$relatedTable][] = $value;
                }
            }

            // Get all related IDs in one query per table
            foreach ($relatedIds as $table => $ids) {
                $existingRelated = DB::connection('reseller')
                    ->table($table)
                    ->whereIn('source_id', $ids)
                    ->get(['id', 'source_id']);

                foreach ($existingRelated as $related) {
                    if (isset($this->idMap[$table][$related->source_id])) {
                        continue;
                    }
                    $this->idMap[$table][$related->source_id] = $related->id;
                }
            }

            // Update foreign keys with new IDs
            foreach ($foreignKeys as $key => $value) {
                // Get the relationship method name by removing '_id' from the foreign key
                $relationName = str_replace('_id', '', $key);

                // Check if the relationship method exists
                if (method_exists($this->model, $relationName)) {
                    $relatedModel = $this->model->{$relationName}()->getRelated();
                    $relatedTable = $relatedModel->getTable();

                    if (isset($this->idMap[$relatedTable][$value])) {
                        $data[$key] = $this->idMap[$relatedTable][$value];
                    }
                }
            }
        }

        // Prepare data for insertion
        $insertData = $data;
        // Set source_id to original ID and remove id from data
        $insertData['source_id'] = $insertData['id'];
        unset($insertData['id']);

        // Insert the data and get the new auto-generated ID
        $newId = DB::connection('reseller')
            ->table($this->table)
            ->insertGetId($insertData);

        // Store the ID mapping
        $this->idMap[$this->table][$this->model->id] = $newId;

        return $newId;
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
                // Configure reseller database connection
                config(['database.connections.reseller' => $reseller->getDatabaseConfig()]);

                // Purge and reconnect to ensure fresh connection
                DB::purge('reseller');
                DB::reconnect('reseller');

                // Get or create the resource
                $newId = $this->getOrCreateResource();

                // Clear reseller's cache
                $reseller->clearResellerCache($this->table);

                Log::info("Successfully copied {$this->table} {$this->model->id} to reseller {$reseller->id}");

            } catch (\Exception $e) {
                Log::error("Failed to copy {$this->table} {$this->model->id} to reseller {$reseller->id}: " . $e->getMessage());
                continue;
            }
        }
    }
}
