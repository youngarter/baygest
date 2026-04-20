<?php

namespace App\Filament\Admin\Resources\Assemblees\Pages;

use App\Filament\Admin\Resources\Assemblees\AssembleeResource;
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
