<?php

namespace App\Filament\Superadmin\Resources\Budgets;

use App\Filament\Superadmin\Resources\Budgets\Pages\CreateBudget;
use App\Filament\Superadmin\Resources\Budgets\Pages\EditBudget;
use App\Filament\Superadmin\Resources\Budgets\Pages\ListBudgets;
use App\Filament\Superadmin\Resources\Budgets\Schemas\BudgetForm;
use App\Filament\Superadmin\Resources\Budgets\Tables\BudgetsTable;
use App\Models\Budget;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Budgets';

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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBudgets::route('/'),
            'create' => CreateBudget::route('/create'),
            'edit' => EditBudget::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
