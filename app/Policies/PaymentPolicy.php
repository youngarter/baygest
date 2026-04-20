<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('payment.view');
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('payment.view')
            && $payment->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('payment.create');
    }

    public function delete(User $user, Payment $payment): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('payment.delete')
            && $payment->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }
}
