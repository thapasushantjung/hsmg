<?php

namespace App\Http\Controllers;

use App\Models\Tenant;

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
}
