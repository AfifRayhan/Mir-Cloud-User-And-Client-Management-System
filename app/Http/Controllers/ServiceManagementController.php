<?php

namespace App\Http\Controllers;

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
        $this->authorizeAdmin();

        $services = Service::with('insertedBy')
            ->orderBy('service_name')
            ->get();
        $editableService = null;

        if ($request->query('service')) {
            $editableService = Service::findOrFail($request->query('service'));
        }

        return view('services.index', compact('services', 'editableService'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'service_name' => 'required|string|max:255|unique:services,service_name',
            'unit' => 'nullable|string|max:255',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        Service::create([
            'service_name' => $validated['service_name'],
            'unit' => $validated['unit'] ?? null,
            'unit_price' => $validated['unit_price'] ?? null,
            'inserted_by' => Auth::id(),
        ]);

        return redirect()->route('services.index')->with('success', 'Service created successfully.');
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'service_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('services', 'service_name')->ignore($service->id),
            ],
            'unit' => 'nullable|string|max:255',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        $service->update([
            'service_name' => $validated['service_name'],
            'unit' => $validated['unit'] ?? null,
            'unit_price' => $validated['unit_price'] ?? null,
            'inserted_by' => $service->inserted_by ?? Auth::id(),
        ]);

        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $this->authorizeAdmin();

        $service->delete();

        return redirect()->route('services.index')->with('success', 'Service removed successfully.');
    }

    private function authorizeAdmin(): void
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403, 'Only administrators can manage services.');
        }
    }
}
