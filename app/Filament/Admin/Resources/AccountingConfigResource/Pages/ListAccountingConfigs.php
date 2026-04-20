<?php

namespace App\Filament\Admin\Resources\AccountingConfigResource\Pages;

use App\Filament\Admin\Resources\AccountingConfigResource;
use App\Models\AccountingConfig;
use Filament\Resources\Pages\ListRecords;

class ListAccountingConfigs extends ListRecords
{
    protected static string $resource = AccountingConfigResource::class;

    public function mount(): void
    {
        $config = AccountingConfig::where('residence_id', filament()->getTenant()?->id)->first();

        if ($config) {
            $this->redirect(AccountingConfigResource::getUrl('edit', ['record' => $config]));

            return;
        }

        parent::mount();
    }
}
