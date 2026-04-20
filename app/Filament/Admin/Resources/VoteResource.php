<?php

namespace App\Filament\Admin\Resources;

use App\Enums\VoteDecision;
use App\Filament\Admin\Resources\VoteResource\Pages;
use App\Models\Vote;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VoteResource extends Resource
{
    protected static ?string $model = Vote::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-hand-raised';

    protected static ?string $navigationLabel = 'Votes';

    protected static \UnitEnum|string|null $navigationGroup = 'Assemblées';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('resolution_id')
                ->relationship('resolution', 'title')
                ->searchable()
                ->preload()
                ->required()
                ->label('Résolution'),
            Select::make('unit_id')
                ->relationship('unit', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->label('Lot'),
            Select::make('decision')
                ->options(collect(VoteDecision::cases())->mapWithKeys(
                    fn (VoteDecision $d): array => [$d->value => $d->label()]
                ))
                ->required()
                ->label('Décision'),
            TextInput::make('weight_used')
                ->numeric()
                ->nullable()
                ->step(0.0001)
                ->label('Poids (tantièmes)'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('resolution.title')->sortable()->label('Résolution'),
                TextColumn::make('unit.name')->sortable()->label('Lot'),
                TextColumn::make('decision')
                    ->badge()
                    ->formatStateUsing(fn (VoteDecision $state): string => $state->label())
                    ->color(fn (VoteDecision $state): string => $state->color())
                    ->label('Décision'),
                TextColumn::make('weight_used')->numeric(4)->label('Poids'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('resolution.assemblee', fn (Builder $q) => $q->where('residence_id', filament()->getTenant()?->id));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVotes::route('/'),
            'create' => Pages\CreateVote::route('/create'),
            'edit'   => Pages\EditVote::route('/{record}/edit'),
        ];
    }
}
