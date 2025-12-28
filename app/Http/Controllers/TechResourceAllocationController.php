<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerStatus;
use App\Models\ResourceDowngradation;
use App\Models\ResourceDowngradationDetail;
use App\Models\ResourceUpgradation;
use App\Models\ResourceUpgradationDetail;
use App\Models\Service;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TechResourceAllocationController extends Controller
{
    public function index(): View
    {
        $customersRaw = Customer::orderBy('customer_name')->get();
        $customers = collect();

        // Group by name to identify duplicates (same logic as ResourceAllocationController)
        $grouped = $customersRaw->groupBy('customer_name');
        foreach ($grouped as $name => $group) {
            if ($group->count() > 1) {
                $index = 1;
                foreach ($group as $customer) {
                    $customer->customer_name = $customer->customer_name.'-'.$index;
                    if (! $customer->hasResourceAllocations()) {
                        $customer->customer_name .= ' (No Resources)';
                    }
                    $customers->push($customer);
                    $index++;
                }
            } else {
                if (! $group->first()->hasResourceAllocations()) {
                    $group->first()->customer_name .= ' (No Resources)';
                }
                $customers->push($group->first());
            }
        }
        $customers = $customers->sortBy('customer_name', SORT_NATURAL | SORT_FLAG_CASE);

        $customerStatuses = CustomerStatus::all();

        // Get all KAM, Pro-KAM, and Admin users
        $kams = User::whereHas('role', function ($q) {
            $q->whereIn('role_name', ['kam', 'pro-kam', 'admin']);
        })->orderBy('name')->get();

        return view('tech-resource-allocation.index', compact('customers', 'customerStatuses', 'kams'));
    }

    public function allocationForm(Request $request, Customer $customer)
    {
        $actionType = $request->query('action_type');
        $statusId = $request->query('status_id');

        $services = Service::all();
        $taskStatuses = TaskStatus::all();

        // Check if this is the first allocation
        $isFirstAllocation = ! $customer->hasResourceAllocations();

        // If first allocation and no status selected, default to "Test"
        if ($isFirstAllocation && ! $statusId) {
            $testStatus = CustomerStatus::where('name', 'Test')->first();
            $statusId = $testStatus ? $testStatus->id : null;
        }

        // For upgrades and downgrades, get the most recent status from upgradations if not provided
        if (($actionType === 'upgrade' || $actionType === 'downgrade') && ! $statusId) {
            $latestUpgradation = ResourceUpgradation::where('customer_id', $customer->id)
                ->orderBy('created_at', 'desc')
                ->first();
            $statusId = $latestUpgradation ? $latestUpgradation->status_id : null;
        }

        // Get default task status "Proceed from KAM" for first allocation
        $defaultTaskStatusId = null;
        if ($isFirstAllocation) {
            $proceedFromKAM = TaskStatus::where('name', 'Proceed from KAM')->first();
            $defaultTaskStatusId = $proceedFromKAM ? $proceedFromKAM->id : null;
        }

        $statusName = null;
        if ($statusId) {
            $status = CustomerStatus::find($statusId);
            $statusName = $status ? $status->name : null;
        }

        // We'll reuse the same partial but might need to adjust it if we want custom styling there
        // For now, let's use the same partial.
        $html = view('resource-allocation.partials.allocation-form', compact('customer', 'services', 'actionType', 'statusId', 'statusName', 'taskStatuses', 'isFirstAllocation', 'defaultTaskStatusId'))->render();

        return response()->json([
            'html' => $html,
            'status_id' => $statusId,
            'customer_name' => $customer->customer_name,
            'action_type' => $actionType,
        ]);
    }

    public function storeAllocation(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'kam_id' => 'required|exists:users,id',
            'action_type' => 'required|in:upgrade,downgrade',
            'status_id' => $request->action_type === 'upgrade' ? 'required|exists:customer_statuses,id' : 'nullable|exists:customer_statuses,id',
            'activation_date' => $request->action_type === 'upgrade' ? 'required|date|after_or_equal:'.$customer->activation_date->format('Y-m-d') : 'nullable|date',
            'services' => 'nullable|array',
            'services.*' => 'nullable|integer|min:0',
        ]);

        $actionType = $validated['action_type'];
        $servicesInput = $validated['services'] ?? [];
        $kamId = $validated['kam_id'];

        // Filter out null and zero values
        $servicesInput = array_filter($servicesInput, function ($value) {
            return ! is_null($value) && $value > 0;
        });

        if (empty($servicesInput)) {
            return response()->json([
                'success' => false,
                'message' => 'Please specify at least one resource change with a value greater than 0.',
                'errors' => ['services' => ['Please specify at least one resource change with a value greater than 0.']],
            ], 422);
        }

        return DB::transaction(function () use ($customer, $validated, $servicesInput, $actionType, $kamId) {
            $lockedCustomer = Customer::where('id', $customer->id)->lockForUpdate()->first();
            $techUser = Auth::user();

            // Task Status should be marked as "Proceed from Tech" (id 3 based on MyTaskController)
            $taskStatusId = 3;

            $resourceId = null;
            if ($actionType === 'upgrade') {
                $upgradation = ResourceUpgradation::create([
                    'customer_id' => $lockedCustomer->id,
                    'status_id' => $validated['status_id'],
                    'activation_date' => $validated['activation_date'],
                    'inactivation_date' => '3000-01-01',
                    'task_status_id' => $taskStatusId,
                    'inserted_by' => $techUser->id,
                ]);
                $resourceId = $upgradation->id;

                $lockedCustomer->update(['activation_date' => $validated['activation_date']]);

                foreach ($servicesInput as $serviceId => $increaseAmount) {
                    $service = Service::find($serviceId);
                    if (! $service) {
                        continue;
                    }
                    $currentValue = $lockedCustomer->getResourceQuantity($service->service_name);
                    $newValue = $currentValue + $increaseAmount;

                    ResourceUpgradationDetail::create([
                        'resource_upgradation_id' => $upgradation->id,
                        'service_id' => $serviceId,
                        'quantity' => $newValue,
                        'upgrade_amount' => $increaseAmount,
                    ]);
                }
            } else {
                $latestUpgradation = ResourceUpgradation::where('customer_id', $lockedCustomer->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
                $statusId = $latestUpgradation ? $latestUpgradation->status_id : null;

                $downgradation = ResourceDowngradation::create([
                    'customer_id' => $lockedCustomer->id,
                    'status_id' => $statusId,
                    'activation_date' => $validated['activation_date'] ?? now(),
                    'inactivation_date' => '3000-01-01',
                    'task_status_id' => $taskStatusId,
                    'inserted_by' => $techUser->id,
                ]);
                $resourceId = $downgradation->id;

                foreach ($servicesInput as $serviceId => $reductionAmount) {
                    $service = Service::find($serviceId);
                    if (! $service) {
                        continue;
                    }
                    $currentValue = $lockedCustomer->getResourceQuantity($service->service_name);
                    $newValue = max(0, $currentValue - $reductionAmount);

                    ResourceDowngradationDetail::create([
                        'resource_downgradation_id' => $downgradation->id,
                        'service_id' => $serviceId,
                        'quantity' => $newValue,
                        'downgrade_amount' => $reductionAmount,
                    ]);
                }
            }

            // Create Task (wait for VDC assignment to mark as complete)
            $task = Task::create([
                'customer_id' => $lockedCustomer->id,
                'status_id' => ($actionType === 'upgrade' ? $validated['status_id'] : $statusId),
                'task_status_id' => $taskStatusId,
                'activation_date' => $validated['activation_date'] ?? now(),
                'allocation_type' => $actionType,
                'resource_upgradation_id' => ($actionType === 'upgrade' ? $resourceId : null),
                'resource_downgradation_id' => ($actionType === 'downgrade' ? $resourceId : null),
                'assigned_to' => $techUser->id,
                'assigned_by' => $kamId,
                'assigned_at' => now(),
                'completed_at' => null, // Will be filled by VDC selection
            ]);

            // Re-sync summary table
            $this->updateCustomerSummary($lockedCustomer->id);

            return response()->json([
                'success' => true,
                'message' => 'Allocation created. Please select a VDC to finalize.',
                'task_id' => $task->id,
                'customer_id' => $lockedCustomer->id,
            ]);
        });
    }

    protected function updateCustomerSummary(int $customerId): void
    {
        $customer = Customer::find($customerId);
        if (! $customer) {
            return;
        }
        $services = Service::all();
        foreach ($services as $service) {
            $quantity = $customer->getResourceQuantity($service->service_name);
            \App\Models\Summary::updateOrCreate(
                ['customer_id' => $customerId, 'service_id' => $service->id],
                ['quantity' => $quantity]
            );
        }
    }
}
