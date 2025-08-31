<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Discount;

class DiscountSeeder extends Seeder
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
                "name" => "Diskon 10%",
                "type" => "percentage",
                "value" => 10,
                "status" => "active"
            ],
            [
                "name" => "Diskon 3rb",
                "type" => "nominal",
                "value" => 3000,
                "status" => "active"
            ]
        ];

        foreach ($data as $key => $value) {
            Discount::create($value);
        }
    }
}
