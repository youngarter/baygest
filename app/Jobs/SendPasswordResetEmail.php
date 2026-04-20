<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Password;

class SendPasswordResetEmail implements ShouldQueue
{
    use Queueable;

    public function __construct(private User $user)
    {
    }

    public function handle(): void
    {
        Password::sendResetLink(['email' => $this->user->email]);
        $this->user->update(['onboarding_email_sent_at' => Carbon::now()]);
    }
}
