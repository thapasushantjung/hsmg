<?php

namespace Database\Factories;

use App\Models\Bed;
use App\Models\BedAssignment;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BedAssignment>
 */
class BedAssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'bed_id' => Bed::factory(),
            'started_at' => now(),
        ];
    }
}
