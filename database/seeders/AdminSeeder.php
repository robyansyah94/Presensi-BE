<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Karyawan',
            'email' => 'Karyawan@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
        ]);
    }
}
