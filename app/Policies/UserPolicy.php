<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('user.view');
    }

    public function view(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('user.view')
            && $model->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('user.create');
    }

    public function update(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('user.update')
            && $model->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) {
            return $user->isNot($model);
        }

        return $user->hasPermissionTo('user.delete')
            && $model->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId()
            && $user->isNot($model);
    }
}
