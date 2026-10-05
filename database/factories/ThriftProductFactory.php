<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ThriftProduct>
 */
class ThriftProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'seller_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->unique()->words(3, true),
            'description' => fake()->paragraphs(2, true),
            'price' => fake()->numberBetween(1, 50) * 10000,
            'condition_status' => fake()->randomElement([
                '90% - Like New',
                '80% - Layak Pakai',
                '70% - Ada Cacat Ringan',
            ]),
            'is_sold' => false,
        ];
    }

    public function sold(): static
    {
        return $this->state(fn () => ['is_sold' => true]);
    }
}