<?php

namespace App\Filament\Fabricator\PageBlocks\Four;

use App\Filament\Fabricator\PageBlocks\HasBlockName;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class AdditionalImages extends PageBlock
{
    use HasBlockName;

    public static function getBlockSchema(): Block
    {
        return Block::make(static::getBlockName())
            ->schema([
                TextInput::make('title')
                    ->required(),
                FileUpload::make('images')
                    ->image()
                    ->multiple()
                    ->reorderable(),
            ]);
    }

    #[\Override]
    public static function mutateData(array $data): array
    {
        return $data;
    }
}
