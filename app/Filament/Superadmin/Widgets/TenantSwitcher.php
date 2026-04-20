<?php

namespace App\Filament\Superadmin\Widgets;

use App\Models\Residence;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;

class TenantSwitcher extends Widget implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.superadmin.widgets.tenant-switcher';

    protected static ?int $sort = -1;

    public ?int $active_residence_id = null;

    public function mount(): void
    {
        $this->active_residence_id = session('active_residence_id');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('active_residence_id')
                ->label('Résidence active (contexte SuperAdmin)')
                ->options(Residence::pluck('name', 'id'))
                ->searchable()
                ->placeholder('Sélectionner une résidence...')
                ->live()
                ->afterStateUpdated(fn ($state) => $this->switchTenant($state)),
        ]);
    }

    public function switchTenant(?int $residenceId): void
    {
        session(['active_residence_id' => $residenceId]);

        Notification::make()
            ->success()
            ->title($residenceId
                ? 'Contexte: '.Residence::find($residenceId)?->name
                : 'Contexte global désactivé')
            ->send();
    }
}
