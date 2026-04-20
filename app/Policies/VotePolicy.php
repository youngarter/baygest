<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vote;
use Spatie\Permission\PermissionRegistrar;

class VotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('vote.view');
    }

    public function view(User $user, Vote $vote): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('vote.view')
            && $vote->resolution->assemblee->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('vote.create');
    }

    public function delete(User $user, Vote $vote): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('vote.delete')
            && $vote->resolution->assemblee->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }
}
