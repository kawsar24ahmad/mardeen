<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Models\Product;
use App\Models\Category;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Hidden;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\IconColumn;



use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\Products\ProductResource;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;


class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                SpatieMediaLibraryImageColumn::make('image') // কাস্টম নাম দেওয়া হলো
                    ->label(' Image')
                    ->collection('categories')
                    ->conversion('thumb')
                    ->extraImgAttributes([
                        'class' => 'object-cover rounded-lg',
                    ])
                    ->alignCenter(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('products_count')
                    ->label('Products')
                    ->counts('products')
                    ->badge()
                    ->numeric(),
                TextColumn::make('products_count')
                    ->label('Products')
                    ->counts('products')
                    ->badge()
                    ->numeric()
                    ->url(function (Category $record): ?string {


                        // 1. Prevent redirection if there are no products
                        // if ($record->products_count === 0) {
                        //     return null;
                        // }

                        // 2. Generate filtered URL
                        return ProductResource::getUrl('index', [
                            'filters' => [
                                'category_id' => [
                                    'value' => $record->id,
                                ],
                            ],
                        ]);
                    })
                    // ->url(fn(Category $record): string => ProductResource::getUrl('index', [
                    //     'tableFilters' => [
                    //         'category_id' => [
                    //             'value' => $record->id,
                    //         ],
                    //     ],
                    // ]))
                    ->openUrlInNewTab(), // Optional: opens product list in a new tab
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                // TextColumn::make('meta_title')
                //     ->searchable(),
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



                // Action::make('arrange')
                //     ->label('Arrange Products')
                //     ->icon('heroicon-o-arrows-up-down')
                //     ->color('warning')
                //     ->modalHeading(
                //         fn(Category $record): string => "Arrange Products — {$record->name}"
                //     )
                //     ->modalSubmitActionLabel('Save Arrangement')
                //     ->modalWidth('2xl')

                //     ->fillForm(function (Category $record): array {
                //         return [
                //             'products' => $record->products()
                //                 ->orderBy('sort_order')
                //                 ->orderBy('id')
                //                 ->get()
                //                 ->map(fn(Product $product): array => [
                //                     'id' => $product->id,
                //                     'name' => $product->name,
                //                     'sort_order' => $product->sort_order,
                //                 ])
                //                 ->toArray(),
                //         ];
                //     })

                //     ->schema([
                //         Repeater::make('products')
                //             ->label('Products')
                //             ->schema([
                //                 Hidden::make('id'),
                //                 SpatieMediaLibraryImageColumn::make('media')
                //                     ->label('Image')
                //                     ->collection('products')
                //                     ->conversion('thumb')
                //                     ->circular()
                //                     ->defaultImageUrl(asset('images/placeholder.png')),
                //                 TextInput::make('name')
                //                     ->hiddenLabel()
                //                     ->disabled()
                //                     ->dehydrated(false)
                //                     ->extraInputAttributes([
                //                         'class' => 'text-sm',
                //                     ]),
                //             ])
                //             ->columns(4)
                //             ->reorderable()
                //             ->reorderableWithButtons()
                //             ->deletable(false)
                //             ->addable(false)
                //             ->itemLabel(
                //                 fn(array $state): ?string => $state['name'] ?? null
                //             )
                //             ->defaultItems(0),
                //     ])

                //     ->action(function (Category $record, array $data): void {
                //         foreach ($data['products'] ?? [] as $index => $product) {
                //             Product::where('id', $product['id'])
                //                 ->update([
                //                     'sort_order' => $index + 1,
                //                 ]);
                //         }
                //     }),

                Action::make('arrange')
                    ->label('Arrange Products')
                    ->icon('heroicon-o-arrows-up-down')
                    ->color('warning')
                    ->modalHeading(
                        fn(Category $record): string => "Arrange Products — {$record->name}"
                    )
                    ->modalSubmitActionLabel('Save Arrangement')
                    ->modalWidth('2xl')

                    ->fillForm(function (Category $record): array {
                        return [
                            'products' => $record->products()
                                ->orderBy('sort_order')
                                ->orderBy('id')
                                ->get()
                                ->map(fn(Product $product): array => [
                                    'id' => $product->id,
                                    'name' => $product->name,
                                    'image' => $product->getFirstMediaUrl('products', 'thumb')
                                        ?: asset('images/placeholder.png'),
                                ])
                                ->toArray(),
                        ];
                    })

                    ->schema([
                        Repeater::make('products')
                            ->label('Products')
                            ->schema([
                                Hidden::make('id'),
                                Hidden::make('image'),

                                Placeholder::make('product_image')
                                    ->hiddenLabel()
                                    ->content(function ($get) {
                                        $product = Product::find($get('id'));

                                        $image = $product?->getFirstMediaUrl('products', 'thumb')
                                            ?: asset('images/placeholder.png');


                                        return new \Illuminate\Support\HtmlString(
                                            '<img  style="
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #e5e7eb;
        " src="' . e($image) . '"
                                class="w-10 h-10 rounded-lg object-cover">'
                                        );
                                    }),

                                TextInput::make('name')
                                    ->hiddenLabel()
                                    ->disabled()
                                    ->dehydrated(false),
                            ])
                            ->columns(4)
                            ->reorderable()
                            ->reorderableWithButtons()
                            ->deletable(false)
                            ->addable(false)
                            ->itemLabel(
                                fn(array $state): ?string => $state['name'] ?? null
                            )
                            ->defaultItems(0),
                    ])

                    ->action(function (Category $record, array $data): void {
                        foreach ($data['products'] ?? [] as $index => $product) {
                            Product::where('id', $product['id'])
                                ->update([
                                    'sort_order' => $index + 1,
                                ]);
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),

            ])->paginated(false);
    }
}
