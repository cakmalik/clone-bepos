<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Purchase;

class PurchaseSeeder extends Seeder
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
                'id' => 1,
                'ref_code' => '',
                'outlet_id' => 1,
                'user_id' => 1,
                'purchase_invoice_id' => null,
                'code' => 'PR-KC-000-23-01-0001',
                'name' => 'Purchase Requisition',
                'purchase_type' => 'Purchase Requisition',
                'purchase_date' => '2021-01-01',
                'purchase_status' => 'Finish',
            ]
        ];

        foreach ($data as $key => $value) {
            Purchase::create($value);
        }
    }
}
