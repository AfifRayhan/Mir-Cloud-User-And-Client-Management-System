<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerStatus;
use App\Models\Platform;
use App\Models\ResourceUpgradation;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Models\UserDepartment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KamTaskVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles exist
        Role::firstOrCreate(['role_name' => 'admin']);
        Role::firstOrCreate(['role_name' => 'kam']);
        Role::firstOrCreate(['role_name' => 'pro-kam']);
    }

    public function test_kam_can_only_see_their_created_tasks()
    {
        // Setup Users
        $kamRole = Role::where('role_name', 'kam')->first();
        $adminRole = Role::where('role_name', 'admin')->first();
        $proKamRole = Role::where('role_name', 'pro-kam')->first();

        $dept = UserDepartment::create(['name' => 'Sales']);

        $kam1 = User::factory()->create(['role_id' => $kamRole->id, 'department_id' => $dept->id]);
        $kam2 = User::factory()->create(['role_id' => $kamRole->id, 'department_id' => $dept->id]);
        $admin = User::factory()->create(['role_id' => $adminRole->id, 'department_id' => $dept->id]);
        $proKam = User::factory()->create(['role_id' => $proKamRole->id, 'department_id' => $dept->id]);

        // Setup Dependencies
        $platform = Platform::create(['name' => 'Cloud']);
        $status = CustomerStatus::create(['name' => 'Active']);

        $customer1 = Customer::create([
            'customer_name' => 'C1_UNIQUE_NAME',
            'platform_id' => $platform->id,
            'submitted_by' => $kam1->id,
        ]);

        $customer2 = Customer::create([
            'customer_name' => 'C2_UNIQUE_NAME',
            'platform_id' => $platform->id,
            'submitted_by' => $kam2->id,
        ]);

        // Create Resource Upgradations (created by KAMs)
        $upgradation1 = ResourceUpgradation::create([
            'customer_id' => $customer1->id,
            'status_id' => $status->id,
            'inserted_by' => $kam1->id, // Created by KAM 1
            'activation_date' => now(),
        ]);

        $upgradation2 = ResourceUpgradation::create([
            'customer_id' => $customer2->id,
            'status_id' => $status->id,
            'inserted_by' => $kam2->id, // Created by KAM 2
            'activation_date' => now(),
        ]);

        // Create Tasks
        $task1 = Task::create([
            'customer_id' => $customer1->id,
            'status_id' => $status->id,
            'allocation_type' => 'upgrade',
            'resource_upgradation_id' => $upgradation1->id,
        ]);

        $task2 = Task::create([
            'customer_id' => $customer2->id,
            'status_id' => $status->id,
            'allocation_type' => 'upgrade',
            'resource_upgradation_id' => $upgradation2->id,
        ]);

        // --- Execute Tests ---

        // 1. KAM 1 should see Task 1 (C1) NOT Task 2 (C2)
        $response = $this->actingAs($kam1)->get(route('kam-task-management.index'));
        $response->assertStatus(200);
        $response->assertSee('C1_UNIQUE_NAME');
        $response->assertDontSee('C2_UNIQUE_NAME');

        // 2. KAM 2 should see Task 2 (C2) NOT Task 1 (C1)
        $response = $this->actingAs($kam2)->get(route('kam-task-management.index'));
        $response->assertStatus(200);
        $response->assertDontSee('C1_UNIQUE_NAME');
        $response->assertSee('C2_UNIQUE_NAME');

        // 3. Admin should see BOTH
        $response = $this->actingAs($admin)->get(route('kam-task-management.index'));
        $response->assertStatus(200);
        $response->assertSee('C1_UNIQUE_NAME');
        $response->assertSee('C2_UNIQUE_NAME');

        // 4. Pro-Kam should see BOTH
        $response = $this->actingAs($proKam)->get(route('kam-task-management.index'));
        $response->assertStatus(200);
        $response->assertSee('C1_UNIQUE_NAME');
        $response->assertSee('C2_UNIQUE_NAME');
    }
}
