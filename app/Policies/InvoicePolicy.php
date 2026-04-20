<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('invoice.view');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('invoice.view')
            && $invoice->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('invoice.create');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('invoice.update')
            && $invoice->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('invoice.delete')
            && $invoice->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }
}
