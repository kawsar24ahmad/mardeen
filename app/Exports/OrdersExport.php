<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OrdersExport implements
    FromCollection,
    WithHeadings,
    WithEvents
{
    protected ?string $from;
    protected ?string $to;

    public function __construct(?string $from = null, ?string $to = null)
    {
        $this->from = $from;
        $this->to = $to;
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

        return $query->latest()->get()->map(function (Order $order) {
            return [
                'Order No' => $order->order_number,
                'Customer' => $order->shipping_full_name,
                'Email' => $order->customer?->email,
                'Phone Number' => $order->shipping_phone ? ' ' . $order->shipping_phone : '',
                'Address' => $order->shipping_address_line_1,
                'Payment Method' => ucfirst($order->payment_method),
                'Payment Status' => ucfirst($order->payment_status),
                'Order Status' => ucfirst($order->status),
                'Subtotal' => (float) $order->subtotal,
                'Discount' => (float) $order->discount_amount,
                'Total' => (float) $order->total,
                'Tracking Code' => $order->tracking_code,
                'Order Date' => $order->created_at->format('d M Y h:i A'),
            ];
        });
    }

    public function headings(): array
    {
        $setting = SiteSetting::first();
        $company = $setting?->site_name ?? config('app.name');

        $period = $this->from && $this->to
            ? Carbon::parse($this->from)->format('d M Y') . ' - ' . Carbon::parse($this->to)->format('d M Y')
            : 'All Historical Orders';

        return [
            // Row 1: Company Name (Pure Centered)
            [strtoupper($company), '', '', '', '', '', '', '', '', '', '', '', ''],
            // Row 2: Subtitle report title (Pure Centered)
            ['ORDER STATEMENT REPORT', '', '', '', '', '', '', '', '', '', '', '', ''],
            // Row 3: Meta details range layout (Pure Centered)
            ["Statement Period: {$period}   •   Generated On: " . now()->format('d M Y, h:i A'), '', '', '', '', '', '', '', '', '', '', '', ''],
            [], // Row 4: Spacer row
            [   // Row 5: Table Column Headers
                'Order No',
                'Customer Details',
                'Email Address',
                'Phone Number',
                'Delivery Address',
                'Method',
                'Payment Status',
                'Order Status',
                'Subtotal (৳)',
                'Discount (৳)',
                'Grand Total (৳)',
                'Tracking Code',
                'Date Generated',
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $worksheet = $event->sheet->getDelegate();
                $lastRow = $worksheet->getHighestRow();
                $dataStartRow = 6;

                // 1. Global Font Matrix
                $worksheet->getStyle("A1:M{$lastRow}")->getFont()->setName('Segoe UI');
                $worksheet->getStyle("A5:M{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // 2. Merging & Cell Scale Heights
                $worksheet->mergeCells('A1:M1');
                $worksheet->mergeCells('A2:M2');
                $worksheet->mergeCells('A3:M3');

                $worksheet->getRowDimension(1)->setRowHeight(38); // Extra breathing space for main title
                $worksheet->getRowDimension(2)->setRowHeight(24);
                $worksheet->getRowDimension(3)->setRowHeight(26);
                $worksheet->getRowDimension(4)->setRowHeight(12); // Spacer line
                $worksheet->getRowDimension(5)->setRowHeight(30); // Table headers height

                // 3. Premium Slate Theme Background Fill for Header (A1:M3)
                $worksheet->getStyle('A1:M3')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '1E293B'], // Clean Professional Deep Slate Dark Hex
                    ],
                ]);

                // Perfect Center Alignments & Text Color for Title Block
                $worksheet->getStyle('A1:M3')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // Row specific fine-tuning typography
                $worksheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('FFFFFF');
                $worksheet->getStyle('A2')->getFont()->setBold(true)->setSize(11)->getColor()->setRGB('94A3B8');
                $worksheet->getStyle('A3')->getFont()->setSize(10)->getColor()->setRGB('CBD5E1');

                // 4. Data Grid Header Visual Structure (Row 5)
                $worksheet->getStyle('A5:M5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '0F172A']], // Darker Accent Border Frame
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // 5. Data Loop Formatting & Zebra Stripes
                $worksheet->freezePane('A6');
                for ($row = $dataStartRow; $row <= $lastRow; $row++) {
                    $worksheet->getRowDimension($row)->setRowHeight(20);

                    if ($row % 2 == 0) {
                        $worksheet->getStyle("A{$row}:M{$row}")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F8FAFC');
                    }
                }

                // 6. Borders, Alignments & Formats
                $worksheet->getStyle("A{$dataStartRow}:M{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E2E8F0'],
                        ],
                    ],
                ]);

                $worksheet->getStyle("A{$dataStartRow}:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $worksheet->getStyle("F{$dataStartRow}:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $worksheet->getStyle("L{$dataStartRow}:M{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Text format fallback map logic for Phone Numbers
                $worksheet->getStyle("D{$dataStartRow}:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $worksheet->getStyle("D{$dataStartRow}:D{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);

                // Currency / Numerical alignment parsing
                $worksheet->getStyle("I{$dataStartRow}:K{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $worksheet->getStyle("I{$dataStartRow}:K{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                // Column scaling widths definition layout
                $widths = [
                    'A' => 25,
                    'B' => 20,
                    'C' => 25,
                    'D' => 18,
                    'E' => 30,
                    'F' => 12,
                    'G' => 16,
                    'H' => 15,
                    'I' => 15,
                    'J' => 15,
                    'K' => 16,
                    'L' => 16,
                    'M' => 24
                ];

                foreach ($widths as $col => $width) {
                    $worksheet->getColumnDimension($col)->setWidth($width);
                }

                $worksheet->getPageSetup()->setPrintArea("A1:M{$lastRow}");
            },
        ];
    }
}
