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
    }
}
