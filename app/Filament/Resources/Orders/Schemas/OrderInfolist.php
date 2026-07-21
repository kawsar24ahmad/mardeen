<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\FontWeight;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use App\Filament\Resources\Customers\CustomerResource;


class OrderInfolist
{
    /**
     * Configure the Order Infolist schema according to Filament v5 specifications.
     */
    public static function configure(Schema $schema): Schema
    {

        return $schema
            ->components([

                Grid::make()
                    ->columns([
                        'default' => 1,
                        'xl' => 3,
                    ])
                    ->columnSpanFull()
                    ->schema([

                        // LEFT SIDE
                        Grid::make(1)
                            ->columnSpan(2)
                            ->schema([

                                Section::make('Order Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('order_number')
                                                    ->label('Order Number'),

                                                TextEntry::make('created_at')
                                                    ->label('Order Date')
                                                    ->dateTime(),

                                                TextEntry::make('status')
                                                    ->badge(),

                                                TextEntry::make('payment_status')
                                                    ->badge(),
                                            ])
                                    ]),

                                Section::make('Ordered Items')
                                    ->icon('heroicon-o-shopping-bag')
                                    ->schema([
                                        RepeatableEntry::make('items')
                                            ->hiddenLabel()
                                            ->contained(false)
                                            ->schema([

                                                Grid::make(12)
                                                    ->schema([


                                                        ImageEntry::make('image')
                                                            ->hiddenLabel()
                                                            ->width(80)
                                                            ->height(80)
                                                            ->getStateUsing(
                                                                fn($record) => $record->variant?->getFirstMediaUrl('variant_images', 'thumb')
                                                                    ?? $record->product?->getFirstMediaUrl('products', 'thumb')
                                                            )
                                                            ->extraImgAttributes([
                                                                'class' => 'object-cover rounded-lg',
                                                            ])
                                                            ->columnSpan(2),



                                                        Grid::make(1)
                                                            ->schema([

                                                                TextEntry::make('product_name')
                                                                    ->hiddenLabel()
                                                                    ->formatStateUsing(
                                                                        fn($record) =>
                                                                        $record->product->name .
                                                                            ($record->variant
                                                                                ? ' - ' . $record->variant->display_label
                                                                                : '')
                                                                    )
                                                                    ->weight(FontWeight::Bold)
                                                                    ->size('sm'),

                                                                TextEntry::make('product_sku')
                                                                    ->label('SKU'),

                                                                TextEntry::make('price')
                                                                    ->money('BDT')
                                                                    ->label('Unit Price'),

                                                            ])
                                                            ->columnSpan(6),

                                                        Grid::make(1)
                                                            ->schema([

                                                                TextEntry::make('quantity')
                                                                    ->badge(),

                                                                TextEntry::make('subtotal')
                                                                    ->money('BDT')
                                                                    ->weight(FontWeight::Bold),

                                                            ])
                                                            ->columnSpan(4),

                                                    ]),
                                            ]),
                                    ]),



                                Section::make('Shipping Address')
                                    ->icon('heroicon-o-map-pin')
                                    ->schema([
                                        TextEntry::make('Customer Info')->state(fn($record) => [
                                            $record->shipping_full_name,
                                            $record->shipping_phone,
                                        ]),



                                        TextEntry::make('address')
                                            ->state(fn($record) => collect([
                                                $record->shipping_address_line_1,
                                                $record->shipping_address_line_2,
                                                $record->shipping_city,
                                                $record->shipping_state,
                                                $record->shipping_postal_code,
                                                $record->shipping_country,
                                            ])->filter()->implode(', '))
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Order History')
                                    ->icon('heroicon-o-clock')
                                    ->visible(fn($record) => $record->orderStatuses->isNotEmpty())
                                    ->schema([
                                        RepeatableEntry::make('orderStatuses')
                                            ->hiddenLabel()
                                            ->contained(false)
                                            ->grid(1)
                                            ->schema([
                                                Section::make()
                                                    ->schema([
                                                        Grid::make([
                                                            'default' => 1,
                                                            'md' => 3,
                                                        ])
                                                            ->schema([
                                                                TextEntry::make('status')
                                                                    ->badge()
                                                                    ->label('Status')
                                                                    ->color(fn(string $state): string => match ($state) {
                                                                        'pending' => 'warning',
                                                                        'processing' => 'info',
                                                                        'shipped' => 'primary',
                                                                        'delivered' => 'success',
                                                                        'cancelled' => 'danger',
                                                                        default => 'gray',
                                                                    }),

                                                                TextEntry::make('notes')
                                                                    ->label('Notes')
                                                                    ->placeholder('No notes provided'),

                                                                TextEntry::make('created_at')
                                                                    ->label('Time')
                                                                    ->since()
                                                                    ->color('gray'),
                                                            ]),
                                                    ])
                                            ]),
                                    ])
                                    ->collapsible(),
                            ]),

                        // RIGHT SIDEBAR
                        Grid::make(1)
                            ->columnSpan(1)
                            ->schema([

                                Section::make('Order Summary')
                                    ->icon('heroicon-o-calculator')
                                    ->schema([
                                        Grid::make(1)
                                            ->schema([

                                                TextEntry::make('subtotal')
                                                    ->label('Subtotal')
                                                    ->money('BDT')
                                                    ->color('gray')
                                                    ->columnSpanFull(),

                                                TextEntry::make('discount_amount')
                                                    ->label('Discount')
                                                    ->money('BDT')
                                                    ->color('danger'),

                                                TextEntry::make('shipping_cost')
                                                    ->label('Shipping Fee')
                                                    ->money('BDT')
                                                    ->color('gray'),

                                                TextEntry::make('total')
                                                    ->label('Grand Total')
                                                    ->money('BDT')
                                                    ->weight(FontWeight::Bold)
                                                    ->size(TextSize::Large)
                                                    ->color('primary'),
                                            ]),
                                    ])
                                    ->extraAttributes([
                                        'class' => 'rounded-xl border border-gray-200 shadow-sm p-2'
                                    ])
                                    ->collapsible(),

                                Section::make('Customer Profile')
                                    ->icon('heroicon-o-user')
                                    ->schema([

                                        Grid::make(1)
                                            ->schema([

                                                // Name (highlighted)
                                                TextEntry::make('customer.name')
                                                    ->hiddenLabel()
                                                    ->weight(FontWeight::Bold)
                                                    ->size(TextSize::Large)
                                                    ->color('primary'),

                                                // Email
                                                TextEntry::make('customer.email')
                                                    ->hiddenLabel()
                                                    ->icon('heroicon-m-envelope')
                                                    ->copyable()
                                                    ->color('gray'),

                                                // Phone (click to call)
                                                TextEntry::make('shipping_phone')
                                                    ->hiddenLabel()
                                                    ->icon('heroicon-m-phone')
                                                    ->url(fn($record) => 'tel:' . $record->shipping_phone)
                                                    ->copyable()
                                                    ->color('success'),
                                            ]),
                                    ])
                                    ->collapsible(),



                                Section::make('Logistics')
                                    ->icon('heroicon-o-truck')
                                    ->schema([
                                        TextEntry::make('tracking_number')
                                            ->copyable(),

                                        TextEntry::make('payment_method')
                                            ->badge(),

                                        TextEntry::make('type')
                                            ->badge(),
                                    ]),

                            ]),
                    ]),

                Section::make('Courier History')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        Section::make('🛡️ Risk Analysis')
                            ->icon('heroicon-o-shield-check')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('courier_check.risk_verdict.label')
                                    ->label('Risk Level')
                                    ->badge()
                                    ->size('lg')
                                    ->color(fn($state) => match (strtolower($state ?? '')) {
                                        'safe' => 'success',
                                        'medium' => 'warning',
                                        'high' => 'danger',
                                        default => 'gray',
                                    }),

                                TextEntry::make('courier_check.risk_verdict.action')
                                    ->label('Recommendation')
                                    ->badge(),

                                TextEntry::make('courier_checked_at')
                                    ->label('Last Checked')
                                    ->since(),
                            ]),

