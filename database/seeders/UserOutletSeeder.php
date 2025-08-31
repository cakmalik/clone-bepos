<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserOutlet;

class UserOutletSeeder extends Seeder
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
                'outlet_id' => 1,
                'user_id' => 1,
            ],
            [
                'outlet_id' => 1,
                'user_id' => 2,
            ],
            [
                'outlet_id' => 1,
                'user_id' => 3,
            ],
            [
                'outlet_id' => 1,
                'user_id' => 4,
            ],
           
        ];

        foreach ($data as $value) {
            UserOutlet::create($value);

        }

        if (env('MULTI_OUTLET_SETUP')) {
            UserOutlet::create([
                'outlet_id' => 2,
                'user_id' => 5,
            ]);
            UserOutlet::create([
                'outlet_id' => 2,
                'user_id' => 6,
            ]);
            UserOutlet::create([
                'outlet_id' => 2,
                'user_id' => 7,
            ]);
        }
    }
}
