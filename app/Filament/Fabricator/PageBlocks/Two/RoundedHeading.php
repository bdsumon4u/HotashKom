<?php

namespace App\Filament\Fabricator\PageBlocks\Two;

use App\Filament\Fabricator\PageBlocks\HasBlockName;
use Filament\Facades\Filament;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class RoundedHeading extends PageBlock
{
    use HasBlockName;

    public static function getBlockSchema(): Block
    {
        return Block::make(static::getBlockName())
            ->schema([
                Hidden::make('thumbnail')
                    ->default(fn () => Filament::getTenant()->base_image->src),
                TextInput::make('heading')
                    ->required(),
                TextInput::make('subheading'),
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
