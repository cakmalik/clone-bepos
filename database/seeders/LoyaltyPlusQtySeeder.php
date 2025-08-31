<?php

namespace Database\Seeders;

use App\Models\LoyaltyPlusQty;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LoyaltyPlusQtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LoyaltyPlusQty::create([
            'min_transaction' => 10000,
            'point_plus' => 10,
            'applies_multiply' => false,
        ]);
    }
}
