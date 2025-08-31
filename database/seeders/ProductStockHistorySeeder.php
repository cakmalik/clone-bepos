<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductStockHistory;

class ProductStockHistorySeeder extends Seeder
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
                'outlet_id' => 1,
                'user_id' => 1,
                'product_id' => 1,
                'stock_change' => 10,
                'stock_before' => 0,
                'stock_after' => 10,
                'desc' => 'Stock awal',
                'changes_type' => 'plus',
            ],
            [
                'outlet_id' => 1,
                'user_id' => 1,
                'product_id' => 2,
                'stock_change' => 10,
                'stock_before' => 0,
                'stock_after' => 10,
                'desc' => 'Stock awal',
                'changes_type' => 'plus',
            ],
            [
                'outlet_id' => 1,
                'user_id' => 1,
                'product_id' => 3,
                'stock_change' => 10,
                'stock_before' => 0,
                'stock_after' => 10,
                'desc' => 'Stock awal',
                'changes_type' => 'plus',
            ],
            [
                'outlet_id' => 1,
                'user_id' => 1,
                'product_id' => 1,
                'stock_change' => 10,
                'stock_before' => 0,
                'stock_after' => 10,
                'desc' => 'Stock awal',
                'changes_type' => 'plus',
            ],
            [
                'outlet_id' => 1,
                'user_id' => 1,
                'product_id' => 2,
                'stock_change' => 10,
                'stock_before' => 0,
                'stock_after' => 10,
                'desc' => 'Stock awal',
                'changes_type' => 'plus',
            ],
            [
                'outlet_id' => 1,
                'user_id' => 1,
                'product_id' => 3,
                'stock_change' => 10,
                'stock_before' => 0,
                'stock_after' => 10,
                'desc' => 'Stock awal',
                'changes_type' => 'plus',
            ],
        
        ];

        foreach ($data as $key => $value) {
            ProductStockHistory::create($value);
        }
        
    }
}
