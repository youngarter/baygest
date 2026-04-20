<?php

namespace App\Filament\Admin\Resources;

use App\Enums\ResolutionType;
use App\Filament\Admin\Resources\ResolutionResource\Pages;
use App\Models\Resolution;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ResolutionResource extends Resource
{
    protected static ?string $model = Resolution::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationLabel = 'Résolutions';

    protected static \UnitEnum|string|null $navigationGroup = 'Assemblées';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('assemblee_id')
                ->relationship('assemblee', 'titre')
                ->searchable()
                ->preload()
                ->required()
                ->label('Assemblée'),
            TextInput::make('title')->required()->maxLength(255)->label('Titre'),
            Select::make('resolution_type')
                ->options(collect(ResolutionType::cases())->mapWithKeys(
                    fn (ResolutionType $t): array => [$t->value => $t->label()]
                ))
                ->required()
                ->label('Type'),
            Textarea::make('description')->nullable()->columnSpanFull()->label('Description'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable()->label('Titre'),
                TextColumn::make('assemblee.titre')->sortable()->label('Assemblée'),
                TextColumn::make('resolution_type')
                    ->badge()
                    ->formatStateUsing(fn (ResolutionType $state): string => $state->label())
                    ->color(fn (ResolutionType $state): string => $state->color())
                    ->label('Type'),
                TextColumn::make('votes_count')->counts('votes')->label('Votes'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('assemblee', fn (Builder $q) => $q->where('residence_id', filament()->getTenant()?->id));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListResolutions::route('/'),
            'create' => Pages\CreateResolution::route('/create'),
            'edit'   => Pages\EditResolution::route('/{record}/edit'),
        ];
    }
}
