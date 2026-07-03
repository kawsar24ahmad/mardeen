<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesOverviewChart extends ChartWidget
{
    protected ?string $heading = 'Sales Overview (Last 30 Days)';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return ! auth()->user()?->hasRole('staff');
    }

    protected function getData(): array
    {
        $startDate = Carbon::now()->subDays(29)->startOfDay();

        $orders = Order::query()
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as orders_count')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $revenue = [];
        $ordersCount = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->format('M d');
            $revenue[] = (float) ($orders[$date]->revenue ?? 0);
            $ordersCount[] = (int) ($orders[$date]->orders_count ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (TK.)',
                    'data' => $revenue,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.15)',
                    'fill' => true,
                    'tension' => 0.35,
                    'pointBackgroundColor' => '#10b981',
                    'pointRadius' => 3,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Orders',
                    'data' => $ordersCount,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.15)',
                    'fill' => true,
                    'tension' => 0.35,
                    'pointBackgroundColor' => '#3b82f6',
                    'pointRadius' => 3,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'position' => 'left',
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Revenue (TK.)',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'position' => 'right',
                    'beginAtZero' => true,
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Orders',
                    ],
                ],
            ],
        ];
    }
}
