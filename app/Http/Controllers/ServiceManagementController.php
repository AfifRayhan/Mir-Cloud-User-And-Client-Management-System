<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServiceManagementController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAccess();

        $query = Service::with(['insertedBy', 'platform'])
            ->orderBy('service_name');

        if ($request->filled('platform_id')) {
            $query->where('platform_id', $request->platform_id);
        }

        $services = $query->get();

        $platforms = Platform::orderBy('platform_name')->get();
        $editableService = null;

        if ($request->query('service')) {
            $editableService = Service::findOrFail($request->query('service'));
        }

        return view('services.index', compact('services', 'editableService', 'platforms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'platform_id' => 'required|exists:platforms,id',
            'service_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('services')->where(function ($query) use ($request) {
                    return $query->where('platform_id', $request->platform_id);
                }),
            ],
            'unit' => 'nullable|string|max:255',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        Service::create([
            'platform_id' => $validated['platform_id'],
            'service_name' => $validated['service_name'],
            'unit' => $validated['unit'] ?? null,
            'unit_price' => $validated['unit_price'] ?? null,
            'inserted_by' => Auth::id(),
        ]);

        return redirect()->route('services.index')->with('success', 'Service created successfully.');
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'platform_id' => 'required|exists:platforms,id',
            'service_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('services')->where(function ($query) use ($request) {
                    return $query->where('platform_id', $request->platform_id);
                })->ignore($service->id),
            ],
            'unit' => 'nullable|string|max:255',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        $service->update([
            'platform_id' => $validated['platform_id'],
            'service_name' => $validated['service_name'],
            'unit' => $validated['unit'] ?? null,
            'unit_price' => $validated['unit_price'] ?? null,
            'inserted_by' => $service->inserted_by ?? Auth::id(),
        ]);

        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $this->authorizeAccess();

        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can delete services.');
        }

        $service->delete();

        return redirect()->route('services.index')->with('success', 'Service removed successfully.');
    }

    private function authorizeAccess(): void
    {
        $user = Auth::user();
        if (! ($user?->isAdmin() || $user?->isProTech() || $user?->isManagement())) {
            abort(403, 'Unauthorized access.');
        }
    }
}
