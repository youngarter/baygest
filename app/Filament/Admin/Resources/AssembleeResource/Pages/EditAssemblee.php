<?php

namespace App\Filament\Admin\Resources\AssembleeResource\Pages;

use App\Filament\Admin\Resources\AssembleeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssemblee extends EditRecord
{
    protected static string $resource = AssembleeResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
