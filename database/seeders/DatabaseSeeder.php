<?php

namespace Database\Seeders;

use App\Enums\TenantLogAction;
use App\Models\Bed;
use App\Models\BedAssignment;
use App\Models\Booking;
use App\Models\Floor;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\TenantLog;
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

        // Create active tenants with bookings and bed assignments
        $beds = Bed::inRandomOrder()->limit(10)->get();
        foreach ($beds as $bed) {
            /** @var \App\Models\Bed $bed */
            $tenant = Tenant::factory()->create();
            $checkInDate = fake()->dateTimeBetween('-6 months', '-1 month');

            $booking = Booking::factory()->create([
                'tenant_id' => $tenant->id,
                'check_in_date' => $checkInDate->format('Y-m-d'),
                'checked_in_at' => $checkInDate,
            ]);

            BedAssignment::create([
                'booking_id' => $booking->id,
                'bed_id' => $bed->id,
                'started_at' => $checkInDate,
            ]);

            $bed->update(['status' => 'occupied']);

            TenantLog::create([
                'tenant_id' => $tenant->id,
                'booking_id' => $booking->id,
                'action' => TenantLogAction::CheckedIn,
                'description' => "Checked into {$bed->name}",
            ]);

            // Create initial deposit payment
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

        // Create a couple of completed bookings with transfer history for demo
        $availableBeds = Bed::where('status', 'available')->inRandomOrder()->limit(4)->get();
        if ($availableBeds->count() >= 4) {
            // Tenant with a transfer history (completed stay)
            $tenant = Tenant::factory()->create();
            $firstBed = $availableBeds[0];
            $secondBed = $availableBeds[1];

            $booking = Booking::factory()->completed()->create([
                'tenant_id' => $tenant->id,
                'check_in_date' => now()->subMonths(4)->toDateString(),
                'checked_in_at' => now()->subMonths(4),
                'actual_check_out_date' => now()->subMonth()->toDateString(),
                'checked_out_at' => now()->subMonth(),
            ]);

            // First bed assignment (ended)
            BedAssignment::create([
                'booking_id' => $booking->id,
                'bed_id' => $firstBed->id,
                'started_at' => now()->subMonths(4),
                'ended_at' => now()->subMonths(2),
                'reason' => 'upgrade',
            ]);

            // Second bed assignment (ended at checkout)
            BedAssignment::create([
                'booking_id' => $booking->id,
                'bed_id' => $secondBed->id,
                'started_at' => now()->subMonths(2),
                'ended_at' => now()->subMonth(),
            ]);

            TenantLog::create([
                'tenant_id' => $tenant->id,
                'booking_id' => $booking->id,
                'action' => TenantLogAction::CheckedIn,
                'description' => "Checked into {$firstBed->name}",
                'created_at' => now()->subMonths(4),
            ]);

            TenantLog::create([
                'tenant_id' => $tenant->id,
                'booking_id' => $booking->id,
                'action' => TenantLogAction::Transferred,
                'description' => "Transferred from {$firstBed->name} to {$secondBed->name} (upgrade)",
                'created_at' => now()->subMonths(2),
            ]);

            TenantLog::create([
                'tenant_id' => $tenant->id,
                'booking_id' => $booking->id,
                'action' => TenantLogAction::CheckedOut,
                'description' => 'Checked out (end_of_stay)',
                'created_at' => now()->subMonth(),
            ]);
        }
    }
}
