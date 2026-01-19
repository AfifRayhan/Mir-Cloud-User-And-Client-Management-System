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
    use \App\Traits\HandlesResourceAllocations;

    public function index(): View
    {
        $customersRaw = Customer::accessibleBy(Auth::user())
            ->with(['submitter.role'])
            ->withExists(['resourceUpgradations', 'resourceDowngradations'])
            ->orderBy('customer_name')
            ->get();

        $customers = collect();

        // Group by name to identify duplicates (same logic as ResourceAllocationController)
        $grouped = $customersRaw->groupBy('customer_name');
        foreach ($grouped as $name => $group) {
            foreach ($group as $index => $customer) {
                $customer->is_new = ! ($customer->resource_upgradations_exists || $customer->resource_downgradations_exists);

                if ($group->count() > 1) {
                    $customer->customer_name = $customer->customer_name.'-'.($index + 1);
                }

                if ($customer->is_new) {
                    $customer->customer_name .= ' (No Resources)';
                }

                $customers->push($customer);
            }
        }
        $customers = $customers->sortBy('customer_name', SORT_NATURAL | SORT_FLAG_CASE);

        $customerStatuses = CustomerStatus::whereIn('name', ['Test', 'Billable'])->get();

        return view('tech-resource-allocation.index', compact('customers', 'customerStatuses'));
    }

    public function allocationForm(Request $request, Customer $customer)
    {
        if (! Customer::accessibleBy(Auth::user())->where('id', $customer->id)->exists()) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        $actionType = $request->query('action_type');
        $statusId = $request->query('status_id');

        $services = Service::where('platform_id', $customer->platform_id)->get();
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

        $testStatusId = CustomerStatus::where('name', 'Test')->first()?->id ?? 1;

        // Determine default Activation Date
        // If Customer Activation Date is in the past -> Default to Current Date.
        // If Customer Activation Date is in the future -> Default to Customer Activation Date.
        $customerActivationDate = $customer->customer_activation_date;
        $defaultActivationDate = $customerActivationDate->isFuture()
            ? $customerActivationDate->format('Y-m-d')
            : now()->format('Y-m-d');

        // We'll reuse the same partial but might need to adjust it if we want custom styling there
        // For now, let's use the same partial.
        $html = view('resource-allocation.partials.allocation-form', compact('customer', 'services', 'actionType', 'statusId', 'statusName', 'taskStatuses', 'isFirstAllocation', 'defaultTaskStatusId', 'testStatusId', 'defaultActivationDate'))->render();

        return response()->json([
            'html' => $html,
            'status_id' => $statusId,
            'test_status_id' => $testStatusId,
            'customer_name' => $customer->customer_name,
            'action_type' => $actionType,
        ]);
    }

    public function storeAllocation(Request $request, Customer $customer)
    {
        if (! Customer::accessibleBy(Auth::user())->where('id', $customer->id)->exists()) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        // Fetch services to map IDs to Names for validation attributes
        $services = Service::where('platform_id', $customer->platform_id)->get();
        $attributes = [];
        foreach ($services as $service) {
            $attributes['services.'.$service->id] = $service->service_name;
        }

        $validated = $request->validate([
            'action_type' => 'required|in:upgrade,downgrade',
            'status_id' => 'required|exists:customer_statuses,id',
            'activation_date' => 'required|date',
            'inactivation_date' => 'nullable|date',
            'services' => 'required|array',
            'services.*' => 'nullable|integer|min:0',
        ], [], $attributes);

        $actionType = $validated['action_type'];
        $statusId = $validated['status_id'];
        $servicesInput = $validated['services'] ?? [];
        $kamId = $customer->submitted_by;

        if (! $kamId) {
            // Fallback to current user if for some reason submitted_by is null
            $kamId = Auth::id();
        }

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

        return DB::transaction(function () use ($customer, $validated, $servicesInput, $actionType, $kamId, $statusId) {
            $lockedCustomer = Customer::where('id', $customer->id)->lockForUpdate()->first();
            $techUser = Auth::user();

            // Task Status should be marked as "Proceed from Tech" (id 3 based on MyTaskController)
            $taskStatusId = 3;

            $testStatusId = \App\Models\CustomerStatus::where('name', 'Test')->first()?->id ?? 1;
            $isTestInput = $statusId == $testStatusId;

            // Calculate assignment and deadline datetimes
            $activationDate = \Carbon\Carbon::parse($validated['activation_date']);
            $now = \Carbon\Carbon::now();

            if ($activationDate->isToday()) {
                $assignmentDatetime = $now;
            } else {
                $assignmentDatetime = $activationDate->copy()->setTime(9, 30);
            }
            $deadlineDatetime = $this->calculateDeadline($assignmentDatetime);

            $resourceId = null;
            if ($actionType === 'upgrade') {
                $upgradation = ResourceUpgradation::create([
                    'customer_id' => $lockedCustomer->id,
                    'status_id' => $validated['status_id'],
                    'activation_date' => $validated['activation_date'],
                    'inactivation_date' => $validated['inactivation_date'] ?? '3000-01-01',
                    'task_status_id' => $taskStatusId,
                    'inserted_by' => $techUser->id,
                    'assignment_datetime' => $assignmentDatetime,
                    'deadline_datetime' => $deadlineDatetime,
                ]);
                $resourceId = $upgradation->id;

                $isTest = $validated['status_id'] == $testStatusId;

                foreach ($servicesInput as $serviceId => $increaseAmount) {
                    $service = Service::find($serviceId);
                    if (! $service) {
                        continue;
                    }

                    $currentPoolValue = $isTest
                        ? $lockedCustomer->getResourceTestQuantity($service->service_name)
                        : $lockedCustomer->getResourceBillableQuantity($service->service_name);

                    $newValue = $currentPoolValue + $increaseAmount;

                    ResourceUpgradationDetail::create([
                        'resource_upgradation_id' => $upgradation->id,
                        'service_id' => $serviceId,
                        'quantity' => $newValue,
                        'upgrade_amount' => $increaseAmount,
                    ]);
                }
            } else {
                $downgradation = ResourceDowngradation::create([
                    'customer_id' => $lockedCustomer->id,
                    'status_id' => $statusId,
                    'activation_date' => $validated['activation_date'],
                    'inactivation_date' => $validated['inactivation_date'] ?? '3000-01-01',
                    'task_status_id' => $taskStatusId,
                    'inserted_by' => $techUser->id,
                    'assignment_datetime' => $assignmentDatetime,
                    'deadline_datetime' => $deadlineDatetime,
                ]);
                $resourceId = $downgradation->id;

                $testStatusId = \App\Models\CustomerStatus::where('name', 'Test')->first()?->id ?? 1;
                $isTest = $statusId == $testStatusId;

                foreach ($servicesInput as $serviceId => $reductionAmount) {
                    $service = Service::find($serviceId);
                    if (! $service) {
                        continue;
                    }

                    $currentPoolValue = $isTest
                        ? $lockedCustomer->getResourceTestQuantity($service->service_name)
                        : $lockedCustomer->getResourceBillableQuantity($service->service_name);

                    $newValue = max(0, $currentPoolValue - $reductionAmount);

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
                'status_id' => $statusId,
                'task_status_id' => $taskStatusId,
                'activation_date' => $validated['activation_date'],
                'allocation_type' => $actionType,
                'resource_upgradation_id' => ($actionType === 'upgrade' ? $resourceId : null),
                'resource_downgradation_id' => ($actionType === 'downgrade' ? $resourceId : null),
                'assigned_to' => $techUser->id,
                'assigned_by' => $kamId,
                'assigned_at' => now(), // Still kept for 'assigned_at' field legacy/compatibility
                'assignment_datetime' => $assignmentDatetime,
                'deadline_datetime' => $deadlineDatetime,
                'completed_at' => null, // Will be filled by VDC selection
            ]);

            // Task will be finalized in MyTaskController

            return response()->json([
                'success' => true,
                'message' => 'Allocation created. Please select a VDC to finalize.',
                'task_id' => $task->id,
                'customer_id' => $lockedCustomer->id,
            ]);
        });
    }
}
