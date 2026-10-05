<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@koskuy.id'],
            [
                'name' => 'Admin KosKuy',
                'password' => Hash::make('admin123'),
                'phone' => '08111111111',
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'owner@koskuy.id'],
            [
                'name' => 'Pemilik Kos Maju Jaya',
                'password' => Hash::make('owner123'),
                'phone' => '08978889402',
                'role' => 'owner',
            ]
        );

        User::updateOrCreate(
            ['email' => 'mahasiswa@koskuy.id'],
            [
                'name' => 'Rangga Pratama',
                'password' => Hash::make('mahasiswa123'),
                'phone' => '081377889402',
                'role' => 'student',
            ]
        );
    }
}
