<?php

namespace Database\Seeders;

use App\Models\UserInventory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserInventory::create([
            'user_id'       => 1,
            'inventory_id'  => 1,
        ]);

        UserInventory::create([
            'user_id'       => 2,
            'inventory_id'  => 1,
        ]);

        if (env('MULTI_OUTLET_SETUP')) {
            UserInventory::create([
                'user_id'       => 5,
                'inventory_id'  => 2,
            ]);
        }

    }
}
