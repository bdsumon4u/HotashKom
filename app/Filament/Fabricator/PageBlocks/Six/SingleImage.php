<?php

namespace App\Filament\Fabricator\PageBlocks\Six;

use App\Filament\Fabricator\PageBlocks\HasBlockName;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class SingleImage extends PageBlock
{
    use HasBlockName;

    public static function getBlockSchema(): Block
    {
        return Block::make(static::getBlockName())
            ->schema([
                FileUpload::make('image')
                    ->image(),
            ]);
    }

    #[\Override]
    public static function mutateData(array $data): array
    {
        return $data;
    }
}
