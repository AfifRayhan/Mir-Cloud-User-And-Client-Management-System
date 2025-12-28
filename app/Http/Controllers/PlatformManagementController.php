<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PlatformManagementController extends Controller
{
    public function index(): View
    {
        $this->authorizeAccess();

        $platforms = Platform::orderBy('platform_name')->get();

        return view('platforms.index', compact('platforms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'platform_name' => 'required|string|max:255|unique:platforms,platform_name',
        ]);

        Platform::create([
            'platform_name' => $validated['platform_name'],
        ]);

        return redirect()->route('platforms.index')->with('success', 'Platform added successfully.');
    }

    public function destroy(Platform $platform): RedirectResponse
    {
        $this->authorizeAccess();

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
