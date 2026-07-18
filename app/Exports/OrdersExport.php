<?php

namespace App\Exports;

use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrdersExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected ?string $from;
    protected ?string $to;

    public function __construct($from = null, $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        $query = Order::query();

        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->from)->startOfDay(),
                Carbon::parse($this->to)->endOfDay(),
            ]);
        }

        return $query->latest()->get([
            'order_number',
            'shipping_full_name',
            'shipping_phone',
            'shipping_address_line_1',
            'payment_method',
            'payment_status',
            'status',
            'subtotal',
            'discount_amount',
            'total',
            'tracking_code',
            'created_at',
        ]);
    }

    public function headings(): array
    {
        return [
            [
                'Kawsar Webs',
            ],
            [
                'Orders Report',
            ],
            [
                $this->from && $this->to
                    ? "Date : {$this->from} - {$this->to}"
                    : "All Orders",
            ],
            [],
            [
                'Order',
                'Customer',
                'Phone',
                'Address',
                'Payment',
                'Payment Status',
                'Status',
                'Subtotal',
                'Discount',
                'Total',
                'Tracking',
                'Date',
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet;

                $sheet->mergeCells('A1:L1');
                $sheet->mergeCells('A2:L2');
                $sheet->mergeCells('A3:L3');

                $sheet->getStyle('A1:A3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '1E40AF'],
                    ],
                ]);

                $sheet->getStyle('A5:L5')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '2563EB'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $sheet->freezePane('A6');
            },
        ];
    }
}
