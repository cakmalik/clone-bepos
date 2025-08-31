<?php

namespace Database\Seeders;

use App\Models\JournalType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JournalTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name' => 'BKK-BKM',],
            ['name' => 'JURNAL-UMUM',],
            ['name' => 'PEMBELIAN',],
            ['name' => 'PENYESUAIAN',],
            ['name' => 'PENJUALAN',],
            ['name' => 'PENUTUP',],
            ['name' => 'RETUR-BELI',],
            ['name' => 'RETUR-JUAL',],
            ['name' => 'SETORAN-BANK',],
        ];

        foreach ($data as $row) {
            JournalType::create($row);
        }
    }
}
