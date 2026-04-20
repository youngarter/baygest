<?php

namespace App\Listeners;

use App\Enums\UserRole;
use App\Events\AssembleeCreated;
use App\Models\User;
use App\Notifications\ResidenceAdminNotification;

class NotifyAdminsOfAssembleeCreated
{
    public function handle(AssembleeCreated $event): void
    {
        $assemblee = $event->assemblee;

        $admins = User::where('residence_id', $assemblee->residence_id)
            ->where('role', UserRole::Admin)
            ->get();

        $admins->each->notify(new ResidenceAdminNotification(
            title: 'Nouvelle assemblée créée',
            body: "L'assemblée \"{$assemblee->titre}\" a été ajoutée pour l'année {$assemblee->annee_syndic}.",
        ));
    }
}
