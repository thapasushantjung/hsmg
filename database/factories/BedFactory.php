<?php

namespace Database\Factories;

use App\Models\Bed;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bed>
 */
class BedFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'name' => $this->faker->randomElement(['A', 'B', 'C', 'D']),
            'status' => 'available',
            'monthly_rate' => $this->faker->numberBetween(5000, 15000),
        ];
    }
}
