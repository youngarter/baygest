<?php

namespace App\Filament\Superadmin\Resources\UserOnboardingAuditResource\Pages;

use App\Filament\Superadmin\Resources\UserOnboardingAuditResource;
use Filament\Resources\Pages\ListRecords;

class ListUserOnboardingAudits extends ListRecords
{
    protected static string $resource = UserOnboardingAuditResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
