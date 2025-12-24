<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\ResourceUpgradationDetail;
use App\Models\ResourceDowngradationDetail;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Summary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Mail\RecommendationSubmissionEmail;

class KamTaskManagementController extends Controller
{
    /**
     * Display a listing of all tasks (KAM and Pro-KAM only)
     */
    public function index(Request $request)
    {
        // Check authorization
        if (! Auth::user()->isAdmin() && ! Auth::user()->isProKam() && ! Auth::user()->isKam()) {
            abort(403, 'Unauthorized access.');
        }

        $query = Task::with(['customer', 'status', 'assignedTo', 'resourceUpgradation.details.service', 'resourceDowngradation.details.service'])
            ->leftJoin('resource_upgradations', 'tasks.resource_upgradation_id', '=', 'resource_upgradations.id')
            ->leftJoin('resource_downgradations', 'tasks.resource_downgradation_id', '=', 'resource_downgradations.id')
            ->orderByRaw('CASE WHEN tasks.assigned_to IS NULL THEN 0 ELSE 1 END')
            ->orderByRaw('COALESCE(resource_upgradations.created_at, resource_downgradations.created_at) ASC');

        // Prioritize specific task if provided (for deep linking)
        if ($request->filled('dtid')) {
            $query->orderByRaw('CASE WHEN tasks.id = ? THEN 0 ELSE 1 END', [$request->dtid]);
        }

        $query->select('tasks.*');

        // Apply filters
        if ($request->filled('allocation_type')) {
            $query->where('allocation_type', $request->allocation_type);
        }

        if ($request->filled('assigned_status')) {
            if ($request->assigned_status === 'pending') {
                $query->whereNull('assigned_to');
            } elseif ($request->assigned_status === 'assigned') {
                $query->whereNotNull('assigned_to');
            }
        }

        if ($request->filled('completion_status')) {
            if ($request->completion_status === 'completed') {
                $query->whereNotNull('completed_at');
            } elseif ($request->completion_status === 'incomplete') {
                $query->whereNull('completed_at');
            }
        }

        $tasks = $query->paginate(10)->appends($request->query());

        // Get all services for the edit modal
        $services = Service::all();

        return view('task-management.kam-index', compact('tasks', 'services'));
    }

