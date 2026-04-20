<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UnitResource\Pages;
use App\Models\Unit;
use App\Models\UnitType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Lots';

    protected static \UnitEnum|string|null $navigationGroup = 'Lots';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(50)->label('Désignation'),
            Select::make('unit_type_id')
                ->relationship('unitType', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->label('Type de lot'),
            TextInput::make('tantiemes')
                ->numeric()
                ->nullable()
                ->step(0.0001)
                ->label('Tantièmes'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->label('Désignation'),
                TextColumn::make('unitType.name')->sortable()->label('Type'),
                TextColumn::make('tantiemes')->numeric(4)->sortable()->label('Tantièmes')->placeholder('—'),
                TextColumn::make('created_at')->dateTime('d/m/Y')->sortable()->label('Créé le'),
            ])
            ->defaultSort('name');
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
            'index'  => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit'   => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}
