<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NepaliCalendarMap;
use Anuzpandey\LaravelNepaliDate\Constants\NepaliDate;

class NepaliCalendarMapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $calendarData = NepaliDate::$CALENDAR_DATA;

        foreach ($calendarData as $data) {
            $year = array_shift($data); // first element is the year
            NepaliCalendarMap::updateOrCreate(
                ['year' => $year],
                ['months' => $data] // remaining elements are the 12 months
            );
        }
    }
}
