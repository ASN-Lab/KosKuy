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
                'description' => 'Kos nyaman dekat Universitas Tidar, lingkungan bersih dan aman. Cocok untuk mahasiswa.',
                'price_per_month' => 850000,
                'gender_type' => 'putra',
                'latitude' => -7.265700,
                'longitude' => 110.840300,
                'address' => 'Jl. Sutan Pahlawan No. 45, Sidorejo, Magelang',
            ],
            [
                'title' => 'Kos Melati Putri',
                'description' => 'Kos eksklusif putri dengan keamanan 24 jam dan fasilitas lengkap, dekat kampus.',
                'price_per_month' => 1200000,
                'gender_type' => 'putri',
                'latitude' => -7.221400,
                'longitude' => 110.856000,
                'address' => 'Jl. Ringinherang No. 8, Banyurejo, Magelang',
            ],
            [
                'title' => 'Kos Sejahtera Campur',
                'description' => 'Kos campur dengan kamar luas, dapur bersama, dan area jemur, sekitar Tidor.',
                'price_per_month' => 700000,
                'gender_type' => 'campur',
                'latitude' => -7.298500,
                'longitude' => 110.829000,
                'address' => 'Jl. Magelang-Kopeng No. 88, Kidor, Magelang',
            ],
        ];

        foreach ($listings as $data) {
            $kos = KosListing::firstOrCreate(
                ['title' => $data['title']],
                array_merge($data, [
                    'owner_id' => $owner->id,
                    'is_available' => true,
                ])
            );

            if ($kos->wasRecentlyCreated) {
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
}
