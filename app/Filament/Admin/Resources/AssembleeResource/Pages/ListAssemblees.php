<?php

namespace App\Filament\Admin\Resources\AssembleeResource\Pages;

use App\Filament\Admin\Resources\AssembleeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssemblees extends ListRecords
{
    protected static string $resource = AssembleeResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
