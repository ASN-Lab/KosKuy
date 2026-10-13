<?php

namespace Database\Factories;

use App\Models\KosListing;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'kos_listing_id' => KosListing::factory(),
            'amount' => fake()->numberBetween(5, 25) * 100000,
            'period' => now()->format('Y-m'),
            'status' => 'paid',
            'paid_at' => now()->format('Y-m-d'),
            'method' => fake()->randomElement(['Transfer', 'Tunai', 'QRIS']),
            'notes' => null,
        ];
    }

    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn () => [
            'tenant_id' => $tenant->id,
            'kos_listing_id' => $tenant->kos_listing_id,
            'amount' => $tenant->monthly_price,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => 'pending',
            'paid_at' => null,
        ]);
    }
}
