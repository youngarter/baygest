<?php

namespace App\Filament\Superadmin\Resources\Budgets\Schemas;

use App\Models\Assemblee;
use App\Models\Residence;
use App\Scopes\TenantScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BudgetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('residence_id')
                    ->label('Résidence')
                    ->options(Residence::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->live(),
                Select::make('assemblee_id')
                    ->label('Assemblée')
                    ->options(fn ($get) => Assemblee::withoutGlobalScope(TenantScope::class)
                        ->when($get('residence_id'), fn ($q, $id) => $q->where('residence_id', $id))
                        ->pluck('titre', 'id')
                    )
                    ->searchable()
                    ->required(),
                TextInput::make('titre')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->options([
                        'global_estimatif' => 'Budget global estimatif',
                        'exceptionnel_estimatif' => 'Budget exceptionnel estimatif',
                    ])
                    ->required(),
                TextInput::make('budget_reel')
                    ->label('Budget réel (MAD)')
                    ->numeric()
                    ->nullable(),
                TextInput::make('seuil_alerte_estimatif')
                    ->label('Seuil alerte estimatif (MAD)')
                    ->numeric()
                    ->nullable(),
                TextInput::make('seuil_alerte_reel')
                    ->label('Seuil alerte réel (MAD)')
                    ->numeric()
                    ->nullable(),
                Textarea::make('description')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }
}
