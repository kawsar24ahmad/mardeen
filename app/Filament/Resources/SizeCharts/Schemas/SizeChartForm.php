<?php

namespace App\Filament\Resources\SizeCharts\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class SizeChartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->schema([
                        TextInput::make('name')
                            ->required(),

                        Toggle::make('is_active')
                            ->default(true),

                        Select::make('measurements')
                            ->relationship('measurements', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),




            ]);
    }
}
