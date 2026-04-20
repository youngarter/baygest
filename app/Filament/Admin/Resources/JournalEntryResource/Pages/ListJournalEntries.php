<?php

namespace App\Filament\Admin\Resources\JournalEntryResource\Pages;

use App\Exceptions\MissingAccountingConfigurationException;
use App\Exceptions\UnbalancedJournalEntryException;
use App\Filament\Admin\Resources\JournalEntryResource;
use App\Models\JournalEntry;
use App\Services\JournalEntryService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListJournalEntries extends ListRecords
{
    protected static string $resource = JournalEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }

    protected function getTableRecordActions(): array
    {
        return [
            Actions\Action::make('reverse')
                ->label('Extourner')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Extourner l\'écriture')
                ->modalDescription('Une écriture miroir (débit ↔ crédit) sera créée et comptabilisée.')
                ->visible(fn (JournalEntry $record): bool => $record->isPosted())
                ->action(function (JournalEntry $record): void {
                    try {
                        app(JournalEntryService::class)->reverse($record);

                        Notification::make()
                            ->success()
                            ->title('Extourne créée')
                            ->body("L'écriture EXTOURNE-{$record->reference} a été comptabilisée.")
                            ->send();
                    } catch (UnbalancedJournalEntryException | MissingAccountingConfigurationException $e) {
                        Notification::make()
                            ->danger()
                            ->title('Erreur')
                            ->body($e->getMessage())
                            ->send();
                    }
                }),
        ];
    }
}
