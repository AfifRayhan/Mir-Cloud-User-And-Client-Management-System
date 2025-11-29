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
        $services = \App\Models\Service::all();
        return view('cloud-details.create', compact('customer', 'cloudDetail', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $customerId)
    {
        $customer = Customer::findOrFail($customerId);

        $validated = $request->validate([
            'services' => 'array',
            'services.*' => 'nullable|integer|min:0',
        ]);

        $payload = [
            'inserted_by' => \Illuminate\Support\Facades\Auth::id(),
        ];

        // Map service IDs to cloud_details columns
        $services = \App\Models\Service::all();
        foreach ($services as $service) {
            $serviceId = $service->id;
            $value = $validated['services'][$serviceId] ?? 0; // Default to 0 instead of null
            
            // Debug logging
            \Log::info("Processing service: {$service->service_name} (ID: {$serviceId}), Value from form: " . ($validated['services'][$serviceId] ?? 'NOT SET') . ", Final value: {$value}");
            
            // Map service names to column names
            $columnName = $this->getColumnNameForService($service->service_name);
            if ($columnName) {
                $payload[$columnName] = $value;
                \Log::info("Mapped to column: {$columnName} = {$value}");
            }
        }

        \Log::info("Final payload before save:", $payload);

        $customer->cloudDetail()->updateOrCreate([], $payload);

        return redirect()->route('dashboard')
            ->with('success', 'Cloud details saved successfully.');
    }

    private function getColumnNameForService($serviceName)
    {
        $mapping = [
            'vCPU' => 'vcpu',
            'RAM' => 'ram',
            'Storage' => 'storage',
            'Internet' => 'internet',
            'Real IP' => 'real_ip',
            'VPN' => 'vpn',
            'BDIX' => 'bdix',
        ];

        return $mapping[$serviceName] ?? null;
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
