<?php

namespace App\Filament\Admin\Resources\Assemblees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AssembleeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('annee_syndic')
                    ->label('Année syndicale')
                    ->required()
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(2100),
                Select::make('type')
                    ->options([
                        'normal' => 'Ordinaire',
                        'extraordinaire' => 'Extraordinaire',
                    ])
                    ->required(),
                DatePicker::make('date_assemblee')
                    ->label("Date de l'assemblée")
                    ->required(),
                RichEditor::make('description')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }
}