                        Section::make('📊 Delivery Summary')
                            ->icon('heroicon-o-chart-bar')
                            ->columns(4)
                            ->schema([
                                TextEntry::make('courier_check.data.summary.total_parcel')
                                    ->label('Total Parcel')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('courier_check.data.summary.success_parcel')
                                    ->label('Delivered')
                                    ->badge()
                                    ->color('success'),

                                TextEntry::make('courier_check.data.summary.cancelled_parcel')
                                    ->label('Cancelled')
                                    ->badge()
                                    ->color('danger'),

                                TextEntry::make('courier_check.data.summary.success_ratio')
                                    ->label('Success Rate')
                                    ->suffix('%')
                                    ->badge()
                                    ->color(fn($state) => match (true) {
                                        $state >= 90 => 'success',
                                        $state >= 70 => 'warning',
                                        default => 'danger',
                                    }),
                            ]),

                        Section::make('Courier Performance')
                            ->icon('heroicon-o-truck')
                            ->columnSpanFull()
                            ->schema([

                                Grid::make(6)
                                    ->schema([
                                        TextEntry::make('header_logo')
                                            ->hiddenLabel()
                                            ->state('Logo')
                                            ->weight(FontWeight::Bold),

                                        TextEntry::make('header_name')
                                            ->hiddenLabel()
                                            ->state('Courier')
                                            ->weight(FontWeight::Bold),

                                        TextEntry::make('header_total')
                                            ->hiddenLabel()
                                            ->state('Total')
                                            ->weight(FontWeight::Bold),

                                        TextEntry::make('header_success')
                                            ->hiddenLabel()
                                            ->state('Delivered')
                                            ->weight(FontWeight::Bold),

                                        TextEntry::make('header_cancel')
                                            ->hiddenLabel()
                                            ->state('Cancelled')
                                            ->weight(FontWeight::Bold),

                                        TextEntry::make('header_ratio')
                                            ->hiddenLabel()
                                            ->state('Success')
                                            ->weight(FontWeight::Bold),
                                    ]),

                                RepeatableEntry::make('courier_stats')
                                    ->hiddenLabel()
                                    ->contained(false)
                                    ->state(fn($record) => collect($record->courier_check['data'] ?? [])
                                        ->except('summary')
                                        ->values()
                                        ->toArray())
                                    ->schema([

                                        ImageEntry::make('logo')
                                            ->hiddenLabel()
                                            ->imageHeight(30)
                                            ->imageWidth(120)
                                            ->extraImgAttributes([
                                                'class' => 'object-contain',
                                            ]),

                                        TextEntry::make('name')
                                            ->hiddenLabel()
                                            ->weight(FontWeight::Bold),

                                        TextEntry::make('total_parcel')
                                            ->hiddenLabel()
                                            ->badge(),

                                        TextEntry::make('success_parcel')
                                            ->hiddenLabel()
                                            ->badge()
                                            ->color('success'),

                                        TextEntry::make('cancelled_parcel')
                                            ->hiddenLabel()
                                            ->badge()
                                            ->color('danger'),

                                        TextEntry::make('success_ratio')
                                            ->hiddenLabel()
                                            ->suffix('%')
                                            ->badge()
                                            ->color(fn($state) => match (true) {
                                                $state >= 90 => 'success',
                                                $state >= 70 => 'warning',
                                                default => 'danger',
                                            }),

                                    ])
                                    ->columns(6),
                            ]),
                    ]),
            ]);
    }
}
