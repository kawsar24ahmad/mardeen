<?php

namespace App\Filament\Resources\SizeCharts\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SizesRelationManager extends RelationManager
{
    protected static string $relationship = 'sizes';

    protected static ?string $title = 'Sizes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Size')
                    ->placeholder('e.g. M, XL, 42, Free')
                    ->required()
                    ->maxLength(50),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('name')
                    ->label('Size')
                    ->searchable(),

                TextColumn::make('sort_order')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Size'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
