<?php

namespace App\Filament\Admin\Resources;

use App\Enums\ChartOfAccountType;
use App\Filament\Admin\Resources\ChartOfAccountResource\Pages;
use App\Models\ChartOfAccount;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ChartOfAccountResource extends Resource
{
    protected static ?string $model = ChartOfAccount::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Plan comptable';

    protected static \UnitEnum|string|null $navigationGroup = 'Comptabilité';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')
                ->required()
                ->maxLength(10)
                ->label('Code')
                ->unique(ChartOfAccount::class, 'code', ignoreRecord: true),
            TextInput::make('name')->required()->maxLength(255)->label('Intitulé'),
            Select::make('type')
                ->options(collect(ChartOfAccountType::cases())->mapWithKeys(
                    fn (ChartOfAccountType $t): array => [$t->value => $t->label()]
                ))
                ->required()
                ->label('Type'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->searchable()->sortable()->label('Code'),
                TextColumn::make('name')->searchable()->label('Intitulé'),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (ChartOfAccountType $state): string => $state->label())
                    ->color(fn (ChartOfAccountType $state): string => $state->color())
                    ->label('Type'),
                TextColumn::make('created_at')->dateTime('d/m/Y')->sortable()->label('Créé le'),
            ])
            ->defaultSort('code');
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
            'index'  => Pages\ListChartOfAccounts::route('/'),
            'create' => Pages\CreateChartOfAccount::route('/create'),
            'edit'   => Pages\EditChartOfAccount::route('/{record}/edit'),
        ];
    }
}
