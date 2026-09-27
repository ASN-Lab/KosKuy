<?php

namespace Database\Seeders;

use App\Models\KosFacility;
use App\Models\KosImage;
use App\Models\KosListing;
use App\Models\User;
use Illuminate\Database\Seeder;

class KosListingSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('email', 'owner@koskuy.id')->first();

        $listings = [
            [
                'title' => 'Kos Maju Jaya Putra',
                'description' => 'Kos nyaman dekat kampus, lingkungan bersih dan aman. Cocok untuk mahasiswa.',
                'price_per_month' => 850000,
                'gender_type' => 'putra',
                'latitude' => -6.200000,
                'longitude' => 106.816666,
                'address' => 'Jl. Maju Jaya No. 12, Jakarta Selatan',
            ],
            [
                'title' => 'Kos Melati Putri',
                'description' => 'Kos eksklusif putri dengan keamanan 24 jam dan fasilitas lengkap.',
                'price_per_month' => 1200000,
                'gender_type' => 'putri',
                'latitude' => -6.210000,
                'longitude' => 106.820000,
                'address' => 'Jl. Melati No. 5, Jakarta Selatan',
            ],
            [
                'title' => 'Kos Sejahtera Campur',
                'description' => 'Kos campur dengan kamar luas, dapur bersama, dan area jemur.',
                'price_per_month' => 700000,
                'gender_type' => 'campur',
                'latitude' => null,
                'longitude' => null,
                'address' => 'Jl. Sejahtera No. 88, Jakarta Timur',
            ],
        ];

        foreach ($listings as $data) {
            $kos = KosListing::create(array_merge($data, [
                'owner_id' => $owner->id,
                'is_available' => true,
            ]));

            $facilities = ['AC', 'Wi-Fi', 'Kamar Mandi Dalam', 'Dapur'];
            foreach (array_slice($facilities, 0, rand(2, 4)) as $facility) {
                KosFacility::create([
                    'kos_id' => $kos->id,
                    'facility_name' => $facility,
                ]);
            }
        }
    }
}
