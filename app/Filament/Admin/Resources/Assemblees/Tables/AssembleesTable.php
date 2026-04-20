<?php

namespace App\Filament\Admin\Resources\Assemblees\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AssembleesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'normal' => 'Ordinaire',
                        'extraordinaire' => 'Extraordinaire',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'normal' => 'info',
                        'extraordinaire' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('date_assemblee')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('annee_syndic')
                    ->label('Année')
                    ->sortable(),
                TextColumn::make('budgets_count')
                    ->label('Budgets')
                    ->counts('budgets')
                    ->sortable(),
            ])
            ->defaultSort('date_assemblee', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
