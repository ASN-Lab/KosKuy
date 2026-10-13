<?php

namespace Database\Seeders;

use App\Models\KosFacility;
use App\Models\KosListing;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

/**
 * Demo storefront listings.
 *
 * Rows are keyed by title and written with explicit timestamps, so a re-seed
 * produces the same ids, the same "Baru" badges, and the same newest-first
 * order on the home page. Photo files are keyed by the resulting id, so keep
 * this list in sync with the manifest in scripts/fetch-demo-photos.php.
 *
 * Prices deliberately span the whole range a student compares: from the
 * cheapest shared room up to a premium en-suite unit.
 */
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
                'facilities' => ['Wi-Fi', 'Parkir', 'Kamar Mandi Dalam', 'Dapur'],
                'days_ago' => 48,
            ],
            [
                'title' => 'Kos Melati Putri',
                'description' => 'Kos eksklusif putri dengan keamanan 24 jam dan fasilitas lengkap, dekat kampus.',
                'price_per_month' => 1200000,
                'gender_type' => 'putri',
                'latitude' => -7.221400,
                'longitude' => 110.856000,
                'address' => 'Jl. Ringinherang No. 8, Banyurejo, Magelang',
                'facilities' => ['AC', 'Wi-Fi', 'Kamar Mandi Dalam', 'Water Heater', 'CCTV'],
                'days_ago' => 40,
            ],
            [
                'title' => 'Kos Sejahtera Campur',
                'description' => 'Kos campur dengan kamar luas, dapur bersama, dan area jemur, sekitar Tidar.',
                'price_per_month' => 700000,
                'gender_type' => 'campur',
                'latitude' => -7.298500,
                'longitude' => 110.829000,
                'address' => 'Jl. Magelang-Kopeng No. 88, Sidorejo, Magelang',
                'facilities' => ['Wi-Fi', 'Dapur', 'Jemuran', 'Parkir'],
                'days_ago' => 34,
            ],
            [
                'title' => 'Kos Tidar Asri',
                'description' => 'Kos putra paling terjangkau di kawasan kampus, kamarnya sederhana tapi bersih dan tenang.',
                'price_per_month' => 550000,
                'gender_type' => 'putra',
                'latitude' => -7.254100,
                'longitude' => 110.836200,
                'address' => 'Jl. Kawi No. 12, Reksosari, Magelang',
                'facilities' => ['Wi-Fi', 'Parkir', 'Kamar Mandi Dalam'],
                'days_ago' => 27,
            ],
            [
                'title' => 'Kos Anggrek Putri',
                'description' => 'Kos putri harga pelajar dengan dapur bersama dan area jemur luas di halaman belakang.',
                'price_per_month' => 600000,
                'gender_type' => 'putri',
                'latitude' => -7.240800,
                'longitude' => 110.847700,
                'address' => 'Jl. Dewi Sartika No. 3, Reksosari, Magelang',
                'facilities' => ['Wi-Fi', 'Dapur', 'Jemuran'],
                'days_ago' => 21,
            ],
            [
                'title' => 'Kos Pandan Campur',
                'description' => 'Kos campur dua lantai, tiap kamar punya kipas angin dan rak belajar pribadi.',
                'price_per_month' => 650000,
                'gender_type' => 'campur',
                'latitude' => -7.283200,
                'longitude' => 110.851500,
                'address' => 'Jl. Pandan No. 21, Wates, Magelang',
                'facilities' => ['Wi-Fi', 'Parkir', 'Laundry', 'Jemuran'],
                'days_ago' => 16,
            ],
            [
                'title' => 'Kos Kenanga Putri',
                'description' => 'Kos putri dengan kamar mandi dalam tiap kamar dan air panas, cocok untuk yang ingin praktis.',
                'price_per_month' => 750000,
                'gender_type' => 'putri',
                'latitude' => -7.233600,
                'longitude' => 110.862400,
                'address' => 'Jl. Kenanga No. 5, Banyurejo, Magelang',
                'facilities' => ['Wi-Fi', 'Kamar Mandi Dalam', 'Water Heater'],
                'days_ago' => 11,
            ],
            [
                'title' => 'Kos Merbabu Putra',
                'description' => 'Kos putra berpenghuni rata-rata mahasiswa semester akhir, ada TV bersama dan area parkir motor.',
                'price_per_month' => 900000,
                'gender_type' => 'putra',
                'latitude' => -7.271900,
                'longitude' => 110.843800,
                'address' => 'Jl. Mondokan No. 17, Sidorejo, Magelang',
                'facilities' => ['Wi-Fi', 'Parkir', 'TV', 'Dapur'],
                'days_ago' => 7,
            ],
            [
                'title' => 'Kos Sendang Sari',
                'description' => 'Kos campur dengan dapur bersama lengkap, kulkas bersama, dan halaman untuk menjemur.',
                'price_per_month' => 950000,
                'gender_type' => 'campur',
                'latitude' => -7.295800,
                'longitude' => 110.834700,
                'address' => 'Jl. Sendang No. 3, Tegalrejo, Magelang',
                'facilities' => ['Wi-Fi', 'Dapur', 'Kulkas', 'Jemuran'],
                'days_ago' => 4,
            ],
            [
                'title' => 'Kos Bougenville',
                'description' => 'Kos putri baru dengan CCTV di pintu masuk dan kamar mandi dalam berwater heater.',
                'price_per_month' => 1100000,
                'gender_type' => 'putri',
                'latitude' => -7.226500,
                'longitude' => 110.859100,
                'address' => 'Jl. Bougenville No. 9, Banyurejo, Magelang',
                'facilities' => ['AC', 'Wi-Fi', 'Kamar Mandi Dalam', 'CCTV'],
                'days_ago' => 2,
            ],
            [
                'title' => 'Kos Borobudur View',
                'description' => 'Kos campur kelas menengah dengan ruang santai bersama dan pemandangan Gunung Merbabu.',
                'price_per_month' => 1350000,
                'gender_type' => 'campur',
                'latitude' => -7.247300,
                'longitude' => 110.828600,
                'address' => 'Jl. Badrawati No. 4, Drono, Magelang',
                'facilities' => ['AC', 'Wi-Fi', 'Kulkas', 'Ruang Santai', 'Parkir'],
                'days_ago' => 12,
            ],
            [
                'title' => 'Kos Tidar Premium',
                'description' => 'Kos putra premium langganan mahasiswa akhir, kamar luas dengan meja kerja dan lemari besar.',
                'price_per_month' => 1650000,
                'gender_type' => 'putra',
                'latitude' => -7.240200,
                'longitude' => 110.841200,
                'address' => 'Jl. Puntadewa No. 26, Reksosari, Magelang',
                'facilities' => ['AC', 'Wi-Fi', 'Kamar Mandi Dalam', 'Water Heater', 'Laundry'],
                'days_ago' => 5,
            ],
            [
                'title' => 'Kos Melati Residence',
                'description' => 'Kos putri satu gedung dengan dapur per kamar lantai, cocok berpasangan dengan teman.',
                'price_per_month' => 1950000,
                'gender_type' => 'putri',
                'latitude' => -7.218700,
                'longitude' => 110.851300,
                'address' => 'Jl. Melati Raya No. 2, Banyurejo, Magelang',
                'facilities' => ['AC', 'Wi-Fi', 'Kulkas', 'Dapur', 'CCTV', 'Parkir'],
                'days_ago' => 3,
            ],
            [
                'title' => 'Kos Sky Terrace',
                'description' => 'Kos campur rooftop dengan area kerja bersama, laundry kiloan langganan, dan lift satu unit.',
                'price_per_month' => 2400000,
                'gender_type' => 'campur',
                'latitude' => -7.259600,
                'longitude' => 110.854800,
                'address' => 'Jl. Jend. Sudirman No. 71, Rejosari, Magelang',
                'facilities' => ['AC', 'Wi-Fi', 'Ruang Santai', 'Water Heater', 'Kamar Mandi Dalam', 'Parkir'],
                'days_ago' => 1,
            ],
            [
                'title' => 'Kos Kasogangan Loft',
                'description' => 'Unit loft eksklusif untuk satu orang, interior baru, perabot lengkap, dan akses kartu.',
                'price_per_month' => 3200000,
                'gender_type' => 'putra',
                'latitude' => -7.263400,
                'longitude' => 110.847100,
                'address' => 'Jl. Kasogangan No. 8, Sidorejo, Magelang',
                'facilities' => ['AC', 'Wi-Fi', 'Kamar Mandi Dalam', 'Dapur', 'Laundry', 'Ruang Santai', 'CCTV'],
                'days_ago' => 6,
            ],
        ];

        Model::unguarded(function () use ($owner, $listings) {
            foreach ($listings as $data) {
                $createdAt = now()->subDays($data['days_ago'])->startOfHour();

                $kos = KosListing::updateOrCreate(
                    ['title' => $data['title']],
                    [
                        'owner_id' => $owner->id,
                        'description' => $data['description'],
                        'price_per_month' => $data['price_per_month'],
                        'gender_type' => $data['gender_type'],
                        'latitude' => $data['latitude'],
                        'longitude' => $data['longitude'],
                        'address' => $data['address'],
                        'is_available' => true,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]
                );

                // Facilities are rewritten every run so the sidebar counts and
                // the chips on each card stay predictable for the demo.
                KosFacility::where('kos_id', $kos->id)->delete();

                foreach ($data['facilities'] as $facility) {
                    KosFacility::create([
                        'kos_id' => $kos->id,
                        'facility_name' => $facility,
                    ]);
                }
            }
        });
    }
}
