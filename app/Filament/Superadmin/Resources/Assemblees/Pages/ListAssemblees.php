<?php

namespace App\Filament\Superadmin\Resources\Assemblees\Pages;

use App\Filament\Superadmin\Resources\Assemblees\AssembleeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAssemblees extends ListRecords
{
    protected static string $resource = AssembleeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
