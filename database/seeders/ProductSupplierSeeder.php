<?php

namespace Database\Seeders;

use App\Models\ProductSupplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSupplierSeeder extends Seeder
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
                'supplier_id' => 1,
            ],
            [
                'product_id' => 2,
                'supplier_id' => 2,
            ],
            [
                'product_id' => 3,
                'supplier_id' => 3,
            ],
            [
                'product_id' => 4,
                'supplier_id' => 4,
            ],
            [
                'product_id' => 5,
                'supplier_id' => 2,
            ],
            [
                'product_id' => 6,
                'supplier_id' => 1,
            ],
            [
                'product_id' => 7,
                'supplier_id' => 3,
            ],

        ];

        foreach ($data as $key => $value) {
            try {

                ProductSupplier::create($value);
            } catch (\Exception $e) {
                error_log($e->getMessage());
            }
        }
    }
}
