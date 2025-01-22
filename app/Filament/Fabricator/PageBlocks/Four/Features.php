<?php

namespace App\Filament\Fabricator\PageBlocks\Four;

use App\Filament\Fabricator\PageBlocks\HasBlockName;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Features extends PageBlock
{
    use HasBlockName;

    public static function getBlockSchema(): Block
    {
        return Block::make(static::getBlockName())
            ->schema([
                TextInput::make('title')
                    ->required(),
                Repeater::make('items')
                    ->schema([
                        FileUpload::make('image')
                            ->image(),
                        TextInput::make('name')
                            ->required(),
                    ])
                    ->collapsed()
                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
