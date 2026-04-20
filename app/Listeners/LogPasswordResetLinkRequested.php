<?php

namespace App\Listeners;

use App\Events\PasswordResetLinkRequested;
use OwenIt\Auditing\Models\Audit;

class LogPasswordResetLinkRequested
{
    public function handle(PasswordResetLinkRequested $event): void
    {
        Audit::create([
            'user_type' => get_class($event->user),
            'user_id' => auth()->id(),
            'event' => 'password_reset_requested',
            'auditable_type' => get_class($event->user),
            'auditable_id' => $event->user->id,
            'old_values' => null,
            'new_values' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
