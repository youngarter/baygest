<?php

namespace App\Services;

use App\Enums\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use OwenIt\Auditing\Models\Audit;
use Spatie\Permission\PermissionRegistrar;

class PermissionService
{
    /** @return array<string> */
    public function allowedPermissions(): array
    {
        return Permission::values();
    }

    /**
     * Get permissions for a user scoped to a specific residence (team).
     * Queries the DB directly to avoid Spatie's model-level cache across multiple team IDs.
     *
     * @return array<string>
     */
    public function getPermissionsForResidence(User $user, int $residenceId): array
    {
        return DB::table('model_has_permissions')
            ->join('permissions', 'permissions.id', '=', 'model_has_permissions.permission_id')
            ->where('model_has_permissions.model_id', $user->getKey())
            ->where('model_has_permissions.model_type', $user->getMorphClass())
            ->where('model_has_permissions.team_id', $residenceId)
            ->pluck('permissions.name')
            ->toArray();
    }

    /**
     * Validates, syncs, and audits permission changes for an Admin user scoped to a residence (team).
     * Uses direct DB operations to avoid Spatie cache inconsistencies in teams mode.
     *
     * @param  array<string>  $permissions
     */
    public function updateUserPermissions(User $admin, array $permissions, User $actor, int $residenceId): void
    {
        if (! $admin->isAdmin()) {
            throw new InvalidArgumentException('La cible doit être un utilisateur Admin.');
        }

        $invalid = array_diff($permissions, $this->allowedPermissions());
        if (! empty($invalid)) {
            throw new InvalidArgumentException('Permissions non autorisées : '.implode(', ', $invalid));
        }

        $oldPermissions = $this->getPermissionsForResidence($admin, $residenceId);

        $this->syncPermissionsForTeam($admin, $permissions, $residenceId);

        $newPermissions = $this->getPermissionsForResidence($admin, $residenceId);

        // Bust Spatie's global cache so subsequent hasPermissionTo() calls reflect the change
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Audit::create([
            'user_type' => User::class,
            'user_id' => $actor->getKey(),
            'event' => 'permission_updated',
            'auditable_type' => User::class,
            'auditable_id' => (string) $admin->getKey(),
            'old_values' => json_encode(['permissions' => $oldPermissions, 'residence_id' => $residenceId]),
            'new_values' => json_encode(['permissions' => $newPermissions, 'residence_id' => $residenceId]),
            'url' => request()->fullUrl(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'tags' => 'permission_management',
        ]);
    }

    /**
     * Sync permissions for a specific team (residence) using direct DB operations.
     *
     * @param  array<string>  $permissionNames
     */
    private function syncPermissionsForTeam(User $user, array $permissionNames, int $residenceId): void
    {
        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissionNames)
            ->pluck('id');

        // Delete existing permissions for this team only
        DB::table('model_has_permissions')
            ->where('model_id', $user->getKey())
            ->where('model_type', $user->getMorphClass())
            ->where('team_id', $residenceId)
            ->delete();

        // Insert new permissions scoped to this team
        $rows = $permissionIds->map(fn (int $id): array => [
            'permission_id' => $id,
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
            'team_id' => $residenceId,
        ])->all();

        if (! empty($rows)) {
            DB::table('model_has_permissions')->insert($rows);
        }
    }
}
