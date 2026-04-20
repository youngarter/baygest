<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // roles table needs team_id for scoped roles
        if (! Schema::hasColumn('roles', 'team_id')) {
            DB::statement('ALTER TABLE roles ADD COLUMN team_id BIGINT UNSIGNED NULL DEFAULT NULL AFTER id');
            DB::statement('ALTER TABLE roles ADD INDEX roles_team_id_index (team_id)');
        }

        // model_has_permissions
        if (! Schema::hasColumn('model_has_permissions', 'team_id')) {
            DB::statement('ALTER TABLE model_has_permissions DROP PRIMARY KEY');
            DB::statement('ALTER TABLE model_has_permissions ADD COLUMN team_id BIGINT UNSIGNED NOT NULL DEFAULT 0 AFTER permission_id');
        } else {
            DB::statement('ALTER TABLE model_has_permissions MODIFY COLUMN team_id BIGINT UNSIGNED NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE model_has_permissions DROP PRIMARY KEY');
        }

        DB::statement('ALTER TABLE model_has_permissions ADD PRIMARY KEY (team_id, permission_id, model_id, model_type(255))');
        DB::statement('ALTER TABLE model_has_permissions ADD INDEX model_has_permissions_team_id_index (team_id)');

        // model_has_roles
        DB::statement('ALTER TABLE model_has_roles DROP FOREIGN KEY model_has_roles_role_id_foreign');
        DB::statement('ALTER TABLE model_has_roles DROP PRIMARY KEY');

        if (! Schema::hasColumn('model_has_roles', 'team_id')) {
            DB::statement('ALTER TABLE model_has_roles ADD COLUMN team_id BIGINT UNSIGNED NOT NULL DEFAULT 0 AFTER role_id');
        }

        DB::statement('ALTER TABLE model_has_roles ADD PRIMARY KEY (team_id, role_id, model_id, model_type(255))');
        DB::statement('ALTER TABLE model_has_roles ADD INDEX model_has_roles_team_id_index (team_id)');
        DB::statement('ALTER TABLE model_has_roles ADD CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE roles DROP INDEX roles_team_id_index');
        DB::statement('ALTER TABLE roles DROP COLUMN team_id');

        DB::statement('ALTER TABLE model_has_permissions DROP INDEX model_has_permissions_team_id_index');
        DB::statement('ALTER TABLE model_has_permissions DROP PRIMARY KEY');
        DB::statement('ALTER TABLE model_has_permissions DROP COLUMN team_id');

        DB::statement('ALTER TABLE model_has_roles DROP FOREIGN KEY model_has_roles_role_id_foreign');
        DB::statement('ALTER TABLE model_has_roles DROP INDEX model_has_roles_team_id_index');
        DB::statement('ALTER TABLE model_has_roles DROP PRIMARY KEY');
        DB::statement('ALTER TABLE model_has_roles DROP COLUMN team_id');
        DB::statement('ALTER TABLE model_has_roles ADD PRIMARY KEY (role_id, model_id, model_type(255))');
        DB::statement('ALTER TABLE model_has_roles ADD CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE');
    }
};
