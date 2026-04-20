<?php

namespace App\Filament\Admin\Resources\Budgets\RelationManagers;

use App\Models\ChartOfAccount;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BudgetLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'budgetLines';

    protected static ?string $title = 'Lignes budgétaires';

    public function form(Schema $schema): Schema
    {
        $residenceId = filament()->getTenant()?->id;

        return $schema->components([
            TextInput::make('title')
                ->label('Désignation')
                ->required()
                ->maxLength(255),
            Select::make('chart_of_account_id')
                ->label('Compte')
                ->options(
                    ChartOfAccount::where('residence_id', $residenceId)
                        ->orderBy('code')
                        ->get()
                        ->mapWithKeys(fn (ChartOfAccount $a): array => [$a->id => "{$a->code} — {$a->name}"])
                )
                ->searchable()
                ->nullable(),
            TextInput::make('amount_previsionnel')
                ->label('Montant prévisionnel (MAD)')
                ->numeric()
                ->step(0.01)
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label('Désignation')
                    ->searchable(),
                TextColumn::make('account.code')
                    ->label('Compte')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('amount_previsionnel')
                    ->label('Prévisionnel')
                    ->money('MAD')
                    ->sortable(),
            ])
            ->defaultSort('title')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
