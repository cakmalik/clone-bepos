<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Inventory::create([
            'code' => autoCode('inventories', 'code', 'INV-', 2),
            'name' => 'GUDANG PUSAT',
            'type' => 'gudang',
            'is_parent' => true,
            'is_active' => true,
        ]);

        if (env('MULTI_OUTLET_SETUP')) {
            Inventory::create([
                'code' => autoCode('inventories', 'code', 'INV-', 2),
                'name' => 'GUDANG OUTLET DUA',
                'type' => 'gudang',
                'is_parent' => true,
                'is_active' => true,
            ]);
        }
    }
}
