<?php

namespace App\Filament\Resources\Couriers\Schemas;

use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;

class CourierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->required()
                    ->readOnly()

                    ->unique(ignoreRecord: true),
                TextInput::make('base_url')
                    ->url()
                    ->default(null),
                Textarea::make('api_key')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('secret_key')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('username')
                    ->default(null),
                TextInput::make('password')
                    ->password()
                    ->default(null),
                Toggle::make('is_active')
                    ->default(true)
                    ->required(),
                Textarea::make('config')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
