<?php

namespace App\Http\Requests;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class ResourceAllocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action_type' => 'required|in:upgrade,downgrade,transfer',
            'status_id' => 'required_if:action_type,upgrade,downgrade|exists:customer_statuses,id',
            'transfer_type' => 'required_if:action_type,transfer|in:test_to_billable,billable_to_test',
            'task_status_id' => 'required|exists:task_statuses,id',
            'activation_date' => 'required|date',
            'inactivation_date' => 'nullable|date',
            'services' => 'required|array',
            'services.*' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $customer = $this->route('customer');
        $attributes = [];

        if ($customer) {
            $services = Service::where('platform_id', $customer->platform_id)->get();
            foreach ($services as $service) {
                $attributes['services.'.$service->id] = $service->service_name;
            }
        }

        return $attributes;
    }
}
