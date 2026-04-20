<?php

namespace App\Filament\Superadmin\Resources;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserOnboardingAuditResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Audit d\'Onboarding';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('onboarding_email_sent_at')
                    ->boolean()
                    ->label('Email envoyé')
                    ->sortable(),
                TextColumn::make('role')
                    ->badge()
                    ->formatStateUsing(fn(UserRole $state) => $state->label())
                    ->color(fn(UserRole $state) => match ($state) {
                        UserRole::SuperAdmin => 'danger',
                        UserRole::Admin => 'warning',
                        UserRole::Coproprietary => 'warning',
                        UserRole::Locataire => 'info',
                    })
                    ->label('Rôle'),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('residences.name')
                    ->label('Résidence')
                    ->formatStateUsing(fn(?string $state) => $state ?? '-'),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Créé le'),
                TextColumn::make('onboarding_email_sent_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Email envoyé le'),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => UserOnboardingAuditResource\Pages\ListUserOnboardingAudits::route('/'),
        ];
    }
}
