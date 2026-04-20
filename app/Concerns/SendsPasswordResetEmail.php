<?php

namespace App\Concerns;

use App\Events\PasswordResetLinkRequested;
use App\Jobs\SendPasswordResetEmail;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;

trait SendsPasswordResetEmail
{
    private static function sendPasswordResetEmail(User $user): void
    {
        $user->update(['onboarding_email_sent_at' => Carbon::now()]);
        SendPasswordResetEmail::dispatch($user);
        PasswordResetLinkRequested::dispatch($user);

        Notification::make()
            ->success()
            ->title('Email envoyé avec succès')
            ->send();
    }
}
