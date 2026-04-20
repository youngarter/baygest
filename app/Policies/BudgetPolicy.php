<?php

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class BudgetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('budget.view');
    }

    public function view(User $user, Budget $budget): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('budget.view')
            && $budget->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('budget.create');
    }

    public function update(User $user, Budget $budget): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('budget.update')
            && $budget->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function delete(User $user, Budget $budget): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('budget.delete')
            && $budget->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function restore(User $user, Budget $budget): bool
    {
        return $user->isSuperAdmin();
    }

    public function forceDelete(User $user, Budget $budget): bool
    {
        return $user->isSuperAdmin();
    }
}
