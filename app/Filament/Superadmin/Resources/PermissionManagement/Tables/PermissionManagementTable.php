<?php

namespace App\Filament\Superadmin\Resources\PermissionManagement\Tables;

use App\Enums\Permission;
use App\Enums\PermissionGroup;
use App\Models\ResidenceUser;
use App\Services\PermissionService;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class PermissionManagementTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('residence.name')
                    ->label('Résidence')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('permissions_summary')
                    ->label('Permissions actives')
                    ->getStateUsing(function (ResidenceUser $record): string {
                        $permissions = app(PermissionService::class)
                            ->getPermissionsForResidence($record->user, $record->residence_id);

                        return collect($permissions)
                            ->map(fn (string $p): string => Permission::tryFrom($p)?->shortLabel() ?? $p)
                            ->implode(', ');
                    })
                    ->wrap()
                    ->placeholder('Aucune permission'),
            ])
            ->filters([
                SelectFilter::make('residence_id')
                    ->label('Résidence')
                    ->relationship('residence', 'name'),
            ])
            ->recordActions([
                Action::make('managePermissions')
                    ->label('Gérer')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->modalHeading(
                        fn (ResidenceUser $record): string => "{$record->user->name} — {$record->residence->name}"
                    )
                    ->modalWidth('2xl')
                    ->modalSubmitActionLabel('Sauvegarder')
                    ->form(fn (ResidenceUser $record): array => [
                        Placeholder::make('context')
                            ->hiddenLabel()
                            ->content(new HtmlString(
                                "<p class=\"text-sm text-gray-500\">Permissions pour <strong>{$record->user->name}</strong> dans <strong>{$record->residence->name}</strong></p>"
                            )),
                        ...collect(PermissionGroup::cases())
                            ->map(fn (PermissionGroup $group): Section => Section::make($group->label())
                                ->icon($group->icon())
                                ->collapsed()
                                ->schema([
                                    CheckboxList::make("{$group->value}_permissions")
                                        ->hiddenLabel()
                                        ->options(Permission::optionsForGroup($group))
                                        ->columns(2),
                                ])
                            )
                            ->all(),
                    ])
                    ->fillForm(fn (ResidenceUser $record): array => self::loadPermissions($record))
                    ->action(function (array $data, ResidenceUser $record): void {
                        $permissions = collect(PermissionGroup::cases())
                            ->flatMap(fn (PermissionGroup $g): array => $data["{$g->value}_permissions"] ?? [])
                            ->all();

                        app(PermissionService::class)->updateUserPermissions(
                            $record->user,
                            $permissions,
                            auth()->user(),
                            $record->residence_id,
                        );

                        Notification::make()
                            ->success()
                            ->title('Permissions mises à jour')
                            ->body("{$record->user->name} — {$record->residence->name}")
                            ->send();
                    }),
            ])
            ->defaultSort('user.name');
    }

    /** @return array<string, array<string>> */
    private static function loadPermissions(ResidenceUser $record): array
    {
        $active = collect(
            app(PermissionService::class)->getPermissionsForResidence(
                $record->user,
                $record->residence_id,
            )
        );

        return collect(PermissionGroup::cases())
            ->mapWithKeys(fn (PermissionGroup $g): array => [
                "{$g->value}_permissions" => $active
                    ->filter(fn (string $p): bool => Permission::tryFrom($p)?->group() === $g)
                    ->values()
                    ->toArray(),
            ])
            ->toArray();
    }
}
