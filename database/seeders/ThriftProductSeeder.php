<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ThriftImage;
use App\Models\ThriftProduct;
use App\Models\User;
use Illuminate\Database\Seeder;

class ThriftProductSeeder extends Seeder
{
    public function run(): void
    {
        $seller = User::where('email', 'owner@koskuy.id')->first();

        $products = [
            [
                'title' => 'Buku Pemrograman Python',
                'description' => 'Buku second namun masih bagus, tidak ada halaman rusak.',
                'price' => 75000,
                'condition_status' => '90% Layak Pakai',
                'category' => 'Buku',
            ],
            [
                'title' => 'Kipas Angi Mini Meja',
                'description' => 'Kipas mini masih normal, cocok untuk kamar kos.',
                'price' => 120000,
                'condition_status' => '85% Layak Pakai',
                'category' => 'Elektronik',
            ],
            [
                'title' => 'Kursi Lipat Mahasiswa',
                'description' => 'Kursi lipat kuat, sedikit cat terkelupis.',
                'price' => 95000,
                'condition_status' => '80% Layak Pakai',
                'category' => 'Furnitur',
            ],
        ];

        foreach ($products as $data) {
            $category = Category::where('name', $data['category'])->first();

            $product = ThriftProduct::firstOrCreate(
                ['title' => $data['title']],
                [
                    'seller_id' => $seller->id,
                    'category_id' => $category->id,
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'condition_status' => $data['condition_status'],
                    'is_sold' => false,
                ]
            );
        }
    }
}
