<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerStatus;
use App\Models\Platform;
use App\Models\Role;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_billing_user_can_bill_a_task(): void
    {
        $billRole = Role::create(['role_name' => 'bill']);
        $billingUser = User::factory()->create(['role_id' => $billRole->id]);

        $platform = Platform::create(['platform_name' => 'Test Platform']);
        $customer = Customer::create([
            'customer_name' => 'Test Customer',
            'customer_activation_date' => now(),
            'customer_address' => 'Test Address',
            'platform_id' => $platform->id,
            'kam_id' => $billingUser->id,
            'account_manager_name' => 'AM',
            'technical_person_name' => 'TP',
        ]);

        $status = CustomerStatus::create(['name' => 'Production']);
        // Use 'name' instead of 'status_name' as per migration
        $taskStatus = TaskStatus::create(['name' => 'Pending']);

        $task = Task::create([
            'customer_id' => $customer->id,
            'allocation_type' => 'upgrade',
            'completed_at' => now(),
            'activation_date' => now(),
            'status_id' => $status->id,
            'task_status_id' => $taskStatus->id,
        ]);

        $this->actingAs($billingUser);
        $response = $this->post(route('billing-task-management.bill', $task->id));

        $response->assertStatus(302);
        $task->refresh();
        $this->assertNotNull($task->billed_at);
        $response->assertSessionHas('success', 'Task marked as billed successfully.');
    }

    public function test_unauthorized_user_cannot_bill_a_task(): void
    {
        $kamRole = Role::create(['role_name' => 'kam']);
        $kamUser = User::factory()->create(['role_id' => $kamRole->id]);

        $platform = Platform::create(['platform_name' => 'Test Platform']);
        $customer = Customer::create([
            'customer_name' => 'Test Customer',
            'customer_activation_date' => now(),
            'customer_address' => 'Test Address',
            'platform_id' => $platform->id,
            'kam_id' => $kamUser->id,
            'account_manager_name' => 'AM',
            'technical_person_name' => 'TP',
        ]);

        $status = CustomerStatus::create(['name' => 'Production']);
        $taskStatus = TaskStatus::create(['name' => 'Pending']);

        $task = Task::create([
            'customer_id' => $customer->id,
            'allocation_type' => 'upgrade',
            'completed_at' => now(),
            'activation_date' => now(),
            'status_id' => $status->id,
            'task_status_id' => $taskStatus->id,
        ]);

        $this->actingAs($kamUser);
        $response = $this->post(route('billing-task-management.bill', $task->id));

        $response->assertStatus(403);
        $this->assertNull($task->billed_at);
    }
}
