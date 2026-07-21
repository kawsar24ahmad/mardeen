<?php

namespace App\Filament\Resources\SizeChartMeasurements;

use App\Filament\Resources\SizeChartMeasurements\Pages\CreateSizeChartMeasurement;
use App\Filament\Resources\SizeChartMeasurements\Pages\EditSizeChartMeasurement;
use App\Filament\Resources\SizeChartMeasurements\Pages\ListSizeChartMeasurements;
use App\Filament\Resources\SizeChartMeasurements\Schemas\SizeChartMeasurementForm;
use App\Filament\Resources\SizeChartMeasurements\Tables\SizeChartMeasurementsTable;
use App\Models\SizeChartMeasurement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SizeChartMeasurementResource extends Resource
{
    protected static ?string $model = SizeChartMeasurement::class;
    protected static UnitEnum|string|null $navigationGroup = 'Size Chart';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SizeChartMeasurementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SizeChartMeasurementsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSizeChartMeasurements::route('/'),
            'create' => CreateSizeChartMeasurement::route('/create'),
            'edit' => EditSizeChartMeasurement::route('/{record}/edit'),
        ];
    }
}
