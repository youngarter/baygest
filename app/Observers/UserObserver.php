<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\UserInvitationNotification;
use Illuminate\Support\Facades\Password;

class UserObserver
{
    public function created(User $user): void
    {
        if (empty($user->password)) {
            $token = Password::broker()->createToken($user);

            $resetUrl = route('password.reset', [
                'token' => $token,
                'email' => $user->email,
            ]);

            $user->notify(new UserInvitationNotification($resetUrl));
        }
    }

    public function updated(User $user): void {}
}
