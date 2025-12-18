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
        if ($task->assigned_to !== Auth::id() && ! Auth::user()->isAdmin()) {
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
        if ($task->assigned_to !== Auth::id() && ! Auth::user()->isAdmin()) {
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

        return \Illuminate\Support\Facades\DB::transaction(function () use ($task) {
            // Lock the task record
            $lockedTask = Task::where('id', $task->id)->lockForUpdate()->first();

            // Check if already completed by a race condition
            if ($lockedTask->completed_at) {
                return back()->with('error', 'Task is already marked as complete.');
            }

            $lockedTask->update([
                'completed_at' => now(),
                'task_status_id' => 3, // Proceed from Tech
            ]);

            // Send email notification
            $sender = Auth::user();

            // Find management users
            $managementUsers = \App\Models\User::whereHas('role', function ($q) {
                $q->where('role_name', 'management');
            })->get();

            // Load relationships required by the email template and determine action type
            $lockedTask->load(['customer', 'customer.platform', 'resourceUpgradation.details.service', 'resourceDowngradation.details.service', 'assignedBy']);
            $actionType = $lockedTask->allocation_type ?? 'allocation';

            // Prepare CC list (assigned_by user)
            $ccUsers = [];
            if ($lockedTask->assignedBy) {
                $ccUsers[] = $lockedTask->assignedBy->email;
            }

            // Send email to each management user
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

            return back()->with('success', 'Task marked as complete and notification sent.');
        });
    }
}
