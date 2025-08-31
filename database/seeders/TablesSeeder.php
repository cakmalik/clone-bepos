<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TablesSeeder extends Seeder
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
                "outlet_id" => 1,
                "name" => "Meja 1",
                "status" => "available"
            ],
            [
                "outlet_id" => 1,
                "name" => "Meja 2",
                "status" => "available"
            ],
            [
                "outlet_id" => 1,
                "name" => "Meja 3",
                "status" => "available"
            ],
            [
                "outlet_id" => 1,
                "name" => "Meja 4",
                "status" => "available"
            ]
        ];

        foreach ($data as $index => $row) {
            \App\Models\Table::create($row);
        }
    }
}
