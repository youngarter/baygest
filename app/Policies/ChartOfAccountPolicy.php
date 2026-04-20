<?php

namespace App\Policies;

use App\Models\ChartOfAccount;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class ChartOfAccountPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('account.view');
    }

    public function view(User $user, ChartOfAccount $chartOfAccount): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('account.view')
            && $chartOfAccount->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('account.create');
    }

    public function update(User $user, ChartOfAccount $chartOfAccount): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('account.update')
            && $chartOfAccount->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function delete(User $user, ChartOfAccount $chartOfAccount): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('account.delete')
            && $chartOfAccount->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }
}
