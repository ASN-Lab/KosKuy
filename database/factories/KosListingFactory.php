<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\KosListing>
 */
class KosListingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'title' => fake()->unique()->words(3, true),
            'description' => fake()->paragraphs(2, true),
            'price_per_month' => fake()->numberBetween(500, 400) * 100000,
            'gender_type' => fake()->randomElement(['putra', 'putri', 'campur']),
            'latitude' => fake()->latitude(-6.4, -6.1),
            'longitude' => fake()->longitude(106.6, 106.9),
            'address' => fake()->streetAddress().', '.fake()->city(),
            'is_available' => true,
        ];
    }

    public function putra(): static
    {
        return $this->state(fn () => ['gender_type' => 'putra']);
    }

    public function putri(): static
    {
        return $this->state(fn () => ['gender_type' => 'putri']);
    }

    public function unavailable(): static
    {
        return $this->state(fn () => ['is_available' => false]);
    }

    public function withFacilities(array $facilities = ['WiFi', 'AC', 'Kamar mandi dalam']): static
    {
        return $this->afterCreating(function ($kos) use ($facilities) {
            foreach ($facilities as $facility) {
                $kos->facilities()->create(['facility_name' => $facility]);
            }
        });
    }
}