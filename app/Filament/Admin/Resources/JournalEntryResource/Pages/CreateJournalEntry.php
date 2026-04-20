<?php

namespace App\Filament\Admin\Resources\JournalEntryResource\Pages;

use App\Exceptions\MissingAccountingConfigurationException;
use App\Exceptions\UnbalancedJournalEntryException;
use App\Filament\Admin\Resources\JournalEntryResource;
use App\Models\JournalEntry;
use App\Services\JournalEntryService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;

class CreateJournalEntry extends CreateRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function handleRecordCreation(array $data): JournalEntry
    {
        try {
            return app(JournalEntryService::class)->create([
                'residence_id' => filament()->getTenant()->id,
                'date'         => $data['date'],
                'reference'    => $data['reference'],
                'description'  => $data['description'] ?? null,
                'source_type'  => null,
                'source_id'    => null,
                'lines'        => $data['lines'] ?? [],
            ]);
        } catch (UnbalancedJournalEntryException $e) {
            Notification::make()
                ->danger()
                ->title('Écriture déséquilibrée')
                ->body($e->getMessage())
                ->persistent()
                ->send();

            throw new Halt();
        } catch (MissingAccountingConfigurationException $e) {
            Notification::make()
                ->danger()
                ->title('Comptabilité non initialisée')
                ->body($e->getMessage())
                ->persistent()
                ->send();

            throw new Halt();
        }
    }
}
