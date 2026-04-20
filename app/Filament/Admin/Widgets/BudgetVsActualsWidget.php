<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\InvoiceStatus;
use App\Models\BudgetLine;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class BudgetVsActualsWidget extends BaseWidget
{
    protected static ?string $heading = 'Budget vs Réalisé';

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                BudgetLine::query()
                    ->with(['budget', 'account', 'invoices'])
                    ->whereHas('budget', fn (Builder $q) => $q
                        ->where('residence_id', filament()->getTenant()?->id)
                        ->whereYear('created_at', now()->year)
                    )
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Ligne budgétaire')
                    ->searchable(),
                TextColumn::make('account.code')
                    ->label('Compte')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('amount_previsionnel')
                    ->label('Prévisionnel')
                    ->money('MAD')
                    ->sortable(),
                TextColumn::make('amount_realise')
                    ->label('Réalisé')
                    ->money('MAD')
                    ->state(fn (BudgetLine $record): float => (float) $record->invoices
                        ->whereNotIn('status', [InvoiceStatus::Draft])
                        ->sum('amount_total')
                    )
                    ->color(fn (BudgetLine $record): string => (float) $record->invoices
                        ->whereNotIn('status', [InvoiceStatus::Draft])
                        ->sum('amount_total') > (float) $record->amount_previsionnel
                        ? 'danger' : 'success'
                    ),
                TextColumn::make('taux_realisation')
                    ->label('Réalisation')
                    ->state(fn (BudgetLine $record): string => (float) $record->amount_previsionnel > 0
                        ? number_format(
                            ((float) $record->invoices
                                ->whereNotIn('status', [InvoiceStatus::Draft])
                                ->sum('amount_total')
                                / (float) $record->amount_previsionnel) * 100,
                            1
                        ) . ' %'
                        : '—'
                    ),
            ])
            ->paginated(false);
    }
}
