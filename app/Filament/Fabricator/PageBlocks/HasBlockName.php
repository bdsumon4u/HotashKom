<?php

namespace App\Filament\Fabricator\PageBlocks;

use Illuminate\Support\Str;

trait HasBlockName
{
    public static function getBlockName(): string
    {
        return Str::of(static::class)->afterLast('PageBlocks\\')->kebab()->replace('\-', '.')->__toString();
    }

    public static function default(array $data = []): array
    {
        return [
            'data' => $data,
            'type' => static::getBlockName(),
        ];
    }
}
