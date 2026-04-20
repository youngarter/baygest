<?php

namespace Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $allPermissions = PermissionEnum::values();

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions($allPermissions);

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([
            PermissionEnum::AssembleeCreate->value,
            PermissionEnum::AssembleeView->value,
            PermissionEnum::AssembleeUpdate->value,
            PermissionEnum::BudgetCreate->value,
            PermissionEnum::BudgetView->value,
            PermissionEnum::BudgetUpdate->value,
        ]);
    }
}
