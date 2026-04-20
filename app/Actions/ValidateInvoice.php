<?php

namespace App\Actions;

use App\Enums\InvoiceStatus;
use App\Exceptions\MissingAccountingConfigurationException;
use App\Models\ChartOfAccount;
use App\Models\Invoice;
use App\Services\JournalEntryService;
use Illuminate\Support\Facades\DB;

class ValidateInvoice
{
    public function __construct(private readonly JournalEntryService $journalEntryService) {}

    /**
     * @throws \LogicException
     * @throws MissingAccountingConfigurationException
     */
    public function execute(Invoice $invoice): Invoice
    {
        if ($invoice->status !== InvoiceStatus::Draft) {
            throw new \LogicException("Invoice #{$invoice->id} is not in draft status.");
        }

        $config = $this->journalEntryService->requireConfig($invoice->residence_id);

        if (! $config->default_vendor_account_id) {
            throw new MissingAccountingConfigurationException($invoice->residence_id);
        }

        $expenseAccountId = $invoice->budgetLine?->chart_of_account_id
            ?? ChartOfAccount::where('residence_id', $invoice->residence_id)->where('code', '6111')->value('id');

        if (! $expenseAccountId) {
            throw new MissingAccountingConfigurationException($invoice->residence_id);
        }

        return DB::transaction(function () use ($invoice, $config, $expenseAccountId): Invoice {
            $this->journalEntryService->create([
                'residence_id' => $invoice->residence_id,
                'date'         => now()->toDateString(),
                'reference'    => 'FACT-' . $invoice->id,
                'description'  => $invoice->title,
                'source_type'  => Invoice::class,
                'source_id'    => $invoice->id,
                'lines'        => [
                    [
                        'chart_of_account_id' => $expenseAccountId,
                        'debit'               => (float) $invoice->amount_total,
                        'credit'              => 0,
                        'unit_id'             => null,
                    ],
                    [
                        'chart_of_account_id' => $config->default_vendor_account_id,
                        'debit'               => 0,
                        'credit'              => (float) $invoice->amount_total,
                        'unit_id'             => null,
                    ],
                ],
            ]);

            $invoice->update(['status' => InvoiceStatus::Validated]);

            return $invoice->refresh();
        });
    }
}
