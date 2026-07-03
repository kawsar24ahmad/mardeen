<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;

class RepeatedCustomerAnalytics extends ChartWidget
{
    protected ?string $heading = 'Returning Customers Breakdown';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    // The active selection state watched live by Livewire
    public ?string $filter = 'year';

    // Temporary storage for metrics to display in our custom header
    protected array $headerMetrics = [];

    public static function canView(): bool
    {
        return ! auth()->user()?->hasRole('staff');
    }

    public function getFilters(): ?array
    {
        return [
            'week' => 'This Week',
            'month' => 'This Month',
            'year' => 'This Year',
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * Extra visual configurations sent directly into the Chart.js runtime engine
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'boxWidth' => 12,
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'padding' => 20,
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'grid' => [
                        'color' => 'rgba(156, 163, 175, 0.1)', // Subtle light gray dividers
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false, // Cleans up vertical grid lines
                    ],
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        [$start, $end, $groupByFormat] = $this->resolvePeriod();

        // 1. Database-level aggregation to find repeat buyers quickly
        $customerOrders = Order::query()
            ->select('customer_id')
            ->selectRaw('COUNT(*) as total_customer_orders')
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('customer_id')
            ->groupBy('customer_id')
            ->get()
            ->keyBy('customer_id');

        $orders = Order::query()
            ->select('id', 'customer_id', 'created_at')
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('customer_id')
            ->orderBy('created_at')
            ->get();

        $labels = [];
        $newData = [];
        $repeatData = [];

        if ($this->filter === 'week') {
            for ($i = 0; $i < 7; $i++) {
                $day = $start->copy()->addDays($i);
                $labels[$day->format('Y-m-d')] = $day->format('l');
                $newData[$day->format('Y-m-d')] = 0;
                $repeatData[$day->format('Y-m-d')] = 0;
            }
        } elseif ($this->filter === 'month') {
            $daysInMonth = $start->daysInMonth;
            for ($i = 0; $i < $daysInMonth; $i++) {
                $day = $start->copy()->addDays($i);
                $labels[$day->format('Y-m-d')] = $day->format('M d');
                $newData[$day->format('Y-m-d')] = 0;
                $repeatData[$day->format('Y-m-d')] = 0;
            }
        } else {
            for ($i = 1; $i <= 12; $i++) {
                $month = Carbon::create($start->year, $i, 1);
                $labels[$month->format('Y-m')] = $month->format('M');
                $newData[$month->format('Y-m')] = 0;
                $repeatData[$month->format('Y-m')] = 0;
            }
        }

        foreach ($orders as $order) {
            $bucketKey = $order->created_at->format($groupByFormat);

            if (! isset($newData[$bucketKey])) {
                continue;
            }

            $customerMetrics = $customerOrders->get($order->customer_id);

            if ($customerMetrics && $customerMetrics->total_customer_orders > 1) {
                $repeatData[$bucketKey]++;
            } else {
                $newData[$bucketKey]++;
            }
        }

        // Cache summaries into memory properties so our header render block can access them
        $totalNew = array_sum($newData);
        $totalRepeat = array_sum($repeatData);
        $grandTotal = $totalNew + $totalRepeat;

        $this->headerMetrics = [
            'total' => number_format($grandTotal),
            'rate' => $grandTotal > 0 ? round(($totalRepeat / $grandTotal) * 100, 1) : 0,
        ];

        return [
            'datasets' => [
                [
                    'label' => 'New Customer Orders',
                    'data' => array_values($newData),
                    'backgroundColor' => '#38bdf8', // Tailwind sky-400
                    'borderColor' => '#0284c7',
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'Returning Customer Orders',
                    'data' => array_values($repeatData),
                    'backgroundColor' => '#34d399', // Tailwind emerald-400
                    'borderColor' => '#059669',
                    'borderRadius' => 4,
                ],
            ],
            'labels' => array_values($labels),
        ];
    }

    /**
     * Renders a dashboard header utilizing native Filament input layouts
     */
    protected function getHeader(): ?View
    {
        $heading = $this->getHeading();
        $filters = $this->getFilters();

        // Grab values computed dynamically during our live data pass
        $metrics = $this->headerMetrics ?: ['total' => '0', 'rate' => '0'];

        return Blade::render('
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 pb-3 border-b border-gray-100 dark:border-gray-800/60">
                <div class="space-y-1">
                    <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        {{ $heading }}
                    </h3>
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <span>Total Volume: <strong class="text-gray-800 dark:text-gray-200">{{ $metrics[\'total\'] }} orders</strong></span>
                        <span class="text-gray-300 dark:text-gray-700">•</span>
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 font-medium">
                            {{ $metrics[\'rate\'] }}% Repeat Rate
                        </span>
                    </div>
                </div>

                <div class="w-full sm:w-44">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="filter">
                            @foreach($filters as $value => $text)
                                <option value="{{ $value }}">{{ $text }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </div>
        ', compact('heading', 'filters', 'metrics'));
    }

    private function resolvePeriod(): array
    {
        $now = now();

        return match ($this->filter) {
            'week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek(), 'Y-m-d'],
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth(), 'Y-m-d'],
            default => [$now->copy()->startOfYear(), $now->copy()->endOfYear(), 'Y-m'],
        };
    }
}
