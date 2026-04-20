<?php

namespace App\Filament\Admin\Pages;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryLine;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GrandLivre extends Page
{
    protected string $view = 'filament.admin.pages.grand-livre';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Grand Livre';

    protected static \UnitEnum|string|null $navigationGroup = 'Comptabilité';

    protected static ?int $navigationSort = 2;

    public ?string $selectedAccountId = null;

    public ?string $startDate = null;

    public ?string $endDate = null;

    public function getAccounts(): Collection
    {
        $residenceId = filament()->getTenant()?->id;

        return ChartOfAccount::where('residence_id', $residenceId)
            ->orderBy('code')
            ->get();
    }

    /**
     * @return array<int, array{date: string, reference: string, description: string, debit: float, credit: float, balance: float}>
     */
    public function getMovements(): array
    {
        if (! $this->selectedAccountId) {
            return [];
        }

        $residenceId = filament()->getTenant()?->id;

        $query = DB::table('journal_entry_lines as jel')
            ->join('journal_entries as je', 'je.id', '=', 'jel.journal_entry_id')
            ->where('je.residence_id', $residenceId)
            ->where('jel.chart_of_account_id', $this->selectedAccountId)
            ->whereNotNull('je.posted_at')
            ->select(
                'je.date',
                'je.reference',
                'je.description',
                'jel.debit',
                'jel.credit',
            )
            ->orderBy('je.date')
            ->orderBy('je.id');

        if ($this->startDate) {
            $query->where('je.date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('je.date', '<=', $this->endDate);
        }

        $account  = ChartOfAccount::find($this->selectedAccountId);
        $debitNormal = $account && in_array($account->type->value, ['asset', 'expense'], true);

        $balance    = 0.0;
        $movements  = [];

        foreach ($query->get() as $row) {
            $debit  = (float) $row->debit;
            $credit = (float) $row->credit;
            $balance += $debitNormal ? ($debit - $credit) : ($credit - $debit);

            $movements[] = [
                'date'        => $row->date,
                'reference'   => $row->reference,
                'description' => $row->description,
                'debit'       => $debit,
                'credit'      => $credit,
                'balance'     => $balance,
            ];
        }

        return $movements;
    }

    public function getSelectedAccount(): ?ChartOfAccount
    {
        return $this->selectedAccountId
            ? ChartOfAccount::find($this->selectedAccountId)
            : null;
    }
}
