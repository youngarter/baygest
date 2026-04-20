<?php

namespace App\Filament\Superadmin\Resources\PermissionManagement\Pages;

use App\Filament\Superadmin\Resources\PermissionManagement\PermissionManagementResource;
use Filament\Resources\Pages\ListRecords;

class ListPermissionManagement extends ListRecords
{
    protected static string $resource = PermissionManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
