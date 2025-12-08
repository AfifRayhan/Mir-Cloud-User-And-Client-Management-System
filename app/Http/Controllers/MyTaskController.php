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
            ->orderBy('assigned_at', 'desc')
            ->paginate(10);

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

    /**
     * Mark task as complete
     */
    public function complete(Task $task)
    {
        // Verify the task is assigned to the current user
        if ($task->assigned_to !== Auth::id()) {
            abort(403, 'Unauthorized access to this task.');
        }

        $task->update([
            'completed_at' => now(),
            'task_status_id' => 3, // Proceed from Tech
        ]);

        // Send email notification
        $sender = Auth::user();
        
        // Find management users
        $managementUsers = \App\Models\User::whereHas('role', function($q) {
            $q->where('role_name', 'management');
        })->get();

        // Prepare CC list (assigned_by user)
        $ccUsers = [];
        if ($task->assignedBy) {
            $ccUsers[] = $task->assignedBy->email;
        }

        // Send email to each management user
        foreach ($managementUsers as $manager) {
            $mail = \Illuminate\Support\Facades\Mail::to($manager->email);
            
            if (!empty($ccUsers)) {
                $mail->cc($ccUsers);
            }
            
            $mail->send(new \App\Mail\TaskCompletionEmail($task, $sender));
        }

        return back()->with('success', 'Task marked as complete and notification sent.');
    }
}
