<?php

namespace Database\Seeders;

use App\Models\ProductBundle;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductBundleSeeder extends Seeder
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
                'product_bundle_id' => 11,
                'product_id' => 1,
                'qty' => 2,
            ],
            [
                'product_bundle_id' => 11,
                'product_id' => 2,
                'qty' => 4,
            ]
        ];

        foreach ($data as $productBundle) {
            ProductBundle::updateOrCreate(
                [
                    'product_bundle_id' => $productBundle['product_bundle_id'],
                    'product_id' => $productBundle['product_id'],
                ],
                [
                    'qty' => $productBundle['qty'],
                ]
            );
        }
    }
}
