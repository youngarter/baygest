<?php

namespace App\Actions;

use App\Exceptions\MissingAccountingConfigurationException;
use App\Models\Payment;
use App\Services\JournalEntryService;
use Illuminate\Support\Facades\DB;

class RecordPayment
{
    public function __construct(private readonly JournalEntryService $journalEntryService) {}

    /**
     * Record a payment and post the corresponding journal entry.
     * Debit: bank account (5141) — Credit: owner receivable (3421).
     *
     * @throws MissingAccountingConfigurationException
     */
    public function execute(Payment $payment): Payment
    {
        $config = $this->journalEntryService->requireConfig($payment->residence_id);

        if (! $config->default_bank_account_id || ! $config->default_owner_account_id) {
            throw new MissingAccountingConfigurationException($payment->residence_id);
        }

        DB::transaction(function () use ($payment, $config): void {
            $this->journalEntryService->create([
                'residence_id' => $payment->residence_id,
                'date'         => $payment->date_received->toDateString(),
                'reference'    => 'ENCAISS-' . $payment->id,
                'description'  => "Encaissement lot {$payment->unit->name}",
                'source_type'  => Payment::class,
                'source_id'    => $payment->id,
                'lines'        => [
                    [
                        'chart_of_account_id' => $config->default_bank_account_id,
                        'debit'               => (float) $payment->amount,
                        'credit'              => 0,
                        'unit_id'             => $payment->unit_id,
                    ],
                    [
                        'chart_of_account_id' => $config->default_owner_account_id,
                        'debit'               => 0,
                        'credit'              => (float) $payment->amount,
                        'unit_id'             => $payment->unit_id,
                    ],
                ],
            ]);
        });

        return $payment;
    }
}
