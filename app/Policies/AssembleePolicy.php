<?php

namespace App\Policies;

use App\Models\Assemblee;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class AssembleePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('assemblee.view');
    }

    public function view(User $user, Assemblee $assemblee): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('assemblee.view')
            && $assemblee->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('assemblee.create');
    }

    public function update(User $user, Assemblee $assemblee): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('assemblee.update')
            && $assemblee->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function delete(User $user, Assemblee $assemblee): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('assemblee.delete')
            && $assemblee->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function restore(User $user, Assemblee $assemblee): bool
    {
        return $user->isSuperAdmin();
    }

    public function forceDelete(User $user, Assemblee $assemblee): bool
    {
        return $user->isSuperAdmin();
    }
}
