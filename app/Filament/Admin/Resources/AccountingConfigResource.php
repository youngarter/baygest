<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AccountingConfigResource\Pages;
use App\Models\AccountingConfig;
use App\Models\ChartOfAccount;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AccountingConfigResource extends Resource
{
    protected static ?string $model = AccountingConfig::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Configuration comptable';

    protected static \UnitEnum|string|null $navigationGroup = 'Comptabilité';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        $residenceId = filament()->getTenant()?->id;

        $accountOptions = ChartOfAccount::where('residence_id', $residenceId)
            ->orderBy('code')
            ->get()
            ->mapWithKeys(fn (ChartOfAccount $a): array => [$a->id => "{$a->code} — {$a->name}"])
            ->toArray();

        return $schema->components([
            Select::make('default_bank_account_id')
                ->options($accountOptions)
                ->searchable()
                ->nullable()
                ->label('Compte banque (5141)'),
            Select::make('default_vendor_account_id')
                ->options($accountOptions)
                ->searchable()
                ->nullable()
                ->label('Compte fournisseurs (4411)'),
            Select::make('default_owner_account_id')
                ->options($accountOptions)
                ->searchable()
                ->nullable()
                ->label('Compte copropriétaires (3421)'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
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
            'index' => Pages\ListAccountingConfigs::route('/'),
            'edit'  => Pages\EditAccountingConfig::route('/{record}/edit'),
        ];
    }
}
