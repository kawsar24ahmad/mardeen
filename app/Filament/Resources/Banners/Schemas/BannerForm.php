<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Banner For Hero section')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('banner_title')
                            ->label('Banner Title')
                            ->placeholder('Enter banner title')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        FileUpload::make('banner_image')
                            ->label('Banner Image')
                            ->image()
                            ->imageEditor()
                            ->directory('hero-banners')
                            ->disk('public')
                            ->visibility('public')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('serial_number')
                            ->label('Serial Number')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                    ]),
            ]);
    }
}
