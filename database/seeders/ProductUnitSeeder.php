<?php
namespace Database\Seeders;

use App\Models\Outlet;
use App\Models\ProductUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        ProductUnit::create([
            'outlet_id'       => 1,
            'user_id'         => 1,
            'name'            => 'Pieces',
            'symbol'          => 'pcs',
            'conversion_rate' => 1,
            'base_unit_id'    => null,
            'desc'            => 'Satuan Hitungan',
        ]);

        $baseUnit = [
            'outlet_id'       => 1,
            'user_id'         => 1,
            'name'            => 'Kilogram',
            'symbol'          => 'kg',
            'conversion_rate' => 1,
            'base_unit_id'    => null,
            'desc'            => 'Satuan Berat',
        ];

        $baseUnitId = DB::table('product_units')->insertGetId($baseUnit);

        $units = [
            [
                'outlet_id'       => 1,
                'user_id'         => 1,
                'name'            => 'Gram',
                'symbol'          => 'g',
                'conversion_rate' => 1000,
                'base_unit_id'    => $baseUnitId,
                'desc'            => 'Satuan Berat',
            ],
            [
                'outlet_id'       => 1,
                'user_id'         => 1,
                'name'            => 'Ton',
                'symbol'          => 'ton',
                'conversion_rate' => 0.001,
                'base_unit_id'    => $baseUnitId,
                'desc'            => 'Satuan Berat',
            ],
            [
                //ons
                'outlet_id'       => 1,
                'user_id'         => 1,
                'name'            => 'Ons',
                'symbol'          => 'ons',
                'conversion_rate' => 10,
                'base_unit_id'    => $baseUnitId,
                'desc'            => 'Satuan Berat',
            ],
            [
                'outlet_id'       => 1,
                'user_id'         => 1,
                'name'            => 'Liter',
                'symbol'          => 'L',
                'conversion_rate' => 1,
                'base_unit_id'    => null,
                'desc'            => 'Satuan Volume',
            ],
            [
                'outlet_id'       => 1,
                'user_id'         => 1,
                'name'            => 'Mililiter',
                'symbol'          => 'mL',
                'conversion_rate' => 1000,
                'base_unit_id'    => null,
                'desc'            => 'Satuan Volume',
            ],

            [
                'outlet_id'       => 1,
                'user_id'         => 1,
                'name'            => 'Roll',
                'symbol'          => 'roll',
                'conversion_rate' => 1,
                'base_unit_id'    => null,
                'desc'            => 'Satuan Gulungan',
            ],
            [
                'outlet_id'       => 1,
                'user_id'         => 1,
                'name'            => 'Meter',
                'symbol'          => 'm',
                'conversion_rate' => 1,
                'base_unit_id'    => null,
                'desc'            => 'Satuan Panjang',
            ],
            [
                'outlet_id'       => 1,
                'user_id'         => 1,
                'name'            => 'Kotak',
                'symbol'          => 'box',
                'conversion_rate' => 1,
                'base_unit_id'    => null,
                'desc'            => 'Satuan Kemasan',
            ],
        ];

        DB::table('product_units')->insert($units);
    }
}
