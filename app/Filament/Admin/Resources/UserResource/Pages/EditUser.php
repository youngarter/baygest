<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Jobs\SendPasswordResetEmail;
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
        $roleChanged = isset($data['role']) && $data['role'] !== $original['role'];

        if ($roleChanged) {
            $data['onboarding_email_sent_at'] = Carbon::now();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $original = $this->record->getOriginal();
        $roleChanged = $this->record->role !== $original['role'];

        if ($roleChanged) {
            SendPasswordResetEmail::dispatch($this->record);
        }
    }

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
        ];
    }
}
