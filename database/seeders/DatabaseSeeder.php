<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name' => 'Test',
            'last_name'  => 'Admin',
            'email'      => 'test@gmail.vmms',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'status'     => 'active',
        ]);
    }
}
