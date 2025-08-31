<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
            'name' => 'stock_alert',
            'value' => false,
            'desc' => 'pesan pengingat akan ditampilkan jika stok produk mendekati mininal,'
        ]);

        Setting::create([
            'name' => 'stock_minus',
            'value' => false,
            'desc' => NULL,
        ]);

        Setting::create([
            'name' => 'superior_validation',
            'value' => true,
            'desc' => 'saat penjualan, jika mau hapus produk dari cart butuh validasi,'
        ]);

        Setting::create([
            'name' => 'minus_price',
            'value' => false,
            'desc' => NULL
        ]);
        
        Setting::create([
            'name' => 'price_change',
            'value' => false,
            'desc' => NULL
        ]);

        Setting::create([
            'name' => 'show_recent_sales',
            'value' => false,
            'desc' => NULL
        ]);

        Setting::create([
            'name' => 'change_qty_direct_after_add',
            'value' => false,
            'desc' => NULL
        ]);

        Setting::create([
            'name' => 'customer_price',
            'value' => false,
            'desc' => 'Penjualan berdasarkan harga kategori pelanggan'
        ]);

        Setting::create([
            'name' => 'price_crossed',
            'value' => false,
            'desc' => 'harga coret pada struk nota kasir'
        ]);

        Setting::create([
            'name' => 'show_and_change_order_status',
            'value' => false,
            'desc' => NULL
        ]);

        Setting::create([
            'name' => 'simple_purchase',
            'value' => false,
            'desc' => 'izinkan penerimaan pembelian tanpa purchase order'
        ]);
    }
}
