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
        if (!Auth::user()->isAdmin() && !Auth::user()->isProTech()) {
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

        // Get all users for assignment dropdown (Tech and Admin only)
        $users = User::whereHas('role', function($q) {
            $q->whereIn('role_name', ['tech', 'admin']);
        })->orderBy('name')->get();

        return view('task-management.index', compact('tasks', 'users'));
    }

    /**
     * Display the specified task
     */
    // public function show(Task $task)
    // {
    //     // Check authorization
    //     if (!Auth::user()->isAdmin() && !Auth::user()->isProTech()) {
    //         abort(403, 'Unauthorized access.');
    //     }

    //     $task->load(['customer', 'status', 'assignedTo', 'assignedBy', 'resourceUpgradation.details.service', 'resourceDowngradation.details.service']);

    //     return view('task-management.show', compact('task'));
    // }

    /**
     * Get task details for AJAX request (inline view)
     */
    public function getDetails(Task $task)
    {
        // Check authorization
        if (!Auth::user()->isAdmin() && !Auth::user()->isProTech()) {
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
        if (!Auth::user()->isAdmin() && !Auth::user()->isProTech()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        // Verify the assigned user is Tech or Admin
        $assignedUser = User::findOrFail($validated['assigned_to']);
        if (!$assignedUser->isAdmin() && !$assignedUser->isTech()) {
            return back()->with('error', 'Tasks can only be assigned to Tech or Admin users.');
        }

        $task->update([
            'assigned_to' => $validated['assigned_to'],
            'assigned_by' => Auth::id(),
            'assigned_at' => now(),
            'task_status_id' => 2, // Proceed from Pro Tech
        ]);

        // Send email notification to the assigned user
        try {
            $sender = Auth::user();
            $actionType = $task->allocation_type ?? 'allocation';
            // Load relationships for email template
            $task->load(['customer', 'customer.platform', 'resourceUpgradation.details.service', 'resourceDowngradation.details.service']);
            \Illuminate\Support\Facades\Mail::to($assignedUser->email)
                ->send(new \App\Mail\TaskAssignmentEmail($task, $sender, $actionType));
        } catch (\Exception $e) {
             // Log error but don't stop execution
             \Illuminate\Support\Facades\Log::error('Failed to send assignment email: ' . $e->getMessage());
        }

        return back()->with('success', 'Task assigned successfully to ' . $assignedUser->name);
    }
}
