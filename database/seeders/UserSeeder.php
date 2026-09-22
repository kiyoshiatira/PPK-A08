<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'        => 'Yosi (Pengguna)',
            'email'       => 'pengguna@kampus.ac.id',
            'password'    => Hash::make('password123'),
            'role'        => 'pengguna',
            'is_verified' => true,
        ]);

        User::create([
            'name'        => 'Petugas Fasilitas',
            'email'       => 'petugas@kampus.ac.id',
            'password'    => Hash::make('password123'),
            'role'        => 'petugas',
            'is_verified' => true,
        ]);

        User::create([
            'name'        => 'Admin Kampus',
            'email'       => 'admin@kampus.ac.id',
            'password'    => Hash::make('password123'),
            'role'        => 'admin',
            'is_verified' => true,
        ]);
    }
}