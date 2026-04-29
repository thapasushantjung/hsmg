<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Tenant;
use App\Services\BookingService;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with(['activeBooking.currentBedAssignment.bed.room.floor'])->latest()->get();

        $tenants->each(function (Tenant $tenant) {
            $tenant->append('full_name');
        });

        return inertia('tenants', [
            'tenants' => $tenants,
        ]);
    }

    public function create(Request $request)
    {
        $bedId = $request->query('bed_id');
        $bed = null;
        if ($bedId) {
            $bed = Bed::with('room.floor')->find($bedId);
        }
        
        $availableBeds = Bed::where('status', 'available')->with('room.floor')->get();

        return inertia('tenants/create', [
            'initialBed' => $bed,
            'availableBeds' => $availableBeds,
        ]);
    }

    public function store(Request $request, BookingService $bookingService)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'phone' => 'required|string|max:255',
            'bed_id' => 'required|exists:beds,id',
            'rent_amount' => 'required|numeric|min:0',
        ]);

        $tenant = Tenant::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'gender' => $validated['gender'],
            'phone' => $validated['phone'],
        ]);

        $bed = Bed::findOrFail($validated['bed_id']);

        $bookingService->checkIn($tenant, $bed, [
            'rent_amount' => $validated['rent_amount'],
        ]);

        return redirect()->route('tenants')->with('success', 'Tenant created and assigned to bed successfully.');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['activeBooking.currentBedAssignment.bed.room.floor']);
        return inertia('tenants/show', [
            'tenant' => $tenant,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        if (! $query) {
            return response()->json([]);
        }

        $tenants = Tenant::where('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function ($tenant) {
                return [
                    'id' => $tenant->id,
                    'full_name' => $tenant->full_name,
                    'phone' => $tenant->phone,
                ];
            });

        return response()->json($tenants);
    }
}
