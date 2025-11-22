<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ResourceAllocationController extends Controller
{
    public function index(): View
    {
        $customers = Customer::with('cloudDetail')
            ->orderBy('customer_name')
            ->get();

        return view('resource-allocation.index', compact('customers'));
    }

    public function process(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'action_type' => ['required', Rule::in(['dismantle', 'rewrite'])],
        ]);

        $customer = Customer::with('cloudDetail')->findOrFail($validated['customer_id']);

        if ($validated['action_type'] === 'dismantle') {
            if ($customer->cloudDetail) {
                $customer->cloudDetail()->delete();

                return redirect()
                    ->route('resource-allocation.index')
                    ->with('success', "{$customer->customer_name}'s resources have been dismantled.");
            }

            return redirect()
                ->route('resource-allocation.index')
                ->with('info', "{$customer->customer_name} does not have active cloud resources to dismantle.");
        }

        // Rewrite action - redirect to cloud details form
        return redirect()
            ->route('cloud-details.create', $customer->id)
            ->with('info', "You can now rewrite the cloud details for {$customer->customer_name}.");
    }
}

