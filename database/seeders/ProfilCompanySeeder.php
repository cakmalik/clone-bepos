<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProfilCompany;

class ProfilCompanySeeder extends Seeder
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
                'image' => 'betech.png',
                'name' => 'PT.BALEDONO INFORMASI TEKNOLOGI',
                'email' => 'betech.id@gmail.com',
                'address' => 'Jl. Gondang Raya No.12 B, Kentungan, Condongcatur, Kec. Depok, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55281',
                'about' => 'BE-TECH merupakan software house yang berkantor di Jogja, berpengalaman dalam mengembangkan sistem untuk berbagai macam platform.',
                'telp' => '08818080001',
                'status' => 'active',
            ]
        ];

        foreach ($data as $key => $value) {
            ProfilCompany::create($value);
        }
    }
}
