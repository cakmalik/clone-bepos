<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
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
                'name' => 'CASH',
                'is_active' => true,
                'image' => 'cash.png'
            ],
            [
                'name' => 'EDC',
                'is_active' => true,
                'image' => 'edc.png'
            ],
            [
                'name' => 'QRIS',
                'is_active' => true,
                'image' => 'qris.png'
            ],
            [
                'name' => 'TEMPO',
                'is_active' => true,
                'image' => 'tempo.svg'
            ]
        ];

        foreach ($data as $item) {
            PaymentMethod::create($item);
        }
    }
}
