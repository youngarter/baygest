<?php

namespace App\Filament\Superadmin\Resources\ResidenceResource\Pages;

use App\Filament\Superadmin\Resources\ResidenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResidences extends ListRecords
{
    protected static string $resource = ResidenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
