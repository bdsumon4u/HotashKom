<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

final class MigratePublicStorage extends Command
{
    protected $signature = 'storage:migrate-public';

    protected $description = 'Migrate storage/app/public assets into public/storage safely.';

    public function handle(): int
    {
        $sourceDirectory = storage_path('app/public');
        $targetDirectory = public_path('storage');

        $this->info("Source: {$sourceDirectory}");
        $this->info("Target: {$targetDirectory}");

        if (! File::exists($sourceDirectory)) {
            $this->error('Source directory does not exist. Nothing to migrate.');

            return self::FAILURE;
        }

        if (! is_link($targetDirectory) && ! is_dir($targetDirectory) && File::exists($targetDirectory)) {
            $this->error('Target exists but is neither a directory nor a symlink. Aborting for safety.');

            return self::FAILURE;
        }

        if (is_link($targetDirectory)) {
            return $this->replaceSymlinkWithMovedDirectory($sourceDirectory, $targetDirectory);
        }

        return $this->copyIntoExistingDirectory($sourceDirectory, $targetDirectory);
    }

    private function copyIntoExistingDirectory(string $sourceDirectory, string $targetDirectory): int
    {
        File::ensureDirectoryExists($targetDirectory);

        if (! is_dir($targetDirectory)) {
            $this->error('Could not ensure target directory exists.');

            return self::FAILURE;
        }

        $this->warn('Target is a normal directory. Copying all files from source into target...');

        if (! File::copyDirectory($sourceDirectory, $targetDirectory)) {
            $this->error('Copy failed. No move operation was performed.');

            return self::FAILURE;
        }

        $this->info('Copy completed successfully.');

        return self::SUCCESS;
    }

    private function replaceSymlinkWithMovedDirectory(string $sourceDirectory, string $targetDirectory): int
    {
        $sourceParentDirectory = dirname($sourceDirectory);
        $sourceGitignoreFile = $sourceDirectory.DIRECTORY_SEPARATOR.'.gitignore';

        $this->warn('Target is a symlink. Removing symlink...');

        if (! @unlink($targetDirectory)) {
            $this->error('Failed to unlink existing symlink. Aborting.');

            return self::FAILURE;
        }

        $this->warn('Symlink removed. Moving source directory into public as storage...');

        if (! @rename($sourceDirectory, $targetDirectory)) {
            $this->error('Move failed. Attempting to recreate target directory...');
            File::ensureDirectoryExists($targetDirectory);

            return self::FAILURE;
        }

        File::ensureDirectoryExists($sourceParentDirectory);
        File::ensureDirectoryExists($sourceDirectory);
        File::put($sourceGitignoreFile, "*\n!.gitignore\n");

        $this->info('Migration completed successfully. public/storage is now a real directory.');

        return self::SUCCESS;
    }
}
