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
                            ->columnSpanFull()
                            // ⬇️ প্লাস আইকন এবং পপআপ ফর্ম যোগ করার জন্য
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->placeholder('e.g., Chest, Length, Shoulder')
                                    ->required()
                                    ->maxLength(255)
                                    // ⬇️ 'measurements' টেবিলের 'name' কলাম ইউনিক হতে হবে
                                    ->unique(table: 'size_chart_measurements', column: 'name', ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'এই Measurement নামটি ইতোমধ্যে ডাটাবেজে রয়েছে!',
                                    ]),
                                TextInput::make('unit')
                                    ->required()
                                    ->default('inch'),
                                TextInput::make('sort_order')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->createOptionAction(
                                fn($action) => $action
                                    ->modalHeading('Create New Measurement')
                                    ->modalButton('Create')
                                    ->modalWidth('md')
                            ),
                    ])
                    ->columns(2),




            ]);
    }
}
