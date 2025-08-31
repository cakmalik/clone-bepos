<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SellingPrice;

class SellingPriceSeeder extends Seeder
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
                'name' => 'Offline',
            ],
            [
                'name' => 'Harga A',
            ],
        ];

        foreach ($data as $key => $value) {
            SellingPrice::create($value);
        }
    }
}
