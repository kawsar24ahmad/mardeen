<?php

namespace App\Filament\Resources\SizeChartMeasurements\Pages;

use App\Filament\Resources\SizeChartMeasurements\SizeChartMeasurementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSizeChartMeasurements extends ListRecords
{
    protected static string $resource = SizeChartMeasurementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
