<?php

namespace Database\Seeders;

use App\Models\Membership;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MembershipsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Membership::create([
            'name' => 'Bronze',
            'score_min' => 0,
            'score_max' => 1000,
            'score_loyalty' => 'Master',
        ]);
        Membership::create([
            'name' => 'Silver',
            'score_min' => 1000,
            'score_max' => 2000,
            'score_loyalty' => 'Epic',
        ]);
        Membership::create([
            'name' => 'Gold',
            'score_min' => 2000,
            'score_max' => 3000,
            'score_loyalty' => 'Legend',
        ]);
    }
}
