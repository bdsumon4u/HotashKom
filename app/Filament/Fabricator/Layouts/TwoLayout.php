<?php

namespace App\Filament\Fabricator\Layouts;

use App\Filament\Fabricator\PageBlocks\Two\YouTubePrice;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Z3d0X\FilamentFabricator\Layouts\Layout;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;

class TwoLayout extends Layout
{
    protected static ?string $name = 'two';

    public static function getPageBlocks(?PageContract $record, Get $get, Set $set): array
    {
        return [
            YouTubePrice::default($record, $get, $set),
        ];
    }
}