<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Models\Courier;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use App\Courier\Services\CourierService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\Orders\OrderResource;

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


        ];
    }
}
