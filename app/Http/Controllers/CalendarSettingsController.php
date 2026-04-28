<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NepaliCalendarMap;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;

class CalendarSettingsController extends Controller
{
    public function index(): Response
    {
        // Get the latest 10 mapped years, ordered descending
        $maps = NepaliCalendarMap::orderBy('year', 'desc')->take(10)->get();

        return Inertia::render('calendar-settings', [
            'maps' => $maps,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'unique:nepali_calendar_maps,year', 'min:1970', 'max:2200'],
            'months' => ['required', 'array', 'size:12'],
            'months.*' => ['required', 'integer', 'min:28', 'max:32'],
        ]);

        NepaliCalendarMap::create($validated);

        // Clear the cache since the map has changed
        Cache::forget('nepali_calendar_map');

        return redirect()->route('calendar-settings')->with('status', 'Calendar year added successfully.');
    }

    public function update(Request $request, NepaliCalendarMap $calendarMap): RedirectResponse
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'unique:nepali_calendar_maps,year,' . $calendarMap->id, 'min:1970', 'max:2200'],
            'months' => ['required', 'array', 'size:12'],
            'months.*' => ['required', 'integer', 'min:28', 'max:32'],
        ]);

        $calendarMap->update($validated);

        // Clear the cache since the map has changed
        Cache::forget('nepali_calendar_map');

        return redirect()->route('calendar-settings')->with('status', 'Calendar year updated successfully.');
    }
}
