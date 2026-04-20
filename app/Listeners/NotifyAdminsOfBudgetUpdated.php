<?php

namespace App\Listeners;

use App\Enums\UserRole;
use App\Events\BudgetUpdated;
use App\Models\User;
use App\Notifications\ResidenceAdminNotification;

class NotifyAdminsOfBudgetUpdated
{
    public function handle(BudgetUpdated $event): void
    {
        $budget = $event->budget;

        $admins = User::where('residence_id', $budget->residence_id)
            ->where('role', UserRole::Admin)
            ->get();

        $admins->each->notify(new ResidenceAdminNotification(
            title: 'Budget mis à jour',
            body: "Le budget \"{$budget->titre}\" a été modifié.",
        ));
    }
}
