<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\PageMargins;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OrdersExport implements
    FromCollection,
    WithHeadings,
    ShouldAutoSize,
    WithEvents,
    WithDrawings
{
    protected ?string $from;
    protected ?string $to;

    public function __construct(?string $from = null, ?string $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    protected function settings(): ?SiteSetting
    {
        return SiteSetting::first();
    }

    public function collection()
    {
        $query = Order::query()->with('customer:id,email');

        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->from)->startOfDay(),
                Carbon::parse($this->to)->endOfDay(),
            ]);
        }

        return $query
            ->latest()
            ->get()
            ->map(function (Order $order) {

                return [

                    'Order No' => $order->order_number,

                    'Customer' => $order->shipping_full_name,

                    'Email' => $order->customer?->email,

                    'Phone' => $order->shipping_phone,

                    'Address' => $order->shipping_address_line_1,

                    'Payment Method' => ucfirst($order->payment_method),

                    'Payment Status' => ucfirst($order->payment_status),

                    'Order Status' => ucfirst($order->status),

                    'Subtotal' => number_format($order->subtotal, 2),

                    'Discount' => number_format($order->discount_amount, 2),

                    'Total' => number_format($order->total, 2),

                    'Tracking Code' => $order->tracking_code,

                    'Order Date' => $order->created_at->format('d M Y h:i A'),
                ];
            });
    }

    public function headings(): array
    {
        $setting = $this->settings();

        $company = $setting?->site_name ?? config('app.name');
        $address = $setting?->address ?? '';
        $phone = $setting?->phone ?? '';
        $email = $setting?->email ?? '';

        return [

            [
                $company,
            ],

            [
                'ORDER REPORT',
            ],

            [
                trim($address),
            ],

            [
                trim($phone . '    ' . $email),
            ],

            [
                $this->from && $this->to
                    ? 'Reporting Period : ' . Carbon::parse($this->from)->format('d M Y')
                    . ' - ' .
                    Carbon::parse($this->to)->format('d M Y')
                    : 'Reporting Period : All Orders',
            ],

            [],

            [
                'Order No',
                'Customer',
                'Email',
                'Phone',
                'Address',
                'Payment Method',
                'Payment Status',
                'Status',
                'Subtotal (৳)',
                'Discount (৳)',
                'Grand Total (৳)',
                'Tracking Code',
                'Order Date',
            ],

        ];
    }

    public function drawings()
    {
        $setting = $this->settings();

        if (! $setting?->logo) {
            return [];
        }

        $path = public_path('storage/' . $setting->logo);

        if (! file_exists($path)) {
            return [];
        }

        $drawing = new Drawing();

        $drawing->setName($setting->site_name ?? config('app.name'));
        $drawing->setDescription('Company Logo');
        $drawing->setPath($path);
        $drawing->setCoordinates('A1');
        $drawing->setHeight(70);
        $drawing->setOffsetX(10);
        $drawing->setOffsetY(5);

        return $drawing;
    }

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet;
                $worksheet = $sheet->getDelegate();

                $lastRow = $worksheet->getHighestRow();

                /*
            |--------------------------------------------------------------------------
            | A4 Landscape
            |--------------------------------------------------------------------------
            */

                $worksheet->getPageSetup()
                    ->setPaperSize(PageSetup::PAPERSIZE_A4);

                $worksheet->getPageSetup()
                    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

                $worksheet->getPageSetup()
                    ->setFitToWidth(1);

                $worksheet->getPageSetup()
                    ->setFitToHeight(0);

                /*
            |--------------------------------------------------------------------------
            | Margins
            |--------------------------------------------------------------------------
            */

                $worksheet->getPageMargins()->setTop(.35);
                $worksheet->getPageMargins()->setBottom(.35);
                $worksheet->getPageMargins()->setLeft(.25);
                $worksheet->getPageMargins()->setRight(.25);

                /*
            |--------------------------------------------------------------------------
            | Header Merge
            |--------------------------------------------------------------------------
            */

                $worksheet->mergeCells('B1:M1');
                $worksheet->mergeCells('B2:M2');
                $worksheet->mergeCells('B3:M3');
                $worksheet->mergeCells('B4:M4');
                $worksheet->mergeCells('B5:M5');

                /*
            |--------------------------------------------------------------------------
            | Row Height
            |--------------------------------------------------------------------------
            */

                $worksheet->getRowDimension(1)->setRowHeight(45);
                $worksheet->getRowDimension(2)->setRowHeight(25);
                $worksheet->getRowDimension(3)->setRowHeight(20);
                $worksheet->getRowDimension(4)->setRowHeight(20);
                $worksheet->getRowDimension(5)->setRowHeight(22);
                $worksheet->getRowDimension(7)->setRowHeight(24);

                /*
            |--------------------------------------------------------------------------
            | Company Name
            |--------------------------------------------------------------------------
            */

                $sheet->getStyle('B1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 22,
                        'color' => ['rgb' => '1E3A8A'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                /*
            |--------------------------------------------------------------------------
            | Report Title
            |--------------------------------------------------------------------------
            */

                $sheet->getStyle('B2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 15,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                /*
            |--------------------------------------------------------------------------
            | Address / Phone
            |--------------------------------------------------------------------------
            */

                $sheet->getStyle('B3:B5')->applyFromArray([
                    'font' => [
                        'size' => 10,
                        'color' => ['rgb' => '555555'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                /*
            |--------------------------------------------------------------------------
            | Table Header
            |--------------------------------------------------------------------------
            */

                $sheet->getStyle('A7:M7')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '2563EB'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                    ],
                ]);

                /*
            |--------------------------------------------------------------------------
            | Currency Format
            |--------------------------------------------------------------------------
            */

                $worksheet->getStyle("I8:K{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                /*
            |--------------------------------------------------------------------------
            | Center Align
            |--------------------------------------------------------------------------
            */

                $worksheet->getStyle("A7:M{$lastRow}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);

                /*
            |--------------------------------------------------------------------------
            | Freeze Header
            |--------------------------------------------------------------------------
            */

                $worksheet->freezePane('A8');

                /*
            |--------------------------------------------------------------------------
            | Auto Filter
            |--------------------------------------------------------------------------
            */

                $worksheet->setAutoFilter("A7:M7");
            },

        ];
    }

// <?php

// namespace App\Exports;

// use Carbon\Carbon;
// use App\Models\Order;
// use App\Models\SiteSetting;
// use Maatwebsite\Excel\Events\AfterSheet;
// use PhpOffice\PhpSpreadsheet\Style\Fill;
// use Maatwebsite\Excel\Concerns\WithEvents;
// use PhpOffice\PhpSpreadsheet\Style\Border;
// use Maatwebsite\Excel\Concerns\WithDrawings;
// use Maatwebsite\Excel\Concerns\WithHeadings;
// use PhpOffice\PhpSpreadsheet\Style\Alignment;
// use Maatwebsite\Excel\Concerns\FromCollection;
// use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// class OrdersExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents,   WithDrawings
// {
//     protected ?string $from;
//     protected ?string $to;

//     public function __construct($from = null, $to = null)
//     {
//         $this->from = $from;
//         $this->to = $to;
//     }

//     public function collection()
//     {
//         $query = Order::query();

//         if ($this->from && $this->to) {
//             $query->whereBetween('created_at', [
//                 Carbon::parse($this->from)->startOfDay(),
//                 Carbon::parse($this->to)->endOfDay(),
//             ]);
//         }

//         return $query
//             ->with('customer:id,email')
//             ->latest()
//             ->get()
//             ->map(function ($order) {
//                 return [
//                     'order_number'            => $order->order_number,
//                     'shipping_full_name'      => $order->shipping_full_name,
//                     'customer_email'          => $order->customer?->email,
//                     'shipping_phone'          => $order->shipping_phone,
//                     'shipping_address_line_1' => $order->shipping_address_line_1,
//                     'payment_method'          => ucfirst($order->payment_method),
//                     'payment_status'          => ucfirst($order->payment_status),
//                     'status'                  => ucfirst($order->status),
//                     'subtotal'                => $order->subtotal,
//                     'discount_amount'         => $order->discount_amount,
//                     'total'                   => $order->total,
//                     'tracking_code'           => $order->tracking_code,
//                     'created_at'              => $order->created_at->format('d M Y, h:i A'),
//                 ];
//             });
//     }

//     public function headings(): array
//     {
//         return [
//             [
//                 optional(SiteSetting::first())->site_name ?? config('app.name'),
//             ],
//             [
//                 'Orders Report',
//             ],
//             [
//                 $this->from && $this->to
//                     ? "Date : {$this->from} - {$this->to}"
//                     : "All Orders",
//             ],
//             [],
//             [
//                 'Order',
//                 'Customer',
//                 'Email',
//                 'Phone',
//                 'Address',
//                 'Payment',
//                 'Payment Status',
//                 'Status',
//                 'Subtotal',
//                 'Discount',
//                 'Total',
//                 'Tracking',
//                 'Date',
//             ],
//         ];
//     }

//     public function registerEvents(): array
//     {
//         return [
//             AfterSheet::class => function (AfterSheet $event) {

//                 $sheet = $event->sheet;

//                 $sheet->mergeCells('A1:L1');
//                 $sheet->mergeCells('A2:L2');
//                 $sheet->mergeCells('A3:L3');

//                 $sheet->getStyle('A1:A3')->applyFromArray([
//                     'font' => [
//                         'bold' => true,
//                         'size' => 16,
//                         'color' => ['rgb' => 'FFFFFF'],
//                     ],
//                     'alignment' => [
//                         'horizontal' => Alignment::HORIZONTAL_CENTER,
//                     ],
//                     'fill' => [
//                         'fillType' => Fill::FILL_SOLID,
//                         'color' => ['rgb' => '1E40AF'],
//                     ],
//                 ]);

//                 $sheet->getStyle('A5:L5')->applyFromArray([
//                     'font' => [
//                         'bold' => true,
//                         'color' => ['rgb' => 'FFFFFF'],
//                     ],
//                     'fill' => [
//                         'fillType' => Fill::FILL_SOLID,
//                         'color' => ['rgb' => '2563EB'],
//                     ],
//                     'borders' => [
//                         'allBorders' => [
//                             'borderStyle' => Border::BORDER_THIN,
//                         ],
//                     ],
//                 ]);

//                 $sheet->freezePane('A6');
//             },
//         ];
//     }

//     public function drawings()
//     {
//         $drawing = new Drawing();

//         $drawing->setName('Logo');
//         $drawing->setDescription('Company Logo');
//         $drawing->setPath(public_path('logo.webp'));
//         $drawing->setHeight(60);
//         $drawing->setCoordinates('A1');

//         return $drawing;
//     }
// }
