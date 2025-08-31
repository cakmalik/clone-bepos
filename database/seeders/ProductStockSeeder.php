<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductStock;

class ProductStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'product_id' => 1,
                'outlet_id' => 1,
                'stock_current' => 100,
            ],
            [
                'product_id' => 2,
                'outlet_id' => 1,
                'stock_current' => 100,
            ],
            [
                'product_id' => 3,
                'outlet_id' => 1,
                'stock_current' => 100,
            ],
            [
                'product_id' => 4,
                'outlet_id' => 1,
                'stock_current' => 100,
            ],
            [
                'product_id' => 5,
                'outlet_id' => 1,
                'stock_current' => 100,
            ],

            // Material

            [
                'product_id' => 6,
                'inventory_id' => 1,
                'stock_current' => 100,
            ],
        ];

        foreach ($data as $key => $value) {
            ProductStock::create($value);
        }
    }
}
