<?php

use App\Enums\BookingStatus;
use App\Enums\CheckoutReason;
use App\Enums\TenantLogAction;
use App\Enums\TransferReason;
use App\Models\Bed;
use App\Models\Tenant;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('checks a tenant into a bed', function () {
    $tenant = Tenant::factory()->create();
    $bed = Bed::factory()->create(['status' => 'available']);
    $user = User::factory()->create();
    $this->actingAs($user);

    $service = new BookingService;
    $booking = $service->checkIn($tenant, $bed, [
        'rent_amount' => 10000,
        'deposit_amount' => 5000,
    ]);

    expect($booking->status)->toBe(BookingStatus::Active)
        ->and((float) $booking->rent_amount)->toBe(10000.0) // cast to float to ignore decimal formatting exactness if not refreshed
        ->and($booking->created_by)->toBe($user->id);

    $this->assertDatabaseHas('bed_assignments', [
        'booking_id' => $booking->id,
        'bed_id' => $bed->id,
        'ended_at' => null,
    ]);

    $this->assertDatabaseHas('beds', [
        'id' => $bed->id,
        'status' => 'occupied',
    ]);

    $this->assertDatabaseHas('tenant_logs', [
        'tenant_id' => $tenant->id,
        'booking_id' => $booking->id,
        'action' => TenantLogAction::CheckedIn->value,
    ]);
});

it('transfers a tenant to a new bed', function () {
    $tenant = Tenant::factory()->create();
    $oldBed = Bed::factory()->create(['status' => 'available']);
    $newBed = Bed::factory()->create(['status' => 'available']);

    $user = User::factory()->create();
    $this->actingAs($user);

    $service = new BookingService;
    $booking = $service->checkIn($tenant, $oldBed, ['rent_amount' => 10000]);

    // Transfer
    $service->transferBed($booking, $newBed, TransferReason::Upgrade);

    $this->assertDatabaseHas('bed_assignments', [
        'booking_id' => $booking->id,
        'bed_id' => $oldBed->id,
    ]);

    $oldAssignment = $booking->bedAssignments()->where('bed_id', $oldBed->id)->first();
    expect($oldAssignment->ended_at)->not->toBeNull()
        ->and($oldAssignment->reason)->toBe(TransferReason::Upgrade->value);

    $this->assertDatabaseHas('bed_assignments', [
        'booking_id' => $booking->id,
        'bed_id' => $newBed->id,
        'ended_at' => null,
    ]);

    $this->assertDatabaseHas('beds', [
        'id' => $oldBed->id,
        'status' => 'available',
    ]);

    $this->assertDatabaseHas('beds', [
        'id' => $newBed->id,
        'status' => 'occupied',
    ]);

    $this->assertDatabaseHas('tenant_logs', [
        'booking_id' => $booking->id,
        'action' => TenantLogAction::Transferred->value,
    ]);
});

it('checks out a tenant', function () {
    $tenant = Tenant::factory()->create();
    $bed = Bed::factory()->create(['status' => 'available']);
    $user = User::factory()->create();
    $this->actingAs($user);

    $service = new BookingService;
    $booking = $service->checkIn($tenant, $bed, ['rent_amount' => 10000]);

    $service->checkOut($booking, CheckoutReason::EndOfStay, 'Leaving city');

    expect($booking->status)->toBe(BookingStatus::Completed)
        ->and($booking->checkout_reason)->toBe(CheckoutReason::EndOfStay)
        ->and($booking->notes)->toBe('Leaving city');

    $this->assertDatabaseHas('beds', [
        'id' => $bed->id,
        'status' => 'available',
    ]);

    $assignment = $booking->bedAssignments()->first();
    expect($assignment->ended_at)->not->toBeNull();

    $this->assertDatabaseHas('tenant_logs', [
        'booking_id' => $booking->id,
        'action' => TenantLogAction::CheckedOut->value,
    ]);
});

it('cancels a booking', function () {
    $tenant = Tenant::factory()->create();
    $bed = Bed::factory()->create(['status' => 'available']);
    $user = User::factory()->create();
    $this->actingAs($user);

    $service = new BookingService;
    $booking = $service->checkIn($tenant, $bed, ['rent_amount' => 10000]);

    $service->cancel($booking, 'Changed mind');

    expect($booking->status)->toBe(BookingStatus::Cancelled)
        ->and($booking->notes)->toBe('Changed mind');

    $this->assertDatabaseHas('beds', [
        'id' => $bed->id,
        'status' => 'available',
    ]);

    $assignment = $booking->bedAssignments()->first();
    expect($assignment->ended_at)->not->toBeNull();

    $this->assertDatabaseHas('tenant_logs', [
        'booking_id' => $booking->id,
        'action' => TenantLogAction::Cancelled->value,
    ]);
});
