<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ConvertTablesToInnoDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:convert-innodb {--connection= : The database connection to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert all tables whose storage engine is not InnoDB to InnoDB before migrations.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $connection = $this->option('connection') ?: config('database.default');
        $db = DB::connection($connection);

        $driver = $db->getDriverName();
        if ($driver !== 'mysql') {
            $this->info("Skipping – connection '{$connection}' is '{$driver}', not MySQL.");

            return self::SUCCESS;
        }

        $databaseName = $db->getDatabaseName();
        $this->info("Checking table engines in database '{$databaseName}' (connection: {$connection})...");

        $tables = $db->select(
            'SELECT TABLE_NAME, ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?',
            [$databaseName]
        );

        $convertedCount = 0;
        foreach ($tables as $table) {
            $tableName = $table->TABLE_NAME;
            $engine = $table->ENGINE;

            if (strtolower((string) $engine) !== 'innodb') {
                $this->line("Converting '{$tableName}' (engine: {$engine}) → InnoDB ...");
                try {
                    $db->statement("ALTER TABLE `{$tableName}` ENGINE=InnoDB");
                    $convertedCount++;
                } catch (\Throwable $e) {
                    $this->error("Failed converting '{$tableName}': ".$e->getMessage());
                }
            }
        }

        $this->info("Conversion complete. {$convertedCount} table(s) updated to InnoDB.");

        return self::SUCCESS;
    }
}
