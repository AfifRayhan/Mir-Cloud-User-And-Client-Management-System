<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerStatus;
use App\Models\Role;
use App\Models\Service;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechResourceAllocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tech_user_can_allocate_resources_and_values_update()
    {
        // Setup
        $techRole = Role::create(['role_name' => 'tech', 'role_label' => 'Tech']);
        $kamRole = Role::create(['role_name' => 'kam', 'role_label' => 'KAM']);

        $techUser = User::factory()->create(['role_id' => $techRole->id]);
        $kamUser = User::factory()->create(['role_id' => $kamRole->id]);

        $service = Service::create(['service_name' => 'vCPU', 'unit' => 'Core']);

        $customerStatus = CustomerStatus::create(['name' => 'Production']);
        $taskStatus1 = TaskStatus::create(['name' => 'Proceed from KAM']); // 1
        $taskStatus2 = TaskStatus::create(['name' => 'Pending']); // 2
        $taskStatus3 = TaskStatus::create(['name' => 'Proceed from Tech']); // 3

        // Create Dependencies
        $platform = \App\Models\Platform::create(['platform_name' => 'Test Platform']);

        $customer = Customer::create([
            'customer_name' => 'Test Customer',
            'activation_date' => now(),
            'platform_id' => $platform->id,
            'submitted_by' => $kamUser->id,
        ]);

        // Authenticate
        $this->actingAs($techUser);

        // Action: Allocate Resources (Upgrade)
        $response = $this->postJson(route('tech-resource-allocation.store', $customer), [
            'action_type' => 'upgrade',
            'status_id' => $customerStatus->id,
            'activation_date' => now()->format('Y-m-d'),
            'services' => [
                $service->id => 5, // Increase by 5
            ],
        ]);

        // Assert Response
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Assert Database State
        $this->assertDatabaseHas('resource_upgradations', [
            'customer_id' => $customer->id,
            'status_id' => $customerStatus->id,
        ]);

        $this->assertDatabaseHas('resource_upgradation_details', [
            'service_id' => $service->id,
            'upgrade_amount' => 5,
            'quantity' => 5, // 0 + 5
        ]);

        // Verify Customer Resource Quantity
        $this->assertEquals(5, $customer->fresh()->getResourceQuantity('vCPU'));
        $this->assertTrue($customer->fresh()->hasResourceAllocations());

        // Second Action: Upgrade again
        $response2 = $this->postJson(route('tech-resource-allocation.store', $customer), [
            'action_type' => 'upgrade',
            'status_id' => $customerStatus->id,
            'activation_date' => now()->addDay()->format('Y-m-d'),
            'services' => [
                $service->id => 2, // Increase by 2
            ],
        ]);

        $response2->assertStatus(200);

        // Verify Updated Customer Resource Quantity
        $this->assertEquals(7, $customer->fresh()->getResourceQuantity('vCPU'));
    }
}
