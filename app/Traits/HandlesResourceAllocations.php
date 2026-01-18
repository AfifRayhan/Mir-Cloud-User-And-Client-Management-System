<?php

namespace App\Traits;

use App\Models\Customer;
use App\Models\Service;
use App\Models\Summary;
use Carbon\Carbon;

trait HandlesResourceAllocations
{
    /**
     * Update the summary table with latest service values for a customer
     */
    protected function updateCustomerSummary(int $customerId): void
    {
        $customer = Customer::find($customerId);
        if (! $customer) {
            return;
        }

        $resources = $customer->getCurrentResources();
        $services = Service::where('platform_id', $customer->platform_id)->get();

        foreach ($services as $service) {
            $pool = $resources[$service->id] ?? ['test' => 0, 'billable' => 0];

            Summary::updateOrCreate(
                ['customer_id' => $customerId, 'service_id' => $service->id],
                [
                    'test_quantity' => $pool['test'],
                    'billable_quantity' => $pool['billable'],
                ]
            );
        }
    }

    /**
     * Calculate the deadline based on working hours (8 business hours ahead)
     */
    protected function calculateDeadline(Carbon $assignmentTime): Carbon
    {
        $workStart = 9.5; // 9:30 AM
        $workEnd = 17.5;  // 5:30 PM
        $hoursToAdd = 8;

        $deadline = $assignmentTime->copy();

        while ($hoursToAdd > 0) {
            // Skip to next working day if currently on weekend (Friday/Saturday based on project logic)
            // Note: The previous logic used in controllers skipped Friday (5) and Saturday (6).
            while (in_array($deadline->dayOfWeek, [5, 6])) {
                $deadline->addDay()->setTime(9, 30, 0);
            }

            $currentHour = $deadline->hour + ($deadline->minute / 60);

            // If before work hours, move to start of work day
            if ($currentHour < $workStart) {
                $deadline->setTime(9, 30, 0);
                $currentHour = $workStart;
            }

            // If after work hours, move to next working day
            if ($currentHour >= $workEnd) {
                $deadline->addDay()->setTime(9, 30, 0);

                continue;
            }

            // Calculate remaining work hours today
            $remainingToday = $workEnd - $currentHour;

            if ($hoursToAdd <= $remainingToday) {
                // Can finish today
                $totalMinutes = ($currentHour + $hoursToAdd) * 60;
                $hours = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;
                $deadline->setTime((int) $hours, (int) $minutes, 0);
                $hoursToAdd = 0;
            } else {
                // Use remaining hours today, continue tomorrow
                $hoursToAdd -= $remainingToday;
                $deadline->addDay()->setTime(9, 30, 0);
            }
        }

        return $deadline;
    }
}
