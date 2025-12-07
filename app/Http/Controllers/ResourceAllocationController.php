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
        $customers = Customer::orderBy('customer_name')
            ->get();

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
            ->with('info', "Dismantle functionality will be implemented through inactivation dates.");
    }

    public function allocationForm(Request $request, Customer $customer)
    {
        $actionType = $request->query('action_type');
        $statusId = $request->query('status_id');
        
        $services = \App\Models\Service::all();
        $taskStatuses = \App\Models\TaskStatus::all();
        
        // Check if this is the first allocation
        $isFirstAllocation = !$customer->hasResourceAllocations();
        
        // If first allocation and no status selected, default to "Test"
        if ($isFirstAllocation && !$statusId) {
            $testStatus = \App\Models\CustomerStatus::where('name', 'Test')->first();
            $statusId = $testStatus ? $testStatus->id : null;
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
        
        $html = view('resource-allocation.partials.allocation-form', compact('customer', 'services', 'actionType', 'statusId', 'statusName', 'taskStatuses', 'isFirstAllocation', 'defaultTaskStatusId'))->render();
        
        return response()->json(['html' => $html]);
    }

    public function storeAllocation(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'action_type' => 'required|in:upgrade,downgrade',
            'status_id' => $request->action_type === 'upgrade' ? 'required|exists:customer_statuses,id' : 'nullable|exists:customer_statuses,id',
            'task_status_id' => 'nullable|exists:task_statuses,id',
            'activation_date' => $request->action_type === 'upgrade' ? 'required|date' : 'nullable|date',
            'inactivation_date' => $request->action_type === 'upgrade' ? 'nullable|date' : 'nullable|date',
            'services' => 'nullable|array',
            'services.*' => 'nullable|integer|min:0',
        ]);

        $actionType = $validated['action_type'];
        $servicesInput = $validated['services'] ?? [];
        
        // Ensure task_status_id defaults to "Proceed from KAM" when not provided
        $taskStatusId = $validated['task_status_id'] ?? \App\Models\TaskStatus::where('name', 'Proceed from KAM')->value('id');
        if (!$taskStatusId) {
            // Fallback to id 1 if seed/data differs
            $taskStatusId = 1;
        }
        // Filter out null and zero values
        $servicesInput = array_filter($servicesInput, function($value) {
            return !is_null($value) && $value > 0;
        });

        if (empty($servicesInput)) {
            return response()->json([
                'success' => false,
                'message' => 'Please specify at least one resource change with a value greater than 0.',
                'errors' => ['services' => ['Please specify at least one resource change with a value greater than 0.']]
            ], 422);
        }

        if ($actionType === 'upgrade') {
            $upgradation = \App\Models\ResourceUpgradation::create([
                'customer_id' => $customer->id,
                'status_id' => $validated['status_id'],
                'activation_date' => $validated['activation_date'],
                'inactivation_date' => $validated['inactivation_date'] ?? '3000-01-01',
                    'task_status_id' => $taskStatusId,
                'inserted_by' => \Illuminate\Support\Facades\Auth::id(),
            ]);

            // Update Customer activation date
            $customer->update([
                'activation_date' => $validated['activation_date'],
            ]);

            foreach ($servicesInput as $serviceId => $increaseAmount) {
                $service = \App\Models\Service::find($serviceId);
                if (!$service) continue;

                // Get current value from resource history
                $currentValue = $customer->getResourceQuantity($service->service_name);

                // Calculate the new value after increase
                $newValue = $currentValue + $increaseAmount;
                
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
                    'customer_id' => $customer->id,
                    'status_id' => $validated['status_id'],
                    'activation_date' => $validated['activation_date'],
                    'allocation_type' => 'upgrade',
                    'resource_upgradation_id' => $upgradation->id,
                ]);
            }
        } else {
            $downgradation = \App\Models\ResourceDowngradation::create([
                'customer_id' => $customer->id,
                'activation_date' => now(),
                'task_status_id' => $taskStatusId,
                'inserted_by' => \Illuminate\Support\Facades\Auth::id(),
            ]);

            foreach ($servicesInput as $serviceId => $reductionAmount) {
                $service = \App\Models\Service::find($serviceId);
                if (!$service) continue;

                // Get current value from resource history
                $currentValue = $customer->getResourceQuantity($service->service_name);

                // Calculate the new value after reduction
                $newValue = max(0, $currentValue - $reductionAmount);
                
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
                    'customer_id' => $customer->id,
                    'activation_date' => now(),
                    'allocation_type' => 'downgrade',
                    'resource_downgradation_id' => $downgradation->id,
                ]);
            }
        }

        $actionName = $actionType === 'upgrade' ? 'upgraded' : 'downgraded';
        return response()->json([
            'success' => true,
            'message' => "Resources {$actionName} successfully for {$customer->customer_name}."
        ]);
    }


}

