<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\Bank;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $banks = [
            ['name' => 'Bank Mandiri', 'code' => 234234],
            ['name' => 'Bank Rakyat Indonesia', 'code' => 234234],
            ['name' => 'BNI (Bank Negara Indonesia)', 'code' => 234234],
            ['name' => 'Bank Central Asia', 'code' => 234234],
            ['name' => 'BSI (Bank Syariah Indonesia)', 'code' => 234234],
            ['name' => 'CIMB Niaga & CIMB Niaga Syariah', 'code' => 234234],
            ['name' => 'Muamalat', 'code' => 234234],
            ['name' => 'Bank Danamon & Danamon Syariah', 'code' => 234234],
            ['name' => 'Bank Permata & Permata Syariah', 'code' => 234234],
            ['name' => 'Maybank Indonesia', 'code' => 234234],
            ['name' => 'Panin Bank', 'code' => 234234],
            ['name' => 'TMRW/UOB', 'code' => 234234],
            ['name' => 'OCBC NISP', 'code' => 234234],
            ['name' => 'Citibank', 'code' => 234234],
            ['name' => 'Bank Artha Graha Internasional', 'code' => 234234],
            ['name' => 'Bank of Tokyo Mitsubishi UFJ', 'code' => 234234],
            ['name' => 'DBS Indonesia', 'code' => 234234],
            ['name' => 'Standard Chartered Bank', 'code' => 234234],
            ['name' => 'Bank Capital Indonesia', 'code' => 234234],
            ['name' => 'ANZ Indonesia', 'code' => 234234],
            ['name' => 'Bank of China (Hong Kong) Limited', 'code' => 234234],
            ['name' => 'Bank Bumi Arta', 'code' => 234234],
            ['name' => 'HSBC Indonesia', 'code' => 234234],
            ['name' => 'Rabobank International Indonesia', 'code' => 234234],
            ['name' => 'Bank Mayapada', 'code' => 234234],
            ['name' => 'BJB', 'code' => 234234],
            ['name' => 'Bank DKI Jakarta', 'code' => 234234],
            ['name' => 'BPD DIY', 'code' => 234234],
            ['name' => 'Bank Jateng', 'code' => 234234],
            ['name' => 'Bank Jatim', 'code' => 234234],
            ['name' => 'Bank Jambi', 'code' => 234234],
            ['name' => 'Bank Sumut', 'code' => 234234],
            ['name' => 'Bank Sumbar (Bank Nagari)', 'code' => 234234],
            ['name' => 'Bank Riau Kepri', 'code' => 234234],
            ['name' => 'Bank Sumsel Babel', 'code' => 234234],
            ['name' => 'Bank Lampung', 'code' => 234234],
            ['name' => 'Bank Kalsel', 'code' => 234234],
            ['name' => 'Bank Kalbar', 'code' => 234234],
            ['name' => 'Bank Kaltim', 'code' => 234234],
            ['name' => 'Bank Kalteng', 'code' => 234234],
            ['name' => 'Bank Sulselbar', 'code' => 234234],
            ['name' => 'Bank SulutGo', 'code' => 234234],
            ['name' => 'Bank NTB Syariah', 'code' => 234234],
            ['name' => 'BPD Bali', 'code' => 234234],
            ['name' => 'Bank NTT', 'code' => 234234],
            ['name' => 'Bank Maluku', 'code' => 234234],
            ['name' => 'Bank Papua', 'code' => 234234],
            ['name' => 'Bank Bengkulu', 'code' => 234234],
            ['name' => 'Bank Sulteng', 'code' => 234234],
            ['name' => 'Bank Sultra', 'code' => 234234],
            ['name' => 'Bank Nusantara Parahyangan', 'code' => 234234],
            ['name' => 'Bank of India Indonesia', 'code' => 234234],
            ['name' => 'Bank Mestika Dharma', 'code' => 234234],
            ['name' => 'Bank Sinarmas', 'code' => 234234],
            ['name' => 'Bank Maspion Indonesia', 'code' => 234234],
            ['name' => 'Bank Ganesha', 'code' => 234234],
            ['name' => 'ICBC Indonesia', 'code' => 234234],
            ['name' => 'QNB Indonesia', 'code' => 234234],
            ['name' => 'BTN/BTN Syariah', 'code' => 234234],
            ['name' => 'Bank Woori Saudara', 'code' => 234234],
            ['name' => 'BTPN', 'code' => 234234],
            ['name' => 'Bank BTPN Syariah', 'code' => 234234],
            ['name' => 'BJB Syariah', 'code' => 234234],
            ['name' => 'Bank Mega', 'code' => 234234],
            ['name' => 'Wokee/Bukopin', 'code' => 234234],
            ['name' => 'Bank Bukopin Syariah', 'code' => 234234],
            ['name' => 'Bank Jasa Jakarta', 'code' => 234234],
            ['name' => 'LINE Bank/KEB Hana', 'code' => 234234],
            ['name' => 'Motion/MNC Bank', 'code' => 234234],
            ['name' => 'BRI Agroniaga', 'code' => 234234],
            ['name' => 'SBI Indonesia', 'code' => 234234],
            ['name' => 'Blu/BCA Digital', 'code' => 234234],
            ['name' => 'Nobu (Nationalnobu) Bank', 'code' => 234234],
            ['name' => 'Bank Mega Syariah', 'code' => 234234],
            ['name' => 'Bank Ina Perdana', 'code' => 234234],
            ['name' => 'Bank Sahabat Sampoerna', 'code' => 234234],
            ['name' => 'Seabank/Bank BKE', 'code' => 234234],
            ['name' => 'BCA (Bank Central Asia) Syariah', 'code' => 234234],
            ['name' => 'Jago/Artos', 'code' => 234234],
            ['name' => 'Bank Mayora Indonesia', 'code' => 234234],
            ['name' => 'Bank Index Selindo', 'code' => 234234],
            ['name' => 'Bank Victoria International', 'code' => 234234],
            ['name' => 'Bank IBK Indonesia', 'code' => 234234],
            ['name' => 'CTBC (Chinatrust) Indonesia', 'code' => 234234],
            ['name' => 'Commonwealth Bank', 'code' => 234234],
            ['name' => 'Bank Victoria Syariah', 'code' => 234234],
            ['name' => 'BPD Banten', 'code' => 234234],
            ['name' => 'Bank Mutiara', 'code' => 234234],
            ['name' => 'Panin Dubai Syariah', 'code' => 234234],
            ['name' => 'Bank Aceh Syariah', 'code' => 234234],
            ['name' => 'Bank Antardaerah', 'code' => 234234],
            ['name' => 'Bank China Construction Bank Indonesia', 'code' => 234234],
            ['name' => 'Bank CNB (Centratama Nasional Bank)', 'code' => 234234],
            ['name' => 'Bank Dinar Indonesia', 'code' => 234234],
            ['name' => 'BPR EKA (Bank Eka)', 'code' => 234234],
            ['name' => 'Allo Bank/Bank Harda Internasional', 'code' => 234234],
            ['name' => 'BANK MANTAP (Mandiri Taspen)', 'code' => 234234],
            ['name' => 'Bank Multi Arta Sentosa (Bank MAS)', 'code' => 234234],
            ['name' => 'Bank Prima Master', 'code' => 234234],
            ['name' => 'Bank Shinhan Indonesia', 'code' => 234234],
            ['name' => 'Neo Commerce/Yudha Bhakti', 'code' => 234234],
            ['name' => 'GoPay', 'code' => 234234],
            ['name' => 'OVO', 'code' => 234234],
            ['name' => 'ShopeePay', 'code' => 234234],
            ['name' => 'Dana', 'code' => 234234],
            ['name' => 'LinkAja', 'code' => 234234],
        ];

        foreach ($banks as $bank) {
            Bank::create($bank);
        }
    }
}
