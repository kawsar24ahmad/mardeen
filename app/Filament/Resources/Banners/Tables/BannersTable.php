<?php

namespace App\Filament\Resources\Banners\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class BannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('banner_title')
                    ->searchable(),


                // ২. Spatie Media Library-এর মাধ্যমে আপলোড হওয়া ছবি
                SpatieMediaLibraryImageColumn::make('spatie_banner') // কাস্টম নাম দেওয়া হলো
                    ->label('Image')
                    ->collection('banners')
                    ->conversion('optimized')
                    ->height(80)
                    ->extraImgAttributes([
                        'class' => 'object-cover rounded-lg',
                    ])
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
