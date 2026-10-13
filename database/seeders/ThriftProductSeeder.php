<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ThriftProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

/**
 * Demo thrift catalogue.
 *
 * Rows are keyed by title and written with explicit timestamps, so a re-seed
 * keeps the same ids (photo files are named after them), the same "Baru"
 * badges, and the same newest-first order on the home page.
 *
 * Prices run from a single paperback up to a used laptop, so the price filter
 * and both sort orders have something meaningful to work with.
 */
class ThriftProductSeeder extends Seeder
{
    public function run(): void
    {
        $seller = User::where('email', 'owner@koskuy.id')->first();

        // Fix a typo from an earlier seed run. Renaming in place keeps the row
        // id stable, which the photo file name depends on.
        ThriftProduct::where('title', 'Kipas Angi Mini Meja')
            ->update(['title' => 'Kipas Angin Meja Mini']);

        $products = [
            [
                'title' => 'Buku Pemrograman Python',
                'description' => 'Buku second namun masih bagus, tidak ada halaman rusak.',
                'price' => 75000,
                'condition_status' => '90% Layak Pakai',
                'category' => 'Buku',
                'days_ago' => 46,
            ],
            [
                'title' => 'Kipas Angin Meja Mini',
                'description' => 'Kipas mini masih normal, cocok untuk kamar kos.',
                'price' => 120000,
                'condition_status' => '85% Layak Pakai',
                'category' => 'Elektronik',
                'days_ago' => 38,
            ],
            [
                'title' => 'Kursi Lipat Mahasiswa',
                'description' => 'Kursi lipat kuat, sedikit cat terkelupas.',
                'price' => 95000,
                'condition_status' => '80% Layak Pakai',
                'category' => 'Furnitur',
                'days_ago' => 31,
            ],
            [
                'title' => 'Laptop Bekas Core i5',
                'description' => 'Laptop kerja 2019, baterai masih awet, sudah ganti SSD 512 GB.',
                'price' => 2750000,
                'condition_status' => '85% Layak Pakai',
                'category' => 'Elektronik',
                'days_ago' => 25,
            ],
            [
                'title' => 'Novel Haruki Murakami',
                'description' => 'Dua novel bekas pilihan, jilid lengkap tanpa sobek.',
                'price' => 45000,
                'condition_status' => '90% Layak Pakai',
                'category' => 'Buku',
                'days_ago' => 19,
            ],
            [
                'title' => 'Keyboard Mekanik RGB',
                'description' => 'Keyboard mekanik switch blue, semua tombol responsif.',
                'price' => 385000,
                'condition_status' => '85% Layak Pakai',
                'category' => 'Elektronik',
                'days_ago' => 15,
            ],
            [
                'title' => 'Setrika Uap Philips',
                'description' => 'Setrika uap masih panas stabil, kabel tidak ada yang terkelupas.',
                'price' => 95000,
                'condition_status' => '85% Layak Pakai',
                'category' => 'Alat Rumah Tangga',
                'days_ago' => 22,
            ],
            [
                'title' => 'Sepeda Lipat Second',
                'description' => 'Sepeda lipat ringan, ban baru, cocok untuk bolak-balik kampus.',
                'price' => 1450000,
                'condition_status' => '80% Layak Pakai',
                'category' => 'Olahraga',
                'days_ago' => 12,
            ],
            [
                'title' => 'Gitar Akustik Pemula',
                'description' => 'Gitar akustik 41 inci lengkap dengan soft case dan kunci senar.',
                'price' => 650000,
                'condition_status' => '90% Layak Pakai',
                'category' => 'Musik',
                'days_ago' => 11,
            ],
            [
                'title' => 'Tas Ransel 25 Liter',
                'description' => 'Ransel laptop 25 liter, resleting mulus, cocok untuk harian.',
                'price' => 175000,
                'condition_status' => '95% Seperti Baru',
                'category' => 'Fashion',
                'days_ago' => 16,
            ],
            [
                'title' => 'Meja Belajar Kayu',
                'description' => 'Meja belajar kayu solid, permukaan rata, tinggi pas untuk kamar kos.',
                'price' => 320000,
                'condition_status' => '85% Layak Pakai',
                'category' => 'Furnitur',
                'days_ago' => 8,
            ],
            [
                'title' => 'Sepatu Sneaker Putih',
                'description' => 'Sneaker putih ukuran 42, dipakai beberapa kali, sol masih tebal.',
                'price' => 210000,
                'condition_status' => '90% Layak Pakai',
                'category' => 'Fashion',
                'days_ago' => 3,
            ],
            [
                'title' => 'Kamera Analog Canon AE-1',
                'description' => 'Kamera analog lengkap dengan lensa 50mm, siap jepret.',
                'price' => 1250000,
                'condition_status' => '80% Layak Pakai',
                'category' => 'Elektronik',
                'days_ago' => 1,
            ],
            [
                'title' => 'Rice Cooker 1.8 Liter',
                'description' => 'Magic com 1,8 liter, inner pot masih bagus, cocok masak harian.',
                'price' => 130000,
                'condition_status' => '85% Layak Pakai',
                'category' => 'Alat Rumah Tangga',
                'days_ago' => 5,
            ],
        ];

        Model::unguarded(function () use ($seller, $products) {
            foreach ($products as $data) {
                $category = Category::where('name', $data['category'])->first();
                $createdAt = now()->subDays($data['days_ago'])->startOfHour();

                ThriftProduct::updateOrCreate(
                    ['title' => $data['title']],
                    [
                        'seller_id' => $seller->id,
                        'category_id' => $category->id,
                        'description' => $data['description'],
                        'price' => $data['price'],
                        'condition_status' => $data['condition_status'],
                        'is_sold' => false,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]
                );
            }
        });
    }
}
