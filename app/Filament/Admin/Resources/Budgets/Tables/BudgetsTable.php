<?php

namespace App\Filament\Admin\Resources\Budgets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BudgetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assemblee.titre')
                    ->label('Assemblée')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'global_estimatif' => 'Global estimatif',
                        'exceptionnel_estimatif' => 'Exceptionnel estimatif',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'global_estimatif' => 'info',
                        'exceptionnel_estimatif' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('budget_reel')
                    ->label('Budget réel')
                    ->money('MAD')
                    ->sortable(),
                TextColumn::make('seuil_alerte_estimatif')
                    ->label('Seuil estimatif')
                    ->money('MAD')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('seuil_alerte_reel')
                    ->label('Seuil réel')
                    ->money('MAD')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
