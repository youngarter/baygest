<?php

namespace App\Filament\Admin\Resources;

use App\Enums\UserRole;
use App\Concerns\SendsPasswordResetEmail;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    use SendsPasswordResetEmail;

    protected static ?string $model = User::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Utilisateurs';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('email')->email()->required()->unique(User::class, 'email', ignoreRecord: true),
            Select::make('role')
                ->options([
                    UserRole::Coproprietary->value => UserRole::Coproprietary->label(),
                    UserRole::Locataire->value => UserRole::Locataire->label(),
                ])
                ->required()
                ->label('Rôle'),
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
                    ->formatStateUsing(fn (UserRole $state) => $state->label())
                    ->color(fn (UserRole $state) => match ($state) {
                        UserRole::Coproprietary => 'info',
                        UserRole::Locataire => 'success',
                        default => 'gray',
                    }),
                IconColumn::make('onboarding_email_sent_at')
                    ->boolean()
                    ->label('Email envoyé')
                    ->sortable(),
                TextColumn::make('created_at')->dateTime('d/m/Y')->sortable(),
            ])
            ->recordActions([
                Action::make('resetPassword')
                    ->label('Réinitialiser le mot de passe')
                    ->icon('heroicon-o-key')
                    ->requiresConfirmation()
                    ->modalHeading('Réinitialiser le mot de passe')
                    ->modalDescription('Un email de réinitialisation de mot de passe sera envoyé à cet utilisateur.')
                    ->action(fn (User $record) => self::sendPasswordResetEmail($record)),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('residence_id', filament()->getTenant()?->id)
            ->whereIn('role', [UserRole::Coproprietary->value, UserRole::Locataire->value]);
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
