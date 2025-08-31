<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Str;
use App\Models\ProductPrice;
use App\Models\ProductStock;
use App\Models\ProductCategory;
use App\Models\ProductSupplier;
use App\Models\ProductUnit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ImportProduct implements ToCollection, SkipsEmptyRows
{
    use Importable;

    protected $outletId;

    public function __construct($outletId)
    {
        $this->outletId = 1;
    }

    public function collection(Collection $collection)
    {
        Log::info('asdfa');
        $rowStart = 1;

        try {

            DB::beginTransaction();

            foreach ($collection as $index => $item) {
                if ($index >= $rowStart && $item[1] != '' && $item[1] != null) {
                    $data = [
                        'code'          => $item[0],
                        'name'          => $item[1],
                        'barcode'       => $item[0],
                        'supplier'      => $item[2],
                        'unit'          => $item[3],
                        'category'      => $item[4],
                        'subcategory'   => $item[5],
                        // 'stock'         => $item[6] ?? 0,
                        'hpp'           => $item[6] ?? 0,
                        'price'         => $item[7] ?? 0
                    ];



                    $categoryId = 1;

                    if ($data['category'] != '') {
                        $category = ProductCategory::where('name', $data['category'])->first();
                        if (!$category) {
                            $category = ProductCategory::create([
                                'outlet_id'         => 1,
                                'is_parent_category' => true,
                                'code'              => abbreviation($data['category']),
                                'name'              => $data['category'],
                                'slug'              => Str::slug($data['category']),
                                'desc'              => '-'
                            ]);
                        }

                        $categoryId = $category->id;

                        if ($data['category'] != $data['subcategory'] && $data['subcategory'] != '') {
                            $subcategory = ProductCategory::where('name', $data['subcategory'])->first();

                            if (!$subcategory) {
                                $parent = ProductCategory::where('name', $data['category'])->first();

                                $subcategory = ProductCategory::create([
                                    'outlet_id'         => 1,
                                    'parent_id'         => $parent->id,
                                    'is_parent_category' => false,
                                    'code'              => abbreviation($data['subcategory']),
                                    'name'              => $data['subcategory'],
                                    'slug'              => Str::slug($data['subcategory']),
                                    'desc'              => '-'
                                ]);
                            }

                            $categoryId = $subcategory->id;
                        }
                    }

                    $productUnit = null;
                    if ($data['unit'] != '') {
                        $productUnit = ProductUnit::whereRaw('LOWER(name) = ?', [strtolower($data['unit'])])->first();
                        if (!$productUnit) {
                            $productUnit = ProductUnit::create([
                                'symbol'            => strtolower($data['unit']),
                                'conversion_rate'   => 1,
                                'outlet_id'         => $this->outletId,
                                'user_id'           => 1,
                                'name'              => $data['unit'],
                                'desc'              => $data['unit']
                            ]);
                        }
                    }


                    $product = Product::query()
                        // ->where('barcode', $data['barcode'])
                        ->where('name', $data['name'])->first();

                    if (!$product) {
                        $product = Product::create([
                            'barcode'               => $data['barcode'],
                            'name'                  => $data['name'],
                            'code'                  => autoCode('products', 'code', 'P', 5),
                            'product_category_id'   => $categoryId,
                            'type_product'          => 'product',
                            'minimum_stock'         => 0,
                            'outlet_id'             => $this->outletId,
                            'user_id'               => 1,
                            'product_unit_id'       => $productUnit ? $productUnit?->id : 1,
                            'capital_price'         => $data['hpp'],
                        ]);

                        ProductPrice::create([
                            'product_id'        => $product->id,
                            'selling_price_id'  => 1,
                            'price'             => $data['price'],
                            'type'              => 'utama'
                        ]);

                        if ($data['supplier'] != '') {
                            $supplier = Supplier::where('name', $data['supplier'])->first();
                            if (!$supplier) {
                                $supplier = Supplier::create([
                                    'code'  => autoCode('suppliers', 'code', 'SP', 8),
                                    'name'  => $data['supplier'],
                                    'slug'  => $data['supplier'],
                                    'date'  => date('Y-m-d H:i:s')
                                ]);
                            }

                            ProductSupplier::create([
                                'product_id'    => $product->id,
                                'supplier_id'   => $supplier->id
                            ]);
                        }
                    }

                    // $productStock = ProductStock::where([
                    //     ['outlet_id', $this->outletId],
                    //     ['product_id', $product->id]
                    // ])->first();

                    // if (!$productStock) {
                    //     ProductStock::create([
                    //         'outlet_id'     => $this->outletId,
                    //         'product_id'    => $product->id,
                    //         'stock_current' => $data['stock']
                    //     ]);
                    // }
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            dd($th->getMessage() . ' - ' . $th->getLine());
        }
    }
}
