<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Outlet;

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Outlet::create(
            [
                'is_main' => true,
                'code' => autoCode('outlets', 'code', 'OUT-', 2),
                'name' => 'Kashir Mart',
                'slug' => 'kashir-mart',
                'address' => 'Jl. Sudirman, Dadapan, Ngadirejo, Kec. Salaman, Kabupaten Magelang, Jawa Tengah 56162',
                'phone' => '081334363696',
                'desc' => 'Kashir Mart',
                'outlet_parent_id' => 0,
            ],
        );

        if (env('MULTI_OUTLET_SETUP')) {
            Outlet::create(
                [
                    'is_main' => false,
                    'code' => autoCode('outlets', 'code', 'OUT-', 2),
                    'name' => 'Kashir Mart Joglo',
                    'slug' => 'kashir-mart-outlet-dua',
                    'address' => 'Jl. Jogja-Solo, Dadapan, Ngadirejo, Kec. Salaman, Kabupaten Magelang, Jawa Tengah 56162',
                    'phone' => '081334363697',
                    'desc' => 'Kashir Mart Outlet 2',
                    'outlet_parent_id' => 1,
                ],
            );
        }
    }
}
