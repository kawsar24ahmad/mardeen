<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;

class RepeatedCustomersTable extends TableWidget
{
    public ?string $period = 'all_time';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 6;

    public static function canView(): bool
    {
        return ! auth()->user()?->hasRole('staff');
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Top Returning Customers')
            ->description('Customers who placed more than one order in the selected period.')
            ->query($this->getTableQuery())
            ->defaultPaginationPageOption(10)
            ->paginated([5, 10, 25, 50])
            ->columns([
                TextColumn::make('name')
                    ->label('Customer')
                    ->searchable(['name', 'email', 'phone'])
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),

                TextColumn::make('orders_count')
                    ->label('Orders')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('repeat_orders_count')
                    ->label('Repeat Orders')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('total_spent')
                    ->label('Total Spent')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('last_order_at')
                    ->label('Last Order')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('period')
                    ->label('Period')
                    ->options([
                        '7d' => 'Last 7 days',
                        '30d' => 'Last 30 days',
                        '90d' => 'Last 90 days',
                        '365d' => 'Last 12 months',
                        'all_time' => 'All time',
                    ])
                    ->default('all_time')
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? 'all_time';
                        return $this->applyPeriodScope($query, $value);
                    }),
                Filter::make('only_repeat')
                    ->label('Only repeat customers')
                    ->toggle()
                    ->query(function (Builder $query) {
                        $query->having('orders_count', '>=', 2);
                    }),
            ]);
    }

    /**
     * @return Builder<Customer> | Relation
     */
    protected function getTableQuery(): Builder|Relation
    {
        $period = $this->period ?: 'all_time';

        $base = Customer::query()
            ->withCount([
                'orders as orders_count',
                'orders as repeat_orders_count' => function ($q) {
                    $q->whereIn('id', function ($sub) {
                        $sub->select('id')->from('orders')->whereRaw('customer_id = customers.id');
                    });
                },
            ])
            ->withSum([
                'orders as total_spent' => function ($q) {
                    $q->where('payment_status', 'paid');
                },
            ], 'total')
            ->withMax('orders as last_order_at', 'created_at');

        $query = $this->applyPeriodScope($base, $period);

        return $query->orderByDesc('total_spent')->orderByDesc('orders_count');
    }

    protected function applyPeriodScope(Builder $query, string $period): Builder
    {
        $now = Carbon::now();

        return match ($period) {
            '7d' => $query->whereHas('orders', fn ($q) => $q->where('created_at', '>=', $now->copy()->subDays(7))),
            '30d' => $query->whereHas('orders', fn ($q) => $q->where('created_at', '>=', $now->copy()->subDays(30))),
            '90d' => $query->whereHas('orders', fn ($q) => $q->where('created_at', '>=', $now->copy()->subDays(90))),
            '365d' => $query->whereHas('orders', fn ($q) => $q->where('created_at', '>=', $now->copy()->subDays(365))),
            default => $query,
        };
    }
}
