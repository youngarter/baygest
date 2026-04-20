<?php

namespace App\Listeners;

use App\Enums\UserRole;
use App\Events\BudgetCreated;
use App\Models\User;
use App\Notifications\ResidenceAdminNotification;

class NotifyAdminsOfBudgetCreated
{
    public function handle(BudgetCreated $event): void
    {
        $budget = $event->budget;

        $admins = User::where('residence_id', $budget->residence_id)
            ->where('role', UserRole::Admin)
            ->get();

        $admins->each->notify(new ResidenceAdminNotification(
            title: 'Nouveau budget créé',
            body: "Le budget \"{$budget->titre}\" a été ajouté.",
        ));
    }
}
