<?php

namespace App\Filament\Fabricator\PageBlocks\Six;

use App\Filament\Fabricator\PageBlocks\HasBlockName;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class AdditionalImages extends PageBlock
{
    use HasBlockName;

    public static function getBlockSchema(): Block
    {
        return Block::make(static::getBlockName())
            ->schema([
                Grid::make(2)->schema([
                    TextInput::make('columns')
                        ->default(1)
                        ->integer(),
                    Select::make('direction')
                        ->default('ltr')
                        ->options([
                            'ltr' => 'Left to Right',
                            'rtl' => 'Right to Left',
                        ]),
                ]),
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
