<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductStock;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportProductStock implements ToCollection, WithHeadingRow
{
    protected $outletId;

    public function __construct($outletId)
    {
        $this->outletId = $outletId;
    }

    public function collection(Collection $collection)
    {
        Log::info('run prodcut stock at:' . Carbon::now()->format('d m Y H:i'));
        $rowStart = 1;

        try {

            DB::beginTransaction();

            foreach ($collection as $index => $item) {
                if ($index >= $rowStart && $item['nama_barang'] != '' && $item['nama_barang'] != null) {
                    $product = Product::whereRaw('LOWER(name) = ?', [strtolower($item['nama_barang'])])->first();

                    if (!$product) {
                        Log::info('Product not found:' . $item['nama_barang']);
                    } else {
                        $productStock = ProductStock::where([
                            ['product_id', $product->id],
                            ['outlet_id', $this->outletId]
                        ])->first();
    
                        if ($productStock) {
                            $productStock->stock_current = $item['stok'];
                            $productStock->save();
                        } else {
                            ProductStock::create([
                                'outlet_id'     => $this->outletId,
                                'product_id'    => $product->id,
                                'stock_current' => $item['stok']
                            ]);
                        }
    
                    }
                }
            }

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollback();
            dd($th);
        }



    }
}
