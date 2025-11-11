<?php

namespace App\Filament\Fabricator\Layouts;

use Filament\Facades\Filament;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Z3d0X\FilamentFabricator\Layouts\Layout;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;

class SixLayout extends Layout
{
    protected static ?string $name = 'six';

    public static function getPageBlocks(?PageContract $record, Get $get, Set $set): array
    {
        return [
            //
        ];
    }
}
