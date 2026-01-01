<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ResourceAllocationController extends Controller
{
    public function index(): View
    {
        $customersRaw = Customer::orderBy('customer_name')->get();
        $customers = collect();

        // Group by name to identify duplicates
        $grouped = $customersRaw->groupBy('customer_name');

        foreach ($grouped as $name => $group) {
            if ($group->count() > 1) {
                // If duplicates exist, append index to each
                $index = 1;
                foreach ($group as $customer) {
                    $customer->is_new = ! $customer->hasResourceAllocations();
                    $customer->customer_name = $customer->customer_name.'-'.$index;
                    if ($customer->is_new) {
                        $customer->customer_name .= ' (No Resources)';
                    }
                    $customers->push($customer);
                    $index++;
                }
            } else {
                // No duplicates, just add
                $customer = $group->first();
                $customer->is_new = ! $customer->hasResourceAllocations();
                if ($customer->is_new) {
                    $customer->customer_name .= ' (No Resources)';
                }
                $customers->push($customer);
            }
        }

        // Re-sort entire collection by modified name so they appear in order
        $customers = $customers->sortBy('customer_name', SORT_NATURAL | SORT_FLAG_CASE);

        $customerStatuses = \App\Models\CustomerStatus::all();

        return view('resource-allocation.index', compact('customers', 'customerStatuses'));
    }

    public function process(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'action_type' => ['required', Rule::in(['dismantle'])],
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);

        // Dismantle functionality can be implemented by setting inactivation dates
        // For now, just return a message
        return redirect()
            ->route('resource-allocation.index')
            ->with('info', 'Dismantle functionality will be implemented through inactivation dates.');
    }

    public function allocationForm(Request $request, Customer $customer)
    {
        $actionType = $request->query('action_type');
        $statusId = $request->query('status_id');

        $services = \App\Models\Service::where('platform_id', $customer->platform_id)->get();
        $taskStatuses = \App\Models\TaskStatus::all();
        $customerStatuses = \App\Models\CustomerStatus::all();

        // Check if this is the first allocation
        $isFirstAllocation = ! $customer->hasResourceAllocations();

        // If first allocation, force upgrade action
        if ($isFirstAllocation && $actionType !== 'upgrade') {
            $actionType = 'upgrade';
        }

        // If first allocation and no status selected, default to "Test"
        if ($isFirstAllocation && ! $statusId) {
            $testStatus = \App\Models\CustomerStatus::where('name', 'Test')->first();
            $statusId = $testStatus ? $testStatus->id : null;
        }

        // For upgrades and downgrades, get the most recent status from upgradations if not provided
        if (($actionType === 'upgrade' || $actionType === 'downgrade') && ! $statusId) {
            $latestUpgradation = \App\Models\ResourceUpgradation::where('customer_id', $customer->id)
                ->orderBy('created_at', 'desc')
                ->first();
            $statusId = $latestUpgradation ? $latestUpgradation->status_id : null;
        }

        // Get default task status "Proceed from KAM" for first allocation
        $defaultTaskStatusId = null;
        if ($isFirstAllocation) {
            $proceedFromKAM = \App\Models\TaskStatus::where('name', 'Proceed from KAM')->first();
            $defaultTaskStatusId = $proceedFromKAM ? $proceedFromKAM->id : null;
        }

        $statusName = null;
        if ($statusId) {
            $status = \App\Models\CustomerStatus::find($statusId);
            $statusName = $status ? $status->name : null;
        }
        $testStatusId = \App\Models\CustomerStatus::where('name', 'Test')->first()?->id ?? 1;

        if ($request->ajax()) {
            return response()->json([
                'html' => view('resource-allocation.partials.allocation-form', compact(
                    'customer',
                    'actionType',
                    'services',
                    'taskStatuses',
                    'customerStatuses',
                    'defaultTaskStatusId',
                    'statusId',
                    'statusName',
                    'isFirstAllocation',
                    'testStatusId'
                ))->render(),
                'status_id' => $statusId,
                'test_status_id' => $testStatusId,
                'customer_name' => $customer->customer_name,
                'action_type' => $actionType,
            ]);
        }

        $html = view('resource-allocation.partials.allocation-form', compact('customer', 'services', 'actionType', 'statusId', 'statusName', 'taskStatuses', 'customerStatuses', 'isFirstAllocation', 'defaultTaskStatusId', 'testStatusId'))->render();

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
            'action_type' => 'required|in:upgrade,downgrade',
            'status_id' => 'required|exists:customer_statuses,id',
            'task_status_id' => 'required|exists:task_statuses,id',
            'activation_date' => 'required|date',
            'inactivation_date' => 'nullable|date',
            'services' => 'required|array',
            'services.*' => 'nullable|integer|min:0',
        ]);

        $actionType = $validated['action_type'];
        $statusId = $validated['status_id'];
        $taskStatusId = $validated['task_status_id'];
        $servicesInput = $validated['services'];
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

        return \Illuminate\Support\Facades\DB::transaction(function () use ($customer, $validated, $servicesInput, $actionType, $taskStatusId, $statusId) {
            // Lock the customer record to serialize allocations for this customer
            // This prevents two simultaneous upgrades from reading the same "current value" and writing inconsistent data
            $lockedCustomer = \App\Models\Customer::where('id', $customer->id)->lockForUpdate()->first();

            $testStatusId = \App\Models\CustomerStatus::where('name', 'Test')->first()?->id ?? 1;
            $isTestInput = $statusId == $testStatusId;

            if ($actionType === 'upgrade') {
                $upgradation = \App\Models\ResourceUpgradation::create([
                    'customer_id' => $lockedCustomer->id,
                    'status_id' => $validated['status_id'],
                    'activation_date' => $validated['activation_date'],
                    'inactivation_date' => $validated['inactivation_date'] ?? '3000-01-01',
                    'task_status_id' => $taskStatusId,
                    'inserted_by' => \Illuminate\Support\Facades\Auth::id(),
                ]);

                // Update Customer activation date
                $lockedCustomer->update([
                    'activation_date' => $validated['activation_date'],
                ]);

                $isTest = $isTestInput;

                foreach ($servicesInput as $serviceId => $increaseAmount) {
                    $service = \App\Models\Service::find($serviceId);
                    if (! $service) {
                        continue;
                    }

                    // Get current value from the specific pool
                    $currentPoolValue = $isTestInput
                        ? $lockedCustomer->getResourceTestQuantity($service->service_name)
                        : $lockedCustomer->getResourceBillableQuantity($service->service_name);

                    // Calculate the new pool total
                    $newValue = $currentPoolValue + $increaseAmount;

                    \App\Models\ResourceUpgradationDetail::create([
                        'resource_upgradation_id' => $upgradation->id,
                        'service_id' => $serviceId,
                        'quantity' => $newValue,
                        'upgrade_amount' => $increaseAmount,
                    ]);
                }

                // Auto-create task if task_status_id is "Proceed from KAM"
                $proceedFromKAM = \App\Models\TaskStatus::where('name', 'Proceed from KAM')->first();
                if ($proceedFromKAM && $taskStatusId == $proceedFromKAM->id) {
                    \App\Models\Task::create([
                        'customer_id' => $lockedCustomer->id,
                        'status_id' => $validated['status_id'],
                        'activation_date' => $validated['activation_date'],
                        'allocation_type' => 'upgrade',
                        'resource_upgradation_id' => $upgradation->id,
                    ]);
                }
            } else {
                // For downgrades, status_id is now passed from the form
                $statusId = $validated['status_id'];

                // If not passed (backward compatibility or edge case), fallback to latest
                if (! $statusId) {
                    $latestUpgradation = \App\Models\ResourceUpgradation::where('customer_id', $lockedCustomer->id)
                        ->orderBy('created_at', 'desc')
                        ->first();
                    $statusId = $latestUpgradation ? $latestUpgradation->status_id : null;
                }

                $downgradation = \App\Models\ResourceDowngradation::create([
                    'customer_id' => $lockedCustomer->id,
                    'status_id' => $statusId,
                    'activation_date' => $validated['activation_date'] ?? now(),
                    'inactivation_date' => $validated['inactivation_date'] ?? '3000-01-01',
                    'task_status_id' => $taskStatusId,
                    'inserted_by' => \Illuminate\Support\Facades\Auth::id(),
                ]);

                $testStatusId = \App\Models\CustomerStatus::where('name', 'Test')->first()?->id ?? 1;
                $isTest = $statusId == $testStatusId;

                foreach ($servicesInput as $serviceId => $reductionAmount) {
                    $service = \App\Models\Service::find($serviceId);
                    if (! $service) {
                        continue;
                    }

                    // Get current value from the specific pool
                    $currentPoolValue = $isTest
                        ? $lockedCustomer->getResourceTestQuantity($service->service_name)
                        : $lockedCustomer->getResourceBillableQuantity($service->service_name);

                    // Calculate the new pool total
                    $newValue = max(0, $currentPoolValue - $reductionAmount);

                    \App\Models\ResourceDowngradationDetail::create([
                        'resource_downgradation_id' => $downgradation->id,
                        'service_id' => $serviceId,
                        'quantity' => $newValue,
                        'downgrade_amount' => $reductionAmount,
                    ]);
                }

                // Auto-create task if task_status_id is "Proceed from KAM"
                $proceedFromKAM = \App\Models\TaskStatus::where('name', 'Proceed from KAM')->first();
                if ($proceedFromKAM && $taskStatusId == $proceedFromKAM->id) {
                    \App\Models\Task::create([
                        'customer_id' => $lockedCustomer->id,
                        'status_id' => $statusId,
                        'activation_date' => $validated['activation_date'],
                        'allocation_type' => 'downgrade',
                        'resource_downgradation_id' => $downgradation->id,
                    ]);
                }
            }

            // Send email notification to all Pro-Tech users
            try {
                $proTechUsers = \App\Models\User::whereHas('role', function ($q) {
                    $q->where('role_name', 'pro-tech');
                })->get();

                $sender = \Illuminate\Support\Facades\Auth::user();

                // We can fetch the latest task for this customer as it was just created.
                $latestTask = \App\Models\Task::where('customer_id', $lockedCustomer->id)->latest()->first();

                if ($latestTask) {
                    foreach ($proTechUsers as $proTech) {
                        \Illuminate\Support\Facades\Mail::to($proTech->email)
                            ->send(new \App\Mail\RecommendationSubmissionEmail($latestTask, $sender, $actionType));
                    }
                }
            } catch (\Exception $e) {
                // Log error but don't stop execution
                \Illuminate\Support\Facades\Log::error('Failed to send recommendation email: '.$e->getMessage());
            }

            $actionName = $actionType === 'upgrade' ? 'upgraded' : 'downgraded';

            return response()->json([
                'success' => true,
                'message' => "Resources {$actionName} successfully for {$lockedCustomer->customer_name}.",
            ]);
        });
    }
}
