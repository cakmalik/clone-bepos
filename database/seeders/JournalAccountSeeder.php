<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JournalAccount;
use Illuminate\Support\Carbon;

class JournalAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        $data = [
            [
                'journal_account_type_id' => 1,
                'code'                   => '100',
                'name'                   => 'KAS BESAR',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '100.1',
                'name'                   => 'KAS DI KASIR DEPO GODEAN',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '100.2',
                'name'                   => 'KAS DI KASIR DEPO PURWOREJO',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '100.3',
                'name'                   => 'KAS DI KASIR DEPO BANTUL',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '100.4',
                'name'                   => 'KAS DI KASIR DEPO KULON PROGO',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '100.5',
                'name'                   => 'KAS DI KASIR DEPO WONOSARI',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '100.6',
                'name'                   => 'KAS DI KASIR DEPO KALASAN',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '101',
                'name'                   => 'KAS KECIL',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 4,
                'code'                   => '600',
                'name'                   => 'BY. GAJI',
                   'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 4,
                'code'                   => '601',
                'name'                   => 'BY. THR',
                   'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 4,
                'code'                   => '602',
                'name'                   => 'BY. INSENTIF KARYAWAN',
                   'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 4,
                'code'                   => '613',
                'name'                   => 'BY.PERJALANAN DINAS',
                   'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 4,
                'code'                   => '613',
                'name'                   => 'BY.AKOMODASI',
                   'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 8,
                'code'                   => '300',
                'name'                   => 'MODAL',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 9,
                'code'                   => '700',
                'name'                   => 'PENDAPATAN BUNGA BANK',
                   'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 9,
                'code'                   => '701',
                'name'                   => 'PENDAPATAN LAIN LAIN',
                   'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 7,
                'code'                   => '203',
                'name'                   => 'HUTANG TOP',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '103',
                'name'                   => 'BANK MANDIRI',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '104',
                'name'                   => 'BANK BCA',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '105',
                'name'                   => 'BANK SINARMAS',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 10,
                'code'                   => '400',
                'name'                   => 'PENJUALAN',
                   'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '110',
                'name'                   => 'PIUTANG DEALER',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 5,
                'code'                   => '410',
                'name'                   => 'HARGA POKOK PENJUALAN',
                   'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '130',
                'name'                   => 'STOCK PULSA FISIK',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '131',
                'name'                   => 'STOCK VOUCHER DATA ALL OPERATOR',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '132',
                'name'                   => 'STOCK ACCESORIES',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '133',
                'name'                   => 'STOCK HP/MODEM',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '134',
                'name'                   => 'STOCK PULSA TRONIK (E-LOAD)',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '135',
                'name'                   => 'STOCK E-LOAD',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '136',
                'name'                   => 'STOCK ELOAD DENOM',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '137',
                'name'                   => 'STOCK PERLENGKAPAN KANTOR',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
            [
                'journal_account_type_id' => 1,
                'code'                   => '122',
                'name'                   => 'ALOKASI PO',
                'created_at'             => $now,
                'updated_at'             => $now
            ],
        ];

        JournalAccount::insert($data);
    }
}
