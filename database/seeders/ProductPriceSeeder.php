<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductPrice;

class ProductPriceSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Beras 5Kg
            [
                'product_id' => 1, // Beras 5Kg
                // 'product_unit_id' => 1, // Pcs
                'selling_price_id' => 1,
                'price' => 80000,
                'type' => 'utama',
                // 'qty_conversion' => 1,
            ],
            [
                'product_id' => 1, // Beras 5Kg
                // 'product_unit_id' => 5, //Lusin
                'selling_price_id' => 2,
                'price' => 90000,
                'type' => 'lain',
                // 'qty_conversion' => 12,   
            ],

            // Minyak Goreng 1L
            [
                'product_id' => 2, // Minyak Goreng 1L
                // 'product_unit_id' => 1, // Pcs
                'selling_price_id' => 1,
                'price' => 25000,
                'type' => 'utama',
                // 'qty_conversion' => 1,
            ],
            // [
            //     'product_id' => 2, // Minyak Goreng 1L
            //     'product_unit_id' => 5, //Lusin
            //     'selling_price_id' => 1,
            //     'price' => 270000,
            //     'type' => 'lain',
            //     'qty_conversion' => 12,
            // ],


            // Pocari Sweat 350ml
            [
                'product_id' => 3, // Pocari Sweat 350ml
                // 'product_unit_id' => 1, // Pcs
                'selling_price_id' => 1,
                'price' => 8000,
                'type' => 'utama',
                // 'qty_conversion' => 1,
            ],
            // [
            //     'product_id' => 3, // Pocari Sweat 350ml
            //     'product_unit_id' => 5, //Lusin
            //     'selling_price_id' => 1,
            //     'price' => 90000,
            //     'type' => 'lain',
            //     'qty_conversion' => 12,
            // ],

            //Pocari Sweat 500ml
            [
                'product_id' => 4, // Pocari 500ml
                // 'product_unit_id' => 1, // Pcs
                'selling_price_id' => 1,
                'price' => 12000,
                'type' => 'utama',
                // 'qty_conversion' => 1,
            ],
            // [
            //     'product_id' => 4, // Pocari 500ml
            //     'product_unit_id' => 5, //Lusin
            //     'selling_price_id' => 1,
            //     'price' => 120000,
            //     'type' => 'lain',
            //     'qty_conversion' => 12,
            // ],

            // Buavita Mango 250ml
            [
                'product_id' => 5, // Buavita Mango 250ml
                // 'product_unit_id' => 1, // Pcs
                'selling_price_id' => 1,
                'price' => 8000,
                'type' => 'utama',
                // 'qty_conversion' => 1,
            ],
            // [
            //     'product_id' => 5, // Buavita Mango 250ml
            //     'product_unit_id' => 5, //Lusin
            //     'selling_price_id' => 1,
            //     'price' => 90000,
            //     'type' => 'lain',
            //     'qty_conversion' => 12,
            // ],

            // Daging Sapi
            [
                'product_id' => 6, // Daging Sapi
                // 'product_unit_id' => 2, // Kg
                'selling_price_id' => 1,
                'price' => 100000,
                'type' => 'utama',
                // 'qty_conversion' => 1,
            ],

            //Ayam Fillet
            [
                'product_id' => 7, // Ayam Fillet
                // 'product_unit_id' => 2, // Kg
                'selling_price_id' => 1,
                'price' => 90000,
                'type' => 'utama',
                // 'qty_conversion' => 1,
            ],

            //Sampoerna Mild
            [
                'product_id' => 8, // Sampoerna Mild
                // 'product_unit_id' => 1, // Pcs
                'selling_price_id' => 1,
                'price' => 45000,
                'type' => 'utama',
                // 'qty_conversion' => 1,
            ],
            // [
            //     'product_id' => 8, // Sampoerna Mild
            //     'product_unit_id' => 15, //Slof
            //     'selling_price_id' => 1,
            //     'price' => 400000,
            //     'type' => 'lain',
            //     'qty_conversion' => 10,
            // ],
            // [
            //     'product_id' => 8, // Sampoerna Mild
            //     'product_unit_id' => 16, //Ball
            //     'selling_price_id' => 1,
            //     'price' => 2000000,
            //     'type' => 'lain',
            //     'qty_conversion' => 50,
            // ],
            // [
            //     'product_id' => 8, // Sampoerna Mild
            //     'product_unit_id' => 17, // Karton
            //     'selling_price_id' => 1,
            //     'price' => 20000000,
            //     'type' => 'lain',
            //     'qty_conversion' => 500,
            // ],

            //Sabun Mandi Lifebuoy 200ml
            [
                'product_id' => 9, // Sabun Mandi Lifebuoy 200ml
                // 'product_unit_id' => 1, // Pcs
                'selling_price_id' => 1,
                'price' => 12000,
                'type' => 'utama',
                // 'qty_conversion' => 1,
            ],

            //Teh Botol Sosro 350ml
            [
                'product_id' => 10, // Teh Botol Sosro 350ml
                // 'product_unit_id' => 1, // Pcs
                'selling_price_id' => 1,
                'price' => 8000,
                'type' => 'utama',
                // 'qty_conversion' => 1,
            ],
            // [
            //     'product_id' => 10, // Teh Botol Sosro 350ml
            //     'product_unit_id' => 5, //Lusin
            //     'selling_price_id' => 1,
            //     'price' => 90000,
            //     'type' => 'lain',
            //     'qty_conversion' => 12
            // ],

            //Paket Lebaran 1
            [
                'product_id' => 11, // Paket Lebaran
                // 'product_unit_id' => 1, // Pcs
                'selling_price_id' => 1,
                'price' => 145000,
                'type' => 'utama',
                // 'qty_conversion' => 1
            ]
        ];

        foreach ($data as $price) {
            ProductPrice::updateOrCreate(
                [
                    'product_id' => $price['product_id'],
                    // 'product_unit_id' => $price['product_unit_id'],
                    'selling_price_id' => $price['selling_price_id'],
                    'type' => $price['type'],
                ],
                [
                    'price' => $price['price'],
                    // 'qty_conversion' => $price['qty_conversion'],
                ]
            );
        }
    }
}
