<?php

namespace Database\Factories;

use App\Models\Bed;
use App\Models\Booking;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'bed_id' => Bed::factory(),
            'check_in_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'status' => 'active',
            'deposit_amount' => 5000,
            'rent_amount' => 10000,
        ];
    }
}
