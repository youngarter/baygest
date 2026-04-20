<?php

namespace App\Filament\Admin\Resources\AccountingConfigResource\Pages;

use App\Filament\Admin\Resources\AccountingConfigResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAccountingConfig extends EditRecord
{
    protected static string $resource = AccountingConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Configuration enregistrée');
    }
}
