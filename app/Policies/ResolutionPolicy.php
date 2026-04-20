<?php

namespace App\Policies;

use App\Models\Resolution;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class ResolutionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('resolution.view');
    }

    public function view(User $user, Resolution $resolution): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('resolution.view')
            && $resolution->assemblee->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('resolution.create');
    }

    public function update(User $user, Resolution $resolution): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('resolution.update')
            && $resolution->assemblee->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function delete(User $user, Resolution $resolution): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('resolution.delete')
            && $resolution->assemblee->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }
}
