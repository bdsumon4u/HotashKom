<?php

namespace App\Filament\Fabricator\PageBlocks\Two;

use App\Filament\Fabricator\PageBlocks\HasBlockName;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class YouTubePrice extends PageBlock
{
    use HasBlockName;

    public static function getBlockSchema(): Block
    {
        return Block::make(static::getBlockName())
            ->schema([
                TextInput::make('youtube_link'),
                TextInput::make('title'),
                RichEditor::make('description'),
                Group::make([
                    TextInput::make('price_text'),
                    TextInput::make('price_amount'),
                    TextInput::make('price_subtext')
                        ->columnSpanFull(),
                ])
                    ->columns(2),
            ]);
    }

    #[\Override]
    public static function mutateData(array $data): array
    {
        return $data;
    }
}
