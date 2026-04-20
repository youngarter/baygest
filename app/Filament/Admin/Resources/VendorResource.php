<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VendorResource\Pages;
use App\Models\Vendor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Fournisseurs';

    protected static \UnitEnum|string|null $navigationGroup = 'Trésorerie';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255)->label('Raison sociale'),
            TextInput::make('tax_id')->nullable()->maxLength(30)->label('ICE / IF'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable()->label('Raison sociale'),
            TextColumn::make('tax_id')->label('ICE / IF')->placeholder('—'),
            TextColumn::make('invoices_count')->counts('invoices')->sortable()->label('Factures'),
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
            'index'  => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'edit'   => Pages\EditVendor::route('/{record}/edit'),
        ];
    }
}
