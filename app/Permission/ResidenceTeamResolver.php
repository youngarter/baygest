<?php

namespace App\Permission;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Contracts\PermissionsTeamResolver;

class ResidenceTeamResolver implements PermissionsTeamResolver
{
    protected int|string|null $teamId = null;

    public function getPermissionsTeamId(): int|string|null
    {
        if ($this->teamId !== null) {
            return $this->teamId;
        }

        try {
            $tenant = Filament::getTenant();
            if ($tenant) {
                return $tenant->getKey();
            }
        } catch (\Throwable) {
            // Filament not booted yet
        }

        if (session()->has('filament_tenant_id')) {
            return session('filament_tenant_id');
        }

        $user = auth()->user();
        if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return $user->residence_id;
        }

        return null;
    }

    public function setPermissionsTeamId(int|string|Model|null $id): void
    {
        if ($id instanceof Model) {
            $id = $id->getKey();
        }

        $this->teamId = $id;
    }
}
