<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            KosListingSeeder::class,
            ThriftProductSeeder::class,
            ImageSeeder::class,
        ]);
    }
}
