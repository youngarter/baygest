<?php

use App\Actions\InitializeResidenceAccounting;
use App\Models\AccountingConfig;
use App\Models\ChartOfAccount;
use App\Models\Residence;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates 15 accounts and an AccountingConfig on first run', function () {
    $residence = Residence::factory()->create();

    $result = app(InitializeResidenceAccounting::class)->execute($residence);

    expect($result['accounts_created'])->toBe(15)
        ->and($result['already_initialized'])->toBeFalse()
        ->and(ChartOfAccount::where('residence_id', $residence->id)->count())->toBe(15)
        ->and(AccountingConfig::where('residence_id', $residence->id)->exists())->toBeTrue();
});

it('links AccountingConfig to accounts 5141, 4411, and 3421', function () {
    $residence = Residence::factory()->create();

    app(InitializeResidenceAccounting::class)->execute($residence);

    $config = AccountingConfig::where('residence_id', $residence->id)->first();

    $bankCode   = ChartOfAccount::find($config->default_bank_account_id)?->code;
    $vendorCode = ChartOfAccount::find($config->default_vendor_account_id)?->code;
    $ownerCode  = ChartOfAccount::find($config->default_owner_account_id)?->code;

    expect($bankCode)->toBe('5141')
        ->and($vendorCode)->toBe('4411')
        ->and($ownerCode)->toBe('3421');
});

it('is idempotent — does not duplicate accounts on second run', function () {
    $residence = Residence::factory()->create();

    app(InitializeResidenceAccounting::class)->execute($residence);
    $result = app(InitializeResidenceAccounting::class)->execute($residence);

    expect($result['accounts_created'])->toBe(0)
        ->and($result['already_initialized'])->toBeTrue()
        ->and(ChartOfAccount::where('residence_id', $residence->id)->count())->toBe(15);
});

it('isolates accounts per residence', function () {
    $r1 = Residence::factory()->create();
    $r2 = Residence::factory()->create();

    app(InitializeResidenceAccounting::class)->execute($r1);
    app(InitializeResidenceAccounting::class)->execute($r2);

    expect(ChartOfAccount::where('residence_id', $r1->id)->count())->toBe(15)
        ->and(ChartOfAccount::where('residence_id', $r2->id)->count())->toBe(15)
        ->and(ChartOfAccount::count())->toBe(30);
});
