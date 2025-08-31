<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SetupFirstTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dev = Role::where('role_name', 'DEVELOPER')->first();

        User::create([
            'role_id' => $dev->id,
            'users_name' => 'Developer',
            'username' => 'developer',
            'email' => 'developer@mail.com',
            'password' => Hash::make('123'),
            'pin' => Crypt::encryptString('123456'),
            'email_verified_at' => now()
        ]);
    }
}
