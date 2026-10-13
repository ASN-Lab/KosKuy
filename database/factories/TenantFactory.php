<?php

namespace Database\Factories;

use App\Models\KosListing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kos_listing_id' => KosListing::factory(),
            'kos_room_id' => null,
            'name' => fake()->name(),
            'phone' => '08'.fake()->numerify('##########'),
            'monthly_price' => fake()->numberBetween(5, 25) * 100000,
            'start_date' => fake()->dateTimeBetween('-8 months', 'now')->format('Y-m-d'),
            'end_date' => null,
            'status' => 'active',
            'notes' => null,
        ];
    }

    public function checkedOut(): static
    {
        return $this->state(fn () => [
            'status' => 'checked_out',
            'end_date' => now()->subDay()->format('Y-m-d'),
        ]);
    }
}
