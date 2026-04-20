<?php

namespace App\Services;

use App\Exceptions\MissingAccountingConfigurationException;
use App\Exceptions\UnbalancedJournalEntryException;
use App\Models\AccountingConfig;
use App\Models\JournalEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JournalEntryService
{
    /**
     * Create a balanced, posted journal entry inside a transaction.
     *
     * @param array{
     *     residence_id: int,
     *     date: string|\DateTimeInterface,
     *     reference: string,
     *     description: string|null,
     *     source_type: string|null,
     *     source_id: int|null,
     *     lines: array<array{chart_of_account_id: int, debit: float, credit: float, unit_id: int|null}>
     * } $data
     *
     * @throws UnbalancedJournalEntryException
     * @throws MissingAccountingConfigurationException
     */
    public function create(array $data): JournalEntry
    {
        $this->requireConfig($data['residence_id']);
        $this->assertBalanced($data['lines']);

        return DB::transaction(function () use ($data): JournalEntry {
            /** @var JournalEntry $entry */
            $entry = JournalEntry::create([
                'residence_id' => $data['residence_id'],
                'date'         => $data['date'],
                'reference'    => $data['reference'],
                'description'  => $data['description'] ?? null,
                'source_type'  => $data['source_type'] ?? null,
                'source_id'    => $data['source_id'] ?? null,
                'posted_at'    => Carbon::now(),
            ]);

            foreach ($data['lines'] as $line) {
                $entry->lines()->create([
                    'chart_of_account_id' => $line['chart_of_account_id'],
                    'debit'               => $line['debit'],
                    'credit'              => $line['credit'],
                    'unit_id'             => $line['unit_id'] ?? null,
                ]);
            }

            return $entry->load('lines');
        });
    }

    /**
     * Create a reversal (extourne) of a posted journal entry.
     *
     * @throws \LogicException if the entry is not posted
     * @throws UnbalancedJournalEntryException
     * @throws MissingAccountingConfigurationException
     */
    public function reverse(JournalEntry $entry): JournalEntry
    {
        if (! $entry->isPosted()) {
            throw new \LogicException("Cannot reverse an unposted journal entry (id: {$entry->id}).");
        }

        $entry->loadMissing('lines');

        $reversedLines = $entry->lines->map(fn ($line): array => [
            'chart_of_account_id' => $line->chart_of_account_id,
            'debit'               => (float) $line->credit,
            'credit'              => (float) $line->debit,
            'unit_id'             => $line->unit_id,
        ])->all();

        return $this->create([
            'residence_id' => $entry->residence_id,
            'date'         => Carbon::now()->toDateString(),
            'reference'    => 'EXTOURNE-' . $entry->reference,
            'description'  => "Extourne de {$entry->reference}",
            'source_type'  => null,
            'source_id'    => null,
            'lines'        => $reversedLines,
        ]);
    }

    /**
     * Retrieve the AccountingConfig for a residence or throw.
     *
     * @throws MissingAccountingConfigurationException
     */
    public function requireConfig(int $residenceId): AccountingConfig
    {
        $config = AccountingConfig::where('residence_id', $residenceId)->first();

        if (! $config) {
            throw new MissingAccountingConfigurationException($residenceId);
        }

        return $config;
    }

    /**
     * @param array<array{debit: float, credit: float}> $lines
     *
     * @throws UnbalancedJournalEntryException
     */
    private function assertBalanced(array $lines): void
    {
        $totalDebit  = round(array_sum(array_column($lines, 'debit')), 2);
        $totalCredit = round(array_sum(array_column($lines, 'credit')), 2);

        if ($totalDebit !== $totalCredit) {
            throw new UnbalancedJournalEntryException($totalDebit, $totalCredit);
        }
    }
}
