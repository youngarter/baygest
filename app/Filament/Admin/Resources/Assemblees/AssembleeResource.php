<?php

namespace App\Filament\Admin\Resources\Assemblees;

use App\Filament\Admin\Resources\Assemblees\Pages\CreateAssemblee;
use App\Filament\Admin\Resources\Assemblees\Pages\EditAssemblee;
use App\Filament\Admin\Resources\Assemblees\Pages\ListAssemblees;
use App\Filament\Admin\Resources\Assemblees\Schemas\AssembleeForm;
use App\Filament\Admin\Resources\Assemblees\Tables\AssembleesTable;
use App\Models\Assemblee;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssembleeResource extends Resource
{
    protected static ?string $model = Assemblee::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Assemblées';

    protected static ?string $modelLabel = 'Assemblée';

    protected static ?string $pluralModelLabel = 'Assemblées';

    public static function form(Schema $schema): Schema
    {
        return AssembleeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssembleesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssemblees::route('/'),
            'create' => CreateAssemblee::route('/create'),
            'edit' => EditAssemblee::route('/{record}/edit'),
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
