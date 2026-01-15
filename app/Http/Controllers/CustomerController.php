<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Platform;
use App\Models\ResourceDowngradation;
use App\Models\ResourceUpgradation;
use App\Models\Service;
use App\Models\Summary;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mostafaznv\PdfOptimizer\Laravel\Facade\PdfOptimizer;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::with(['submitter', 'processor'])->latest()->paginate(10);

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $platforms = Platform::orderBy('platform_name')->get();

        return view('customers.create', compact('platforms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_activation_date' => 'required|date|after_or_equal:today',
            'customer_address' => 'nullable|string|max:500',
            'bin_number' => 'nullable|string|max:50',
            'po_number' => 'nullable|string|max:50',
            'commercial_contact_name' => 'nullable|string|max:100',
            'commercial_contact_designation' => 'nullable|string|max:100',
            'commercial_contact_email' => 'nullable|email|max:100',
            'commercial_contact_phone' => 'nullable|string|max:20',
            'technical_contact_name' => 'nullable|string|max:100',
            'technical_contact_designation' => 'nullable|string|max:100',
            'technical_contact_email' => 'nullable|email|max:100',
            'technical_contact_phone' => 'nullable|string|max:20',
            'optional_contact_name' => 'nullable|string|max:100',
            'optional_contact_designation' => 'nullable|string|max:100',
            'optional_contact_email' => 'nullable|email|max:100',
            'optional_contact_phone' => 'nullable|string|max:20',
            'platform_id' => 'required|exists:platforms,id',
            'po_project_sheets' => 'nullable|array',
            'po_project_sheets.*' => 'file|mimes:pdf|max:20480',
        ]);

        $poSheets = [];
        if ($request->hasFile('po_project_sheets')) {
            foreach ($request->file('po_project_sheets') as $file) {
                $poSheets[] = $this->optimizeAndStorePdf($file, $request->po_number);
            }
        }

        $customer = Customer::create([
            ...$validated,
            'po_project_sheets' => $poSheets,
            'submitted_by' => Auth::id(),
        ]);

        $redirectRoute = Auth::user()->isTech() ? 'tech-resource-allocation.index' : 'resource-allocation.index';

        return redirect()->route($redirectRoute)
            ->with('success', 'Customer created successfully. Please allocate resources.')
            ->with('new_customer_id', $customer->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $customer->load(['submitter', 'processor', 'platform']);

        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $platforms = Platform::orderBy('platform_name')->get();

        return view('customers.edit', compact('customer', 'platforms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_activation_date' => 'required|date',
            'customer_address' => 'nullable|string|max:500',
            'bin_number' => 'nullable|string|max:50',
            'po_number' => 'nullable|string|max:50',
            'commercial_contact_name' => 'nullable|string|max:100',
            'commercial_contact_designation' => 'nullable|string|max:100',
            'commercial_contact_email' => 'nullable|email|max:100',
            'commercial_contact_phone' => 'nullable|string|max:20',
            'technical_contact_name' => 'nullable|string|max:100',
            'technical_contact_designation' => 'nullable|string|max:100',
            'technical_contact_email' => 'nullable|email|max:100',
            'technical_contact_phone' => 'nullable|string|max:20',
            'optional_contact_name' => 'nullable|string|max:100',
            'optional_contact_designation' => 'nullable|string|max:100',
            'optional_contact_email' => 'nullable|email|max:100',
            'optional_contact_phone' => 'nullable|string|max:20',
            'platform_id' => 'required|exists:platforms,id',
            'po_project_sheets' => 'nullable|array',
            'po_project_sheets.*' => 'file|mimes:pdf|max:20480',
            'removed_sheets' => 'nullable|array',
        ]);

        $poSheets = $customer->po_project_sheets ?? [];

        // Handle removals
        if ($request->has('removed_sheets')) {
            foreach ($request->removed_sheets as $index) {
                if (isset($poSheets[$index])) {
                    Storage::disk('public')->delete($poSheets[$index]['path']);
                    unset($poSheets[$index]);
                }
            }
            $poSheets = array_values($poSheets);
        }

        // Handle new uploads
        if ($request->hasFile('po_project_sheets')) {
            foreach ($request->file('po_project_sheets') as $file) {
                // Use new PO number if present, otherwise fallback to existing
                $poNumber = $request->po_number ?? $customer->po_number;
                $poSheets[] = $this->optimizeAndStorePdf($file, $poNumber);
            }
        }

        $customer->update([
            ...$validated,
            'po_project_sheets' => $poSheets,
            'processed_by' => Auth::id(),
        ]);

        // Check for platform change and trigger automated tasks
        if ($customer->wasChanged('platform_id')) {
            $this->handlePlatformChangeMigration($customer, $customer->getOriginal('platform_id'), $customer->platform_id);
        }

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Get PO Project Sheets for a customer (AJAX).
     */
    public function getPoSheets(Customer $customer)
    {
        return response()->json([
            'success' => true,
            'po_project_sheets' => $customer->po_project_sheets ?? [],
        ]);
    }

    /**
     * Upload PO Project Sheets for a customer (AJAX).
     */
    public function uploadPoSheets(Request $request, Customer $customer)
    {
        $request->validate([
            'po_project_sheets' => 'required|array',
            'po_project_sheets.*' => 'file|mimes:pdf|max:20480',
        ]);

        $poSheets = $customer->po_project_sheets ?? [];

        if ($request->hasFile('po_project_sheets')) {
            foreach ($request->file('po_project_sheets') as $file) {
                $poSheets[] = $this->optimizeAndStorePdf($file, $customer->po_number);
            }
        }

        $customer->update(['po_project_sheets' => $poSheets]);

        return response()->json([
            'success' => true,
            'message' => 'PO Project Sheets uploaded successfully.',
            'po_project_sheets' => $poSheets,
        ]);
    }

    /**
     * Optimize and store PDF file.
     */
    private function optimizeAndStorePdf($file, $poNumber = null): array
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();

        // Sanitize PO Number (replace illegal chars with underscores)
        $safePoNumber = $poNumber ? preg_replace('/[^A-Za-z0-9_\-]/', '_', $poNumber) : null;

        // Construct filename: OriginalName_PONumber.pdf
        // If PO Number is missing, fall back to timestamp to ensure uniqueness
        if ($safePoNumber) {
            $fileName = "{$originalName}_{$safePoNumber}.{$extension}";
        } else {
            $fileName = "{$originalName}_".time().".{$extension}";
        }

        // Ensure uniqueness by checking if file exists, appending counter if necessary
        $directory = 'customer_po_sheets';
        $finalPath = $directory.'/'.$fileName;
        $counter = 1;

        while (Storage::disk('public')->exists($finalPath)) {
            if ($safePoNumber) {
                $fileName = "{$originalName}_{$safePoNumber}_{$counter}.{$extension}";
            } else {
                $fileName = "{$originalName}_".time()."_{$counter}.{$extension}";
            }
            $finalPath = $directory.'/'.$fileName;
            $counter++;
        }

        if (! Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $tempPath = $file->getRealPath();
        $absolutePath = Storage::disk('public')->path($finalPath);

        try {
            // Attempt to optimize
            PdfOptimizer::open($tempPath)
                ->optimize($absolutePath);

            // Check if file was actually created/optimized
            if (! file_exists($absolutePath) || filesize($absolutePath) === 0) {
                throw new \Exception("Optimization failed to create file at $absolutePath");
            }
        } catch (\Exception $e) {
            // Fallback to regular upload if optimization fails
            Storage::disk('public')->put($finalPath, file_get_contents($tempPath));
        }

        // Final check to avoid Flysystem error
        if (! Storage::disk('public')->exists($finalPath)) {
            throw new \Exception("File could not be stored at $finalPath");
        }

        return [
            'name' => $fileName, // Store the actual filename on disk as the 'name'
            'path' => $finalPath,
            'size' => Storage::disk('public')->size($finalPath),
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        // Check authorization (Only Admin or Management can delete)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isManagement()) {
            abort(403, 'Unauthorized access.');
        }

        $customerName = $customer->customer_name;
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', "{$customerName} has been deleted successfully.");
    }

    /**
     * Handle automated task creation for platform migration.
     */
    use \App\Traits\CalculatesDeadlines;

    /**
     * Handle automated task creation for platform migration.
     */
    protected function handlePlatformChangeMigration(Customer $customer, $oldPlatformId, $newPlatformId)
    {
        // Get all summaries (current resources) for the customer
        // We need to fetch services belonging to the OLD platform to get correct current usage
        // Note: The Summary model links to Service. Even if we changed platform_id on customer,
        // the existing summary rows still point to the old service_ids.
        $summaries = Summary::where('customer_id', $customer->id)
            ->where(function ($q) {
                $q->where('test_quantity', '>', 0)
                    ->orWhere('billable_quantity', '>', 0);
            })
            ->with('service') // Eager load service to get names
            ->get();

        if ($summaries->isEmpty()) {
            return;
        }

        $testSummaries = $summaries->where('test_quantity', '>', 0);
        $billableSummaries = $summaries->where('billable_quantity', '>', 0);

        // Calculate deadline: 8 working hours from now
        $deadline = $this->calculateDeadline(now());

        DB::transaction(function () use ($customer, $testSummaries, $billableSummaries, $newPlatformId, $deadline) {
            // Task Status ID 1 = Proceed from KAM (Pending Tech Action)
            $pendingTaskStatusId = 1;

            // Handle Test Resources (Status ID 2 = Test)
            if ($testSummaries->isNotEmpty()) {
                $this->createMigrationTasks($customer, $testSummaries, 2, 'test_quantity', $newPlatformId, $pendingTaskStatusId, $deadline);
            }

            // Handle Billable Resources (Status ID 1 = Billable)
            if ($billableSummaries->isNotEmpty()) {
                $this->createMigrationTasks($customer, $billableSummaries, 1, 'billable_quantity', $newPlatformId, $pendingTaskStatusId, $deadline);
            }
        });
    }

    protected function createMigrationTasks(Customer $customer, $summaries, $statusId, $quantityColumn, $newPlatformId, $taskStatusId, $deadline)
    {
        // 1. Upgrade Task (Re-allocation)
        $upgrade = ResourceUpgradation::create([
            'customer_id' => $customer->id,
            'status_id' => $statusId,
            'activation_date' => now(),
            'task_status_id' => $taskStatusId,
            'inserted_by' => Auth::id(),
            'assignment_datetime' => now(),
            'deadline_datetime' => $deadline,
        ]);

        // Fetch all services for the new platform to match against
        $newPlatformServices = Service::where('platform_id', $newPlatformId)->get();

        $hasUpgradeDetails = false;
        foreach ($summaries as $summary) {
            $oldService = $summary->service;
            if (! $oldService) {
                continue;
            }

            $oldServiceName = $oldService->service_name;

            // normalize function for comparison
            $normalize = function ($name) {
                $name = strtolower(trim($name));

                return ($name === 'nvme') ? 'ssd' : $name;
            };

            $normalizedOldName = $normalize($oldServiceName);

            // Find matching service on new platform
            $newService = $newPlatformServices->first(function ($service) use ($normalize, $normalizedOldName) {
                return $normalize($service->service_name) === $normalizedOldName;
            });

            if ($newService) {
                $currentQty = $summary->{$quantityColumn};
                $upgrade->details()->create([
                    'service_id' => $newService->id,
                    'quantity' => $currentQty, // New Total (0 + current)
                    'upgrade_amount' => $currentQty, // Increase by current
                ]);
                $hasUpgradeDetails = true;
            }
        }

        // Fetch Pro-Tech users for email notification
        $proTechUsers = \App\Models\User::whereHas('role', function ($q) {
            $q->where('role_name', 'pro-tech');
        })->get();
        $sender = Auth::user();

        if ($hasUpgradeDetails) {
            $upgradeTask = Task::create([
                'customer_id' => $customer->id,
                'status_id' => $statusId,
                'task_status_id' => $taskStatusId,
                'allocation_type' => 'upgrade',
                'resource_upgradation_id' => $upgrade->id,
                'assigned_by' => Auth::id(),
                'activation_date' => now(),
                'assignment_datetime' => now(),
                'deadline_datetime' => $deadline,
            ]);

            // Send email to Pro-Techs
            foreach ($proTechUsers as $proTech) {
                \Illuminate\Support\Facades\Mail::to($proTech->email)
                    ->send(new \App\Mail\RecommendationSubmissionEmail($upgradeTask, $sender, 'upgrade'));
            }
        } else {
            // Cleanup empty upgrade if no services matched
            $upgrade->delete();
        }

        // 2. Dismantle Task (Downgrade)
        $downgrade = ResourceDowngradation::create([
            'customer_id' => $customer->id,
            'status_id' => $statusId,
            'activation_date' => now(), // Assume immediate effect
            'task_status_id' => $taskStatusId, // Unused in logic but good for record
            'inserted_by' => Auth::id(),
            'assignment_datetime' => now(),
            'deadline_datetime' => $deadline,
        ]);

        foreach ($summaries as $summary) {
            $currentQty = $summary->{$quantityColumn};
            $downgrade->details()->create([
                'service_id' => $summary->service_id,
                'quantity' => 0, // New Total (current - current)
                'downgrade_amount' => $currentQty, // Remove all
            ]);
        }

        $downgradeTask = Task::create([
            'customer_id' => $customer->id,
            'status_id' => $statusId,
            'task_status_id' => $taskStatusId,
            'allocation_type' => 'downgrade',
            'resource_downgradation_id' => $downgrade->id,
            'assigned_by' => Auth::id(),
            'activation_date' => now(),
            'assignment_datetime' => now(),
            'deadline_datetime' => $deadline,
        ]);

        // Send email to Pro-Techs
        foreach ($proTechUsers as $proTech) {
            \Illuminate\Support\Facades\Mail::to($proTech->email)
                ->send(new \App\Mail\RecommendationSubmissionEmail($downgradeTask, $sender, 'downgrade'));
        }
    }
}
