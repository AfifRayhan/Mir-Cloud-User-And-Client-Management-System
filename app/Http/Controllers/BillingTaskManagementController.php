<?php

namespace App\Http\Controllers;

use App\Exports\KamCustomerSummaryExport;
use App\Exports\KamTasksExport;
use App\Models\Customer;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class BillingTaskManagementController extends Controller
{
    /**
     * Display a listing of completed tasks for Billing
     */
    public function index(Request $request)
    {
        // Check authorization
        if (! Auth::user()->isAdmin() && ! Auth::user()->isBill()) {
            abort(403, 'Unauthorized access.');
        }

        $query = Task::with(['customer', 'status', 'assignedTo', 'resourceUpgradation.details.service', 'resourceDowngradation.details.service', 'resourceTransfer.details.service'])
            ->leftJoin('resource_upgradations', 'tasks.resource_upgradation_id', '=', 'resource_upgradations.id')
            ->leftJoin('resource_downgradations', 'tasks.resource_downgradation_id', '=', 'resource_downgradations.id')
            ->leftJoin('resource_transfers', 'tasks.resource_transfer_id', '=', 'resource_transfers.id')
            ->whereNotNull('tasks.completed_at') // Only completed tasks
            ->orderByRaw('tasks.billed_at IS NOT NULL')
            ->orderBy('tasks.completed_at', 'ASC');

        $query->select('tasks.*');

        // Filter by Allocation Type
        if ($request->filled('allocation_type')) {
            $query->where('allocation_type', $request->allocation_type);
        }

        // Filter by Inserted By (KAM/Pro-KAM who created the resource request)
        if ($request->filled('inserted_by')) {
            $insertedBy = $request->inserted_by;
            $query->where(function ($q) use ($insertedBy) {
                $q->whereHas('resourceUpgradation', function ($sq) use ($insertedBy) {
                    $sq->where('inserted_by', $insertedBy);
                })->orWhereHas('resourceDowngradation', function ($sq) use ($insertedBy) {
                    $sq->where('inserted_by', $insertedBy);
                })->orWhereHas('resourceTransfer', function ($sq) use ($insertedBy) {
                    $sq->where('inserted_by', $insertedBy);
                });
            });
        }

        // Filter by Assigned To
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by Status (Test/Billable)
        if ($request->filled('status_id')) {
            $query->where('tasks.status_id', $request->status_id);
        }

        $tasks = $query->paginate(10)->appends($request->query());

        // Users for filters
        $kams = User::whereHas('role', function ($q) {
            $q->whereIn('role_name', ['kam', 'pro-kam']);
        })->orderBy('name')->get();

        $techs = User::whereHas('role', function ($q) {
            $q->whereIn('role_name', ['tech', 'pro-tech', 'admin']);
        })->orderBy('name')->get();

        $statuses = \App\Models\CustomerStatus::all();

        return view('task-management.billing-index', compact('tasks', 'kams', 'techs', 'statuses'));
    }

    public function getDetails(Task $task)
    {
        // Check authorization
        if (! Auth::user()->isAdmin() && ! Auth::user()->isBill()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->load(['customer', 'status', 'assignedTo', 'assignedBy', 'resourceUpgradation.details.service', 'resourceDowngradation.details.service', 'resourceTransfer.details.service']);

        return response()->json([
            'task' => $task,
            'resourceDetails' => $task->resourceDetails,
        ]);
    }

    /**
     * Export task report to Excel
     */
    public function export(Request $request)
    {
        // Check authorization
        if (! Auth::user()->isAdmin() && ! Auth::user()->isBill()) {
            abort(403, 'Unauthorized access.');
        }

        $query = Task::whereNotNull('completed_at');

        // Apply same filters as index
        if ($request->filled('allocation_type')) {
            $query->where('allocation_type', $request->allocation_type);
        }
        if ($request->filled('inserted_by')) {
            $insertedBy = $request->inserted_by;
            $query->where(function ($q) use ($insertedBy) {
                $q->whereHas('resourceUpgradation', function ($sq) use ($insertedBy) {
                    $sq->where('inserted_by', $insertedBy);
                })->orWhereHas('resourceDowngradation', function ($sq) use ($insertedBy) {
                    $sq->where('inserted_by', $insertedBy);
                })->orWhereHas('resourceTransfer', function ($sq) use ($insertedBy) {
                    $sq->where('inserted_by', $insertedBy);
                });
            });
        }
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        $tasks = $query->get();

        $userName = str_replace(' ', '_', Auth::user()->name);
        $dateTime = now()->format('Ymd_His');
        $prefix = Auth::user()->isBill() ? 'Billing' : 'Admin';
        $fileName = "{$prefix}_Task_Summary_{$userName}-{$dateTime}.xlsx";

        return Excel::download(new KamTasksExport($tasks), $fileName);
    }

    /**
     * Export customer summary to Excel
     */
    public function exportCustomers(Request $request)
    {
        // Check authorization
        if (! Auth::user()->isAdmin() && ! Auth::user()->isBill()) {
            abort(403, 'Unauthorized access.');
        }

        // For billing, show all customers who have at least one completed task (excluding transfers)
        $customers = Customer::whereHas('resourceUpgradations', function ($q) {
            $q->whereHas('task', function ($sq) {
                $sq->whereNotNull('completed_at');
            });
        })->orWhereHas('resourceDowngradations', function ($q) {
            $q->whereHas('task', function ($sq) {
                $sq->whereNotNull('completed_at');
            });
        })->with('platform')->get();

        $userName = str_replace(' ', '_', Auth::user()->name);
        $dateTime = now()->format('Ymd_His');
        $prefix = Auth::user()->isBill() ? 'Billing' : 'Admin';
        $fileName = "{$prefix}_Customer_Summary_{$userName}-{$dateTime}.xlsx";

        return Excel::download(new KamCustomerSummaryExport($customers), $fileName);
    }

    /**
     * Mark task as billed
     */
    public function bill(Task $task)
    {
        // Check authorization
        if (! Auth::user()->isAdmin() && ! Auth::user()->isBill()) {
            abort(403, 'Unauthorized access.');
        }

        if ($task->billed_at) {
            return back()->with('error', 'Task is already marked as billed.');
        }

        $task->update([
            'billed_at' => now(),
        ]);

        return back()->with('success', 'Task marked as billed successfully.');
    }
}
