<?php

namespace App\Filament\Superadmin\Resources\Budgets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BudgetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('residence.name')
                    ->label('Résidence')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('assemblee.titre')
                    ->label('Assemblée')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('titre')
                    ->searchable()
                    ->sortable(),
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
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
