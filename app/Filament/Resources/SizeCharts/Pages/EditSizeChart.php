<?php

namespace App\Filament\Resources\SizeCharts\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SizeCharts\SizeChartResource;

class EditSizeChart extends EditRecord
{
    protected static string $resource = SizeChartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            Action::make('values')
                ->label('Manage Values')
                ->icon('heroicon-o-table-cells')
                ->url(fn() => static::getResource()::getUrl('values', [
                    'record' => $this->record,
                ])),
        ];
    }
}
