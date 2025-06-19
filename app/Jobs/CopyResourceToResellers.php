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

    protected $table;

    protected $idMap = [];

    /**
     * Create a new job instance.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
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

        // If not found by source_id, check if any unique column exists
        $modelClass = $this->getModelClassByTable($this->table);
        $existingByUnique = $this->findExistingRecordByUniqueColumns($this->table, $modelClass, $this->model);

        if ($existingByUnique) {
            // Update source_id in reseller's database to match original resource's ID
            DB::connection('reseller')
                ->table($this->table)
                ->where('id', $existingByUnique->id)
                ->update(['source_id' => $this->model->id]);

            // Store the ID mapping
            $this->idMap[$this->table][$this->model->id] = $existingByUnique->id;

            return $existingByUnique->id;
        }

        // Handle foreign keys in the data
        $foreignKeys = [];
        foreach ($data as $key => $value) {
            if (str_ends_with($key, '_id') && $value && $key !== 'id') {
                $foreignKeys[$key] = $value;
            }
        }

        // Resolve foreign key relationships
        if (! empty($foreignKeys)) {
            foreach ($foreignKeys as $foreignKey => $sourceId) {
                // Get the related table name
                $relatedTable = $this->getRelatedTableName($foreignKey);

                if ($relatedTable) {
                    // Find the reseller's ID for this source_id
                    $resellerId = $this->getResellerIdBySourceId($relatedTable, $sourceId);

                    if ($resellerId) {
                        // Replace the foreign key with the reseller's ID
                        $data[$foreignKey] = $resellerId;
                    } else {
                        // If related resource doesn't exist, copy it first
                        $resellerId = $this->copyRelatedResource($relatedTable, $sourceId);

                        if ($resellerId) {
                            // Replace the foreign key with the reseller's ID
                            $data[$foreignKey] = $resellerId;
                        } else {
                            // If we still can't get the related resource, remove the foreign key
                            unset($data[$foreignKey]);
                            Log::warning("Failed to copy related resource: {$relatedTable} with source_id {$sourceId}");
                        }
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
     * Get the related table name for a foreign key
     */
    protected function getRelatedTableName(string $foreignKey): ?string
    {
        // Remove '_id' suffix to get the relationship name
        $relationName = str_replace('_id', '', $foreignKey);

        // Check if the relationship method exists
        if (method_exists($this->model, $relationName)) {
            $relatedModel = $this->model->{$relationName}()->getRelated();

            return $relatedModel->getTable();
        }

        return null;
    }

    /**
     * Get reseller's ID by source_id
     */
    protected function getResellerIdBySourceId(string $table, int $sourceId): ?int
    {
        // Check if we already have this mapping
        if (isset($this->idMap[$table][$sourceId])) {
            return $this->idMap[$table][$sourceId];
        }

        // Query the reseller's database
        $resellerRecord = DB::connection('reseller')
            ->table($table)
            ->where('source_id', $sourceId)
            ->first();

        if ($resellerRecord) {
            // Store the mapping for future use
            $this->idMap[$table][$sourceId] = $resellerRecord->id;

            return $resellerRecord->id;
        }

        return null;
    }

    /**
     * Copy a related resource to reseller's database
     */
    protected function copyRelatedResource(string $table, int $sourceId): ?int
    {
        try {
            // Get the related model class
            $modelClass = $this->getModelClassByTable($table);

            if (! $modelClass) {
                Log::error("Could not determine model class for table: {$table}");

                return null;
            }

            // Find the source record
            $sourceRecord = $modelClass::find($sourceId);

            if (! $sourceRecord) {
                Log::error("Source record not found: {$table} with id {$sourceId}");

                return null;
            }

            // Check if a record with any unique identifier already exists
            $existingRecord = $this->findExistingRecordByUniqueColumns($table, $modelClass, $sourceRecord);

            if ($existingRecord) {
                // Update the existing record's source_id
                DB::connection('reseller')
                    ->table($table)
                    ->where('id', $existingRecord->id)
                    ->update(['source_id' => $sourceId]);

                // Store the mapping
                $this->idMap[$table][$sourceId] = $existingRecord->id;

                return $existingRecord->id;
            }

            // Copy the record to reseller's database
            $data = $sourceRecord->getRawOriginal();

            // Handle nested foreign keys recursively
            $foreignKeys = [];
            foreach ($data as $key => $value) {
                if (str_ends_with($key, '_id') && $value && $key !== 'id') {
                    $foreignKeys[$key] = $value;
                }
            }

            // Resolve nested foreign keys
            foreach ($foreignKeys as $foreignKey => $nestedSourceId) {
                $nestedTable = $this->getRelatedTableNameForModel($modelClass, $foreignKey);

                if ($nestedTable) {
                    $nestedResellerId = $this->getResellerIdBySourceId($nestedTable, $nestedSourceId);

                    if ($nestedResellerId) {
                        $data[$foreignKey] = $nestedResellerId;
                    } else {
                        // Recursively copy nested resource
                        $nestedResellerId = $this->copyRelatedResource($nestedTable, $nestedSourceId);
                        if ($nestedResellerId) {
                            $data[$foreignKey] = $nestedResellerId;
                        } else {
                            unset($data[$foreignKey]);
                        }
                    }
                }
            }

            // Prepare data for insertion
            $insertData = $data;
            $insertData['source_id'] = $insertData['id'];
            unset($insertData['id']);

            // Insert the data
            $newId = DB::connection('reseller')
                ->table($table)
                ->insertGetId($insertData);

            // Store the mapping
            $this->idMap[$table][$sourceId] = $newId;

            Log::info("Successfully copied related resource: {$table} {$sourceId} to reseller database");

            return $newId;

        } catch (\Exception $e) {
            Log::error("Failed to copy related resource {$table} {$sourceId}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Get model class by table name
     */
    protected function getModelClassByTable(string $table): ?string
    {
        // Common model mappings
        $modelMappings = [
            'attributes' => 'App\Models\Attribute',
            'options' => 'App\Models\Option',
            'categories' => 'App\Models\Category',
            'brands' => 'App\Models\Brand',
            'products' => 'App\Models\Product',
            'images' => 'App\Models\Image',
        ];

        return $modelMappings[$table] ?? null;
    }

    /**
     * Get all unique columns for a model
     */
    protected function getUniqueColumns(string $modelClass): array
    {
        // Multiple unique column mappings
        $uniqueColumnsMappings = [
            'App\Models\Product' => ['sku', 'slug'],
            'App\Models\Attribute' => ['name'],
            'App\Models\Option' => ['name'],
            'App\Models\Category' => ['slug', 'name'],
            'App\Models\Brand' => ['name', 'slug'],
            'App\Models\Image' => ['url'],
        ];

        return $uniqueColumnsMappings[$modelClass] ?? ['id'];
    }

    /**
     * Check if a record exists using multiple unique columns
     */
    protected function findExistingRecordByUniqueColumns(string $table, string $modelClass, Model $sourceRecord): ?object
    {
        $uniqueColumns = $this->getUniqueColumns($modelClass);

        foreach ($uniqueColumns as $uniqueColumn) {
            $uniqueValue = $sourceRecord->getAttribute($uniqueColumn);

            if ($uniqueValue) {
                $existingRecord = DB::connection('reseller')
                    ->table($table)
                    ->where($uniqueColumn, $uniqueValue)
                    ->first();

                if ($existingRecord) {
                    Log::info("Found existing record in {$table} by {$uniqueColumn}: {$uniqueValue}");

                    return $existingRecord;
                }
            }
        }

        return null;
    }

    /**
     * Get related table name for a model and foreign key
     */
    protected function getRelatedTableNameForModel(string $modelClass, string $foreignKey): ?string
    {
        $model = new $modelClass;
        $relationName = str_replace('_id', '', $foreignKey);

        if (method_exists($model, $relationName)) {
            $relatedModel = $model->{$relationName}()->getRelated();

            return $relatedModel->getTable();
        }

        return null;
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
                $this->idMap = [];
                // Configure reseller database connection
                config(['database.connections.reseller' => $reseller->getDatabaseConfig()]);

                // Purge and reconnect to ensure fresh connection
                DB::purge('reseller');
                DB::reconnect('reseller');

                // Get or create the resource
                $newId = $this->getOrCreateResource();

                // Clear reseller's cache
                $reseller->clearResellerCache($this->table);

                Log::info("Successfully copied {$this->table} {$this->model->id} to reseller {$reseller->id} [".DB::connection('reseller')->getDatabaseName().']');

            } catch (\Exception $e) {
                Log::error("Failed to copy {$this->table} {$this->model->id} to reseller {$reseller->id}: ".$e->getMessage());

                continue;
            }
        }
    }
}
