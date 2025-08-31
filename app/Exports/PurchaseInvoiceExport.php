<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PurchaseInvoiceExport implements FromView, WithEvents, ShouldAutoSize, WithColumnFormatting
{
   /**
    * @return \Illuminate\Support\Collection
    */
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public static function afterSheet(AfterSheet $event)
    {
        $event->sheet->getStyle('A1:' . $event->sheet->getHighestColumn()
            . $event->sheet->getHighestRow())->applyFromArray([
            'font' => [
                'name' => 'Roboto',
                'size' => 12,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

       
        $event->sheet->getStyle('A1:I1')->getFill()->applyFromArray([
            'fillType' => 'solid',
            'rotation' => 0,
            'color' => ['rgb' => 'e0e0e0'],
            'font' => [
                'name' => 'Roboto',
                'size' => 14,
                'bold'  =>  true,
            ],
        ]);

        $lastRow = $event->sheet->getHighestDataRow();
        for ($i = 2; $i <= $lastRow; $i++) {
            $event->sheet->getStyle('F' . $i. ':H' . $i)->getNumberFormat()->setFormatCode('#,##0');
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => [
                self::class, 'afterSheet'
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F2:H2' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

 
    public function view(): View
    {
        return view('export.purchase_invoice_excel', [
            'title'             => 'DATA PURCHASE INVOICE',
            'purchaseInvoices'  => $this->data,
        ]);
    }
}
