<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\AdminSitePanelProvider;
use App\Providers\Filament\SuperadminPanelProvider;
use App\Providers\FortifyServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    AdminSitePanelProvider::class,
    SuperadminPanelProvider::class,
    FortifyServiceProvider::class,
];
