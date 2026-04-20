<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;
use Spatie\Permission\PermissionRegistrar;

class VendorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('vendor.view');
    }

    public function view(User $user, Vendor $vendor): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('vendor.view')
            && $vendor->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('vendor.create');
    }

    public function update(User $user, Vendor $vendor): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('vendor.update')
            && $vendor->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function delete(User $user, Vendor $vendor): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('vendor.delete')
            && $vendor->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }
}
