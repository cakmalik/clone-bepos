<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
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
                'user_id' => 1,
                'code' => 'SUP0001',
                'name' => 'PT ANAK BANGSA BERKARYA',
                'slug' => 'supplier-1',
                'date' => '2021-12-13',
                'supplier_image' => 'supplier-1.jpg',
                'phone' => '08123456789',
                'address' => 'Jl. Supplier 1',
                'desc' => 'Supplier 1',
            ],
            [
                'user_id' => 1,
                'code' => 'SUP0002',
                'name' => 'CV ENGGAL JAYA ABADI',
                'slug' => 'supplier-2',
                'date' => '2021-12-13',
                'supplier_image' => 'supplier-2.jpg',
                'phone' => '08123456789',
                'address' => 'Jl. Supplier 2',
                'desc' => 'Supplier 2',
            ],
            [
                'user_id' => 1,
                'code' => 'SUP0003',
                'name' => 'PT PERINDO JAYA ABADI',
                'slug' => 'supplier-3',
                'date' => '2021-12-13',
                'supplier_image' => 'supplier-3.jpg',
                'phone' => '08123456789',
                'address' => 'Jl. Supplier 3',
                'desc' => 'Supplier 3',
            ],
            [
                'user_id' => 1,
                'code' => 'SUP0004',
                'name' => 'PT SUBAH KARYA ABADI',
                'slug' => 'supplier-4',
                'date' => '2021-12-13',
                'supplier_image' => 'supplier-4.jpg',
                'phone' => '08123456789',
                'address' => 'Jl. Supplier 4',
                'desc' => 'Supplier 4',
            ],
        ];

        foreach ($data as $key => $value) {
            \App\Models\Supplier::create($value);
        }
    }
}
