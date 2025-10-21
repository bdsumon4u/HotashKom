<?php

namespace App\Filament\Fabricator\PageBlocks\Four;

use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Filament\Fabricator\PageBlocks\HasBlockName;
use Filament\Facades\Filament;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Header extends PageBlock
{
    use HasBlockName;

    public static function getBlockSchema(): Block
    {
        return Block::make(static::getBlockName())
            ->schema([
                Hidden::make('thumbnail')
                    ->default(fn () => Filament::getTenant()->base_image->src),
                TextInput::make('title')
                    ->required(),
                TinyEditor::make('content')
                    ->required(),
            ]);
    }

    #[\Override]
    public static function mutateData(array $data): array
    {
        return $data;
    }
}
