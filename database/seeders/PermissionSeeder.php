<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    
        $menu = Menu::all();

        foreach ($menu as $value) {
            Permission::create([
                'outlet_id' => 1,
                'role_id'   => 1,
                'menu_id'   => $value->id
            ]);

            Permission::create([
                'outlet_id' => 1,
                'role_id'   => 2,
                'menu_id'   => $value->id
            ]);

            if (env('MULTI_OUTLET_SETUP')) {
                Permission::create([
                    'outlet_id' => 2,
                    'role_id'   => 2,
                    'menu_id'   => $value->id
                ]);
            }
        }

    }
}
