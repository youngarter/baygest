<?php

namespace App\Filament\Admin\Resources\Budgets;

use App\Filament\Admin\Resources\Budgets\Pages\CreateBudget;
use App\Filament\Admin\Resources\Budgets\Pages\EditBudget;
use App\Filament\Admin\Resources\Budgets\Pages\ListBudgets;
use App\Filament\Admin\Resources\Budgets\Schemas\BudgetForm;
use App\Filament\Admin\Resources\Budgets\Tables\BudgetsTable;
use App\Filament\Admin\Resources\Budgets\RelationManagers\BudgetLinesRelationManager;
use App\Models\Budget;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Budgets';

    protected static \UnitEnum|string|null $navigationGroup = 'Comptabilité';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Budget';

    protected static ?string $pluralModelLabel = 'Budgets';

    public static function form(Schema $schema): Schema
    {
        return BudgetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BudgetsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('residence_id', filament()->getTenant()?->id);
    }

    public static function getRelations(): array
    {
        return [
            BudgetLinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListBudgets::route('/'),
            'create' => CreateBudget::route('/create'),
            'edit'   => EditBudget::route('/{record}/edit'),
        ];
    }
}
