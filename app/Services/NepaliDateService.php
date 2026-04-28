<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\NepaliCalendarMap;
use Illuminate\Support\Facades\Cache;

class NepaliDateService
{
    private const REFERENCE_BS_YEAR = 2000;
    private const REFERENCE_BS_MONTH = 1;
    private const REFERENCE_BS_DAY = 1;

    // 2000-01-01 BS corresponds to 1943-04-14 AD
    private const REFERENCE_AD_DATE = '1943-04-14';

    private array $calendarMap = [];

    public function __construct()
    {
        $this->calendarMap = Cache::rememberForever('nepali_calendar_map', function () {
            return NepaliCalendarMap::orderBy('year')->get()->keyBy('year')->toArray();
        });
    }

    /**
     * Convert AD to BS
     */
    public function toBS(Carbon|string $adDate, string $format = 'Y-m-d'): string
    {
        if (is_string($adDate)) {
            $adDate = Carbon::parse($adDate);
        }

        // Force to midnight to avoid Daylight Saving Time issues
        $adDate->startOfDay();
        $refDate = Carbon::parse(self::REFERENCE_AD_DATE)->startOfDay();

        $diffDays = $refDate->diffInDays($adDate, false);

        if ($diffDays < 0) {
            throw new \InvalidArgumentException("Date cannot be before Reference AD Date (1943-04-14)");
        }

        $bsYear = self::REFERENCE_BS_YEAR;
        $bsMonth = self::REFERENCE_BS_MONTH;
        $bsDay = self::REFERENCE_BS_DAY;

        while ($diffDays > 0) {
            if (!isset($this->calendarMap[$bsYear])) {
                // Fallback to the package if DB map is missing
                return \Anuzpandey\LaravelNepaliDate\LaravelNepaliDate::from($adDate->toDateString())->toNepaliDate(format: $format);
            }

            $daysInMonth = $this->calendarMap[$bsYear]['months'][$bsMonth - 1];

            // If remaining days can fit in current month
            if ($bsDay + $diffDays <= $daysInMonth) {
                $bsDay += $diffDays;
                $diffDays = 0;
            } else {
                // Move to next month
                $diffDays -= ($daysInMonth - $bsDay + 1);
                $bsDay = 1;
                $bsMonth++;

                if ($bsMonth > 12) {
                    $bsMonth = 1;
                    $bsYear++;
                }
            }
        }

        return $this->formatDate($bsYear, $bsMonth, $bsDay, $format);
    }

    /**
     * Convert BS to AD
     */
    public function toAD(string $bsDateString): Carbon
    {
        // Expects YYYY-MM-DD
        [$targetYear, $targetMonth, $targetDay] = array_map('intval', explode('-', $bsDateString));

        if ($targetYear < self::REFERENCE_BS_YEAR) {
             throw new \InvalidArgumentException("Date cannot be before Reference BS Date (2000-01-01)");
        }

        $bsYear = self::REFERENCE_BS_YEAR;
        $bsMonth = self::REFERENCE_BS_MONTH;

        $totalDays = 0;

        while ($bsYear < $targetYear || ($bsYear === $targetYear && $bsMonth < $targetMonth)) {
            if (!isset($this->calendarMap[$bsYear])) {
                 // Fallback to the package if DB map is missing
                 return Carbon::parse(\Anuzpandey\LaravelNepaliDate\LaravelNepaliDate::from($bsDateString)->toEnglishDate());
            }

            $totalDays += $this->calendarMap[$bsYear]['months'][$bsMonth - 1];
            
            $bsMonth++;
            if ($bsMonth > 12) {
                $bsMonth = 1;
                $bsYear++;
            }
        }

        $totalDays += ($targetDay - 1);

        $refDate = Carbon::parse(self::REFERENCE_AD_DATE)->startOfDay();
        return $refDate->addDays($totalDays);
    }

    private function formatDate(int $year, int $month, int $day, string $format): string
    {
        // A simple formatter for Y-m-d.
        // For more complex formatting (like localized month names), we rely on the package.
        if ($format === 'Y-m-d') {
            return sprintf('%04d-%02d-%02d', $year, $month, $day);
        }

        // If complex format, delegate to package
        return \Anuzpandey\LaravelNepaliDate\LaravelNepaliDate::from(
            $this->toAD(sprintf('%04d-%02d-%02d', $year, $month, $day))->toDateString()
        )->toNepaliDate(format: $format);
    }
}
