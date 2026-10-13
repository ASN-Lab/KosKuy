<?php

namespace Database\Factories;

use App\Models\KosListing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\KosRoom>
 */
class KosRoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kos_listing_id' => KosListing::factory(),
            'room_number' => (string) fake()->unique()->numberBetween(1, 40),
            'price_per_month' => fake()->numberBetween(5, 25) * 100000,
            'notes' => null,
        ];
    }
}
