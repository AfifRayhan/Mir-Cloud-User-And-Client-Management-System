<?php

namespace App\Traits;

use Carbon\Carbon;

trait CalculatesDeadlines
{
    /**
     * Calculate deadline based on working hours (Sun-Thu, 9:30 AM - 5:30 PM).
     * Duration: 8 hours.
     *
     * @return Carbon
     */
    protected function calculateDeadline(Carbon $startTime): Carbon
    {
        $start = $startTime->copy();
        $remainingMinutes = 8 * 60; // 8 hours in minutes

        $workingDayStart = 9 * 60 + 30; // 9:30 AM in minutes
        $workingDayEnd = 17 * 60 + 30;  // 5:30 PM in minutes
        // $workingDayDuration = $workingDayEnd - $workingDayStart; // 480 minutes (8 hours) - Unused variable

        while ($remainingMinutes > 0) {
            // Check if current day is a working day (Sunday=0, Thursday=4)
            // Friday=5, Saturday=6 are off days
            $isWorkingDay = ! in_array($start->dayOfWeek, [5, 6]); // 5=Friday, 6=Saturday

            if (! $isWorkingDay) {
                // Move to next day 9:30 AM
                $start->addDay()->setTime(9, 30, 0);

                continue;
            }

            // Current time in minutes from start of day
            $currentTimeMinutes = $start->hour * 60 + $start->minute;

            // If current time is past working hours, move to next working day
            if ($currentTimeMinutes >= $workingDayEnd) {
                $start->addDay()->setTime(9, 30, 0);

                continue;
            }

            // If current time is before working hours, set to working hours start
            if ($currentTimeMinutes < $workingDayStart) {
                $start->setTime(9, 30, 0);
                $currentTimeMinutes = $workingDayStart;
            }

            // Calculate available minutes in current day
            $availableMinutes = $workingDayEnd - $currentTimeMinutes;

            if ($remainingMinutes <= $availableMinutes) {
                // Can finish in this day
                $start->addMinutes($remainingMinutes);
                $remainingMinutes = 0;
            } else {
                // Use all available minutes and move to next day
                $remainingMinutes -= $availableMinutes;
                $start->addDay()->setTime(9, 30, 0);
            }
        }

        return $start;
    }
}
