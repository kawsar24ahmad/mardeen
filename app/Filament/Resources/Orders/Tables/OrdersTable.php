<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use App\Models\Courier;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use App\Courier\Services\CourierService;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteBulkAction;
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
                    ->sortable(),
                TextColumn::make('discount_amount')
                    ->label('Discount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('shipping_cost')
                    ->money('BDT')
                    ->sortable(),
                // TextColumn::make('tax_amount')
                //     ->numeric()
                //     ->sortable(),
                TextColumn::make('total')
                    ->numeric()
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

                            $response =  app(CourierService::class)
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
