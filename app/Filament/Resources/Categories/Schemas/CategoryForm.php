<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Information')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('slug')
                            ->unique(ignoreRecord: true)
                            ->readOnly()
                            ->visibleOn('edit'),
                        Textarea::make('description')
                            ->rows(3)
                            ->default(null)
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('image')
                            ->label('Category Image')
                            ->collection('categories') // 👈 নির্দিষ্ট Spatie Collection নাম
                            ->conversion('thumb')      // 👈 ফর্ম প্রভিউতে লাইটওয়েট thumb কনভার্সন দেখাবে
                            ->disk('public')
                            ->image()
                            ->imageEditor()
                            ->preserveFilenames()
                            ->downloadable()
                            ->maxSize(5120)
                            ->required(),
                    ]),

                Section::make('Display Settings')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->required(),
                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),
                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->default(null),
                        Textarea::make('meta_description')
                            ->default(null)
                            ->columnSpanFull(),
                    ]),



            ]);
    }
}
