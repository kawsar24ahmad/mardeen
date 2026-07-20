<?php

namespace App\Filament\Resources\SizeCharts;

use BackedEnum;
use App\Models\SizeChart;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\SizeCharts\Pages\EditSizeChart;
use App\Filament\Resources\SizeCharts\Pages\ListSizeCharts;
use App\Filament\Resources\SizeCharts\Pages\CreateSizeChart;
use App\Filament\Resources\SizeCharts\Schemas\SizeChartForm;
use App\Filament\Resources\SizeCharts\Tables\SizeChartsTable;
use App\Filament\Resources\SizeCharts\RelationManagers\SizesRelationManager;

class SizeChartResource extends Resource
{
    protected static ?string $model = SizeChart::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SizeChartForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SizeChartsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SizesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSizeCharts::route('/'),
            'create' => CreateSizeChart::route('/create'),
            'edit' => EditSizeChart::route('/{record}/edit'),
            'values' => Pages\ManageSizeChartValues::route('/{record}/values'),
        ];
    }
}
