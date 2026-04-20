<?php

namespace App\Filament\Superadmin\Resources\PermissionManagement;

use App\Enums\UserRole;
use App\Filament\Superadmin\Resources\PermissionManagement\Pages\ListPermissionManagement;
use App\Filament\Superadmin\Resources\PermissionManagement\Tables\PermissionManagementTable;
use App\Models\ResidenceUser;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PermissionManagementResource extends Resource
{
    protected static ?string $model = ResidenceUser::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Permissions Admins';

    protected static ?string $modelLabel = 'Permission';

    protected static ?string $pluralModelLabel = 'Permissions Admins';

    protected static ?string $slug = 'permission-management';

    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return PermissionManagementTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->join('users', 'users.id', '=', 'residence_user.user_id')
            ->where('users.role', UserRole::Admin)
            ->select('residence_user.*');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPermissionManagement::route('/'),
        ];
    }
}
