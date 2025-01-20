<?php

namespace App\Filament\Fabricator\PageBlocks\Two;

use App\Filament\Fabricator\PageBlocks\HasBlockName;
use Filament\Forms\Components\Builder\Block;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;

class YouTubePrice extends PageBlock
{
    use HasBlockName;

    public static function getBlockSchema(): Block
    {
        return Block::make(static::getBlockName())
            ->schema([
                //
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }

    public static function default($record, $get, $set): array
    {
        return [
            'data' => [],
            'type' => static::getBlockName(),
        ];
    }
}