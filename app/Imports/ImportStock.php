<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportStock implements ToCollection, WithHeadingRow
{
    protected $destinationId;
    protected $destinationType;

    public function __construct($destinationId, $destinationType)
    {
        $this->destinationId = $destinationId;
        $this->destinationType = $destinationType;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $product = Product::where('barcode', $row['barcode'])->first();

            if (!$product) {
                $product = Product::where('name', 'like', '%' . $row['nama_barang'] . '%')->first();
            }

            if (!$product) {
                continue;
            }

            $stockData = [
                'product_id' => $product->id,
                'stock_current' => $row['stok'],
            ];

            if ($this->destinationType === 'inventory') {

                $stockData['inventory_id'] = $this->destinationId;
                ProductStock::updateOrCreate(
                    ['inventory_id' => $this->destinationId, 'product_id' => $product->id],
                    ['stock_current' => $row['stok']]
                );

            } elseif ($this->destinationType === 'outlet') {
                
                $stockData['outlet_id'] = $this->destinationId;
                ProductStock::updateOrCreate(
                    ['outlet_id' => $this->destinationId, 'product_id' => $product->id],
                    ['stock_current' => $row['stok']]
                );
            }

        }
    }
}
