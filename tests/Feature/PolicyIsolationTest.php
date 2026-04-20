<?php

use App\Enums\UserRole;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\Residence;
use App\Models\Unit;
use App\Models\User;
use App\Policies\ChartOfAccountPolicy;
use App\Policies\JournalEntryPolicy;
use App\Policies\UnitPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(\Database\Seeders\PermissionSeeder::class);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

function userWithPermission(int $residenceId, string $permission): User
{
    // Set team context BEFORE creating the permission assignment
    app(PermissionRegistrar::class)->setPermissionsTeamId($residenceId);

    $user = User::factory()->create(['residence_id' => $residenceId, 'role' => UserRole::Admin]);
    $perm = Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
    $user->givePermissionTo($perm);

    return $user;
}

function superAdmin(): User
{
    return User::factory()->create(['role' => UserRole::SuperAdmin]);
}

// ── UnitPolicy ───────────────────────────────────────────────────────────────

it('UnitPolicy::viewAny grants access when user has unit.view for the residence', function () {
    $residence = Residence::factory()->create();
    $user      = userWithPermission($residence->id, 'unit.view');

    app(PermissionRegistrar::class)->setPermissionsTeamId($residence->id);

    expect((new UnitPolicy())->viewAny($user))->toBeTrue();
});

it('UnitPolicy::viewAny denies access when user has no unit.view permission', function () {
    $user = User::factory()->create(['role' => UserRole::Admin]);

    expect((new UnitPolicy())->viewAny($user))->toBeFalse();
});

it('UnitPolicy::view grants access when unit belongs to the active residence', function () {
    $residence = Residence::factory()->create();
    $user      = userWithPermission($residence->id, 'unit.view');
    $unit      = Unit::factory()->create(['residence_id' => $residence->id]);

    app(PermissionRegistrar::class)->setPermissionsTeamId($residence->id);

    expect((new UnitPolicy())->view($user, $unit))->toBeTrue();
});

it('UnitPolicy::view denies access when unit belongs to a different residence', function () {
    $r1   = Residence::factory()->create();
    $r2   = Residence::factory()->create();
    $user = userWithPermission($r1->id, 'unit.view');
    $unit = Unit::factory()->create(['residence_id' => $r2->id]);

    app(PermissionRegistrar::class)->setPermissionsTeamId($r1->id);

    expect((new UnitPolicy())->view($user, $unit))->toBeFalse();
});

it('SuperAdmin bypasses UnitPolicy::view for any residence', function () {
    $sa       = superAdmin();
    $unit     = Unit::factory()->create();

    expect((new UnitPolicy())->view($sa, $unit))->toBeTrue();
});

// ── ChartOfAccountPolicy ─────────────────────────────────────────────────────

it('ChartOfAccountPolicy::delete denies user without account.delete', function () {
    $residence = Residence::factory()->create();
    $user      = userWithPermission($residence->id, 'account.view');
    $account   = ChartOfAccount::factory()->create(['residence_id' => $residence->id]);

    app(PermissionRegistrar::class)->setPermissionsTeamId($residence->id);

    expect((new ChartOfAccountPolicy())->delete($user, $account))->toBeFalse();
});

it('ChartOfAccountPolicy::delete grants user with account.delete', function () {
    $residence = Residence::factory()->create();
    $user      = userWithPermission($residence->id, 'account.delete');
    $account   = ChartOfAccount::factory()->create(['residence_id' => $residence->id]);

    app(PermissionRegistrar::class)->setPermissionsTeamId($residence->id);

    expect((new ChartOfAccountPolicy())->delete($user, $account))->toBeTrue();
});

// ── JournalEntryPolicy ───────────────────────────────────────────────────────

it('JournalEntryPolicy::reverse requires journal_entry.reverse permission', function () {
    $residence = Residence::factory()->create();
    $user      = userWithPermission($residence->id, 'journal_entry.view');
    $entry     = JournalEntry::factory()->create(['residence_id' => $residence->id, 'posted_at' => now()]);

    app(PermissionRegistrar::class)->setPermissionsTeamId($residence->id);

    expect((new JournalEntryPolicy())->reverse($user, $entry))->toBeFalse();
});

it('JournalEntryPolicy::reverse granted with journal_entry.reverse permission', function () {
    $residence = Residence::factory()->create();
    $user      = userWithPermission($residence->id, 'journal_entry.reverse');
    $entry     = JournalEntry::factory()->create(['residence_id' => $residence->id, 'posted_at' => now()]);

    app(PermissionRegistrar::class)->setPermissionsTeamId($residence->id);

    expect((new JournalEntryPolicy())->reverse($user, $entry))->toBeTrue();
});

it('JournalEntryPolicy::reverse denies for wrong residence even with permission', function () {
    $r1    = Residence::factory()->create();
    $r2    = Residence::factory()->create();
    $user  = userWithPermission($r1->id, 'journal_entry.reverse');
    $entry = JournalEntry::factory()->create(['residence_id' => $r2->id, 'posted_at' => now()]);

    app(PermissionRegistrar::class)->setPermissionsTeamId($r1->id);

    expect((new JournalEntryPolicy())->reverse($user, $entry))->toBeFalse();
});
