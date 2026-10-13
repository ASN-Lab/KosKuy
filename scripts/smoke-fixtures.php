<?php

/*
|--------------------------------------------------------------------------
| Smoke test fixtures
|--------------------------------------------------------------------------
| Shared by storage/app/smoke-routes.php and storage/app/audit-html.php so
| both dev harnesses provision, and later roll back, exactly the same rows.
|
| Every row created here is recorded so `smoke_teardown()` can restore the
| database to its pre-run state. Nothing in here is meant for production.
*/

use App\Models\Category;
use App\Models\KosListing;
use App\Models\ThriftProduct;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Provision a signed-in-able owner plus, when the tables are empty, one Kos and
 * one Thrift listing so the detail pages have something to render.
 *
 * @return array{0: User, 1: array<string, array<int, mixed>>}
 */
function smoke_fixtures(): array
{
    $created = ['users' => [], 'categories' => [], 'kos' => [], 'thrift' => [], 'newsletter' => []];

    if (Category::count() === 0) {
        foreach (['Buku', 'Elektronik', 'Furnitur', 'Olahraga'] as $name) {
            $created['categories'][] = Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ])->id;
        }
    }

    $user = User::firstOrCreate(
        ['email' => 'smoke@koskuy.test'],
        [
            'name' => 'Smoke Tester',
            'phone' => '081234567890',
            'role' => 'owner',
            'password' => 'password123',
        ]
    );

    if ($user->wasRecentlyCreated) {
        $created['users'][] = $user->id;
    }

    if (KosListing::count() === 0) {
        $kos = KosListing::create([
            'owner_id' => $user->id,
            'title' => 'Kos-[[TEST]] Contoh Dekat Kampus',
            'description' => "Deskripsi kos untuk smoke test.\n\nParagraf kedua untuk menguji rich text.",
            'price_per_month' => 1250000,
            'gender_type' => 'putri',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'address' => 'Jl. Contoh No. 1, Aurora City',
            'is_available' => true,
        ]);

        foreach (['WiFi', 'Kamar mandi dalam', 'AC', 'Dapur bersama'] as $facility) {
            $kos->facilities()->create(['facility_name' => $facility]);
        }

        $kos->images()->create(['image_path' => 'smoke/placeholder.jpg']);

        $created['kos'][] = $kos->id;
    }

    if (ThriftProduct::count() === 0) {
        $created['thrift'][] = ThriftProduct::create([
            'seller_id' => $user->id,
            'category_id' => Category::first()->id,
            'title' => 'Barang [[TEST]] Smoke',
            'description' => "Barang thrift untuk smoke test.\n\nKondisi baik.",
            'price' => 85000,
            'condition_status' => '80% - Layak Pakai',
            'is_sold' => false,
        ])->id;
    }

    return [$user, $created];
}

/**
 * Roll back everything `smoke_fixtures()` and the harnesses added.
 *
 * @param  array<string, array<int, mixed>>  $created
 * @return array{ok: bool, message: string}
 */
function smoke_teardown(array $created): array
{
    foreach ($created['newsletter'] ?? [] as $email) {
        // NB: the `cache()` helper is remember($key, $ttl, $callback), NOT
        // put($key, $value, $ttl) -- using it as a setter silently no-ops.
        // Always go through the Cache facade for writes.
        $subscribers = array_values(array_filter(
            (array) Cache::get('newsletter:subscribers', []),
            fn ($subscriber) => ! in_array($subscriber, $created['newsletter'], true)
        ));

        Cache::put('newsletter:subscribers', $subscribers, now()->addYear());
    }

    ThriftProduct::whereIn('id', $created['thrift'] ?? [])->delete();
    KosListing::whereIn('id', $created['kos'] ?? [])->delete();
    Category::whereIn('id', $created['categories'] ?? [])->delete();
    User::whereIn('id', $created['users'] ?? [])->delete();

    $leftovers = User::where('email', 'like', '%koskuy.test')->count()
        + KosListing::where('title', 'like', '%[[TEST]]%')->count()
        + ThriftProduct::where('title', 'like', '%[[TEST]]%')->count()
        + count(array_intersect(
            ['test@koskuy.test', 'json@koskuy.test'],
            (array) Cache::get('newsletter:subscribers', [])
        ));

    return $leftovers > 0
        ? ['ok' => false, 'message' => "{$leftovers} smoke fixture(s) still present"]
        : ['ok' => true, 'message' => 'database restored to its pre-run state'];
}