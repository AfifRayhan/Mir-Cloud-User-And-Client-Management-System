<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ResourceAllocationController extends Controller
{
    use \App\Traits\HandlesResourceAllocations;

    public function index(): View
    {
        $customersRaw = Customer::accessibleBy(Auth::user())
            ->withExists(['resourceUpgradations', 'resourceDowngradations'])
            ->orderBy('customer_name')
            ->get();
        $customers = collect();

        // Group by name to identify duplicates
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

        // Re-sort entire collection by modified name so they appear in order
        $customers = $customers->sortBy('customer_name', SORT_NATURAL | SORT_FLAG_CASE);

        $customerStatuses = \App\Models\CustomerStatus::whereIn('name', ['Test', 'Billable'])->get();

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
        if (! Customer::accessibleBy(Auth::user())->where('id', $customer->id)->exists()) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        $actionType = $request->query('action_type');
        $statusId = $request->query('status_id');
        $transferType = $request->query('transfer_type');

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

        // Calculate availability once to use for both auto-selection and AJAX response
        $currentResources = $customer->getCurrentResources();
        $hasTest = false;
        $hasBillable = false;

        if (empty($currentResources)) {
            // Fallback to Summary table if no historical changes found
            $summaries = \App\Models\Summary::where('customer_id', $customer->id)->get();
            $hasTest = $summaries->sum('test_quantity') > 0;
            $hasBillable = $summaries->sum('billable_quantity') > 0;
        } else {
            foreach ($currentResources as $pool) {
                if (($pool['test'] ?? 0) > 0) {
                    $hasTest = true;
                }
                if (($pool['billable'] ?? 0) > 0) {
                    $hasBillable = true;
                }
            }
        }

        // Auto-select Transfer Type if not provided
        if ($actionType === 'transfer' && ! $transferType) {
            if ($hasTest && ! $hasBillable) {
                $transferType = 'test_to_billable';
            } elseif ($hasBillable && ! $hasTest) {
                $transferType = 'billable_to_test';
            }
        }

        // Determine default Activation Date
        // If Customer Activation Date is in the past -> Default to Current Date.
        // If in the future -> Default to Customer Activation Date.
        $customerActivationDate = $customer->customer_activation_date;
        $defaultActivationDate = $customerActivationDate->isFuture()
            ? $customerActivationDate->format('Y-m-d')
            : now()->format('Y-m-d');

        // Check for pending tasks
        $hasPendingTasks = \App\Models\Task::where('customer_id', $customer->id)
            ->whereNull('completed_at')
            ->exists();

        // Fetch summaries for transfer validation and display
        $summaries = \App\Models\Summary::where('customer_id', $customer->id)
            ->get()
            ->keyBy('service_id');

        $responseData = [
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
                'testStatusId',
                'defaultActivationDate',
                'transferType',
                'hasPendingTasks'
            ))->render(),
            'has_pending_tasks' => $hasPendingTasks,
            'status_id' => $statusId,
            'test_status_id' => $testStatusId,
            'customer_name' => $customer->customer_name,
            'action_type' => $actionType,
            'transfer_type' => $transferType,
            'has_test' => $hasTest,
            'has_billable' => $hasBillable,
        ];

        return response()->json($responseData);
    }

    public function storeAllocation(\App\Http\Requests\ResourceAllocationRequest $request, Customer $customer)
    {
        if (! Customer::accessibleBy(Auth::user())->where('id', $customer->id)->exists()) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        $validated = $request->validated();

        $actionType = $validated['action_type'];
        $statusId = $validated['status_id'] ?? null;
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

        // ADDITIONAL VALIDATION FOR TRANSFERS: Check if move amount exceeds available quantity
        if ($actionType === 'transfer') {
            // Check for pending tasks
            if (\App\Models\Task::where('customer_id', $customer->id)->whereNull('completed_at')->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot confirm transfer: There are unassigned or incomplete tasks for this customer.',
                    'errors' => ['transfer' => ['Cannot confirm transfer: There are unassigned or incomplete tasks for this customer.']],
                ], 422);
            }

            $transferType = $validated['transfer_type'];
            $summaries = \App\Models\Summary::where('customer_id', $customer->id)
                ->get()
                ->keyBy('service_id');

            foreach ($servicesInput as $serviceId => $moveAmount) {
                $summary = $summaries->get($serviceId);
                if (! $summary) {
                    continue;
                }

                // Determine source pool based on transfer type
                $availableQuantity = ($transferType === 'test_to_billable')
                    ? ($summary->test_quantity ?? 0)
                    : ($summary->billable_quantity ?? 0);

                // Check if trying to move more than available
                if ($moveAmount > $availableQuantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The pre-requisite task has not been completed',
                        'errors' => ['transfer' => ['The pre-requisite task has not been completed']],
                    ], 422);
                }
            }
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($customer, $validated, $servicesInput, $actionType, $taskStatusId, $statusId) {
            // Lock the customer record to serialize allocations for this customer
            // This prevents two simultaneous upgrades from reading the same "current value" and writing inconsistent data
            $lockedCustomer = \App\Models\Customer::where('id', $customer->id)->lockForUpdate()->first();

            $testStatusId = \App\Models\CustomerStatus::where('name', 'Test')->first()?->id ?? 1;
            $isTestInput = $statusId == $testStatusId;

            // Calculate assignment and deadline datetimes
            $activationDate = \Illuminate\Support\Carbon::parse($validated['activation_date']);
            $now = now();

            if ($activationDate->isSameDay($now)) {
                $assignmentDatetime = $now;
            } else {
                // If any date other than current is selected
                $assignmentDatetime = $activationDate->copy()->setTime(9, 30, 0);
            }
            $deadlineDatetime = $this->calculateDeadline($assignmentDatetime);

            if ($actionType === 'upgrade') {
                $upgradation = \App\Models\ResourceUpgradation::create([
                    'customer_id' => $lockedCustomer->id,
                    'status_id' => $validated['status_id'],
                    'activation_date' => $validated['activation_date'],
                    'inactivation_date' => $validated['inactivation_date'] ?? '3000-01-01',
                    'task_status_id' => $taskStatusId,
                    'inserted_by' => \Illuminate\Support\Facades\Auth::id(),
                    'assignment_datetime' => $assignmentDatetime,
                    'deadline_datetime' => $deadlineDatetime,
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
                        'assignment_datetime' => $assignmentDatetime,
                        'deadline_datetime' => $deadlineDatetime,
                    ]);
                }
            } elseif ($actionType === 'downgrade') {
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
                    'assignment_datetime' => $assignmentDatetime,
                    'deadline_datetime' => $deadlineDatetime,
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
                        'assignment_datetime' => $assignmentDatetime,
                        'deadline_datetime' => $deadlineDatetime,
                    ]);
                }
            } elseif ($actionType === 'transfer') {
                $transferType = $validated['transfer_type'];
                $testStatus = \App\Models\CustomerStatus::where('name', 'Test')->first();
                $billableStatus = \App\Models\CustomerStatus::where('name', 'Billable')->first();

                // New statuses
                $testToBillableStatus = \App\Models\CustomerStatus::where('name', 'Test to Billable')->first();
                $billableToTestStatus = \App\Models\CustomerStatus::where('name', 'Billable to Test')->first();

                if ($transferType === 'test_to_billable') {
                    $statusFromId = $testStatus->id;
                    $statusToId = $billableStatus->id;
                    $taskStatusIdForTask = $testToBillableStatus->id;
                } else {
                    $statusFromId = $billableStatus->id;
                    $statusToId = $testStatus->id;
                    $taskStatusIdForTask = $billableToTestStatus->id;
                }

                $transfer = \App\Models\ResourceTransfer::create([
                    'customer_id' => $lockedCustomer->id,
                    'status_from_id' => $statusFromId,
                    'status_to_id' => $statusToId,
                    'transfer_datetime' => now(),
                    'inserted_by' => \Illuminate\Support\Facades\Auth::id(),
                ]);

                // Create Task for transfer
                \App\Models\Task::create([
                    'customer_id' => $lockedCustomer->id,
                    'status_id' => $taskStatusIdForTask,
                    'activation_date' => $validated['activation_date'],
                    'allocation_type' => 'transfer',
                    'resource_transfer_id' => $transfer->id,
                    'assignment_datetime' => $transfer->transfer_datetime,
                    'deadline_datetime' => $transfer->transfer_datetime,
                    'assigned_to' => \Illuminate\Support\Facades\Auth::id(),
                    'assigned_by' => \Illuminate\Support\Facades\Auth::id(),
                    'assigned_at' => now(),
                    'completed_at' => now(),
                ]);

                // Get fresh resources directly from the historical records to avoid stale Summary table issues
                $currentResources = $lockedCustomer->getCurrentResources();

                foreach ($servicesInput as $serviceId => $transferAmount) {
                    if ($transferAmount <= 0) continue;

                    $pool = $currentResources[$serviceId] ?? ['test' => 0, 'billable' => 0];
                    $currentTest = $pool['test'];
                    $currentBillable = $pool['billable'];

                        if ($transferType === 'test_to_billable') {
                            $currentSource = $currentTest;
                            $currentTarget = $currentBillable;
                            $newSource = max(0, $currentTest - $transferAmount);
                            $newTarget = $currentBillable + $transferAmount;
                        } else {
                            $currentSource = $currentBillable;
                            $currentTarget = $currentTest;
                            $newSource = max(0, $currentBillable - $transferAmount);
                            $newTarget = $currentTest + $transferAmount;
                        }

                        \App\Models\ResourceTransferDetail::create([
                            'resource_transfer_id' => $transfer->id,
                            'service_id' => $serviceId,
                            'current_source_quantity' => $currentSource,
                            'current_target_quantity' => $currentTarget,
                            'transfer_amount' => $transferAmount,
                            'new_source_quantity' => $newSource,
                            'new_target_quantity' => $newTarget,
                        ]);
                }

                // IMPORTANT: Since transfers are auto-completed by the system (KAM), 
                // we MUST update the summary table here so the dashboard shows the change.
                $this->updateCustomerSummary($lockedCustomer->id);
            }

            // Send email notification to all Pro-Tech users (skip for transfers)
            if ($actionType !== 'transfer') {
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
            }

            // Send email notification to all Bill users for transfers
            if ($actionType === 'transfer' && isset($transfer)) {
                try {
                    $billingUsers = \App\Models\User::whereHas('role', function ($q) {
                        $q->where('role_name', 'bill');
                    })->get();

                    $sender = \Illuminate\Support\Facades\Auth::user();
                    $latestTask = \App\Models\Task::where('customer_id', $lockedCustomer->id)
                        ->where('allocation_type', 'transfer')
                        ->latest()
                        ->first();

                    foreach ($billingUsers as $billingUser) {
                        \Illuminate\Support\Facades\Mail::to($billingUser->email)
                            ->send(new \App\Mail\BillAssignmentEmail($transfer, $sender, $latestTask));
                    }
                } catch (\Exception $e) {
                    // Log error but don't stop execution
                    \Illuminate\Support\Facades\Log::error('Failed to send bill assignment email: '.$e->getMessage());
                }
            }

            if ($actionType === 'transfer') {
                $message = "Resources transfered successfully for {$lockedCustomer->customer_name}.";
            } else {
                $actionName = $actionType === 'upgrade' ? 'upgraded' : 'downgraded';
                $message = "Resources {$actionName} successfully for {$lockedCustomer->customer_name}.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        });
    }
}
