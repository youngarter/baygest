<?php

namespace App\Filament\Admin\Resources;

use App\Actions\ValidateInvoice;
use App\Enums\InvoiceStatus;
use App\Filament\Admin\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Factures';

    protected static \UnitEnum|string|null $navigationGroup = 'Trésorerie';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255)->label('Objet'),
            Select::make('vendor_id')
                ->relationship('vendor', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->label('Fournisseur'),
            Select::make('budget_line_id')
                ->relationship('budgetLine', 'title')
                ->searchable()
                ->nullable()
                ->label('Ligne budgétaire'),
            TextInput::make('amount_total')
                ->required()
                ->numeric()
                ->step(0.01)
                ->label('Montant TTC'),
            Select::make('status')
                ->options(collect(InvoiceStatus::cases())->mapWithKeys(
                    fn (InvoiceStatus $s): array => [$s->value => $s->label()]
                ))
                ->required()
                ->default(InvoiceStatus::Draft->value)
                ->label('Statut'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->label('Objet'),
                TextColumn::make('vendor.name')->sortable()->label('Fournisseur'),
                TextColumn::make('amount_total')->money('MAD')->sortable()->label('Montant TTC'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (InvoiceStatus $state): string => $state->label())
                    ->color(fn (InvoiceStatus $state): string => $state->color())
                    ->label('Statut'),
                TextColumn::make('created_at')->dateTime('d/m/Y')->sortable()->label('Créé le'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('validate')
                    ->label('Valider')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('La facture sera validée et une écriture comptable sera générée automatiquement.')
                    ->visible(fn (Invoice $record): bool => $record->status === InvoiceStatus::Draft)
                    ->action(function (Invoice $record): void {
                        try {
                            app(ValidateInvoice::class)->execute($record);
                            Notification::make()->success()->title('Facture validée')->send();
                        } catch (\Throwable $e) {
                            Notification::make()->danger()->title('Erreur')->body($e->getMessage())->send();
                        }
                    }),
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
            'index'  => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit'   => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
