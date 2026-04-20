<?php

namespace App\Services;

use App\Enums\ChartOfAccountType;
use App\Models\AccountingConfig;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;

class SoldeService
{
    /**
     * Compute the balance of a chart of account for a given residence.
     * Only posted journal entries are considered.
     * Sign convention: Asset/Expense → debit−credit; Liability/Equity/Revenue → credit−debit.
     */
    public function solde(int $residenceId, int $chartOfAccountId): float
    {
        $account = ChartOfAccount::find($chartOfAccountId);

        if (! $account) {
            return 0.0;
        }

        $row = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entries.residence_id', $residenceId)
            ->where('journal_entry_lines.chart_of_account_id', $chartOfAccountId)
            ->whereNotNull('journal_entries.posted_at')
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->first();

        $debit  = (float) $row->total_debit;
        $credit = (float) $row->total_credit;

        return $this->isDebitNormal($account->type) ? $debit - $credit : $credit - $debit;
    }

    /**
     * Compute the balance by account code for a given residence.
     */
    public function soldeByCode(int $residenceId, string $code): float
    {
        $account = ChartOfAccount::where('residence_id', $residenceId)
            ->where('code', $code)
            ->first();

        if (! $account) {
            return 0.0;
        }

        return $this->solde($residenceId, $account->id);
    }

    /**
     * Compute balances for the three key accounts of a residence using its AccountingConfig.
     *
     * @return array{tresorerie: float, creances: float, dettes: float}
     */
    public function dashboard(int $residenceId): array
    {
        $config = AccountingConfig::where('residence_id', $residenceId)->first();

        if (! $config) {
            return ['tresorerie' => 0.0, 'creances' => 0.0, 'dettes' => 0.0];
        }

        return [
            'tresorerie' => $config->default_bank_account_id
                ? $this->solde($residenceId, $config->default_bank_account_id)
                : 0.0,
            'creances'   => $config->default_owner_account_id
                ? $this->solde($residenceId, $config->default_owner_account_id)
                : 0.0,
            'dettes'     => $config->default_vendor_account_id
                ? $this->solde($residenceId, $config->default_vendor_account_id)
                : 0.0,
        ];
    }

    private function isDebitNormal(ChartOfAccountType|string $type): bool
    {
        $value = $type instanceof ChartOfAccountType ? $type->value : $type;

        return in_array($value, [
            ChartOfAccountType::Asset->value,
            ChartOfAccountType::Expense->value,
        ], true);
    }
}
