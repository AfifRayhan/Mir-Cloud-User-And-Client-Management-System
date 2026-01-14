<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerStatus;
use App\Models\Platform;
use App\Models\Role;
use App\Models\Service;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResourceTransferTaskTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic data in correct order
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\CustomerStatusSeeder::class);
        $this->seed(\Database\Seeders\TaskStatusSeeder::class);

        // ServiceSeeder needs Platforms to exist
        Platform::create(['platform_name' => 'ACS']);
        Platform::create(['platform_name' => 'Huawei']);

        $this->seed(\Database\Seeders\ServiceSeeder::class);
    }

    public function test_resource_transfer_creates_task_automatically(): void
    {
        $this->withoutExceptionHandling();
        $kamRole = Role::where('role_name', 'kam')->first();
        $kam = User::factory()->create(['role_id' => $kamRole->id]);

        $platform = Platform::where('platform_name', 'ACS')->first();
        $service = Service::where('platform_id', $platform->id)->first();

        $customer = Customer::create([
            'customer_name' => 'Test Customer',
            'customer_address' => 'Test Address',
            'platform_id' => $platform->id,
            'kam_id' => $kam->id,
            'customer_activation_date' => now()->subDay(),
            'account_manager_name' => 'AM Name',
            'technical_person_name' => 'TP Name',
        ]);

        $testStatus = CustomerStatus::where('name', 'Test')->first();
        $summary = \App\Models\Summary::create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'quantity' => 10,
            'test_quantity' => 10,
            'billable_quantity' => 0,
            'customer_status_id' => $testStatus->id,
        ]);

        $taskStatus = TaskStatus::first();

        $this->actingAs($kam);

        $response = $this->post(route('resource-allocation.store', $customer->id), [
            'action_type' => 'transfer',
            'transfer_type' => 'test_to_billable',
            'customer_id' => $customer->id,
            'activation_date' => now()->format('Y-m-d'),
            'task_status_id' => $taskStatus->id,
            'services' => [
                $service->id => 5, // Transfer 5 units
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify task was created
        $task = Task::where('customer_id', $customer->id)->where('allocation_type', 'transfer')->first();
        $this->assertNotNull($task);
        $this->assertEquals('transfer', $task->allocation_type);
        $this->assertNotNull($task->resource_transfer_id);

        // Verify defaults
        $this->assertNotNull($task->completed_at);
        $this->assertNotNull($task->assigned_at);
        $this->assertEquals($kam->id, $task->assigned_by);
        $this->assertEquals($kam->id, $task->assigned_to);

        // Verify Task ID contains the '2' bit
        $this->assertEquals($task->generateTaskId(), $task->task_id);
    }

    public function test_transfer_task_visibility_in_kam_task_management(): void
    {
        $this->withoutExceptionHandling();
        $kamRole = Role::where('role_name', 'kam')->first();
        $kam = User::factory()->create(['role_id' => $kamRole->id]);
        $otherKam = User::factory()->create(['role_id' => $kamRole->id]);

        $platform = Platform::where('platform_name', 'ACS')->first();
        $service = Service::where('platform_id', $platform->id)->first();

        $customer = Customer::create([
            'customer_name' => 'KAM Customer',
            'customer_address' => 'Test Address',
            'platform_id' => $platform->id,
            'kam_id' => $kam->id,
            'customer_activation_date' => now()->subDay(),
            'account_manager_name' => 'AM Name',
            'technical_person_name' => 'TP Name',
        ]);

        $testStatus = CustomerStatus::where('name', 'Test')->first();
        \App\Models\Summary::create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'test_quantity' => 10,
            'quantity' => 10,
            'customer_status_id' => $testStatus->id,
        ]);

        $taskStatus = TaskStatus::first();

        // Trigger transfer via controller to create task
        $this->actingAs($kam);
        $this->post(route('resource-allocation.store', $customer->id), [
            'action_type' => 'transfer',
            'transfer_type' => 'test_to_billable',
            'customer_id' => $customer->id,
            'activation_date' => now()->format('Y-m-d'),
            'task_status_id' => $taskStatus->id,
            'services' => [$service->id => 1],
        ]);

        // KAM should see the task
        $response = $this->get(route('kam-task-management.index'));
        $response->assertStatus(200);
        $response->assertSee('KAM Customer');
        $response->assertSee('Transfer');

        // Other KAM should NOT see the task
        $this->actingAs($otherKam);
        $response = $this->get(route('kam-task-management.index'));
        $response->assertStatus(200);
        $response->assertDontSee('KAM Customer');
    }

    public function test_transfer_task_visibility_in_billing_task_management(): void
    {
        $this->withoutExceptionHandling();
        $kamRole = Role::where('role_name', 'kam')->first();
        $kam = User::factory()->create(['role_id' => $kamRole->id]);

        $billingRole = Role::where('role_name', 'bill')->first();
        $billingUser = User::factory()->create(['role_id' => $billingRole->id]);

        $platform = Platform::where('platform_name', 'ACS')->first();
        $service = Service::where('platform_id', $platform->id)->first();

        $customer = Customer::create([
            'customer_name' => 'Billing Customer',
            'customer_address' => 'Test Address',
            'platform_id' => $platform->id,
            'kam_id' => $kam->id,
            'customer_activation_date' => now()->subDay(),
            'account_manager_name' => 'AM Name',
            'technical_person_name' => 'TP Name',
        ]);

        $testStatus = CustomerStatus::where('name', 'Test')->first();
        \App\Models\Summary::create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'test_quantity' => 10,
            'quantity' => 10,
            'customer_status_id' => $testStatus->id,
        ]);

        $taskStatus = TaskStatus::first();

        // Trigger transfer
        $this->actingAs($kam);
        $this->post(route('resource-allocation.store', $customer->id), [
            'action_type' => 'transfer',
            'transfer_type' => 'test_to_billable',
            'customer_id' => $customer->id,
            'activation_date' => now()->format('Y-m-d'),
            'task_status_id' => $taskStatus->id,
            'services' => [$service->id => 1],
        ]);

        // Billing user should see the task
        $this->actingAs($billingUser);
        $response = $this->get(route('billing-task-management.index'));
        $response->assertStatus(200);
        $response->assertSee('Billing Customer');
        $response->assertSee('Transfer');

        $taskId = Task::where('customer_id', $customer->id)->where('allocation_type', 'transfer')->first()->task_id;

        $adminRole = Role::where('role_name', 'admin')->first();
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        // 4. Verify NOT visible in main Task Management
        $this->actingAs($admin)
            ->get(route('task-management.index'))
            ->assertStatus(200)
            ->assertDontSee($taskId);
    }
}
