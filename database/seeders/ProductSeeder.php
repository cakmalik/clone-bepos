<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductUnit;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $berasUnit = ProductUnit::whereSymbol('kg')->first();

        $data = [
            [
                'outlet_id' => 1,
                'barcode' => $faker->ean13(),
                'user_id' => 1,
                'product_category_id' => 1, //food
                'product_unit_id' => $berasUnit?->id,
                'code' => 'P0001',
                'name' => 'Beras 5kg',
                'slug' => 'beras-5kg',
                'desc' => 'Beras kualitas premium 5kg',
                'capital_price' => 50000,
                'minimum_stock' => 100,
                'type_product' => 'product',
                // 'is_multiple' => false, //pcs
            ],
            [
                'outlet_id' => 1,
                'barcode' => $faker->ean13(),
                'user_id' => 1,
                'product_category_id' => 1, //food
                'product_unit_id' => 1, //pcs
                'code' => 'P0002',
                'name' => 'Minyak Goreng 1L',
                'slug' => 'minyak-goreng-1l',
                'desc' => 'Minyak goreng 1 liter',
                'capital_price' => 15000,
                'minimum_stock' => 100,
                'type_product' => 'product',
                // 'is_multiple' => true, //pcs, lusin
            ],
            [
                'outlet_id' => 1,
                'barcode' => $faker->ean13(),
                'user_id' => 1,
                'product_category_id' => 2, //drink
                'product_unit_id' => 1, //pcs
                'code' => 'P0003',
                'name' => 'Pocari Sweat ISD 350ml',
                'slug' => 'pocari-sweat-isd-350ml',
                'desc' => 'Pocari Sweat ISD 350ml',
                'capital_price' => 5000,
                'minimum_stock' => 100,
                'type_product' => 'product',
                // 'is_multiple' => true, //pcs, lusin
            ],
            [
                'outlet_id' => 1,
                'barcode' => $faker->ean13(),
                'user_id' => 1,
                'product_category_id' => 2, //drink
                'product_unit_id' => 1, //pcs
                'code' => 'P0004',
                'name' => 'Pocari Sweat ISD 500ml',
                'slug' => 'pocari-sweat-isd-500ml',
                'desc' => 'Pocari Sweat ISD 500ml',
                'capital_price' => 7000,
                'minimum_stock' => 100,
                'type_product' => 'product',
                // 'is_multiple' => true, //pcs, lusin
            ],
            [
                'outlet_id' => 1,
                'barcode' => $faker->ean13(),
                'user_id' => 1,
                'product_category_id' => 2, //drink
                'product_unit_id' => 1, //pcs
                'code' => 'P0005',
                'name' => 'Buavita Mango 250ml',
                'slug' => 'buavita-mango-250ml',
                'desc' => 'Buavita Mango 250ml',
                'capital_price' => 5000,
                'minimum_stock' => 100,
                'type_product' => 'product',
                // 'is_multiple' => true, //pcs, lusin
            ],
            [
                'outlet_id' => 1,
                'barcode' => $faker->ean13(),
                'user_id' => 1,
                'product_category_id' => 14, //meat -> beef
                'product_unit_id' => 2, //kg
                'code' => 'P0006',
                'name' => 'Daging Sapi',
                'slug' => 'daging-sapi',
                'desc' => 'Daging sapi kualitas premium',
                'capital_price' => 80000,
                'minimum_stock' => 50,
                'type_product' => 'product',
                // 'is_multiple' => false, //kg
            ],
            [
                'outlet_id' => 1,
                'barcode' => $faker->ean13(),
                'user_id' => 1,
                'product_category_id' => 13, //meat -> chicken
                'product_unit_id' => 2, //kg
                'code' => 'P0007',
                'name' => 'Ayam Fillet',
                'slug' => 'ayam-fillet',
                'desc' => 'Ayam fillet',
                'capital_price' => 70000,
                'minimum_stock' => 50,
                'type_product' => 'product',
                // 'is_multiple' => false, //kg
            ],
            [
                'outlet_id' => 1,
                'barcode' => $faker->ean13(),
                'user_id' => 1,
                'product_category_id' => 11, //tobacco
                'product_unit_id' => 1, //pcs
                'code' => 'P0008',
                'name' => 'Sampoerna Mild',
                'slug' => 'sampoerna-mild',
                'desc' => 'Sampoerna Mild',
                'capital_price' => 30000,
                'minimum_stock' => 100,
                'type_product' => 'product',
                // 'is_multiple' => true, //pcs, slof, ball, karton
            ],
            [
                'outlet_id' => 1,
                'barcode' => $faker->ean13(),
                'user_id' => 1,
                'product_category_id' => 16, //personal care -> soap
                'product_unit_id' => 1, //pcs
                'code' => 'P0009',
                'name' => 'Sabun Mandi Lifebuoy 200ml',
                'slug' => 'sabun-mandi-lifebuoy-200ml',
                'desc' => 'Sabun mandi Lifebuoy 200ml',
                'capital_price' => 10000,
                'minimum_stock' => 100,
                'type_product' => 'product',
                // 'is_multiple' => false, //pcs
            ],
            [
                'outlet_id' => 1,
                'barcode' => $faker->ean13(),
                'user_id' => 1,
                'product_category_id' => 2, //drink
                'product_unit_id' => 1, //pcs
                'code' => 'P0010',
                'name' => 'Teh Botol Sosro 350ml',
                'slug' => 'teh-botol-sosro-350ml',
                'desc' => 'Teh Botol Sosro 350ml',
                'capital_price' => 6000,
                'minimum_stock' => 100,
                'type_product' => 'product',
                // 'is_multiple' => true, //pcs, lusin
            ],
            [
                'outlet_id' => 1,
                'barcode' => $faker->ean13(),
                'user_id' => 1,
                'product_category_id' => 17, // bundling
                'product_unit_id' => 1, //pcs
                'code' => 'P0011',
                'name' => 'Paket Lebaran 1',
                'slug' => 'paket-lebaran-1',
                'desc' => 'Paket Lebaran 1 (Penjualan Bundling)',
                'capital_price' => 85000,
                'minimum_stock' => 100,
                'type_product' => 'product',
                'is_bundle' => true,
                // 'is_multiple' => true, //pcs, lusin
            ],
        ];


        foreach ($data as $product) {
            try {
                Product::updateOrCreate(
                    ['code' => $product['code']],
                    $product
                );
            } catch (\Exception $e) {
                error_log("Error inserting product: " . $e->getMessage());
            }
        }
    }
}
