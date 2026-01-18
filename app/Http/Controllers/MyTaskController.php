<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Vdc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyTaskController extends Controller
{
    use \App\Traits\HandlesResourceAllocations;

    /**
     * Display tasks assigned to the authenticated user
     */
    public function index()
    {
        $tasks = Task::with([
            'customer.platform',
            'status',
            'assignedBy',
            'resourceUpgradation.details.service',
            'resourceUpgradation.insertedBy',
            'resourceDowngradation.details.service',
            'resourceDowngradation.insertedBy',
        ])
            ->leftJoin('resource_upgradations', 'tasks.resource_upgradation_id', '=', 'resource_upgradations.id')
            ->leftJoin('resource_downgradations', 'tasks.resource_downgradation_id', '=', 'resource_downgradations.id')
            ->where('tasks.assigned_to', Auth::id())
            ->where('tasks.allocation_type', '!=', 'transfer')
            ->orderByRaw('CASE WHEN tasks.id = ? THEN 0 ELSE 1 END', [request('dtid')])
            ->orderByRaw('tasks.completed_at IS NOT NULL')
            ->orderByRaw('COALESCE(resource_upgradations.created_at, resource_downgradations.created_at) ASC')
            ->select('tasks.*')
            ->paginate(10);

        return view('my-tasks.index', compact('tasks'));
    }

    /**
     * Get task details for AJAX request (inline view)
     */
    public function getDetails(Task $task)
    {
        // Verify the task is assigned to the current user or user is admin
        if ($task->assigned_to !== Auth::id() && ! Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->load([
            'customer',
            'status',
            'assignedTo',
            'assignedBy',
            'vdc',
            'resourceUpgradation.details.service',
            'resourceUpgradation.insertedBy',
            'resourceDowngradation.details.service',
            'resourceDowngradation.insertedBy',
        ]);

        return response()->json([
            'task' => $task,
            'resourceDetails' => $task->resourceDetails,
        ]);
    }

    /**
     * Display the specified task (only if assigned to current user)
     */
    public function show(Task $task)
    {
        // Verify the task is assigned to the current user or user is admin
        if ($task->assigned_to !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access to this task.');
        }

        $task->load([
            'customer',
            'status',
            'assignedTo',
            'assignedBy',
            'resourceUpgradation.details.service',
            'resourceUpgradation.insertedBy',
            'resourceDowngradation.details.service',
            'resourceDowngradation.insertedBy',
        ]);

        return view('my-tasks.show', compact('task'));
    }

    /**
     * Mark task as complete with VDC assignment
     */
    public function complete(Request $request, Task $task)
    {
        // Verify the task is assigned to the current user
        if ($task->assigned_to !== Auth::id()) {
            abort(403, 'Unauthorized access to this task.');
        }

        // Validate VDC input
        $validated = $request->validate([
            'vdc_id' => 'nullable|exists:vdcs,id',
            'new_vdc_name' => 'nullable|string|max:255',
            'source' => 'nullable|string',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($task, $validated) {
            // Lock the task record
            $lockedTask = Task::where('id', $task->id)->lockForUpdate()->first();

            // Check if already completed by a race condition
            if ($lockedTask->completed_at) {
                return back()->with('error', 'Task is already marked as complete.');
            }

            // Handle VDC selection/creation
            $vdcId = null;
            if (! empty($validated['new_vdc_name'])) {
                // Create new VDC
                try {
                    $vdc = Vdc::create([
                        'customer_id' => $lockedTask->customer_id,
                        'vdc_name' => $validated['new_vdc_name'],
                    ]);
                    $vdcId = $vdc->id;
                } catch (\Illuminate\Database\QueryException $e) {
                    // Handle duplicate VDC name
                    if ($e->getCode() == 23000) {
                        if (request()->expectsJson() || request()->header('Accept') === 'application/json') {
                            return response()->json([
                                'success' => false,
                                'message' => 'A VDC with this name already exists for this customer.',
                            ], 422);
                        }

                        return back()->with('error', 'A VDC with this name already exists for this customer.');
                    }
                    throw $e;
                }
            } elseif (! empty($validated['vdc_id'])) {
                $vdcId = $validated['vdc_id'];
            }

            $lockedTask->update([
                'completed_at' => now(),
                'task_status_id' => 3, // Proceed from Tech
                'vdc_id' => $vdcId,
            ]);

            // Update summary table with latest service values now that task is complete
            $this->updateCustomerSummary($lockedTask->customer_id);

            // Send email notification
            $sender = Auth::user();
            $sender->load('role');

            // Load relationships required by the email template and determine action type
            $lockedTask->load(['customer', 'customer.platform', 'resourceUpgradation.details.service', 'resourceDowngradation.details.service', 'assignedBy', 'vdc', 'assignedBy.role']);
            $actionType = $lockedTask->allocation_type ?? 'allocation';

            // Determine email type and recipient based on source
            $source = $validated['source'] ?? null;

            if ($source === 'tech_allocation' && $lockedTask->assignedBy) {
                \Illuminate\Support\Facades\Log::info('Tech allocation confirm email block entered', ['assigned_by' => $lockedTask->assignedBy->email]);
                // Case 1: Tech Resource Allocation - Send Confirmation to Assigner (KAM)
                try {
                    \Illuminate\Support\Facades\Mail::to($lockedTask->assignedBy->email)
                        ->send(new \App\Mail\TechResourceConfirmationEmail($lockedTask, $sender, $actionType));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send tech confirmation email: '.$e->getMessage());
                }
            } else {
                // Case 2: Standard My Tasks Completion - Send Completion Email to Management & Billing
                $managementUsers = \App\Models\User::whereHas('role', function ($q) {
                    $q->where('role_name', 'management');
                })->get();

                // Billing Users get a specific email
                $billingUsers = \App\Models\User::whereHas('role', function ($q) {
                    $q->where('role_name', 'bill');
                })->get();

                // Prepare CC list (assigned_by user)
                $ccUsers = [];
                if ($lockedTask->assignedBy) {
                    $ccUsers[] = $lockedTask->assignedBy->email;
                }

                // Send to Management (Standard Email)
                foreach ($managementUsers as $manager) {
                    try {
                        $mail = \Illuminate\Support\Facades\Mail::to($manager->email);

                        if (! empty($ccUsers)) {
                            $mail->cc($ccUsers);
                        }

                        $mail->send(new \App\Mail\TaskCompletionEmail($lockedTask, $sender, $actionType));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to send completion email to '.$manager->email.': '.$e->getMessage());
                    }
                }

                // Send to Billing (Specific Email with Buttons)
                foreach ($billingUsers as $biller) {
                    try {
                        // Billing users don't need CCs of KAMs, they just need the notification
                        \Illuminate\Support\Facades\Mail::to($biller->email)
                            ->send(new \App\Mail\BillTaskCompletionEmail($lockedTask, $sender, $actionType));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to send billing completion email to '.$biller->email.': '.$e->getMessage());
                    }
                }
            }

            // Return JSON for AJAX requests or if explictly accepted
            if (request()->expectsJson() || request()->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => true,
                    'message' => 'Task marked as complete and notification sent.',
                ]);
            }

            return back()->with('success', 'Task marked as complete and notification sent.');
        });
    }

    /**
     * Get VDCs for a customer (for AJAX dropdown)
     */
    public function getCustomerVdcs($customerId)
    {
        $vdcs = Vdc::where('customer_id', $customerId)->get();

        return response()->json(['vdcs' => $vdcs]);
    }
}
