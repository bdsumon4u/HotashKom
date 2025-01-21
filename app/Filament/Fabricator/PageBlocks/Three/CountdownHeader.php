<?php

namespace App\Filament\Fabricator\PageBlocks\Three;

use App\Filament\Fabricator\PageBlocks\HasBlockName;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class CountdownHeader extends PageBlock
{
    use HasBlockName;

    public static function getBlockSchema(): Block
    {
        return Block::make(static::getBlockName())
            ->schema([
                DateTimePicker::make('deadline'),
                TextInput::make('title'),
                TextInput::make('subtitle'),
                TextInput::make('note'),
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}