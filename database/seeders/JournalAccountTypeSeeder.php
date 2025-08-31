<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JournalAccountType;

class JournalAccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JournalAccountType::create([
            'name' => 'AKTIVA LANCAR',
            'transaction_type' => 'debit',
            'position' => 'neraca',
        ]);
        JournalAccountType::create([
            'name' => 'AKTIVA TETAP',
            'transaction_type' => 'debit',
            'position' => 'neraca',
        ]);
        JournalAccountType::create([
            'name' => 'BIAYA NON OPERASIONAL',
            'transaction_type' => 'debit',
            'position' => 'laba rugi',
        ]);
        JournalAccountType::create([
            'name' => 'BIAYA OPERASIONAL',
            'transaction_type' => 'debit',
            'position' => 'laba rugi',
        ]);
        JournalAccountType::create([
            'name' => 'HARGA POKOK PENJUALAN',
            'transaction_type' => 'debit',
            'position' => 'laba rugi',
        ]);
        JournalAccountType::create([
            'name' => 'KENDARAAN/MOBIL',
            'transaction_type' => 'debit',
            'position' => 'neraca',
        ]);
        JournalAccountType::create([
            'name' => 'HUTANG LANCAR',
            'transaction_type' => 'kredit',
            'position' => 'neraca',
        ]);
        JournalAccountType::create([
            'name' => 'MODAL',
            'transaction_type' => 'kredit',
            'position' => 'neraca',
        ]);
        JournalAccountType::create([
            'name' => 'PENDAPATAN NON OPERASIONAL',
            'transaction_type' => 'kredit',
            'position' => 'laba rugi',
        ]);
        JournalAccountType::create([
            'name' => 'PENJUALAN',
            'transaction_type' => 'kredit',
            'position' => 'laba rugi',
        ]);
    }
}
