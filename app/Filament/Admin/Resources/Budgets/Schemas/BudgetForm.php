<?php

namespace App\Filament\Admin\Resources\Budgets\Schemas;

use App\Models\Assemblee;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class BudgetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('assemblee_id')
                    ->label('Assemblée')
                    ->options(Assemblee::pluck('titre', 'id'))
                    ->searchable()
                    ->required()
                    ->rule(fn () => Rule::exists('assemblees', 'id')
                        ->where('residence_id', filament()->getTenant()?->id)
                    ),
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
