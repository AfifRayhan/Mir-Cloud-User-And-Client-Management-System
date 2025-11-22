<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $roleName = $user->role->role_name ?? '';

        if (in_array($roleName, ['admin', 'pro-tech'])) {
            // Admin and pro-tech can see all tasks
            $tasks = Task::with(['assignedUser', 'creator'])
                ->latest()
                ->get();
        } else {
            // Tech can only see tasks assigned to them
            $tasks = Task::with(['assignedUser', 'creator'])
                ->where('assigned_to', $user->id)
                ->latest()
                ->get();
        }

        return view('tasks.index', compact('tasks'));
    }

    public function create(): View
    {
        $user = Auth::user();
        $roleName = $user->role->role_name ?? '';

        if (!in_array($roleName, ['admin', 'pro-tech'])) {
            abort(403, 'Only admin and pro-tech roles can create tasks.');
        }

        // Get users with pro-tech or tech roles
        $assignableUsers = User::whereHas('role', function ($query) {
            $query->whereIn('role_name', ['pro-tech', 'tech']);
        })->orderBy('name')->get();

        return view('tasks.create', compact('assignableUsers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $roleName = $user->role->role_name ?? '';

        if (!in_array($roleName, ['admin', 'pro-tech'])) {
            abort(403, 'Only admin and pro-tech roles can create tasks.');
        }

        $validated = $request->validate([
            'task_name' => 'required|string|max:255',
            'task_details' => 'nullable|string',
            'assigned_to' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $assignedUser = User::find($value);
                    if ($assignedUser && $assignedUser->role) {
                        $roleName = $assignedUser->role->role_name;
                        if (!in_array($roleName, ['pro-tech', 'tech'])) {
                            $fail('Tasks can only be assigned to pro-tech or tech users.');
                        }
                    }
                },
            ],
            'task_status' => ['nullable', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
        ]);

        Task::create([
            'task_name' => $validated['task_name'],
            'task_details' => $validated['task_details'] ?? null,
            'assigned_to' => $validated['assigned_to'],
            'task_status' => $validated['task_status'] ?? 'pending',
            'created_by' => $user->id,
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'Task created and assigned successfully.');
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $user = Auth::user();
        
        // Only the assigned user can update the task status
        if ($task->assigned_to !== $user->id) {
            abort(403, 'You can only update tasks assigned to you.');
        }

        $validated = $request->validate([
            'task_status' => ['required', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
        ]);

        $task->update([
            'task_status' => $validated['task_status'],
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'Task status updated successfully.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $user = Auth::user();
        $roleName = $user->role->role_name ?? '';

        // Only pro-tech can delete tasks
        if ($roleName !== 'pro-tech') {
            abort(403, 'Only pro-tech role can delete tasks.');
        }

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}

