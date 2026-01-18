<?php

namespace Tests\Feature;

use App\Mail\BillAssignmentEmail;
use App\Models\Customer;
use App\Models\CustomerStatus;
use App\Models\Platform;
use App\Models\ResourceTransfer;
use App\Models\ResourceTransferDetail;
use App\Models\Role;
use App\Models\Service;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillAssignmentEmailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\CustomerStatusSeeder::class);
        $this->seed(\Database\Seeders\TaskStatusSeeder::class);

        Platform::create(['platform_name' => 'ACS']);
        Platform::create(['platform_name' => 'Huawei']);

        $this->seed(\Database\Seeders\ServiceSeeder::class);
    }

    public function test_bill_assignment_email_content_for_test_to_billable()
    {
        $platform = Platform::firstOrCreate(['platform_name' => 'ACS']);
        $user = User::factory()->create();
        $kamRole = Role::firstOrCreate(['role_name' => 'kam']);
        $user->role_id = $kamRole->id;
        $user->save();

        $customer = Customer::factory()->create([
            'platform_id' => $platform->id,
            'submitted_by' => $user->id,
        ]);

        $statusTest = CustomerStatus::firstWhere('name', 'test') ?? CustomerStatus::create(['name' => 'test']);
        $statusBillable = CustomerStatus::firstWhere('name', 'billable') ?? CustomerStatus::create(['name' => 'billable']);

        $transfer = ResourceTransfer::create([
            'customer_id' => $customer->id,
            'status_from_id' => $statusTest->id,
            'status_to_id' => $statusBillable->id,
            'transfer_datetime' => now(),
            'inserted_by' => $user->id,
        ]);

        $service = Service::factory()->create(['service_name' => 'CPU', 'unit' => 'Core', 'platform_id' => $platform->id]);

        ResourceTransferDetail::create([
            'resource_transfer_id' => $transfer->id,
            'service_id' => $service->id,
            'current_source_quantity' => 10, // Old Test
            'current_target_quantity' => 5,  // Old Billable
            'transfer_amount' => 2,
            'new_source_quantity' => 8,      // New Test
            'new_target_quantity' => 7,      // New Billable
        ]);

        $taskStatus = TaskStatus::firstOrCreate(['name' => 'Pending']);

        $task = Task::create([
            'customer_id' => $customer->id,
            'status_id' => $statusBillable->id,
            'task_status_id' => $taskStatus->id,
            'resource_transfer_id' => $transfer->id,
            'task_id' => 'TASK-12345',
            'assigned_to' => $user->id,
            'assigned_by' => $user->id,
            'assigned_at' => now(),
            'activation_date' => now(),
            'assignment_datetime' => now(),
            'deadline_datetime' => now()->addDay(),
            'allocation_type' => 'transfer',
            'completed_at' => now(),
        ]);

        $mailable = new BillAssignmentEmail($transfer, $user, $task);

        $mailable->assertSeeInHtml('Billable');
        $mailable->assertSeeInHtml('Transfer Amount');
        $mailable->assertSeeInHtml('Test');
        $mailable->assertSeeInHtml('CPU');
        $mailable->assertSeeInHtml('7 Core');
        $mailable->assertSeeInHtml('2 Core');
        $mailable->assertSeeInHtml('8 Core');

        $mailable->assertSeeInHtml('color:#0d6efd; font-weight:bold;'); // Blue transfer
        $mailable->assertSeeInHtml('color: #7c3aed; font-weight:bold;'); // Purple Test
    }

    public function test_bill_assignment_email_content_for_billable_to_test()
    {
        $platform = Platform::firstOrCreate(['platform_name' => 'ACS']);
        $user = User::factory()->create();
        $kamRole = Role::firstOrCreate(['role_name' => 'kam']);
        $user->role_id = $kamRole->id;
        $user->save();

        $customer = Customer::factory()->create([
            'platform_id' => $platform->id,
            'submitted_by' => $user->id,
        ]);

        $statusTest = CustomerStatus::firstWhere('name', 'test') ?? CustomerStatus::create(['name' => 'test']);
        $statusBillable = CustomerStatus::firstWhere('name', 'billable') ?? CustomerStatus::create(['name' => 'billable']);

        $transfer = ResourceTransfer::create([
            'customer_id' => $customer->id,
            'status_from_id' => $statusBillable->id,
            'status_to_id' => $statusTest->id,
            'transfer_datetime' => now(),
            'inserted_by' => $user->id,
        ]);

        $service = Service::factory()->create(['service_name' => 'RAM', 'unit' => 'GB', 'platform_id' => $platform->id]);

        ResourceTransferDetail::create([
            'resource_transfer_id' => $transfer->id,
            'service_id' => $service->id,
            'current_source_quantity' => 20, // Old Billable
            'current_target_quantity' => 0,  // Old Test
            'transfer_amount' => 5,
            'new_source_quantity' => 15,     // New Billable
            'new_target_quantity' => 5,      // New Test
        ]);

        $taskStatus = TaskStatus::firstOrCreate(['name' => 'Pending']);

        $task = Task::create([
            'customer_id' => $customer->id,
            'status_id' => $statusTest->id,
            'task_status_id' => $taskStatus->id,
            'resource_transfer_id' => $transfer->id,
            'task_id' => 'TASK-67890',
            'assigned_to' => $user->id,
            'assigned_by' => $user->id,
            'assigned_at' => now(),
            'activation_date' => now(),
            'assignment_datetime' => now(),
            'deadline_datetime' => now()->addDay(),
            'allocation_type' => 'transfer',
            'completed_at' => now(),
        ]);

        $mailable = new BillAssignmentEmail($transfer, $user, $task);

        // For Billable to Test:
        // Left Column: Billable (Source, decreased) -> 15 GB
        // Right Column: Test (Target, increased) -> 5 GB (Purple)
        $mailable->assertSeeInHtml('Billable');
        $mailable->assertSeeInHtml('Transfer Amount');
        $mailable->assertSeeInHtml('Test');
        $mailable->assertSeeInHtml('RAM');
        $mailable->assertSeeInHtml('15 GB');
        $mailable->assertSeeInHtml('5 GB'); // This appears twice

        $mailable->assertSeeInHtml('color:#0d6efd; font-weight:bold;'); // Blue transfer
        $mailable->assertSeeInHtml('color: #7c3aed; font-weight:bold;'); // Purple Test
    }

    public function test_bill_assignment_email_has_correct_subject(): void
    {
        $platform = Platform::firstOrCreate(['platform_name' => 'ACS']);
        $kamRole = Role::firstOrCreate(['role_name' => 'kam']);
        $kam = User::factory()->create(['role_id' => $kamRole->id]);

        $customer = Customer::factory()->create([
            'platform_id' => $platform->id,
            'submitted_by' => $kam->id,
        ]);

        $testStatus = CustomerStatus::firstWhere('name', 'test') ?? CustomerStatus::create(['name' => 'test']);
        $billableStatus = CustomerStatus::firstWhere('name', 'billable') ?? CustomerStatus::create(['name' => 'billable']);

        $transfer = ResourceTransfer::create([
            'customer_id' => $customer->id,
            'status_from_id' => $testStatus->id,
            'status_to_id' => $billableStatus->id,
            'transfer_datetime' => now(),
            'inserted_by' => $kam->id,
        ]);

        $mailable = new BillAssignmentEmail($transfer, $kam);

        $mailable->assertHasSubject('New Transfer Assignment');
    }
}
