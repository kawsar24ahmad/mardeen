<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Models\Courier;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\EditAction;
use App\Services\BDCourierService;
use Filament\Forms\Components\Select;
use App\Courier\Services\CourierService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\Orders\OrderResource;
use Filament\Infolists\Components\RepeatableEntry;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;
    // protected  string $view = 'filament.pages.view-order';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('downloadInvoice')
                ->label('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {

                    $pdf = Pdf::loadView('pdf.order-invoice', [
                        'order' => $this->record,
                    ]);

                    return response()->streamDownload(
                        fn() => print($pdf->output()),
                        'order-' . $this->record->id . '.pdf'
                    );
                }),


            Action::make('sendToCourier')
                ->label('Send To Courier')
                ->icon('heroicon-o-truck')
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
                ->action(function (array $data) {

                    try {
                        $this->record->update([
                            'courier_id' => $data['courier_id'],
                        ]);

                        $response =  app(CourierService::class)
                            ->send($this->record->fresh());

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


        ];
    }
}
