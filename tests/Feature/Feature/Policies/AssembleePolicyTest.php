<?php

use App\Enums\UserRole;
use App\Models\Assemblee;
use App\Models\Residence;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;
use App\Services\PermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\PermissionSeeder::class);

    $this->residence = Residence::factory()->create();

    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    $this->admin->residences()->attach($this->residence);

    app(PermissionService::class)->updateUserPermissions(
        $this->admin,
        ['assemblee.view', 'assemblee.create', 'assemblee.update'],
        $this->admin,
        $this->residence->id,
    );

    $this->assemblee = Assemblee::create([
        'residence_id'    => $this->residence->id,
        'annee_syndic'    => 2025,
        'type'            => 'normal',
        'titre'           => 'AG 2025',
        'date_assemblee'  => '2025-06-01',
    ]);

    app(PermissionRegistrar::class)->setPermissionsTeamId($this->residence->id);
});

it('allows view when admin has assemblee.view permission for the active residence', function () {
    expect($this->admin->can('view', $this->assemblee))->toBeTrue();
});

it('allows update when admin has assemblee.update permission for the active residence', function () {
    expect($this->admin->can('update', $this->assemblee))->toBeTrue();
});

it('denies view when residence does not match the active tenant', function () {
    $otherResidence = Residence::factory()->create();
    app(PermissionRegistrar::class)->setPermissionsTeamId($otherResidence->id);

    expect($this->admin->can('view', $this->assemblee))->toBeFalse();
});

it('denies update when admin lacks assemblee.update permission', function () {
    $residence = Residence::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $admin->residences()->attach($residence);

    app(PermissionService::class)->updateUserPermissions(
        $admin,
        ['assemblee.view'],
        $admin,
        $residence->id,
    );

    $assemblee = Assemblee::create([
        'residence_id'   => $residence->id,
        'annee_syndic'   => 2025,
        'type'           => 'normal',
        'titre'          => 'AG 2025',
        'date_assemblee' => '2025-06-01',
    ]);

    app(PermissionRegistrar::class)->setPermissionsTeamId($residence->id);

    expect($admin->can('update', $assemblee))->toBeFalse();
});
