<?php

namespace App\Imports;

use App\Models\CustomerCategory;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\TieredPrices;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportTieredPrice implements ToCollection, WithHeadingRow
{
    // BARCODE
    // NAMA BARANG
    // BRAND
    // KATEGORI
    // SUB KATEGORI
    // STOK
    // HPP
    // HARGA JUAL
    // MULAI DARI 1
    // SAMPAI DENGAN 1
    // TINGKAT 1
    // MULAI DARI 2
    // SAMPAI DENGAN 2
    // TINGKAT 2
    // MULAI DARI 3
    // SAMPAI DENGAN 3
    // TINGKAT 3
    // MULAI DARI 4
    // SAMPAI DENGAN 4
    // TINGKAT 4

    public function collection(Collection $collection): void
    {
        Log::info('run tiered price at:' . Carbon::now()->format('d m Y H:i'));
        $rowStart = 1;

        try {

            DB::beginTransaction();

            foreach ($collection as $index => $item) {
                if ($index >= $rowStart && $item['nama_barang'] != '' && $item['nama_barang'] != null) {
                    $data = [
                        'barcode' => $item['barcode'],
                        'name' => $item['nama_barang'],
                        'qty' => $item['stok'],
                        'hpp' => $item['hpp'],
                        'price' => $item['harga_jual'],

                        'min_first_price' => $item['mulai_dari_1'],
                        'max_first_price' => $item['sampai_dengan_1'],
                        'first_price' => $item['tingkat_1'],

                        'min_second_price' => $item['mulai_dari_2'],
                        'max_second_price' => $item['sampai_dengan_2'],
                        'second_price' => $item['tingkat_2'],

                        'min_third_price' => $item['mulai_dari_3'],
                        'max_third_price' => $item['sampai_dengan_3'],
                        'third_price' => $item['tingkat_3'],

                        'min_four_price' => $item['mulai_dari_4'],
                        'max_four_price' => $item['sampai_dengan_4'],
                        'four_price' => $item['tingkat_4'],
                    ];

                    $product = Product::where([
                        ['barcode', $data['barcode']],
                        ['name', $data['name']],
                    ])->first();

                    if ($product) {
                        if (
                            $data['min_first_price'] != '' && $data['min_first_price'] != null
                            && $data['min_first_price'] != '-' && $data['first_price'] != null
                        ) {
                            if ($data['max_first_price'] == '<' || $data['max_first_price'] == '>') {
                                $data['max_first_price'] = 10000;
                            }

                            TieredPrices::create([
                                'product_id' => $product->id,
                                'min_qty' => $data['min_first_price'],
                                'max_qty' => $data['max_first_price'] ?? 10000,
                                'price' => $data['first_price'],
                            ]);
                        }

                        if (
                            $data['min_second_price'] != '' && $data['max_second_price'] != null
                            && $data['min_second_price'] != '-' && $data['second_price'] != null
                        ) {
                            if ($data['max_second_price'] == '<' || $data['max_second_price'] == '>') {
                                $data['max_second_price'] = 10000;
                            }

                            TieredPrices::create([
                                'product_id' => $product->id,
                                'min_qty' => $data['min_second_price'],
                                'max_qty' => $data['max_second_price'] ?? 10000,
                                'price' => $data['second_price'],
                            ]);
                        }

                        if (
                            $data['min_third_price'] != '' && $data['max_third_price'] != null
                            && $data['min_third_price'] != '-' && $data['third_price'] != null
                        ) {
                            if ($data['max_third_price'] == '<' || $data['max_third_price'] == '>') {
                                $data['max_third_price'] = 10000;
                            }

                            TieredPrices::create([
                                'product_id' => $product->id,
                                'min_qty' => $data['min_third_price'],
                                'max_qty' => $data['max_third_price'] ?? 10000,
                                'price' => $data['third_price'],
                            ]);
                        }

                        if (
                            $data['min_four_price'] != '' && $data['max_four_price'] != null
                            && $data['min_four_price'] != '-' && $data['four_price'] != null
                        ) {
                            if ($data['max_four_price'] == '<' || $data['max_four_price'] == '>') {
                                $data['max_four_price'] = 10000;
                            }

                            TieredPrices::create([
                                'product_id' => $product->id,
                                'min_qty' => $data['min_four_price'],
                                'max_qty' => $data['max_four_price'] ?? 10000,
                                'price' => $data['four_price'],
                            ]);
                        }


                        // jika harga kategori pelanggan di set
                        if (
                            isset($item['kategori_pelanggan']) && isset($item['harga_kategori'])
                        ) {
                            $categoryName = $item['kategori_pelanggan'];
                            $price = $item['harga_kategori'];
                            $this->submitCustomerCategory($categoryName, $price, $product->id);
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


    private function submitCustomerCategory($categoryName, $price, $productId)
    {
        $category = CustomerCategory::firstOrCreate(
            ['name' => $categoryName],
            [
                'slug' => Str::slug($categoryName),
                'code' => 'Cus-' . Str::random(5),
            ]
        );

        $productPrice = new ProductPrice();
        $productPrice->product_id = $productId;
        $productPrice->customer_category_id = $category->id;
        $productPrice->type = 'lain';
        $productPrice->price = $price;
        $productPrice->selling_price_id = 1;
        $productPrice->save();

        return $productPrice;
    }
}
