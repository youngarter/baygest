<?php

namespace App\Filament\Superadmin\Resources;

use App\Enums\UserRole;
use App\Concerns\SendsPasswordResetEmail;
use App\Filament\Superadmin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
class UserResource extends Resource
{
    use SendsPasswordResetEmail;

    protected static ?string $model = User::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Utilisateurs';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('email')->email()->required()->unique(User::class, 'email', ignoreRecord: true),
            Select::make('role')
                ->options(collect(UserRole::cases())->mapWithKeys(
                    fn (UserRole $role) => [$role->value => $role->label()]
                ))
                ->required()
                ->live(),
            Select::make('residences')
                ->label('Résidences assignées')
                ->relationship('residences', 'name')
                ->multiple()
                ->searchable()
                ->preload()
                ->visible(fn ($get) => $get('role') === UserRole::Admin->value)
                ->required(fn ($get) => $get('role') === UserRole::Admin->value)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('role')
                    ->badge()
                    ->formatStateUsing(fn(UserRole $state) => $state->label())
                    ->color(fn(UserRole $state) => match ($state) {
                        UserRole::SuperAdmin => 'danger',
                        UserRole::Admin => 'warning',
                        UserRole::Coproprietary => 'warning',
                        UserRole::Locataire => 'info',
                    }),
                IconColumn::make('onboarding_email_sent_at')
                    ->boolean()
                    ->label('Email envoyé')
                    ->sortable(),
                TextColumn::make('residences_count')->counts('residences')->sortable(),
                TextColumn::make('created_at')->dateTime('d/m/Y')->sortable(),
            ])
            ->recordActions([
                Action::make('resetPassword')
                    ->label('Réinitialiser le mot de passe')
                    ->icon('heroicon-o-key')
                    ->requiresConfirmation()
                    ->modalHeading('Réinitialiser le mot de passe')
                    ->modalDescription('Un email de réinitialisation de mot de passe sera envoyé à cet utilisateur.')
                    ->action(fn(User $record) => self::sendPasswordResetEmail($record)),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

}
