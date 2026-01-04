<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PlatformManagementController extends Controller
{
    public function index(): View
    {
        $this->authorizeAccess();

        $platforms = Platform::with('services')->orderBy('platform_name')->get();

        return view('platforms.index', compact('platforms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'platform_name' => 'required|string|max:255|unique:platforms,platform_name',
        ]);

        $platform = Platform::create([
            'platform_name' => $validated['platform_name'],
        ]);

        // Seed default services for the new platform
        $defaultServices = [
            ['service_name' => 'vCPU', 'unit' => 'core', 'unit_price' => 500.00],
            ['service_name' => 'Memory', 'unit' => 'GB', 'unit_price' => 300.00],
            ['service_name' => 'NVMe', 'unit' => 'GB', 'unit_price' => 1000.00],
            ['service_name' => 'BS', 'unit' => 'GB', 'unit_price' => 150.00],
            ['service_name' => 'EIP', 'unit' => null, 'unit_price' => 1000.00],
            ['service_name' => 'VPN', 'unit' => null, 'unit_price' => 1500.00],
            ['service_name' => 'BDIX', 'unit' => null, 'unit_price' => 2000.00],
            ['service_name' => 'BW', 'unit' => 'Mbps', 'unit_price' => 2500.00],
        ];

        foreach ($defaultServices as $serviceData) {
            Service::create([
                'platform_id' => $platform->id,
                'service_name' => $serviceData['service_name'],
                'unit' => $serviceData['unit'],
                'unit_price' => $serviceData['unit_price'],
                'inserted_by' => Auth::id(),
            ]);
        }

        return redirect()->route('platforms.index')->with('success', 'Platform added and default services seeded successfully.');
    }

    public function destroy(Platform $platform): RedirectResponse
    {
        $this->authorizeAccess();

        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can delete platforms.');
        }

        if ($platform->customers()->exists()) {
            return redirect()->route('platforms.index')->withErrors([
                'platform' => 'Cannot delete a platform that is assigned to customers.',
            ]);
        }

        $platform->delete();

        return redirect()->route('platforms.index')->with('success', 'Platform removed successfully.');
    }

    private function authorizeAccess(): void
    {
        $user = Auth::user();
        if (! ($user?->isAdmin() || $user?->isProTechOrTech() || $user?->isManagement())) {
            abort(403, 'Unauthorized access.');
        }
    }
}
