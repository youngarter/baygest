<?php

namespace App\Listeners;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Carbon;

class MarkEmailAsVerifiedOnPasswordReset
{
    public function handle(PasswordReset $event): void
    {
        $event->user->forceFill([
            'email_verified_at' => Carbon::now(),
        ])->save();
    }
}
