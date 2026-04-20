<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UnitTypeResource\Pages;
use App\Models\UnitType;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UnitTypeResource extends Resource
{
    protected static ?string $model = UnitType::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Types de lots';

    protected static \UnitEnum|string|null $navigationGroup = 'Lots';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255)->label('Nom'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable()->label('Nom'),
            TextColumn::make('units_count')->counts('units')->sortable()->label('Lots'),
            TextColumn::make('created_at')->dateTime('d/m/Y')->sortable()->label('Créé le'),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('residence_id', filament()->getTenant()?->id);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUnitTypes::route('/'),
            'create' => Pages\CreateUnitType::route('/create'),
            'edit'   => Pages\EditUnitType::route('/{record}/edit'),
        ];
    }
}
