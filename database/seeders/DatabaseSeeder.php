<?php

namespace Database\Seeders;

use App\Models\Bed;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Booking;
use App\Models\Floor;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $floors = Floor::factory(3)->create();

        foreach ($floors as $floor) {
            $rooms = Room::factory(5)->create(['floor_id' => $floor->id]);
            foreach ($rooms as $room) {
                Bed::factory($room->capacity)->create(['room_id' => $room->id]);
            }
        }

        // Create some tenants and active bookings
        $beds = Bed::inRandomOrder()->limit(10)->get();
        foreach ($beds as $bed) {
            $tenant = Tenant::factory()->create();
            $booking = Booking::factory()->create([
                'tenant_id' => $tenant->id,
                'bed_id' => $bed->id,
            ]);
            $bed->update(['status' => 'occupied']);

            // Create initial payment
            Payment::factory()->create([
                'booking_id' => $booking->id,
                'tenant_id' => $tenant->id,
                'amount' => $booking->deposit_amount,
                'type' => 'deposit',
                'status' => 'paid',
                'due_date' => $booking->check_in_date,
                'paid_date' => $booking->check_in_date,
            ]);
        }
    }
}
