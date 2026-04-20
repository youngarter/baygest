<?php

namespace App\Filament\Admin\Resources\ChartOfAccountResource\Pages;

use App\Filament\Admin\Resources\ChartOfAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChartOfAccount extends EditRecord
{
    protected static string $resource = ChartOfAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
