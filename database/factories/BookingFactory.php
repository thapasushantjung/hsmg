<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
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
        $checkInDate = fake()->dateTimeBetween('-1 year', 'now');

        return [
            'tenant_id' => Tenant::factory(),
            'check_in_date' => $checkInDate->format('Y-m-d'),
            'checked_in_at' => $checkInDate,
            'status' => BookingStatus::Active,
            'deposit_amount' => 5000,
            'rent_amount' => 10000,
        ];
    }

    /**
     * Mark the booking as completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'actual_check_out_date' => now()->toDateString(),
            'checked_out_at' => now(),
            'status' => BookingStatus::Completed,
        ]);
    }
}
