<?php

namespace App\Filament\Resources\SizeChartMeasurements\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SizeChartMeasurementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->placeholder('e.g., Chest, Length, Shoulder')
                    ->required(),
                TextInput::make('unit')
                    ->required()
                    ->default('inch'),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
