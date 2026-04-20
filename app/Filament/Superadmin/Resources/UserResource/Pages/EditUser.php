<?php

namespace App\Filament\Superadmin\Resources\UserResource\Pages;

use App\Filament\Superadmin\Resources\UserResource;
use App\Jobs\SendPasswordResetEmail;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Password;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $original = $this->record->getOriginal();
        $changed = false;

        if (isset($data['role']) && $data['role'] !== $original['role']) {
            $changed = true;
        }

        if (isset($data['residences'])) {
            $currentResidences = $this->record->residences()->pluck('residences.id')->sort()->values()->toArray();
            $newResidences = collect($data['residences'])->sort()->values()->toArray();
            if ($newResidences !== $currentResidences) {
                $changed = true;
            }
            $data['residence_id'] = $data['residences'][0] ?? null;
        }

        if ($changed) {
            $data['onboarding_email_sent_at'] = Carbon::now();
            $this->shouldSendNotification = true;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // If the primary residence was removed from the pivot, point to another or null
        $record = $this->record->refresh();
        if ($record->residence_id && ! $record->residences()->whereKey($record->residence_id)->exists()) {
            $record->updateQuietly(['residence_id' => $record->residences()->first()?->id]);
        }

        if ($this->shouldSendNotification) {
            SendPasswordResetEmail::dispatch($record);
        }
    }

    private bool $shouldSendNotification = false;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sendPasswordReset')
                ->label('Envoyer lien de réinitialisation')
                ->icon('heroicon-o-key')
                ->requiresConfirmation()
                ->action(function () {
                    Password::sendResetLink(['email' => $this->record->email]);
                    $this->record->update(['onboarding_email_sent_at' => Carbon::now()]);
                    SendPasswordResetEmail::dispatch($this->record);

                    Notification::make()
                        ->success()
                        ->title('Email envoyé avec succès')
                        ->send();
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
