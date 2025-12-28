<?php

namespace App\Http\Controllers;

use App\Mail\TaskAssignmentEmail;
use App\Mail\TaskCompletionEmail;
use App\Models\ResourceDowngradation;
use App\Models\ResourceDowngradationDetail;
use App\Models\ResourceUpgradation;
use App\Models\ResourceUpgradationDetail;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TaskActionController extends Controller
{
    /**
     * Approve a task and notify management.
     */
    public function approve(Request $request, Task $task)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired signature.');
        }

        // Ensure user is logged in
        if (! Auth::check()) {
            return redirect()->route('login')->with('info', 'Please log in to approve this task.');
        }

        // Ensure the logged-in user is the one who assigned the task (the KAM)
        if (Auth::user()->id !== $task->assigned_by) {
            abort(403, 'Only the KAM who assigned this task can approve it.');
        }

        // The task is completed by Tech and waiting for KAM approval
        if (! $task->completed_at) {
            return view('tasks.action-result', [
                'success' => false,
                'message' => 'This task has not been completed yet.',
            ]);
        }

        // Send email to management as if from the Tech user
        $techUser = $task->assignedTo;
        $kam = $task->assignedBy;

        if (! $techUser) {
            return view('tasks.action-result', [
                'success' => false,
                'message' => 'No technician assigned to this task.',
            ]);
        }

        // Find management users
        $managementUsers = User::whereHas('role', function ($q) {
            $q->where('role_name', 'management');
        })->get();

        $actionType = $task->allocation_type ?? 'allocation';
        $ccUsers = $kam ? [$kam->email] : [];

        $lockedTask = $task->load(['customer', 'customer.platform', 'resourceUpgradation.details.service', 'resourceDowngradation.details.service', 'assignedBy', 'vdc']);

        foreach ($managementUsers as $manager) {
            try {
                $mail = Mail::to($manager->email);
                if (! empty($ccUsers)) {
                    $mail->cc($ccUsers);
                }
                $mail->send(new TaskCompletionEmail($lockedTask, $techUser, $actionType));
            } catch (\Exception $e) {
                Log::error('Failed to send completion email to '.$manager->email.' during approval: '.$e->getMessage());
            }
        }

        return view('tasks.action-result', [
            'success' => true,
            'message' => 'Task approved successfully. Management has been notified.',
        ]);
    }

    /**
     * Undo a task by reversing the allocation and creating a new task for tech.
     */
    public function undo(Request $request, Task $task)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired signature.');
        }

        // Ensure user is logged in
        if (! Auth::check()) {
            return redirect()->route('login')->with('info', 'Please log in to undo this task.');
        }

        // Ensure the logged-in user is the one who assigned the task (the KAM)
        if (Auth::user()->id !== $task->assigned_by) {
            abort(403, 'Only the KAM who assigned this task can undo it.');
        }

        if (! $task->completed_at) {
            return view('tasks.action-result', [
                'success' => false,
                'message' => 'This task has not been completed yet.',
            ]);
        }

        return DB::transaction(function () use ($task) {
            $originalType = $task->allocation_type;
            $newType = ($originalType === 'upgrade') ? 'downgrade' : 'upgrade';
            $customer = $task->customer;
            $techUser = $task->assignedTo;
            $kam = $task->assignedBy; // The one who clicked undo

            if (! $techUser || ! $kam) {
                return view('tasks.action-result', [
                    'success' => false,
                    'message' => 'Missing technician or KAM information.',
                ]);
            }

            // 1. Create reverse allocation
            $newResourceId = null;
            if ($newType === 'upgrade') {
                $upgradation = ResourceUpgradation::create([
                    'customer_id' => $customer->id,
                    'status_id' => $customer->status_id,
                    'activation_date' => now(),
                    'inactivation_date' => '3000-01-01',
                    'task_status_id' => 1, // Assigned
                    'inserted_by' => $kam->id,
                ]);
                $newResourceId = $upgradation->id;

                // Copy details from the original downgrade (reversing decrease to increase)
                $originalDetails = ResourceDowngradationDetail::where('resource_downgradation_id', $task->resource_downgradation_id)->get();
                foreach ($originalDetails as $detail) {
                    $amount = $detail->downgrade_amount;
                    ResourceUpgradationDetail::create([
                        'resource_upgradation_id' => $upgradation->id,
                        'service_id' => $detail->service_id,
                        'quantity' => $detail->quantity + $amount, // Previous qty was quantity after downgrade
                        'upgrade_amount' => $amount,
                    ]);
                }
            } else {
                $downgradation = ResourceDowngradation::create([
                    'customer_id' => $customer->id,
                    'status_id' => $customer->status_id,
                    'activation_date' => now(),
                    'inactivation_date' => '3000-01-01',
                    'task_status_id' => 1, // Assigned
                    'inserted_by' => $kam->id,
                ]);
                $newResourceId = $downgradation->id;

                // Copy details from the original upgrade (reversing increase to decrease)
                $originalDetails = ResourceUpgradationDetail::where('resource_upgradation_id', $task->resource_upgradation_id)->get();
                foreach ($originalDetails as $detail) {
                    $amount = $detail->upgrade_amount;
                    ResourceDowngradationDetail::create([
                        'resource_downgradation_id' => $downgradation->id,
                        'service_id' => $detail->service_id,
                        'quantity' => max(0, $detail->quantity - $amount),
                        'downgrade_amount' => $amount,
                    ]);
                }
            }

            // 2. Create new Task
            $newTask = Task::create([
                'customer_id' => $customer->id,
                'allocation_type' => $newType,
                'resource_upgradation_id' => ($newType === 'upgrade') ? $newResourceId : null,
                'resource_downgradation_id' => ($newType === 'downgrade') ? $newResourceId : null,
                'assigned_to' => $techUser->id,
                'assigned_by' => $kam->id,
                'task_status_id' => 1, // Assigned
                'activation_date' => now(),
                'completed_at' => null,
                'vdc_id' => $task->vdc_id,
            ]);

            // 3. Send TaskAssignmentEmail to Tech
            try {
                Mail::to($techUser->email)->send(new TaskAssignmentEmail($newTask, $kam, $newType));
            } catch (\Exception $e) {
                Log::error('Failed to send assignment email to '.$techUser->email.' during undo: '.$e->getMessage());
            }

            return view('tasks.action-result', [
                'success' => true,
                'message' => 'Task undone successfully. A reverse task has been assigned to '.$techUser->name.'.',
            ]);
        });
    }
}
