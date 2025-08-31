<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'role_id' => 1,
            'users_name' => 'superadmin',
            'username' => 'superadmin',
            'email' => 'superadmin@mail.com',
            'password' => Hash::make('123'),
            'pin' => Crypt::encryptString('123456')
        ]);

        //outlet 1
        User::create([
            'role_id' => 2,
            'users_name' => 'Supervisor 1',
            'username' => 'admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('123'),
            'pin' => Crypt::encryptString('123456')
        ]);
        User::create([
            'role_id' => 3,
            'users_name' => 'demo_kasir',
            'username' => 'demo_kasir',
            'email' => 'demokasir@mail.com',
            'password' => Hash::make('123'),
            'pin' => Crypt::encryptString('123456')
        ]);

        User::create([
            'role_id' => 3,
            'users_name' => 'Kasir Mart 1',
            'username' => 'kasir_mart_satu',
            'email' => 'kasir_mart_satu@mail.com',
            'password' => Hash::make('123'),
            'pin' => Crypt::encryptString('123456')
        ]);

        if (env('MULTI_OUTLET_SETUP')) {

            //outlet 2
            User::create([
                'role_id' => 2,
                'users_name' => 'Supervisor 2',
                'username' => 'admin_dua',
                'email' => 'admin_dua@mail.com',
                'password' => Hash::make('123'),
                'pin' => Crypt::encryptString('123456')
            ]);
            User::create([
                'role_id' => 3,
                'users_name' => 'Kasir Joglo 1',
                'username' => 'kasir_joglo',
                'email' => 'kasir_joglo_satu@mail.com',
                'password' => Hash::make('123'),
                'pin' => Crypt::encryptString('123456')
            ]);

            User::create([
                'role_id' => 3,
                'users_name' => 'Kasir Joglo 2',
                'username' => 'kasir_joglo_dua',
                'email' => 'kasir_joglo_dua@mail.com',
                'password' => Hash::make('123'),
                'pin' => Crypt::encryptString('123456')
            ]);
        }
    }
}
