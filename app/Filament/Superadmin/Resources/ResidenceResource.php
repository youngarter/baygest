<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\Superadmin\Resources\ResidenceResource\Pages;
use App\Models\Residence;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ResidenceResource extends Resource
{
    protected static ?string $model = Residence::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Résidences';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('slug')->required()->maxLength(255)->unique(Residence::class, 'slug', ignoreRecord: true),
            TextInput::make('address')->required()->maxLength(500),
            Textarea::make('description')->nullable(),
            FileUpload::make('avatar')->image()->nullable()->directory('residences/avatars'),
            FileUpload::make('images')->image()->multiple()->nullable()->directory('residences/images')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('slug')->searchable(),
            TextColumn::make('address')->limit(40),
            TextColumn::make('users_count')->counts('users')->sortable(),
            TextColumn::make('created_at')->dateTime('d/m/Y')->sortable(),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResidences::route('/'),
            'create' => Pages\CreateResidence::route('/create'),
            'edit' => Pages\EditResidence::route('/{record}/edit'),
        ];
    }
}
