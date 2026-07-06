<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use App\Models\Courier;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use App\Services\BDCourierService;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use App\Courier\Services\CourierService;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use App\Filament\Resources\Customers\CustomerResource;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->searchable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable()
                    ->url(fn($record) => $record->customer ? CustomerResource::getUrl('edit', [$record->customer]) : null),
                TextColumn::make('coupon.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subtotal')
                    ->numeric()
                    ->money('BDT')
                    ->sortable(),
                TextColumn::make('discount_amount')
                    ->label('Discount')
                    ->numeric()
                    ->money('BDT')
                    ->sortable(),
                TextColumn::make('shipping_cost')
                    ->money('BDT')
                    ->sortable(),
                // TextColumn::make('tax_amount')
                //     ->numeric()
                //     ->sortable(),
                TextColumn::make('total')
                    ->numeric()
                    ->money('BDT')
                    ->sortable(),
                TextColumn::make('shipping_full_name')
                    ->searchable(),
                TextColumn::make('shipping_phone')
                    ->searchable(),
                TextColumn::make('shipping_address_line_1')
                    ->label('Address')
                    ->searchable(),
                // TextColumn::make('shipping_address_line_2')
                //     ->searchable(),
                // TextColumn::make('shipping_city')
                //     ->searchable(),
                // TextColumn::make('shipping_state')
                //     ->searchable(),
                // TextColumn::make('shipping_postal_code')
                //     ->searchable(),
                // TextColumn::make('shipping_country')
                //     ->searchable(),
                // IconColumn::make('is_default')
                //     ->boolean(),
                // TextColumn::make('type')
                //     ->badge(),
                TextColumn::make('payment_method')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('payment_status')
                    ->searchable()
                    ->badge()
                    ->color('info'),
                // TextColumn::make('transaction_id')
                //     ->searchable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('items_count')
                    ->badge()
                    ->counts('items'),
                // TextColumn::make('tracking_number')
                //     ->searchable(),
                TextColumn::make('tracking_code')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('consignment_id')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('courier.name')
                    ->badge()
                    ->color('danger')
                    ->searchable(),
                TextColumn::make('courier_status')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                TextColumn::make('admin_notes')
                    ->color('success')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ])
                    ->multiple()
                    ->native(false),
                SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'returned' => 'Returned',
                    ])
                    ->multiple()
                    ->native(false),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('sendToCourier')
                    ->label('Send To Courier')
                    ->icon('heroicon-o-truck')
                    ->color('danger')
                    ->schema([
                        Select::make('courier_id')
                            ->label('Courier')
                            ->options(
                                Courier::where('is_active', true)
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (Order $record, array $data) {

                        try {
                            $record->update([
                                'courier_id' => $data['courier_id'],
                            ]);

                            $response = app(CourierService::class)
                                ->send($record->fresh());

                            Notification::make()
                                ->title('Order sent to courier successfully.')
                                ->body($response->message)
                                ->success()
                                ->send();
                        } catch (\Throwable $th) {
                            Notification::make()
                                ->title('Courier Error')
                                ->body($th->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('courier_check')
                    ->label('Fraud Check')
                    ->icon('heroicon-o-shield-check')
                    ->color('warning')
                    ->modalWidth('5xl')
                    ->modalHeading('Courier Fraud Report')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->schema(function ($record) {

                        $result = app(BDCourierService::class)
                            ->check($record->shipping_phone);

                        $record->update([
                            'courier_check' => $result,
                            'courier_checked_at' => now(),
                        ]);

                        if ($result['status'] !== 'success') {
                            return [
                                Section::make('Result')
                                    ->schema([
                                        TextEntry::make('message')
                                            ->state($result['message']),
                                    ]),
                            ];
                        }

                        $summary = $result['data']['summary'];
                        $risk = $result['risk_verdict'];

                        $couriers = collect($result['data'])
                            ->except('summary')
                            ->values()
                            ->toArray();

                        return [

                            Section::make('Risk Verdict')
                                ->schema([

                                    TextEntry::make('level')
                                        ->label('Risk')
                                        ->badge()
                                        ->color(match ($risk['color']) {
                                            'green' => 'success',
                                            'yellow' => 'warning',
                                            'red' => 'danger',
                                            default => 'gray',
                                        })
                                        ->state($risk['label']),

                                    TextEntry::make('action')
                                        ->label('Recommended Action')
                                        ->state($risk['action']),

                                    TextEntry::make('reason')
                                        ->label('Reason')
                                        ->state(implode(', ', $risk['reasons'])),

                                ])
                                ->columns(3),

                            Section::make('Overall Summary')
                                ->schema([

                                    TextEntry::make('total')
                                        ->label('Total Parcel')
                                        ->state($summary['total_parcel']),

                                    TextEntry::make('success')
                                        ->label('Delivered')
                                        ->state($summary['success_parcel']),

                                    TextEntry::make('cancelled')
                                        ->label('Cancelled')
                                        ->state($summary['cancelled_parcel']),

                                    TextEntry::make('ratio')
                                        ->label('Success Ratio')
                                        ->badge()
                                        ->color(
                                            $summary['success_ratio'] >= 90
                                                ? 'success'
                                                : ($summary['success_ratio'] >= 70 ? 'warning' : 'danger')
                                        )
                                        ->state($summary['success_ratio'] . '%'),

                                ])
                                ->columns(4),

                            RepeatableEntry::make('couriers')
                                ->label('Courier Performance')
                                ->state($couriers)
                                ->schema([

                                    ImageEntry::make('logo')
                                        ->label('')
                                        ->imageHeight(30)
                                        ->imageWidth(120),

                                    TextEntry::make('name'),

                                    TextEntry::make('total_parcel')
                                        ->label('Total'),

                                    TextEntry::make('success_parcel')
                                        ->label('Delivered'),

                                    TextEntry::make('cancelled_parcel')
                                        ->label('Cancelled'),

                                    TextEntry::make('success_ratio')
                                        ->label('Success')
                                        ->badge()
                                        ->suffix('%'),

                                ])
                                ->columns(6),

                            RepeatableEntry::make('reports')
                                ->label('Fraud Reports')
                                ->state($result['reports'])
                                ->schema([

                                    ImageEntry::make('courierLogo')
                                        ->label(''),

                                    TextEntry::make('courierName')
                                        ->label('Courier'),

                                    TextEntry::make('name'),

                                    TextEntry::make('details'),

                                    TextEntry::make('created_at')
                                        ->date(),

                                ])
                                ->columns(5)
                                ->visible(fn() => count($result['reports']) > 0),

                        ];
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
