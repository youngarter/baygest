<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class UnitPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('unit.view');
    }

    public function view(User $user, Unit $unit): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('unit.view')
            && $unit->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('unit.create');
    }

    public function update(User $user, Unit $unit): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('unit.update')
            && $unit->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function delete(User $user, Unit $unit): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('unit.delete')
            && $unit->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }
}