    /**
     * Get task details via AJAX
     */
    public function getDetails(Task $task)
    {
        // Check authorization
        if (! Auth::user()->isAdmin() && ! Auth::user()->isProKam() && ! Auth::user()->isKam()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->load(['customer', 'status', 'assignedTo', 'assignedBy', 'resourceUpgradation.details.service', 'resourceDowngradation.details.service']);

        // Get all services
        $allServices = Service::all();
        $existingDetails = $task->resourceDetails;
        
        // Create a map of existing details by service_id
        $detailsMap = $existingDetails->keyBy('service_id');
        
        // Build complete resource details array with all services
        $completeResourceDetails = $allServices->map(function($service) use ($detailsMap, $task) {
            $existingDetail = $detailsMap->get($service->id);
            
            if ($existingDetail) {
                // Service has existing data
                return $existingDetail;
            } else {
                // Service doesn't exist in task, create a placeholder with 0 values
                $isUpgrade = $task->allocation_type === 'upgrade';
                return (object) [
                    'service' => $service,
                    'service_id' => $service->id,
                    'upgrade_amount' => 0,
                    'downgrade_amount' => 0,
                    'quantity' => $task->customer->getResourceQuantity($service->service_name),
                ];
            }
        });

        return response()->json([
            'task' => $task,
            'resourceDetails' => $completeResourceDetails,
        ]);
    }

    /**
     * Update task and associated resource details
     */
    public function update(Request $request, Task $task)
    {
        // Check authorization
        if (! Auth::user()->isAdmin() && ! Auth::user()->isProKam() && ! Auth::user()->isKam()) {
            abort(403, 'Unauthorized access.');
        }

        // Only unassigned tasks can be edited CHECK REMOVED per user request
        // if ($task->assigned_to) {
        //     return back()->with('error', 'Cannot edit a task that has already been assigned.');
        // }

        $validated = $request->validate([
            'activation_date' => 'required|date',
            'services' => 'required|array',
            'services.*' => 'required|integer|min:0',
        ]);

        return DB::transaction(function () use ($task, $validated) {
            $customerId = $task->customer_id;
            $affectedServiceIds = [];

            // Update Task activation date
            $task->update([
                'activation_date' => $validated['activation_date'],
            ]);

            // Update Resource Record (Upgradation or Downgradation)
            if ($task->allocation_type === 'upgrade' && $task->resourceUpgradation) {
                $task->resourceUpgradation->update([
                    'activation_date' => $validated['activation_date'],
                ]);

                // Get existing details mapped by service_id
                $existingDetails = $task->resourceUpgradation->details()->get()->keyBy('service_id');

                foreach ($validated['services'] as $serviceId => $amount) {
                    $affectedServiceIds[] = $serviceId;
                    
                    if ($existingDetails->has($serviceId)) {
                        // Update existing detail
                        $existingDetails[$serviceId]->update([
                            'upgrade_amount' => $amount,
                        ]);
                    } else if ($amount > 0) {
                        // Create new detail only if amount > 0
                        ResourceUpgradationDetail::create([
                            'resource_upgradation_id' => $task->resourceUpgradation->id,
                            'service_id' => $serviceId,
                            'upgrade_amount' => $amount,
                            'quantity' => 0, // Will be updated by sync
                        ]);
                    }
                }
                
                // Delete details with 0 amount
                $task->resourceUpgradation->details()->where('upgrade_amount', 0)->delete();

            } elseif ($task->allocation_type === 'downgrade' && $task->resourceDowngradation) {
                $task->resourceDowngradation->update([
                    'activation_date' => $validated['activation_date'],
                ]);

                // Get existing details mapped by service_id
                $existingDetails = $task->resourceDowngradation->details()->get()->keyBy('service_id');

                foreach ($validated['services'] as $serviceId => $amount) {
                    $affectedServiceIds[] = $serviceId;
                    
                    if ($existingDetails->has($serviceId)) {
                        // Update existing detail
                        $existingDetails[$serviceId]->update([
                            'downgrade_amount' => $amount,
                        ]);
                    } else if ($amount > 0) {
                        // Create new detail only if amount > 0
                        ResourceDowngradationDetail::create([
                            'resource_downgradation_id' => $task->resourceDowngradation->id,
                            'service_id' => $serviceId,
                            'downgrade_amount' => $amount,
                            'quantity' => 0, // Will be updated by sync
                        ]);
                    }
                }
                
                // Delete details with 0 amount
                $task->resourceDowngradation->details()->where('downgrade_amount', 0)->delete();
            }

            // Sync the entire chain for affected services to ensure all quantities are correct
            if (!empty($affectedServiceIds)) {
                $this->syncCustomerResourceChains($customerId, array_unique($affectedServiceIds));
            }

            // Re-sync summary table
            $this->updateCustomerSummary($customerId);

            // Send recommendation email to Pro-Techs (like a new resource allocation)
            $proTechUsers = User::where('role_id', 4)->get(); // Pro-Tech Role ID is 4
            
            foreach ($proTechUsers as $recipient) {
                try {
                    Mail::to($recipient->email)->send(new RecommendationSubmissionEmail(
                        $task,
                        Auth::user(),
                        $task->allocation_type
                    ));
                } catch (\Exception $e) {
                    Log::error('Failed to send email: ' . $e->getMessage());
                }
            }

            return back()->with('success', 'Task and resource details updated successfully.');
        });
    }

    /**
     * Delete task and associated resource records
     */
    public function destroy(Task $task)
    {
        // Check authorization
        if (! Auth::user()->isAdmin() && ! Auth::user()->isProKam() && ! Auth::user()->isKam()) {
            abort(403, 'Unauthorized access.');
        }

        // Only unassigned tasks can be deleted
        if ($task->assigned_to) {
            return back()->with('error', 'Cannot delete a task that has already been assigned.');
        }

        return DB::transaction(function () use ($task) {
            $customerId = $task->customer_id;
            $affectedServiceIds = [];

            // Capture service IDs before deletion
            if ($task->allocation_type === 'upgrade' && $task->resourceUpgradation) {
                $affectedServiceIds = $task->resourceUpgradation->details()->pluck('service_id')->toArray();
                $task->resourceUpgradation->details()->delete();
                $task->resourceUpgradation->delete();
            } elseif ($task->allocation_type === 'downgrade' && $task->resourceDowngradation) {
                $affectedServiceIds = $task->resourceDowngradation->details()->pluck('service_id')->toArray();
                $task->resourceDowngradation->details()->delete();
                $task->resourceDowngradation->delete();
            }

            // Delete the task itself
            $task->delete();

            // Sync the entire chain for affected services
            if (!empty($affectedServiceIds)) {
                $this->syncCustomerResourceChains($customerId, array_unique($affectedServiceIds));
            }

            // Re-sync summary table
            $this->updateCustomerSummary($customerId);

            return back()->with('success', 'Task and associated resource request deleted successfully.');
        });
    }

    /**
     * Synchronize the quantity chain for specific services of a customer
     */
    protected function syncCustomerResourceChains(int $customerId, array $serviceIds): void
    {
        foreach ($serviceIds as $serviceId) {
            // Get all upgrades for this customer and service
            $upgrades = ResourceUpgradationDetail::where('service_id', $serviceId)
                ->whereHas('resourceUpgradation', function($q) use ($customerId) {
                    $q->where('customer_id', $customerId);
                })
                ->with(['resourceUpgradation.task'])
                ->get()
                ->map(function($detail) {
                    $detail->xtype = 'upgrade';
                    $detail->sort_date = $detail->resourceUpgradation->activation_date;
                    $detail->sort_created = $detail->resourceUpgradation->created_at;
                    return $detail;
                });

            // Get all downgrades for this customer and service
            $downgrades = ResourceDowngradationDetail::where('service_id', $serviceId)
                ->whereHas('resourceDowngradation', function($q) use ($customerId) {
                    $q->where('customer_id', $customerId);
                })
                ->with(['resourceDowngradation.task'])
                ->get()
                ->map(function($detail) {
                    $detail->xtype = 'downgrade';
                    $detail->sort_date = $detail->resourceDowngradation->activation_date;
                    $detail->sort_created = $detail->resourceDowngradation->created_at;
                    return $detail;
                });

            // Merge and sort chronologically ASC
            $all = $upgrades->concat($downgrades)->sort(function($a, $b) {
                $dateCompare = strcmp($a->sort_date, $b->sort_date);
                if ($dateCompare !== 0) return $dateCompare;
                return strcmp($a->sort_created, $b->sort_created);
            });

            $runningQuantity = 0;
            foreach ($all as $item) {
                $hasConflict = false;

                if ($item->xtype === 'upgrade') {
                    $runningQuantity += $item->upgrade_amount;
                    
                    // Clear conflict flag for upgrades if it was set (though upgrades rarely cause conflicts themselves)
                    if ($item->resourceUpgradation && $item->resourceUpgradation->task) {
                        $item->resourceUpgradation->task->update(['has_resource_conflict' => false]);
                    }

                } else {
                    // Check for conflict BEFORE applying the downgrade
                    if ($runningQuantity - $item->downgrade_amount < 0) {
                        $hasConflict = true;
                    }

                    // Allow negative values to reflect the deficit
                    $runningQuantity = $runningQuantity - $item->downgrade_amount;

                    // Update Task Conflict Status
                    if ($item->resourceDowngradation && $item->resourceDowngradation->task) {
                         $item->resourceDowngradation->task->update(['has_resource_conflict' => $hasConflict]);
                    }
                }
                
                // Update quantity in DB
                DB::table($item->xtype === 'upgrade' ? 'resource_upgradation_details' : 'resource_downgradation_details')
                    ->where('id', $item->id)
                    ->update(['quantity' => $runningQuantity]);
            }
        }
    }

    /**
     * Update the summary table with latest service values for a customer
     */
    protected function updateCustomerSummary(int $customerId): void
    {
        $customer = Customer::find($customerId);
        if (! $customer) {
            return;
        }

        // Get all services
        $services = Service::all();

        foreach ($services as $service) {
            $quantity = $customer->getResourceQuantity($service->service_name);

            // Upsert summary record
            Summary::updateOrCreate(
                [
                    'customer_id' => $customerId,
                    'service_id' => $service->id,
                ],
                [
                    'quantity' => $quantity,
                ]
            );
        }
    }
}
