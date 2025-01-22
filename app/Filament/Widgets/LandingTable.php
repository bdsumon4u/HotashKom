<?php

namespace App\Filament\Widgets;

use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Z3d0X\FilamentFabricator\Resources\PageResource;

class LandingTable extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return PageResource::table($table)
            ->query(PageResource::getEloquentQuery());
    }
}
