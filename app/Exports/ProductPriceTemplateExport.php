<?php

namespace App\Exports;

use App\Models\Product;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductPriceTemplateExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        $products = Product::select('barcode', 'name')
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                return [
                    'barcode' => $product->barcode,
                    'name' => $product->name,
                    'start_1' => '',
                    'end_1' => '',
                    'price_1' => '',
                    'start_2' => '',
                    'end_2' => '',
                    'price_2' => '',
                    'start_3' => '',
                    'end_3' => '',
                    'price_3' => '',
                    'start_4' => '',
                    'end_4' => '',
                    'price_4' => '',
                    'cust_category_1' => '',
                    'cust_price_1' => '',
                    'cust_category_2' => '',
                    'cust_price_2' => '',
                    'cust_category_3' => '',
                    'cust_price_3' => '',
                    'cust_category_4' => '',
                    'cust_price_4' => '',
                ];
            });

        // Baris dummy di atas
        $example = collect([[
            'barcode' => '1234567890123',
            'name' => 'CONTOH (JANGAN HAPUS BARIS INI)',
            'start_1' => 1,
            'end_1' => 10,
            'price_1' => 10000,
            'start_2' => 11,
            'end_2' => 20,
            'price_2' => 9500,
            'start_3' => 21,
            'end_3' => 50,
            'price_3' => 9000,
            'start_4' => 51,
            'end_4' => 100,
            'price_4' => 8500,
            'cust_category_1' => 'UMUM',
            'cust_price_1' => 10000,
            'cust_category_2' => 'RESELLER',
            'cust_price_2' => 9500,
            'cust_category_3' => 'GROSIR',
            'cust_price_3' => 9000,
            'cust_category_4' => 'VIP',
            'cust_price_4' => 8500,
        ]]);

        return $example->merge($products);
    }

    public function headings(): array
    {
        return [
            'BARCODE',
            'NAMA PRODUK',
            'MULAI DARI 1',
            'SAMPAI DENGAN 1',
            'HARGA TINGKAT 1',
            'MULAI DARI 2',
            'SAMPAI DENGAN 2',
            'HARGA TINGKAT 2',
            'MULAI DARI 3',
            'SAMPAI DENGAN 3',
            'HARGA TINGKAT 3',
            'MULAI DARI 4',
            'SAMPAI DENGAN 4',
            'HARGA TINGKAT 4',
            'KATEGORI PELANGGAN 1',
            'HARGA KATEGORI 1',
            'KATEGORI PELANGGAN 2',
            'HARGA KATEGORI 2',
            'KATEGORI PELANGGAN 3',
            'HARGA KATEGORI 3',
            'KATEGORI PELANGGAN 4',
            'HARGA KATEGORI 4',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Border seluruh tabel
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
            ->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Heading
        $sheet->getStyle("A1:{$highestColumn}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A1:{$highestColumn}1")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Warna untuk masing-masing kelompok
        $sheet->getStyle("A1:B1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00'); // Baris judul

        $sheet->getStyle("A2:{$highestColumn}2")->getFont()->setBold(true);
        $sheet->getStyle("A2:{$highestColumn}2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E0E0E0');

        $sheet->getStyle("C1:E1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFC1C1'); // Tingkat 1
        $sheet->getStyle("F1:H1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('90CAF9'); // Tingkat 2
        $sheet->getStyle("I1:K1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('A5D6A7'); // Tingkat 3
        $sheet->getStyle("L1:N1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFF59D'); // Tingkat 4

        $sheet->getStyle("O1:P1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CE93D8'); // Kategori 1
        $sheet->getStyle("Q1:R1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('80DEEA'); // Kategori 2
        $sheet->getStyle("S1:T1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DCEDC8'); // Kategori 3
        $sheet->getStyle("U1:V1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFCC80'); // Kategori 4

        // Format angka ribuan
        $priceCols = ['E', 'H', 'K', 'N', 'P', 'R', 'T', 'V'];
        foreach ($priceCols as $col) {
            $sheet->getStyle("{$col}2:{$col}{$highestRow}")
                ->getNumberFormat()
                ->setFormatCode('#,##0');
        }

        return [];
    }

}
