<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyTaskController extends Controller
{
    /**
     * Display tasks assigned to the authenticated user
     */
    public function index()
    {
        $tasks = Task::with(['customer', 'status', 'assignedBy', 'resourceUpgradation.details.service', 'resourceDowngradation.details.service'])
            ->where('assigned_to', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('my-tasks.index', compact('tasks'));
    }

    /**
     * Get task details for AJAX request (inline view)
     */
    public function getDetails(Task $task)
    {
        // Verify the task is assigned to the current user or user is admin
        if ($task->assigned_to !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->load(['customer', 'status', 'assignedTo', 'assignedBy', 'resourceUpgradation.details.service', 'resourceDowngradation.details.service']);

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
        if ($task->assigned_to !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access to this task.');
        }

        $task->load(['customer', 'status', 'assignedTo', 'assignedBy', 'resourceUpgradation.details.service', 'resourceDowngradation.details.service']);

        return view('my-tasks.show', compact('task'));
    }
}
