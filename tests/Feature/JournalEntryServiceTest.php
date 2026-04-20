<?php

use App\Exceptions\MissingAccountingConfigurationException;
use App\Exceptions\UnbalancedJournalEntryException;
use App\Models\AccountingConfig;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\Residence;
use App\Services\JournalEntryService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeEntryData(int $residenceId, int $accountId, float $amount): array
{
    return [
        'residence_id' => $residenceId,
        'date'         => '2025-01-15',
        'reference'    => 'TEST-0001',
        'description'  => 'Test entry',
        'source_type'  => null,
        'source_id'    => null,
        'lines'        => [
            ['chart_of_account_id' => $accountId, 'debit' => $amount, 'credit' => 0,      'unit_id' => null],
            ['chart_of_account_id' => $accountId, 'debit' => 0,       'credit' => $amount, 'unit_id' => null],
        ],
    ];
}

it('creates a balanced journal entry with lines', function () {
    $residence = Residence::factory()->create();
    AccountingConfig::factory()->create(['residence_id' => $residence->id]);
    $account = ChartOfAccount::factory()->create(['residence_id' => $residence->id]);

    $entry = app(JournalEntryService::class)->create(
        makeEntryData($residence->id, $account->id, 1000)
    );

    expect($entry)->toBeInstanceOf(JournalEntry::class)
        ->and($entry->posted_at)->not->toBeNull()
        ->and($entry->lines)->toHaveCount(2)
        ->and((float) $entry->lines->sum('debit'))->toBe(1000.0)
        ->and((float) $entry->lines->sum('credit'))->toBe(1000.0);
});

it('throws UnbalancedJournalEntryException when debit ≠ credit', function () {
    $residence = Residence::factory()->create();
    AccountingConfig::factory()->create(['residence_id' => $residence->id]);
    $account = ChartOfAccount::factory()->create(['residence_id' => $residence->id]);

    app(JournalEntryService::class)->create([
        'residence_id' => $residence->id,
        'date'         => '2025-01-15',
        'reference'    => 'BAD-0001',
        'description'  => null,
        'source_type'  => null,
        'source_id'    => null,
        'lines'        => [
            ['chart_of_account_id' => $account->id, 'debit' => 1000, 'credit' => 0,   'unit_id' => null],
            ['chart_of_account_id' => $account->id, 'debit' => 0,    'credit' => 500, 'unit_id' => null],
        ],
    ]);
})->throws(UnbalancedJournalEntryException::class);

it('throws MissingAccountingConfigurationException when no config exists', function () {
    $residence = Residence::factory()->create();
    $account = ChartOfAccount::factory()->create(['residence_id' => $residence->id]);

    app(JournalEntryService::class)->create(
        makeEntryData($residence->id, $account->id, 500)
    );
})->throws(MissingAccountingConfigurationException::class);

it('rolls back transaction when balance fails — no entry persisted', function () {
    $residence = Residence::factory()->create();
    AccountingConfig::factory()->create(['residence_id' => $residence->id]);
    $account = ChartOfAccount::factory()->create(['residence_id' => $residence->id]);

    try {
        app(JournalEntryService::class)->create([
            'residence_id' => $residence->id,
            'date'         => '2025-01-15',
            'reference'    => 'FAIL-0001',
            'description'  => null,
            'source_type'  => null,
            'source_id'    => null,
            'lines'        => [
                ['chart_of_account_id' => $account->id, 'debit' => 999, 'credit' => 0, 'unit_id' => null],
            ],
        ]);
    } catch (UnbalancedJournalEntryException) {
    }

    expect(JournalEntry::count())->toBe(0);
});

it('reverses a posted entry with inverted lines', function () {
    $residence = Residence::factory()->create();
    AccountingConfig::factory()->create(['residence_id' => $residence->id]);
    $account = ChartOfAccount::factory()->create(['residence_id' => $residence->id]);

    $service = app(JournalEntryService::class);

    $original = $service->create(makeEntryData($residence->id, $account->id, 2000));

    $reversal = $service->reverse($original);

    expect($reversal->reference)->toBe('EXTOURNE-TEST-0001')
        ->and($reversal->posted_at)->not->toBeNull()
        ->and($reversal->lines)->toHaveCount(2);

    $debitLine = $reversal->lines->firstWhere('debit', '>', 0);
    expect((float) $debitLine->debit)->toBe(2000.0);
});

it('throws LogicException when reversing an unposted entry', function () {
    $residence = Residence::factory()->create();
    AccountingConfig::factory()->create(['residence_id' => $residence->id]);

    $entry = JournalEntry::factory()->create(['residence_id' => $residence->id, 'posted_at' => null]);

    app(JournalEntryService::class)->reverse($entry);
})->throws(\LogicException::class);

it('isolates entries across residences', function () {
    $r1 = Residence::factory()->create();
    $r2 = Residence::factory()->create();
    AccountingConfig::factory()->create(['residence_id' => $r1->id]);
    AccountingConfig::factory()->create(['residence_id' => $r2->id]);
    $a1 = ChartOfAccount::factory()->create(['residence_id' => $r1->id]);
    $a2 = ChartOfAccount::factory()->create(['residence_id' => $r2->id]);

    $service = app(JournalEntryService::class);
    $service->create(makeEntryData($r1->id, $a1->id, 100));
    $service->create([...(makeEntryData($r2->id, $a2->id, 200)), 'reference' => 'TEST-0001']);

    expect(JournalEntry::where('residence_id', $r1->id)->count())->toBe(1)
        ->and(JournalEntry::where('residence_id', $r2->id)->count())->toBe(1);
});
