<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersStatusChart extends ChartWidget
{
    protected ?string $heading = 'Orders by Status';

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return ! auth()->user()?->hasRole('staff');
    }

    protected function getData(): array
    {
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        $counts = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $data = [];
        foreach ($statuses as $status) {
            $data[] = (int) ($counts[$status] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $data,
                    'backgroundColor' => [
                        '#f59e0b', // pending
                        '#3b82f6', // processing
                        '#8b5cf6', // shipped
                        '#10b981', // delivered
                        '#ef4444', // cancelled
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => array_map(fn ($s) => ucfirst($s), $statuses),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}