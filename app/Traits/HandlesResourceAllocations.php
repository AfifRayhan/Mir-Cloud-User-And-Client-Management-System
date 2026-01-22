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
    use CalculatesDeadlines;
}
