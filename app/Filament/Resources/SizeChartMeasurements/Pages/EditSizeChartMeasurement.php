<?php

namespace App\Filament\Resources\SizeChartMeasurements\Pages;

use App\Filament\Resources\SizeChartMeasurements\SizeChartMeasurementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSizeChartMeasurement extends EditRecord
{
    protected static string $resource = SizeChartMeasurementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
