<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\CheckoutReason;
use App\Enums\TenantLogAction;
use App\Enums\TransferReason;
use App\Models\Bed;
use App\Models\BedAssignment;
use App\Models\Booking;
use App\Models\Tenant;
use App\Models\TenantLog;
use Illuminate\Support\Facades\DB;

class BookingService
{
    /**
     * Check a tenant into a bed.
     *
     * Creates a booking (stay), the first bed assignment, and an audit log entry.
     *
     * @param  array{rent_amount: numeric-string|float, deposit_amount?: numeric-string|float, expected_check_out_date?: string|null, notes?: string|null}  $data
     */
    public function checkIn(Tenant $tenant, Bed $bed, array $data): Booking
    {
        return DB::transaction(function () use ($tenant, $bed, $data) {
            $booking = Booking::create([
                'tenant_id' => $tenant->id,
                'check_in_date' => now()->toDateString(),
                'checked_in_at' => now(),
                'expected_check_out_date' => $data['expected_check_out_date'] ?? null,
                'status' => BookingStatus::Active,
                'rent_amount' => $data['rent_amount'],
                'deposit_amount' => $data['deposit_amount'] ?? 0,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            BedAssignment::create([
                'booking_id' => $booking->id,
                'bed_id' => $bed->id,
                'started_at' => now(),
            ]);

            $bed->update(['status' => 'occupied']);

            TenantLog::create([
                'tenant_id' => $tenant->id,
                'booking_id' => $booking->id,
                'action' => TenantLogAction::CheckedIn,
                'description' => "Checked into {$bed->name}",
            ]);

            return $booking->load('currentBedAssignment.bed');
        });
    }

    /**
     * Transfer a tenant to a different bed without ending the stay.
     */
    public function transferBed(Booking $booking, Bed $newBed, ?TransferReason $reason = null): BedAssignment
    {
        return DB::transaction(function () use ($booking, $newBed, $reason) {
            $currentAssignment = $booking->bedAssignments()
                ->current()
                ->firstOrFail();

            $oldBed = $currentAssignment->bed;

            $currentAssignment->update([
                'ended_at' => now(),
                'reason' => $reason?->value,
            ]);

            $newAssignment = BedAssignment::create([
                'booking_id' => $booking->id,
                'bed_id' => $newBed->id,
                'started_at' => now(),
            ]);

            $oldBed->update(['status' => 'available']);
            $newBed->update(['status' => 'occupied']);

            $booking->update(['updated_by' => auth()->id()]);

            TenantLog::create([
                'tenant_id' => $booking->tenant_id,
                'booking_id' => $booking->id,
                'action' => TenantLogAction::Transferred,
                'description' => "Transferred from {$oldBed->name} to {$newBed->name}"
                    .($reason ? " ({$reason->value})" : ''),
            ]);

            return $newAssignment->load('bed');
        });
    }

    /**
     * Check out a tenant — closes the current bed assignment and completes the booking.
     */
    public function checkOut(Booking $booking, ?CheckoutReason $checkoutReason = null, ?string $notes = null): Booking
    {
        return DB::transaction(function () use ($booking, $checkoutReason, $notes) {
            $currentAssignment = $booking->bedAssignments()
                ->current()
                ->first();

            if ($currentAssignment) {
                $currentAssignment->update(['ended_at' => now()]);
                $currentAssignment->bed->update(['status' => 'available']);
            }

            $booking->update([
                'actual_check_out_date' => now()->toDateString(),
                'checked_out_at' => now(),
                'status' => BookingStatus::Completed,
                'checkout_reason' => $checkoutReason,
                'notes' => $notes ?? $booking->notes,
                'updated_by' => auth()->id(),
            ]);

            TenantLog::create([
                'tenant_id' => $booking->tenant_id,
                'booking_id' => $booking->id,
                'action' => TenantLogAction::CheckedOut,
                'description' => 'Checked out'
                    .($checkoutReason ? " ({$checkoutReason->value})" : ''),
            ]);

            return $booking->refresh();
        });
    }

    /**
     * Cancel a booking — closes assignment, marks cancelled, logs event.
     */
    public function cancel(Booking $booking, ?string $notes = null): Booking
    {
        return DB::transaction(function () use ($booking, $notes) {
            $currentAssignment = $booking->bedAssignments()
                ->current()
                ->first();

            if ($currentAssignment) {
                $currentAssignment->update(['ended_at' => now()]);
                $currentAssignment->bed->update(['status' => 'available']);
            }

            $booking->update([
                'status' => BookingStatus::Cancelled,
                'notes' => $notes ?? $booking->notes,
                'updated_by' => auth()->id(),
            ]);

            TenantLog::create([
                'tenant_id' => $booking->tenant_id,
                'booking_id' => $booking->id,
                'action' => TenantLogAction::Cancelled,
                'description' => 'Booking cancelled'.($notes ? ": {$notes}" : ''),
            ]);

            return $booking->refresh();
        });
    }
}
