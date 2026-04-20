<?php

namespace App\Policies;

use App\Models\UnitType;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class UnitTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('unit_type.view');
    }

    public function view(User $user, UnitType $unitType): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('unit_type.view')
            && $unitType->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('unit_type.create');
    }

    public function update(User $user, UnitType $unitType): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('unit_type.update')
            && $unitType->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function delete(User $user, UnitType $unitType): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('unit_type.delete')
            && $unitType->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }
}
