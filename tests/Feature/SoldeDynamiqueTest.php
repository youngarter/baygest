<?php

use App\Actions\InitializeResidenceAccounting;
use App\Actions\RecordPayment;
use App\Actions\ValidateInvoice;
use App\Models\AccountingConfig;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\Payment;
use App\Models\Residence;
use App\Models\Unit;
use App\Models\Vendor;
use App\Services\JournalEntryService;
use App\Services\SoldeService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function residenceWithAccounting(): Residence
{
    $residence = Residence::factory()->create();
    app(InitializeResidenceAccounting::class)->execute($residence);

    return $residence;
}

it('bank balance equals sum of debit minus credit on account 5141', function () {
    $residence = residenceWithAccounting();
    $config    = AccountingConfig::where('residence_id', $residence->id)->first();

    $service = app(JournalEntryService::class);

    $service->create([
        'residence_id' => $residence->id,
        'date'         => now()->toDateString(),
        'reference'    => 'REF-001',
        'description'  => null,
        'source_type'  => null,
        'source_id'    => null,
        'lines'        => [
            ['chart_of_account_id' => $config->default_bank_account_id, 'debit' => 5000, 'credit' => 0,    'unit_id' => null],
            ['chart_of_account_id' => $config->default_owner_account_id, 'debit' => 0,   'credit' => 5000, 'unit_id' => null],
        ],
    ]);

    $solde = app(SoldeService::class)->solde($residence->id, $config->default_bank_account_id);

    expect($solde)->toBe(5000.0);
});

it('bank balance decreases after payment to vendor', function () {
    $residence = residenceWithAccounting();
    $config    = AccountingConfig::where('residence_id', $residence->id)->first();
    $service   = app(JournalEntryService::class);

    $service->create([
        'residence_id' => $residence->id,
        'date'         => now()->toDateString(),
        'reference'    => 'INIT',
        'description'  => null,
        'source_type'  => null,
        'source_id'    => null,
        'lines'        => [
            ['chart_of_account_id' => $config->default_bank_account_id,   'debit' => 10000, 'credit' => 0,     'unit_id' => null],
            ['chart_of_account_id' => $config->default_owner_account_id,  'debit' => 0,     'credit' => 10000, 'unit_id' => null],
        ],
    ]);

    $service->create([
        'residence_id' => $residence->id,
        'date'         => now()->toDateString(),
        'reference'    => 'PAYMENT',
        'description'  => null,
        'source_type'  => null,
        'source_id'    => null,
        'lines'        => [
            ['chart_of_account_id' => $config->default_vendor_account_id, 'debit' => 3000, 'credit' => 0,    'unit_id' => null],
            ['chart_of_account_id' => $config->default_bank_account_id,   'debit' => 0,    'credit' => 3000, 'unit_id' => null],
        ],
    ]);

    $solde = app(SoldeService::class)->solde($residence->id, $config->default_bank_account_id);

    expect($solde)->toBe(7000.0);
});

it('unposted entries are excluded from balance', function () {
    $residence = residenceWithAccounting();
    $config    = AccountingConfig::where('residence_id', $residence->id)->first();

    JournalEntry::factory()
        ->create(['residence_id' => $residence->id, 'posted_at' => null])
        ->lines()
        ->create([
            'chart_of_account_id' => $config->default_bank_account_id,
            'debit'               => 9999,
            'credit'              => 0,
        ]);

    $solde = app(SoldeService::class)->solde($residence->id, $config->default_bank_account_id);

    expect($solde)->toBe(0.0);
});

it('reversal zeroes out the original entry balance', function () {
    $residence = residenceWithAccounting();
    $config    = AccountingConfig::where('residence_id', $residence->id)->first();
    $service   = app(JournalEntryService::class);

    $entry = $service->create([
        'residence_id' => $residence->id,
        'date'         => now()->toDateString(),
        'reference'    => 'ORIG',
        'description'  => null,
        'source_type'  => null,
        'source_id'    => null,
        'lines'        => [
            ['chart_of_account_id' => $config->default_bank_account_id,  'debit' => 2000, 'credit' => 0,    'unit_id' => null],
            ['chart_of_account_id' => $config->default_owner_account_id, 'debit' => 0,    'credit' => 2000, 'unit_id' => null],
        ],
    ]);

    $service->reverse($entry);

    $solde = app(SoldeService::class)->solde($residence->id, $config->default_bank_account_id);

    expect($solde)->toBe(0.0);
});

it('dashboard balances are isolated per residence', function () {
    $r1 = residenceWithAccounting();
    $r2 = residenceWithAccounting();

    $c1 = AccountingConfig::where('residence_id', $r1->id)->first();
    $c2 = AccountingConfig::where('residence_id', $r2->id)->first();

    app(JournalEntryService::class)->create([
        'residence_id' => $r1->id,
        'date'         => now()->toDateString(),
        'reference'    => 'R1-INIT',
        'description'  => null,
        'source_type'  => null,
        'source_id'    => null,
        'lines'        => [
            ['chart_of_account_id' => $c1->default_bank_account_id,  'debit' => 8000, 'credit' => 0,    'unit_id' => null],
            ['chart_of_account_id' => $c1->default_owner_account_id, 'debit' => 0,    'credit' => 8000, 'unit_id' => null],
        ],
    ]);

    $d1 = app(SoldeService::class)->dashboard($r1->id);
    $d2 = app(SoldeService::class)->dashboard($r2->id);

    expect($d1['tresorerie'])->toBe(8000.0)
        ->and($d2['tresorerie'])->toBe(0.0);
});

it('invoice validation flow reflects in vendor payables balance', function () {
    $residence = residenceWithAccounting();
    $vendor    = Vendor::factory()->create(['residence_id' => $residence->id]);
    $invoice   = Invoice::factory()->create([
        'residence_id' => $residence->id,
        'vendor_id'    => $vendor->id,
        'amount_total' => 4500,
        'status'       => \App\Enums\InvoiceStatus::Draft,
    ]);

    app(ValidateInvoice::class)->execute($invoice);

    $config = AccountingConfig::where('residence_id', $residence->id)->first();
    $dettes = app(SoldeService::class)->solde($residence->id, $config->default_vendor_account_id);

    expect($dettes)->toBe(4500.0);
});

it('payment flow reflects in bank and owner balance', function () {
    $residence = residenceWithAccounting();
    $unit      = Unit::factory()->create(['residence_id' => $residence->id]);
    $payment   = Payment::factory()->create([
        'residence_id' => $residence->id,
        'unit_id'      => $unit->id,
        'amount'       => 1200,
    ]);

    app(RecordPayment::class)->execute($payment);

    $config     = AccountingConfig::where('residence_id', $residence->id)->first();
    $tresorerie = app(SoldeService::class)->solde($residence->id, $config->default_bank_account_id);
    $creances   = app(SoldeService::class)->solde($residence->id, $config->default_owner_account_id);

    expect($tresorerie)->toBe(1200.0)
        ->and($creances)->toBe(-1200.0);
});
