<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::create([
            'customer_category_id' => 1,
            'province_code' => '11',
            'city_code' => '1101',
            'district_code' => '110101',
            'village_code' => '11010101',
            'code' => 'CUST-01',
            'name' => 'Budi Santoso',
            'phone' => '08123456789',
            'date' => Carbon::now(),
            'sub_village' => 'Customer 1',
            'address' => 'Jl. Customer 1',
        ]);

        Customer::create([
            'customer_category_id' => 2,
            'province_code' => '11',
            'city_code' => '1101',
            'district_code' => '110101',
            'village_code' => '11010101',
            'code' => 'CUST-02',
            'name' => 'Andi Santoso',
            'phone' => '08123456789',
            'date' => Carbon::now(),
            'sub_village' => 'Customer 2',
            'address' => 'Jl. Customer 2',
        ]);

        Customer::create([
            'customer_category_id' => 1,
            'province_code' => '11',
            'city_code' => '1101',
            'district_code' => '110101',
            'village_code' => '11010101',
            'code' => 'CUST-03',
            'name' => 'Cindy Santoso',
            'phone' => '08123456789',
            'date' => Carbon::now(),
            'sub_village' => 'Customer 3',
            'address' => 'Jl. Customer 3',
        ]);
    }
}
