<?php

namespace Database\Seeders;

use App\Models\KosListing;
use App\Models\KosRoom;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class KosRentalSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('role', 'owner')->first();

        if (! $owner) {
            $this->command->info('Tidak ada akun pemilik untuk disekon kamar.');

            return;
        }

        $kosIds = KosListing::where('owner_id', $owner->id)->pluck('id');

        if (KosRoom::whereIn('kos_listing_id', $kosIds)->exists()) {
            $this->command->info('Kamar kos pemilik sudah disekon, dilewati.');

            return;
        }

        $months = collect([0, 1, 2, 3])->map(fn ($gone) => now()->subMonths($gone)->format('Y-m'));
        $current = $months->first();

        foreach (KosListing::whereIn('id', $kosIds)->get(['id', 'price_per_month', 'title']) as $kos) {
            $rooms = collect([]);
            $roomCount = fake()->numberBetween(3, 8);

            for ($i = 1; $i <= $roomCount; $i++) {
                $rooms->push(KosRoom::create([
                    'kos_listing_id' => $kos->id,
                    'room_number' => (string) $i,
                    'price_per_month' => $kos->price_per_month,
                ]));
            }

            $occupied = (int) round($roomCount * 0.7);

            foreach ($rooms->slice(0, $occupied) as $room) {
                $tenant = Tenant::create([
                    'kos_listing_id' => $kos->id,
                    'kos_room_id' => $room->id,
                    'name' => fake()->name(),
                    'phone' => '08'.fake()->numerify('##########'),
                    'monthly_price' => $kos->price_per_month,
                    'start_date' => now()->subMonths(fake()->numberBetween(1, 8))->format('Y-m-d'),
                    'end_date' => null,
                    'status' => 'active',
                ]);

                $paidThisMonth = fake()->boolean(75);

                foreach ($months as $index => $month) {
                    if ($month === $current && ! $paidThisMonth) {
                        Payment::create([
                            'tenant_id' => $tenant->id,
                            'kos_listing_id' => $kos->id,
                            'amount' => $tenant->monthly_price,
                            'period' => $month,
                            'status' => 'pending',
                            'paid_at' => null,
                            'method' => null,
                        ]);

                        continue;
                    }

                    if (fake()->boolean(90) === false) {
                        continue;
                    }

                    Payment::create([
                        'tenant_id' => $tenant->id,
                        'kos_listing_id' => $kos->id,
                        'amount' => $tenant->monthly_price,
                        'period' => $month,
                        'status' => 'paid',
                        'paid_at' => now()->subMonths($index)->subDays(fake()->numberBetween(0, 6))->format('Y-m-d'),
                        'method' => fake()->randomElement(['Transfer', 'Tunai', 'QRIS']),
                    ]);
                }
            }

            $this->command->info("Kos \"{$kos->title}\": {$occupied}/{$roomCount} kamar terisi.");
        }

        $this->command->info('Kamar, penghuni, dan pembayaran demo selesai dibuat.');
    }
}