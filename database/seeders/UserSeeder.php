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
                'phone' => '628123456789',
                'role' => 'owner',
            ]
        );

        User::updateOrCreate(
            ['email' => 'asn.labs1210@gmail.com'],
            [
                'name' => 'Aldiyan Setyo Nugroho',
                'password' => Hash::make('student123'),
                'phone' => '0895326651161',
                'role' => 'student',
            ]
        );

        User::updateOrCreate(
            ['email' => 'refasya.audrey.mukhni@students.untidar.ac.id'],
            [
                'name' => 'Refasya Audrey M',
                'password' => Hash::make('student123'),
                'phone' => '085260851563',
                'role' => 'student',
            ]
        );
    }
}
