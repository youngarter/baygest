<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\SoldeService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ComptabiliteStatsWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $residenceId = filament()->getTenant()?->id;

        if (! $residenceId) {
            return [];
        }

        $soldes = app(SoldeService::class)->dashboard($residenceId);

        $facturesEnAttente = Invoice::where('residence_id', $residenceId)
            ->where('status', InvoiceStatus::Draft)
            ->count();

        $encaissementsMois = Payment::where('residence_id', $residenceId)
            ->whereMonth('date_received', now()->month)
            ->whereYear('date_received', now()->year)
            ->sum('amount');

        return [
            Stat::make('Solde trésorerie', number_format($soldes['tresorerie'], 2, ',', ' ') . ' MAD')
                ->description('Compte bancaire (5141)')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($soldes['tresorerie'] >= 0 ? 'success' : 'danger'),

            Stat::make('Créances copropriétaires', number_format($soldes['creances'], 2, ',', ' ') . ' MAD')
                ->description('À recouvrer (3421)')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($soldes['creances'] > 0 ? 'warning' : 'success'),

            Stat::make('Dettes fournisseurs', number_format($soldes['dettes'], 2, ',', ' ') . ' MAD')
                ->description('À régler (4411)')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color($soldes['dettes'] > 0 ? 'danger' : 'success'),

            Stat::make('Factures en attente', $facturesEnAttente)
                ->description('Brouillons à valider')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($facturesEnAttente > 0 ? 'warning' : 'success'),

            Stat::make('Encaissements ce mois', number_format((float) $encaissementsMois, 2, ',', ' ') . ' MAD')
                ->description(now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),
        ];
    }
}
