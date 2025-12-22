<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'activation_date' => 'required|date|after_or_equal:today',
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
            'platform_id' => 'nullable|exists:platforms,id',
        ]);

        $customer = Customer::create([
            ...$validated,
            'submitted_by' => Auth::id(),
        ]);

        return redirect()->route('resource-allocation.index')
            ->with('success', 'Customer created successfully. Please allocate resources.')
            ->with('new_customer_id', $customer->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
            'activation_date' => 'required|date',
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
            'platform_id' => 'nullable|exists:platforms,id',
        ]);

        $customer->update([
            ...$validated,
            'processed_by' => Auth::id(),
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customerName = $customer->customer_name;
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', "{$customerName} has been deleted successfully.");
    }
}
