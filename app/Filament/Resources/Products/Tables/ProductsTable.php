<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $query
                    ->join('categories', 'products.category_id', '=', 'categories.id')
                    ->orderBy('categories.sort_order')
                    ->orderBy('products.sort_order')
                    ->select('products.*');
            })
            ->columns([
                SpatieMediaLibraryImageColumn::make('media')
                    ->label('Image')
                    ->collection('products')
                    ->conversion('thumb')
                    ->circular()
                    ->defaultImageUrl(asset('images/placeholder.png')),


                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->sortable(),
                // TextColumn::make('brand.name')
                //      ->searchable()
                //     ->badge()
                //     ->sortable(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('price')
                    ->money('BDT')
                    ->sortable(),
                TextColumn::make('compare_price')
                    ->money('BDT')
                    ->sortable(),
                TextColumn::make('cost_price')
                    ->money('BDT')
                    ->sortable(),
                TextColumn::make('stock_quantity')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('stock_status')
                    ->badge(),
                IconColumn::make('is_active')
                    ->boolean(),
                IconColumn::make('is_featured')
                    ->boolean(),
                // IconColumn::make('has_variants')
                //     ->boolean(),

                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('views_count')
                    ->numeric()
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    // ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // TrashedFilter::make(),
                SelectFilter::make('category_id') // <-- Must match 'category_id' in tableFilters array
                    ->label('Category')
                    ->relationship('category', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->paginated(false);
    }
}
