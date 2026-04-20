<?php

namespace App\Policies;

use App\Models\BudgetLine;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class BudgetLinePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('budget_line.view');
    }

    public function view(User $user, BudgetLine $budgetLine): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('budget_line.view')
            && $budgetLine->budget->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('budget_line.create');
    }

    public function update(User $user, BudgetLine $budgetLine): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('budget_line.update')
            && $budgetLine->budget->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function delete(User $user, BudgetLine $budgetLine): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('budget_line.delete')
            && $budgetLine->budget->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }
}
