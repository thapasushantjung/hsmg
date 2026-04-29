<?php

namespace App\Http\Controllers;

use App\Models\Floor;

class BedGridController extends Controller
{
    public function index()
    {
        $floors = Floor::with(['rooms.beds.currentAssignment.booking.tenant'])->get();

        return inertia('bed-grid', [
            'floors' => $floors,
        ]);
    }
}
