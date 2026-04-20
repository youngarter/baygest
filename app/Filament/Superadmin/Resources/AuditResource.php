<?php

namespace App\Filament\Superadmin\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use OwenIt\Auditing\Models\Audit;

class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static ?string $navigationLabel = 'Audit Logs';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i:s')
                    ->label('Date')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('event')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'password_reset_requested' => 'info',
                        default => 'gray',
                    })
                    ->label('Événement')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('auditable_type')
                    ->label('Type')
                    ->formatStateUsing(fn(string $state): string => class_basename($state))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('auditable_id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(50),

            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => AuditResource\Pages\ListAudits::route('/'),
        ];
    }
}
