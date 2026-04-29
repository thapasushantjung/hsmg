<?php

namespace App\Http\Controllers;

use App\Enums\CheckoutReason;
use App\Enums\TransferReason;
use App\Models\Bed;
use App\Models\Tenant;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BedActionController extends Controller
{
    public function available()
    {
        $beds = Bed::where('status', 'available')
            ->with('room.floor')
            ->get()
            ->map(function ($bed) {
                return [
                    'id' => $bed->id,
                    'name' => $bed->name,
                    'room_name' => $bed->room->name,
                    'floor_name' => $bed->room->floor->name,
                    'monthly_rate' => $bed->monthly_rate,
                ];
            });

        return response()->json($beds);
    }

    public function assign(Request $request, Bed $bed, BookingService $service)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'rent_amount' => 'required|numeric|min:0',
        ]);

        $tenant = Tenant::findOrFail($validated['tenant_id']);

        try {
            $service->checkIn($tenant, $bed, [
                'rent_amount' => $validated['rent_amount'],
            ]);

            return back()->with('success', 'Tenant assigned successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function checkout(Request $request, Bed $bed, BookingService $service)
    {
        $booking = $bed->currentAssignment?->booking;

        if (! $booking) {
            return back()->with('error', 'No active booking found for this bed.');
        }

        try {
            $service->checkOut($booking, CheckoutReason::EndOfStay);

            return back()->with('success', 'Tenant checked out successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function transfer(Request $request, Bed $bed, BookingService $service)
    {
        $validated = $request->validate([
            'new_bed_id' => 'required|exists:beds,id',
        ]);

        $newBed = Bed::findOrFail($validated['new_bed_id']);
        $booking = $bed->currentAssignment?->booking;

        if (! $booking) {
            return back()->with('error', 'No active booking found for this bed.');
        }

        try {
            $service->transferBed($booking, $newBed, TransferReason::RequestedChange);

            return back()->with('success', 'Tenant transferred successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
