<?php

namespace App\Filament\Superadmin\Resources\ResidenceResource\Pages;

use App\Actions\InitializeResidenceAccounting;
use App\Filament\Superadmin\Resources\ResidenceResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditResidence extends EditRecord
{
    protected static string $resource = ResidenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('initializeAccounting')
                ->label('Initialiser la comptabilité')
                ->icon('heroicon-o-calculator')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Initialiser le plan comptable')
                ->modalDescription('Cette action créera le plan comptable marocain (15 comptes) et configurera la comptabilité de cette résidence. Elle est idempotente : les comptes déjà existants ne seront pas dupliqués.')
                ->modalSubmitActionLabel('Initialiser')
                ->action(function () {
                    $result = app(InitializeResidenceAccounting::class)->execute($this->record);

                    $message = $result['already_initialized']
                        ? "Comptabilité mise à jour — {$result['accounts_created']} compte(s) ajouté(s)."
                        : "Comptabilité initialisée — {$result['accounts_created']} compte(s) créé(s).";

                    Notification::make()
                        ->success()
                        ->title('Comptabilité initialisée')
                        ->body($message)
                        ->send();
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
