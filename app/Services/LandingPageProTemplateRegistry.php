<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LandingPageProTemplateRegistry
{
    public function all(): Collection
    {
        $root = resource_path('views/landing-pro');

        if (! File::isDirectory($root)) {
            return collect();
        }

        return collect(File::directories($root))
            ->map(function (string $directory): array {
                $key = basename($directory);

                return [
                    'key' => $key,
                    'name' => Str::of($key)->replace('-', ' ')->title()->toString(),
                    'view' => 'landing-pro.'.$key.'.land',
                    'exists' => File::exists($directory.'/land.blade.php'),
                ];
            })
            ->filter(fn (array $template): bool => $template['exists'])
            ->values();
    }

    public function options(): array
    {
        return $this->all()
            ->pluck('name', 'key')
            ->toArray();
    }

    public function has(string $key): bool
    {
        return $this->all()->contains(fn (array $template): bool => $template['key'] === $key);
    }

    public function viewFor(string $key): ?string
    {
        return $this->all()
            ->firstWhere('key', $key)['view'] ?? null;
    }
}
