<?php

namespace App\Filament\Resources\SizeCharts\Pages;

use App\Filament\Resources\SizeCharts\SizeChartResource;
use App\Models\SizeChartMeasurement;
use App\Models\SizeChartSize;
use App\Models\SizeChartValue;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class ManageSizeChartValues extends Page
{
    use InteractsWithRecord;

    protected static string $resource = SizeChartResource::class;

    protected string $view = 'filament.resources.size-charts.pages.manage-size-chart-values';

    /**
     * Selected Measurements
     */
    public $measurements;

    /**
     * Selected Sizes
     */
    public $sizes;

    /**
     * Matrix Values
     *
     * [
     *   size_id => [
     *      measurement_id => value
     *   ]
     * ]
     */
    public array $values = [];

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->loadData();
    }

    protected function loadData(): void
    {
        $this->measurements = $this->record
            ->measurements()
            ->orderBy('sort_order')
            ->get();

        $this->sizes = $this->record
            ->sizes()
            ->orderBy('sort_order')
            ->get();

        foreach ($this->sizes as $size) {

            foreach ($this->measurements as $measurement) {

                $value = SizeChartValue::query()
                    ->where('size_chart_size_id', $size->id)
                    ->where('size_chart_measurement_id', $measurement->id)
                    ->value('value');

                $this->values[$size->id][$measurement->id] = $value;
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Values')
                ->icon('heroicon-o-check')
                ->color('success')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        foreach ($this->sizes as $size) {

            foreach ($this->measurements as $measurement) {

                SizeChartValue::updateOrCreate(
                    [
                        'size_chart_size_id' => $size->id,
                        'size_chart_measurement_id' => $measurement->id,
                    ],
                    [
                        'value' => $this->values[$size->id][$measurement->id] ?? null,
                    ]
                );
            }
        }

        Notification::make()
            ->title('Size Chart Updated Successfully')
            ->success()
            ->send();
    }
}
