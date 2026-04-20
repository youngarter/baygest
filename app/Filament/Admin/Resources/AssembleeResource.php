<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AssembleeResource\Pages;
use App\Models\Assemblee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AssembleeResource extends Resource
{
    protected static ?string $model = Assemblee::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Assemblées';

    protected static \UnitEnum|string|null $navigationGroup = 'Assemblées';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('titre')->required()->maxLength(255)->label('Titre'),
            Select::make('type')
                ->options(['normal' => 'Ordinaire', 'extraordinaire' => 'Extraordinaire'])
                ->required()
                ->default('normal')
                ->label('Type'),
            TextInput::make('annee_syndic')
                ->numeric()
                ->required()
                ->default(now()->year)
                ->label('Année syndic'),
            DatePicker::make('date_assemblee')
                ->required()
                ->label('Date')
                ->displayFormat('d/m/Y'),
            Textarea::make('description')->nullable()->columnSpanFull()->label('Description'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titre')->searchable()->sortable()->label('Titre'),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'extraordinaire' ? 'Extraordinaire' : 'Ordinaire')
                    ->color(fn (string $state): string => $state === 'extraordinaire' ? 'warning' : 'info')
                    ->label('Type'),
                TextColumn::make('annee_syndic')->sortable()->label('Année'),
                TextColumn::make('date_assemblee')->date('d/m/Y')->sortable()->label('Date'),
                TextColumn::make('resolutions_count')->counts('resolutions')->label('Résolutions'),
            ])
            ->defaultSort('date_assemblee', 'desc');
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
            'index'  => Pages\ListAssemblees::route('/'),
            'create' => Pages\CreateAssemblee::route('/create'),
            'edit'   => Pages\EditAssemblee::route('/{record}/edit'),
        ];
    }
}
