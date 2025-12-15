<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskManagementController extends Controller
{
    /**
     * Display a listing of all tasks (Admin and ProTech only)
     */
    public function index(Request $request)
    {
        // Check authorization
        if (!Auth::user()->isAdmin() && !Auth::user()->isProTech() && !Auth::user()->isManagement()) {
            abort(403, 'Unauthorized access.');
        }

        $query = Task::with(['customer', 'status', 'assignedTo', 'resourceUpgradation.details.service', 'resourceDowngradation.details.service'])
            ->leftJoin('resource_upgradations', 'tasks.resource_upgradation_id', '=', 'resource_upgradations.id')
            ->leftJoin('resource_downgradations', 'tasks.resource_downgradation_id', '=', 'resource_downgradations.id')
            ->orderByRaw('CASE WHEN tasks.assigned_to IS NULL THEN 0 ELSE 1 END')
            ->orderByRaw('COALESCE(resource_upgradations.created_at, resource_downgradations.created_at) ASC')
            ->select('tasks.*');

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

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('completion_status')) {
            if ($request->completion_status === 'completed') {
                $query->whereNotNull('completed_at');
            } elseif ($request->completion_status === 'incomplete') {
                $query->whereNull('completed_at');
            }
        }

        $tasks = $query->paginate(10)->appends($request->query());

        // Get all users for assignment dropdown (Tech, Pro-Tech, and Admin)
        // Management can assign to Tech/Pro-Tech but can't be assigned to (Standard logic: Tech/ProTech exec tasks)
        $users = User::whereHas('role', function($q) {
            $q->whereIn('role_name', ['tech', 'pro-tech', 'admin']);
        })->orderBy('name')->get();

        return view('task-management.index', compact('tasks', 'users'));
    }

    public function getDetails(Task $task)
    {
        // Check authorization
        if (!Auth::user()->isAdmin() && !Auth::user()->isProTech() && !Auth::user()->isManagement()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->load(['customer', 'status', 'assignedTo', 'assignedBy', 'resourceUpgradation.details.service', 'resourceDowngradation.details.service']);

        return response()->json([
            'task' => $task,
            'resourceDetails' => $task->resourceDetails,
        ]);
    }

    /**
     * Assign task to a user
     */
    public function assign(Request $request, Task $task)
    {
        // Check authorization
        if (!Auth::user()->isAdmin() && !Auth::user()->isProTech() && !Auth::user()->isManagement()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $task, $validated) {
            // Lock the task record for update to prevent race conditions
            $lockedTask = Task::where('id', $task->id)->lockForUpdate()->first();

            // Check if task was assigned by another request while we were waiting
            if ($lockedTask->assigned_to) {
                 // Prevent "surprise" overwrites if the task is already assigned
                 return back()->with('error', 'Task was already assigned to ' . ($lockedTask->assignedTo->name ?? 'someone else') . '. Please refresh.');
            }

            // Verify the assigned user is Tech or Pro-Tech
            $assignedUser = User::findOrFail($validated['assigned_to']);
            if (!$assignedUser->isAdmin() && !$assignedUser->isTech() && !$assignedUser->isProTech()) {
                return back()->with('error', 'Tasks can only be assigned to Tech or Pro-Tech users.');
            }

            $lockedTask->update([
                'assigned_to' => $validated['assigned_to'],
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
                'task_status_id' => 2, // Proceed from Pro Tech
            ]);

            // Send email notification to the assigned user
            try {
                $sender = Auth::user();
                $actionType = $lockedTask->allocation_type ?? 'allocation';
                // Load relationships for email template
                $lockedTask->load(['customer', 'customer.platform', 'resourceUpgradation.details.service', 'resourceDowngradation.details.service']);
                \Illuminate\Support\Facades\Mail::to($assignedUser->email)
                    ->send(new \App\Mail\TaskAssignmentEmail($lockedTask, $sender, $actionType));
            } catch (\Exception $e) {
                 // Log error but don't stop execution
                 \Illuminate\Support\Facades\Log::error('Failed to send assignment email: ' . $e->getMessage());
            }

            return back()->with('success', 'Task assigned successfully to ' . $assignedUser->name);
        });
    }
}
