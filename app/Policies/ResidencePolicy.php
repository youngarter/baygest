<?php

namespace App\Policies;

use App\Models\Residence;
use App\Models\User;

class ResidencePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Residence $residence): bool
    {
        return $user->isSuperAdmin() || $user->canAccessTenant($residence);
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Residence $residence): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Residence $residence): bool
    {
        return $user->isSuperAdmin();
    }

    public function assignUsers(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
