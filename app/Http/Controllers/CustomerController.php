<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'customer_name' => 'required|string|max:255',
            'customer_activation_date' => 'required|date|after_or_equal:today',
            'customer_address' => 'nullable|string',
            'bin_number' => 'nullable|string|max:255',
            'po_number' => 'nullable|string|max:255',
            'commercial_contact_name' => 'nullable|string|max:255',
            'commercial_contact_designation' => 'nullable|string|max:255',
            'commercial_contact_email' => 'nullable|email|max:255',
            'commercial_contact_phone' => 'nullable|string|max:255',
            'technical_contact_name' => 'nullable|string|max:255',
            'technical_contact_designation' => 'nullable|string|max:255',
            'technical_contact_email' => 'nullable|email|max:255',
            'technical_contact_phone' => 'nullable|string|max:255',
            'optional_contact_name' => 'nullable|string|max:255',
            'optional_contact_designation' => 'nullable|string|max:255',
            'optional_contact_email' => 'nullable|email|max:255',
            'optional_contact_phone' => 'nullable|string|max:255',
            'platform_id' => 'required|exists:platforms,id',
            'po_project_sheets' => 'nullable|array',
            'po_project_sheets.*' => 'file|mimes:pdf|max:20480',
        ]);

        $poSheets = [];
        if ($request->hasFile('po_project_sheets')) {
            foreach ($request->file('po_project_sheets') as $file) {
                $poSheets[] = $this->optimizeAndStorePdf($file);
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
            'customer_name' => 'required|string|max:255',
            'customer_activation_date' => 'required|date',
            'customer_address' => 'nullable|string',
            'bin_number' => 'nullable|string|max:255',
            'po_number' => 'nullable|string|max:255',
            'commercial_contact_name' => 'nullable|string|max:255',
            'commercial_contact_designation' => 'nullable|string|max:255',
            'commercial_contact_email' => 'nullable|email|max:255',
            'commercial_contact_phone' => 'nullable|string|max:255',
            'technical_contact_name' => 'nullable|string|max:255',
            'technical_contact_designation' => 'nullable|string|max:255',
            'technical_contact_email' => 'nullable|email|max:255',
            'technical_contact_phone' => 'nullable|string|max:255',
            'optional_contact_name' => 'nullable|string|max:255',
            'optional_contact_designation' => 'nullable|string|max:255',
            'optional_contact_email' => 'nullable|email|max:255',
            'optional_contact_phone' => 'nullable|string|max:255',
            'platform_id' => 'required|exists:platforms,id',
            'po_project_sheets' => 'nullable|array',
            'po_project_sheets.*' => 'file|mimes:pdf|max:20480',
            'removed_sheets' => 'nullable|array',
        ]);

        $poSheets = $customer->po_project_sheets ?? [];

        // Handle removals
        if ($request->filled('removed_sheets')) {
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
                $poSheets[] = $this->optimizeAndStorePdf($file);
            }
        }

        $customer->update([
            ...$validated,
            'po_project_sheets' => $poSheets,
            'processed_by' => Auth::id(),
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Optimize and store PDF file.
     */
    private function optimizeAndStorePdf($file): array
    {
        $originalName = $file->getClientOriginalName();
        $fileName = time().'_'.uniqid().'.pdf';
        $directory = 'customer_po_sheets';

        if (! Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $tempPath = $file->getRealPath();
        $finalPath = $directory.'/'.$fileName;
        $absolutePath = storage_path('app/public/'.$finalPath);

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
            'name' => $originalName,
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
}
