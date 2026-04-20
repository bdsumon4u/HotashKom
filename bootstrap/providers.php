<?php

use App\Providers\AppServiceProvider;
use App\Providers\BladeServiceProvider;
use App\Providers\ComposerServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\Filament\LandingPanelProvider;
use App\Providers\TelescopeServiceProvider;
use Yajra\DataTables\DataTablesServiceProvider;

return [
    AppServiceProvider::class,
    EventServiceProvider::class,
    BladeServiceProvider::class,
    ComposerServiceProvider::class,
    LandingPanelProvider::class,
    TelescopeServiceProvider::class,
    BladeServiceProvider::class,
    DataTablesServiceProvider::class,
];
