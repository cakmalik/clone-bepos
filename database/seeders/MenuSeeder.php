<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $data = [
            //dashboard
            [
                'id' => 1,
                'parent_menu_id' => 0,
                'menu_name' => 'Dashboard',
                'is_parent' => true,
            ],
            //supplier
            [
                'id' => 2,
                'parent_menu_id' => 0,
                'menu_name' => 'Supplier',
                'is_parent' => true,
            ],
            //customer
            [
                'id' => 3,
                'parent_menu_id' => 0,
                'menu_name' => 'Customer',
                'is_parent' => true,
            ],
            // customer category
            [
                'id' => 62,
                'parent_menu_id' => 3,
                'menu_name' => 'Customer Category',
                'is_parent' => false,
            ],
            //product
            [
                'id' => 4,
                'parent_menu_id' => 0,
                'menu_name' => 'Product',
                'is_parent' => true,
            ],
            [
                'id' => 5,
                'parent_menu_id' => 4,
                'menu_name' => 'Product Unit',
                'is_parent' => false,
            ],
            [
                'id' => 6,
                'parent_menu_id' => 4,
                'menu_name' => 'Product Category',
                'is_parent' => false,
            ],
            [
                'id' => 7,
                'parent_menu_id' => 4,
                'menu_name' => 'Product Selling Price',
                'is_parent' => false,
            ],
            [
                'id' => 8,
                'parent_menu_id' => 4,
                'menu_name' => 'Product Stock',
                'is_parent' => false,
            ],
            [
                'id' => 49,
                'parent_menu_id' => 4,
                'menu_name' => 'Produk Diskon',
                'is_parent' => false,
            ],
            [
                'id' => 50,
                'parent_menu_id' => 4,
                'menu_name' => 'Harga Bertingkat',
                'is_parent' => false,
            ],

            [
                'id' => 52,
                'parent_menu_id' => 4,
                'menu_name' => 'Cetak Barcode',
                'is_parent' => false,
            ],
            [
                'id' => 57,
                'parent_menu_id' => 4,
                'menu_name' => 'Brand',
                'is_parent' => false,
            ],


            //inventory
            [
                'id' => 9,
                'parent_menu_id' => 0,
                'menu_name' => 'Inventory',
                'is_parent' => true,
            ],
            [
                'id' => 10,
                'parent_menu_id' => 9,
                'menu_name' => 'Stock History',
                'is_parent' => false,
            ],
            [
                'id' => 11,
                'parent_menu_id' => 9,
                'menu_name' => 'Stock Opname',
                'is_parent' => false,
            ],
            [
                'id' => 12,
                'parent_menu_id' => 9,
                'menu_name' => 'Stock Adjustment',
                'is_parent' => false,
            ],
            [
                'id' => 13,
                'parent_menu_id' => 9,
                'menu_name' => 'Stock Mutation',
                'is_parent' => false,
            ],
            [
                'id' => 14,
                'parent_menu_id' => 9,
                'menu_name' => 'Stock Mutation Inventory to Outlet',
                'is_parent' => false,
            ],
            // [
            //     'id' => 53,
            //     'parent_menu_id' => 9,
            //     'menu_name' => 'Laporan Stok Konsolidasi Outlet',
            //     'is_parent' => false,
            // ],
            // [
            //     'id' => 54,
            //     'parent_menu_id' => 9,
            //     'menu_name' => 'Laporan Stok Konsolidasi Gudang',
            //     'is_parent' => false,
            // ],
            [
                'id' => 55,
                'parent_menu_id' => 9,
                'menu_name' => 'Laporan Stok Gudang',
                'is_parent' => false,
            ],
            [
                'id' => 56,
                'parent_menu_id' => 9,
                'menu_name' => 'Laporan Stok Outlet',
                'is_parent' => false,
            ],

            //Profil Company
            [
                'id' => 15,
                'parent_menu_id' => 0,
                'menu_name' => 'Profile Company',
                'is_parent' => true,
            ],

            //Outlet
            [
                'id' => 16,
                'parent_menu_id' => 0,
                'menu_name' => 'Outlet',
                'is_parent' => true,
            ],

            //Bukti Cash
            // [
            //     'id' => 17,
            //     'parent_menu_id' => 0,
            //     'menu_name' => 'Cash Proof In',
            //     'is_parent' => true,
            // ],
            // [
            //     'id' => 18,
            //     'parent_menu_id' => 0,
            //     'menu_name' => 'Cash Proof Out',
            //     'is_parent' => true,
            // ],

            //Purchase
            [
                'id' => 19,
                'parent_menu_id' => 0,
                'menu_name' => 'Purchase',
                'is_parent' => true,
            ],
            [
                'id' => 20,
                'parent_menu_id' => 19,
                'menu_name' => 'Purchase Requisition',
                'is_parent' => false,
            ],
            [
                'id' => 21,
                'parent_menu_id' => 19,
                'menu_name' => 'Purchase Order',
                'is_parent' => false,
            ],
            [
                'id' => 22,
                'parent_menu_id' => 19,
                'menu_name' => 'Purchase Reception',
                'is_parent' => false,
            ],
            [
                'id' => 23,
                'parent_menu_id' => 19,
                'menu_name' => 'Purchase Invoice',
                'is_parent' => false,
            ],
            [
                'id' => 24,
                'parent_menu_id' => 19,
                'menu_name' => 'Purchase Return',
                'is_parent' => false,
            ],
            [
                'id' => 25,
                'parent_menu_id' => 19,
                'menu_name' => 'Invoice Payment',
                'is_parent' => false,
            ],

            //sales
            [
                'id' => 26,
                'parent_menu_id' => 0,
                'menu_name' => 'Sales',
                'is_parent' => true,
            ],
            [
                'id' => 27,
                'parent_menu_id' => 26,
                'menu_name' => 'Payment Method',
                'is_parent' => false,
            ],
            [
                'id' => 28,
                'parent_menu_id' => 26,
                'menu_name' => 'Cashflow Close',
                'is_parent' => false,
            ],
            [
                'id' => 29,
                'parent_menu_id' => 26,
                'menu_name' => 'Retur Sales',
                'is_parent' => false,
            ],
            [
                'id' => 63,
                'parent_menu_id' => 26,
                'menu_name' => 'Debt Payment',
                'is_parent' => false,
            ],


            //Cashier Machine
            [
                'id' => 30,
                'parent_menu_id' => 0,
                'menu_name' => 'Cashier Machine',
                'is_parent' => true,
            ],

            //Role & Permission
            [
                'id' => 31,
                'parent_menu_id' => 0,
                'menu_name' => 'Role',
                'is_parent' => true,
            ],
            [
                'id' => 32,
                'parent_menu_id' => 31,
                'menu_name' => 'Permission',
                'is_parent' => false,
            ],

            //Laporan

            //laporan sales
            [
                'id' => 33,
                'parent_menu_id' => 0,
                'menu_name' => 'Report Sales',
                'is_parent' => true,
            ],
            [
                'id' => 34,
                'parent_menu_id' => 33,
                'menu_name' => 'Report Sales Overview',
                'is_parent' => false,
            ],
            [
                'id' => 35,
                'parent_menu_id' => 33,
                'menu_name' => 'Report Sales Return',
                'is_parent' => false,
            ],
            [
                'id' => 58,
                'parent_menu_id' => 33,
                'menu_name' => 'Report Laba Rugi',
                'is_parent' => false,
            ],
            [
                'id' => 59,
                'parent_menu_id' => 33,
                'menu_name' => 'Report Void Penjualan',
                'is_parent' => false,
            ],

            //laporan Purchase
            [
                'id' => 36,
                'parent_menu_id' => 0,
                'menu_name' => 'Report Purchase Order',
                'is_parent' => true,
            ],
            [
                'id' => 37,
                'parent_menu_id' => 0,
                'menu_name' => 'Report Purchase Reception',
                'is_parent' => true,
            ],
            [
                'id' => 61,
                'parent_menu_id' => 0,
                'menu_name' => 'Report Purchase Return',
                'is_parent' => true,
            ],

            //laporan inventory
            [
                'id' => 38,
                'parent_menu_id' => 0,
                'menu_name' => 'Report Inventory',
                'is_parent' => true,
            ],

            //akunting
            // [
            //     'id' => 39,
            //     'parent_menu_id' => 0,
            //     'menu_name' => 'Accounting',
            //     'is_parent' => true,
            // ],

            //User management
            [
                'id' => 40,
                'parent_menu_id' => 0,
                'menu_name' => 'User',
                'is_parent' => true,
            ],

            //settings
            [
                'id' => 41,
                'parent_menu_id' => 0,
                'menu_name' => 'Setting Product Stock',
                'is_parent' => true,
            ],
            [
                'id' => 42,
                'parent_menu_id' => 0,
                'menu_name' => 'Setting Stock Reminder',
                'is_parent' => true,
            ],
            [
                'id' => 64,
                'parent_menu_id' => 0,
                'menu_name' => 'Setting Report',
                'is_parent' => true,
            ],


            //tambahan menu yang tidak terduga
            [
                'id' => 43,
                'parent_menu_id' => 4,
                'menu_name' => 'Product Price',
                'is_parent' => false,
            ],
            [
                'id' => 44,
                'parent_menu_id' => 0,
                'menu_name' => 'Report',
                'is_parent' => true,
            ],
            [
                'id' => 45,
                'parent_menu_id' => 0,
                'menu_name' => 'Settings',
                'is_parent' => true,
            ],

            //kasir
            [
                'id' => 46,
                'parent_menu_id' => 0,
                'menu_name' => 'Selling Kitchen',
                'is_parent' => true,
            ],
            [
                'id' => 47,
                'parent_menu_id' => 0,
                'menu_name' => 'Selling Order',
                'is_parent' => true,
            ],
            [
                'id' => 48,
                'parent_menu_id' => 0,
                'menu_name' => 'Selling Payment',
                'is_parent' => true,
            ],

        ];

        foreach ($data as $value) {
            Menu::create($value);
        }

        if (env('MEMBERSHIP_LOYALITY')) {
            Menu::create([
                'id' => 60,
                'parent_menu_id' => 0,
                'menu_name' => 'Membership',
                'is_parent' => true,
            ]);
            Menu::create([
                'parent_menu_id' => 0,
                'menu_name' => 'Loyalty',
                'is_parent' => true,
            ]);
        }

        if (env('GIFT_PRODUCT')) {
            Menu::create([
                'id' => 51,
                'parent_menu_id' => 4,
                'menu_name' => 'Mutasi Hadiah',
                'is_parent' => false,
            ],);
        }
    }
}
