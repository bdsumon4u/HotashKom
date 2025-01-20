<?php

namespace App\Filament\Fabricator\Layouts;

use App\Filament\Fabricator\PageBlocks\One\NormalText;
use App\Filament\Fabricator\PageBlocks\One\OneColList;
use App\Filament\Fabricator\PageBlocks\One\TwoColList;
use App\Filament\Fabricator\PageBlocks\One\YouTubeHeader;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Z3d0X\FilamentFabricator\Layouts\Layout;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;

class OneLayout extends Layout
{
    protected static ?string $name = 'one';

    public static function getPageBlocks(?PageContract $record, Get $get, Set $set): array
    {
        return [
            YouTubeHeader::default($record, $get, $set),
            TwoColList::default($record, $get, $set),
            NormalText::default($record, $get, $set),
            OneColList::default($record, $get, $set),
        ];
    }
}