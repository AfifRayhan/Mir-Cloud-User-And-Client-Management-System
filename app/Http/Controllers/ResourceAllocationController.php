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
        $customers = Customer::with('cloudDetail')
            ->orderBy('customer_name')
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

        $customer = Customer::with('cloudDetail')->findOrFail($validated['customer_id']);

        if ($customer->cloudDetail) {
            $customer->cloudDetail()->delete();

            return redirect()
                ->route('resource-allocation.index')
                ->with('success', "{$customer->customer_name}'s resources have been dismantled.");
        }

        return redirect()
            ->route('resource-allocation.index')
            ->with('info', "{$customer->customer_name} does not have active cloud resources to dismantle.");
    }

    public function allocationForm(Request $request, Customer $customer)
    {
        $actionType = $request->query('action_type');
        $statusId = $request->query('status_id');
        
        $services = \App\Models\Service::all();
        $taskStatuses = \App\Models\TaskStatus::all();
        
        $statusName = null;
        if ($statusId) {
            $status = \App\Models\CustomerStatus::find($statusId);
            $statusName = $status ? $status->name : null;
        }
        
        $html = view('resource-allocation.partials.allocation-form', compact('customer', 'services', 'actionType', 'statusId', 'statusName', 'taskStatuses'))->render();
        
        return response()->json(['html' => $html]);
    }

    public function storeAllocation(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'action_type' => 'required|in:upgrade,downgrade',
            'status_id' => $request->action_type === 'upgrade' ? 'required|exists:customer_statuses,id' : 'nullable|exists:customer_statuses,id',
            'task_status_id' => 'required|exists:task_statuses,id',
            'activation_date' => $request->action_type === 'upgrade' ? 'required|date' : 'nullable|date',
            'inactivation_date' => $request->action_type === 'upgrade' ? 'nullable|date' : 'nullable|date',
            'services' => 'nullable|array',
            'services.*' => 'nullable|integer|min:0',
        ]);

        $actionType = $validated['action_type'];
        $servicesInput = $validated['services'] ?? [];
        
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
                'task_status_id' => $validated['task_status_id'],
                'inserted_by' => \Illuminate\Support\Facades\Auth::id(),
            ]);

            // Update Customer activation date
            $customer->update([
                'activation_date' => $validated['activation_date'],
            ]);

            // Store inactivation_date in CloudDetail other_configuration
            if ($customer->cloudDetail) {
                $otherConfig = $customer->cloudDetail->other_configuration ?? [];
                $otherConfig['inactivation_date'] = $validated['inactivation_date'] ?? '3000-01-01';
                $customer->cloudDetail->other_configuration = $otherConfig;
                $customer->cloudDetail->save();
            }

            foreach ($servicesInput as $serviceId => $increaseAmount) {
                $service = \App\Models\Service::find($serviceId);
                if (!$service) continue;

                // Get current value
                $columnName = $this->getColumnNameForService($service->service_name);
                $currentValue = 0;
                if ($customer->cloudDetail) {
                    if ($columnName) {
                        $currentValue = $customer->cloudDetail->{$columnName} ?? 0;
                    } else {
                        // Check other_configuration for unmapped services
                        $otherConfig = $customer->cloudDetail->other_configuration ?? [];
                        $currentValue = $otherConfig[$service->service_name] ?? 0;
                    }
                }

                // Calculate the new value after increase
                $newValue = $currentValue + $increaseAmount;
                
                \App\Models\ResourceUpgradationDetail::create([
                    'resource_upgradation_id' => $upgradation->id,
                    'service_id' => $serviceId,
                    'quantity' => $newValue,
                    'upgrade_amount' => $increaseAmount,
                ]);
                
                // Update Cloud Details by adding the increase amount
                $this->updateCloudDetails($customer, $serviceId, $increaseAmount, 'add');
            }
        } else {
            $downgradation = \App\Models\ResourceDowngradation::create([
                'customer_id' => $customer->id,
                'activation_date' => now(),
                'task_status_id' => $validated['task_status_id'],
                'inserted_by' => \Illuminate\Support\Facades\Auth::id(),
            ]);

            foreach ($servicesInput as $serviceId => $reductionAmount) {
                $service = \App\Models\Service::find($serviceId);
                if (!$service) continue;

                // Get current value
                $columnName = $this->getColumnNameForService($service->service_name);
                $currentValue = 0;
                if ($customer->cloudDetail) {
                    if ($columnName) {
                        $currentValue = $customer->cloudDetail->{$columnName} ?? 0;
                    } else {
                        // Check other_configuration for unmapped services
                        $otherConfig = $customer->cloudDetail->other_configuration ?? [];
                        $currentValue = $otherConfig[$service->service_name] ?? 0;
                    }
                }

                // Calculate the new value after reduction
                $newValue = max(0, $currentValue - $reductionAmount);
                
                \App\Models\ResourceDowngradationDetail::create([
                    'resource_downgradation_id' => $downgradation->id,
                    'service_id' => $serviceId,
                    'quantity' => $newValue,
                    'downgrade_amount' => $reductionAmount,
                ]);
                
                // Update Cloud Details by reducing
                $this->updateCloudDetails($customer, $serviceId, $reductionAmount, 'subtract');
            }
        }

        $actionName = $actionType === 'upgrade' ? 'upgraded' : 'downgraded';
        return response()->json([
            'success' => true,
            'message' => "Resources {$actionName} successfully for {$customer->customer_name}."
        ]);
    }

    private function updateCloudDetails($customer, $serviceId, $value, $operation)
    {
        $service = \App\Models\Service::find($serviceId);
        if (!$service || !$customer->cloudDetail) return;

        $columnName = $this->getColumnNameForService($service->service_name);
        
        if ($columnName) {
            // Update dedicated column
            if ($operation == 'add') {
                // For upgrade: add the increase amount to current value
                $current = $customer->cloudDetail->{$columnName} ?? 0;
                $customer->cloudDetail->{$columnName} = $current + $value;
            } elseif ($operation == 'subtract') {
                // For downgrade: subtract the reduction amount
                $current = $customer->cloudDetail->{$columnName} ?? 0;
                $customer->cloudDetail->{$columnName} = max(0, $current - $value);
            }
        } else {
            // Update other_configuration for unmapped services
            $otherConfig = $customer->cloudDetail->other_configuration ?? [];
            $current = $otherConfig[$service->service_name] ?? 0;
            
            if ($operation == 'add') {
                $otherConfig[$service->service_name] = $current + $value;
            } elseif ($operation == 'subtract') {
                $otherConfig[$service->service_name] = max(0, $current - $value);
            }
            
            $customer->cloudDetail->other_configuration = $otherConfig;
        }
        
        $customer->cloudDetail->save();
    }

    private function getColumnNameForService($serviceName)
    {
        $mapping = [
            'vCPU' => 'vcpu',
            'RAM' => 'ram',
            'Storage' => 'storage',
            'Internet' => 'internet',
            'Real IP' => 'real_ip',
            'VPN' => 'vpn',
            'BDIX' => 'bdix',
        ];

        return $mapping[$serviceName] ?? null;
    }
}

