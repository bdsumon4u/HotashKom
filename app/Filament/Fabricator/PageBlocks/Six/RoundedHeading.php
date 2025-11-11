<?php

namespace App\Filament\Fabricator\PageBlocks\Six;

use App\Filament\Fabricator\PageBlocks\HasBlockName;
use Filament\Facades\Filament;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class RoundedHeading extends PageBlock
{
    use HasBlockName;

    public static function getBlockSchema(): Block
    {
        return Block::make(static::getBlockName())
            ->schema([
                TextInput::make('heading')
                    ->default(fn () => Filament::getTenant()->name),
                TextInput::make('subheading'),
            ]);
    }

    #[\Override]
    public static function mutateData(array $data): array
    {
        return $data;
    }
}
