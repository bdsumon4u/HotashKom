<?php

namespace App\Filament\Pages;

use Filament\Actions;
use Filament\Facades\Filament;
use Z3d0X\FilamentFabricator\Resources\PageResource\Pages\CreatePage;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getTitle(): string
    {
        return Filament::getTenant()->name;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New landing')
                ->url(fn () => CreatePage::getUrl()),
        ];  
    }
}
