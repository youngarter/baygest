<?php

namespace App\Filament\Admin\Resources;

use App\Actions\RecordPayment;
use App\Filament\Admin\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Encaissements';

    protected static \UnitEnum|string|null $navigationGroup = 'Trésorerie';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('unit_id')
                ->relationship('unit', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->label('Lot'),
            TextInput::make('amount')
                ->required()
                ->numeric()
                ->step(0.01)
                ->label('Montant'),
            Select::make('payment_method')
                ->options([
                    'virement' => 'Virement bancaire',
                    'cheque'   => 'Chèque',
                    'especes'  => 'Espèces',
                ])
                ->required()
                ->label('Mode de règlement'),
            DatePicker::make('date_received')
                ->required()
                ->label('Date de réception')
                ->displayFormat('d/m/Y'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('unit.name')->sortable()->label('Lot'),
                TextColumn::make('amount')->money('MAD')->sortable()->label('Montant'),
                TextColumn::make('payment_method')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'virement' => 'Virement',
                        'cheque'   => 'Chèque',
                        'especes'  => 'Espèces',
                        default    => $state,
                    })
                    ->label('Mode'),
                TextColumn::make('date_received')->date('d/m/Y')->sortable()->label('Date'),
            ])
            ->defaultSort('date_received', 'desc')
            ->recordActions([
                Action::make('post')
                    ->label('Comptabiliser')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalDescription('Une écriture bancaire (débit banque / crédit copropriétaire) sera générée.')
                    ->visible(fn (Payment $record): bool => ! $record->journalEntries()->exists())
                    ->action(function (Payment $record): void {
                        try {
                            app(RecordPayment::class)->execute($record);
                            Notification::make()->success()->title('Encaissement comptabilisé')->send();
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
            'index'  => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit'   => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
