<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\JournalEntryResource\Pages;
use App\Models\JournalEntry;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JournalEntryResource extends Resource
{
    protected static ?string $model = JournalEntry::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Écritures comptables';

    protected static \UnitEnum|string|null $navigationGroup = 'Comptabilité';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('reference')
                ->required()
                ->maxLength(100)
                ->label('Référence'),
            DatePicker::make('date')
                ->required()
                ->label('Date')
                ->displayFormat('d/m/Y'),
            Textarea::make('description')
                ->nullable()
                ->columnSpanFull()
                ->label('Description'),
            Repeater::make('lines')
                ->label('Lignes d\'écriture')
                ->columnSpanFull()
                ->minItems(2)
                ->addActionLabel('Ajouter une ligne')
                ->schema([
                    Select::make('chart_of_account_id')
                        ->relationship('account', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('Compte'),
                    TextInput::make('debit')
                        ->numeric()
                        ->step(0.01)
                        ->default(0)
                        ->required()
                        ->label('Débit'),
                    TextInput::make('credit')
                        ->numeric()
                        ->step(0.01)
                        ->default(0)
                        ->required()
                        ->label('Crédit'),
                    Select::make('unit_id')
                        ->relationship('unit', 'name')
                        ->searchable()
                        ->nullable()
                        ->label('Lot (optionnel)'),
                ])
                ->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')->searchable()->sortable()->label('Référence'),
                TextColumn::make('date')->date('d/m/Y')->sortable()->label('Date'),
                TextColumn::make('description')->limit(40)->label('Description')->placeholder('—'),
                IconColumn::make('posted_at')
                    ->boolean()
                    ->label('Comptabilisée'),
                TextColumn::make('created_at')->dateTime('d/m/Y')->sortable()->label('Créé le'),
            ])
            ->defaultSort('date', 'desc');
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
            'index'  => Pages\ListJournalEntries::route('/'),
            'create' => Pages\CreateJournalEntry::route('/create'),
        ];
    }
}
