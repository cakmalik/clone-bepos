<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerCategory;

class CustomerCategorySeeder extends Seeder
{
    public function run(): void
    {
        CustomerCategory::create([
            'code' =>  customerCategoryCode(),
            'name' => 'Premium',
            'slug' => 'premium',
            'description' => 'Pelanggan dengan keistimewaan eksklusif.'
        ]);

        CustomerCategory::create([
            'code' =>  customerCategoryCode(),
            'name' => 'Regular',
            'slug' => 'regular',
            'description' => 'Pelanggan standar tanpa keistimewaan tambahan.'
        ]);
    }
}
