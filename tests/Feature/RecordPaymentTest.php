<?php

use App\Actions\InitializeResidenceAccounting;
use App\Actions\RecordPayment;
use App\Exceptions\MissingAccountingConfigurationException;
use App\Models\AccountingConfig;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\Payment;
use App\Models\Residence;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a balanced journal entry on payment recording', function () {
    $residence = Residence::factory()->create();
    app(InitializeResidenceAccounting::class)->execute($residence);
    $unit    = Unit::factory()->create(['residence_id' => $residence->id]);
    $payment = Payment::factory()->create([
        'residence_id'  => $residence->id,
        'unit_id'       => $unit->id,
        'amount'        => 1500,
        'date_received' => '2025-03-01',
    ]);

    app(RecordPayment::class)->execute($payment);

    $entry = JournalEntry::where('source_type', Payment::class)
        ->where('source_id', $payment->id)
        ->first();

    expect($entry)->not->toBeNull()
        ->and($entry->posted_at)->not->toBeNull()
        ->and((float) $entry->lines->sum('debit'))->toBe(1500.0)
        ->and((float) $entry->lines->sum('credit'))->toBe(1500.0);
});

it('debits bank account and credits owner account', function () {
    $residence = Residence::factory()->create();
    app(InitializeResidenceAccounting::class)->execute($residence);
    $config  = AccountingConfig::where('residence_id', $residence->id)->first();
    $unit    = Unit::factory()->create(['residence_id' => $residence->id]);
    $payment = Payment::factory()->create([
        'residence_id' => $residence->id,
        'unit_id'      => $unit->id,
        'amount'       => 800,
    ]);

    app(RecordPayment::class)->execute($payment);

    $entry     = JournalEntry::where('source_type', Payment::class)->where('source_id', $payment->id)->first();
    $debitLine = $entry->lines->firstWhere('debit', '>', 0);
    $creditLine = $entry->lines->firstWhere('credit', '>', 0);

    expect($debitLine->chart_of_account_id)->toBe($config->default_bank_account_id)
        ->and($creditLine->chart_of_account_id)->toBe($config->default_owner_account_id);
});

it('throws MissingAccountingConfigurationException when config has no bank account', function () {
    $residence = Residence::factory()->create();
    $account   = ChartOfAccount::factory()->create(['residence_id' => $residence->id]);
    AccountingConfig::factory()->create([
        'residence_id'          => $residence->id,
        'default_bank_account_id' => null,
        'default_owner_account_id' => $account->id,
    ]);
    $unit    = Unit::factory()->create(['residence_id' => $residence->id]);
    $payment = Payment::factory()->create(['residence_id' => $residence->id, 'unit_id' => $unit->id]);

    app(RecordPayment::class)->execute($payment);
})->throws(MissingAccountingConfigurationException::class);
