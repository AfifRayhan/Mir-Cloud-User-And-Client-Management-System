<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CloudDetailController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create($customerId)
    {
        $customer = Customer::with('cloudDetail')->findOrFail($customerId);
        $cloudDetail = $customer->cloudDetail;
        return view('cloud-details.create', compact('customer', 'cloudDetail'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $customerId)
    {
        $customer = Customer::findOrFail($customerId);

        $validated = $request->validate([
            'vcpu' => 'nullable|integer|min:0',
            'ram' => 'nullable|integer|min:0',
            'storage' => 'nullable|integer|min:0',
            'real_ip' => 'nullable|boolean',
            'vpn' => 'nullable|boolean',
            'bdix' => 'nullable|boolean',
            'internet' => 'nullable|integer|min:0',
            'billing_type' => 'required|in:billable,test',
        ]);

        $payload = [
            'vcpu' => $validated['vcpu'] ?? null,
            'ram' => $validated['ram'] ?? null,
            'storage' => $validated['storage'] ?? null,
            'real_ip' => $request->boolean('real_ip'),
            'vpn' => $request->boolean('vpn'),
            'bdix' => $request->boolean('bdix'),
            'internet' => $validated['internet'] ?? null,
            'billing_type' => $validated['billing_type'],
        ];

        $customer->cloudDetail()->updateOrCreate([], $payload);

        return redirect()->route('dashboard')
            ->with('success', 'Cloud details saved successfully.');
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
