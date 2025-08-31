<?php

namespace Database\Seeders;

use App\Models\TieredPrices;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TIeredPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TieredPrices::insert(
            [
                [
                    'product_id' => 1,
                    'min_qty' => 5,
                    'max_qty' => 10,
                    'price' => 75000
                ],
                [
                    'product_id' => 1,
                    'min_qty' => 11,
                    'max_qty' => 20,
                    'price' => 70000
                ],
            ]
        );

        if (env('MULTI_OUTLET_SETUP')) {
            TieredPrices::insert(
                [
                    [
                        'product_id' => 1,
                        'outlet_id' => 1,
                        'min_qty' => 1,
                        'max_qty' => 10,
                        'price' => 78000
                    ],
                    [
                        'product_id' => 1,
                        'outlet_id' => 1,
                        'min_qty' => 11,
                        'max_qty' => 20,
                        'price' => 75000
                    ],
                ]
            );
        }
    }
}
