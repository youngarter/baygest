<?php

namespace App\Policies;

use App\Models\JournalEntry;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class JournalEntryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('journal_entry.view');
    }

    public function view(User $user, JournalEntry $journalEntry): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('journal_entry.view')
            && $journalEntry->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermissionTo('journal_entry.create');
    }

    public function delete(User $user, JournalEntry $journalEntry): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('journal_entry.delete')
            && $journalEntry->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }

    public function reverse(User $user, JournalEntry $journalEntry): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('journal_entry.reverse')
            && $journalEntry->residence_id === app(PermissionRegistrar::class)->getPermissionsTeamId();
    }
}
