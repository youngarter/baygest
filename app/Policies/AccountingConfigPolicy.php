<?php

namespace App\Policies;

use App\Models\AccountingConfig;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class AccountingConfigPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('accounting_config.view');
    }

    public function view(User $user, AccountingConfig $accountingConfig): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('accounting_config.view')
            && $accountingConfig->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function update(User $user, AccountingConfig $accountingConfig): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('accounting_config.update')
            && $accountingConfig->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }
}
