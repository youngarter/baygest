<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Jobs\SendPasswordResetEmail;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = bcrypt(Str::random(32));
        $data['residence_id'] = filament()->getTenant()?->id;

        return $data;
    }

    protected function afterCreate(): void
    {
        $residenceId = filament()->getTenant()?->id;
        if ($residenceId) {
            $this->record->residences()->attach($residenceId);
        }
        SendPasswordResetEmail::dispatch($this->record);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Utilisateur créé avec succès';
    }
}
