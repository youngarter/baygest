<?php

namespace App\Filament\Admin\Resources\Assemblees\Pages;

use App\Filament\Admin\Resources\Assemblees\AssembleeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditAssemblee extends EditRecord
{
    protected static string $resource = AssembleeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
