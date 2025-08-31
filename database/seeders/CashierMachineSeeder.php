<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CashierMachine;

class CashierMachineSeeder extends Seeder
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
                'name' => 'Kasir 1',
                'outlet_id' => 1,
                'code' => 'KASIR-1'
            ],
        ];

        foreach ($data as $item) {
            CashierMachine::create($item);
        }
    }
}
