<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::updateOrCreate([
            'role_name' => 'SUPERADMIN',
        ], [
            'outlet_id' => 1,
        ]);
        Role::updateOrCreate([
            'role_name' => 'SUPERVISOR',
        ], [
            'outlet_id' => 1,
        ]);
        Role::updateOrCreate([
            'role_name' => 'KASIR',
        ], [
            'outlet_id' => 1,
        ]);
        Role::updateOrCreate([
            'role_name' => 'PURCHASING',
        ], [
            'outlet_id' => 1,
        ]);
        Role::updateOrCreate([
            'role_name' => 'GUDANG',
        ], [
            'outlet_id' => 1,
        ]);
        Role::updateOrCreate([
            'role_name' => 'DEVELOPER',
        ], [
            'outlet_id' => 1,
        ]);
    }
}
