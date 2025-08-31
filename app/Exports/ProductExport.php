<?php

namespace App\Exports;

use App\Models\Product;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $stockType; // 'inventory', 'outlet', atau 'all'

    public function __construct($stockType = 'all')
    {
        $this->stockType = $stockType;
    }

    public function collection()
    {
        $products = Product::with([
            'productUnit',
            'productCategory.parent',
            'productStock',
        ])
        ->whereHas('productStock', function ($query) {
            if ($this->stockType === 'inventory') {
                $query->whereNull('outlet_id')->where('stock_current', '>', 0);
            } elseif ($this->stockType === 'outlet') {
                $query->whereNotNull('outlet_id')->where('stock_current', '>', 0);
            } else {
                $query->where('stock_current', '>', 0);
            }
        })
        ->get();

        return $products->map(function ($product) {
            $category = optional($product->productCategory);
            $subCategory = ($category && $category->is_parent_category == 0) ? $category : null;
            $parentCategory = $subCategory ? $category->parent : $category;

            $stockInventory = $product->productStock->whereNull('outlet_id')->sum('stock_current');
            $stockOutlet = $product->productStock->whereNotNull('outlet_id')->sum('stock_current');

            if ($this->stockType === 'inventory') {
                return [
                    $product->barcode,
                    $product->name,
                    optional($product->productUnit)->name,
                    optional($parentCategory)->name,
                    optional($subCategory)->name,
                    $stockInventory,
                ];
            } elseif ($this->stockType === 'outlet') {
                return [
                    $product->barcode,
                    $product->name,
                    optional($product->productUnit)->name,
                    optional($parentCategory)->name,
                    optional($subCategory)->name,
                    $stockOutlet,
                ];
            } else {
                return [
                    $product->barcode,
                    $product->name,
                    optional($product->productUnit)->name,
                    optional($parentCategory)->name,
                    optional($subCategory)->name,
                    $stockInventory,
                    $stockOutlet,
                ];
            }
        });
    }

    public function headings(): array
    {
        if ($this->stockType === 'inventory') {
            return ['BARCODE', 'NAMA PRODUK', 'SATUAN', 'KATEGORI', 'SUB KATEGORI', 'STOK GUDANG'];
        } elseif ($this->stockType === 'outlet') {
            return ['BARCODE', 'NAMA PRODUK', 'SATUAN', 'KATEGORI', 'SUB KATEGORI', 'STOK OUTLET'];
        } else {
            return ['BARCODE', 'NAMA PRODUK', 'SATUAN', 'KATEGORI', 'SUB KATEGORI', 'STOK GUDANG', 'STOK OUTLET'];
        }
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'font' => ['bold' => true],
        ]);

        return [];
    }
}