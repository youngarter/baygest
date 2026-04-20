<?php

namespace App\Filament\Superadmin\Resources\UserResource\Pages;

use App\Filament\Superadmin\Resources\UserResource;
use App\Jobs\SendPasswordResetEmail;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = bcrypt(Str::random(32));
        if (!empty($data['residences'])) {
            $data['residence_id'] = $data['residences'][0];
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        SendPasswordResetEmail::dispatch($this->record);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Utilisateur créé avec succès';
    }
}
