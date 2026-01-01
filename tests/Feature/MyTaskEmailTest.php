<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerStatus;
use App\Models\Role;
use App\Models\Service;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MyTaskEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_task_sends_tech_confirmation_when_source_is_tech_allocation()
    {
        Mail::fake();

        // Setup Roles
        $techRole = Role::create(['role_name' => 'tech', 'role_label' => 'Tech']);
        $kamRole = Role::create(['role_name' => 'kam', 'role_label' => 'KAM']);
        $managementRole = Role::create(['role_name' => 'management', 'role_label' => 'Management']);

        // Setup Users
        $techUser = User::factory()->create(['role_id' => $techRole->id]);
        $kamUser = User::factory()->create(['role_id' => $kamRole->id]);
        $managerUser = User::factory()->create(['role_id' => $managementRole->id]);

        // Setup Data
        Service::create(['service_name' => 'vCPU', 'unit' => 'Core']);
        CustomerStatus::create(['name' => 'Production']);
        TaskStatus::create(['name' => 'Proceed from KAM']);
        TaskStatus::create(['name' => 'Pending']);
        TaskStatus::create(['name' => 'Proceed from Tech']);

        $platform = \App\Models\Platform::create(['platform_name' => 'Test Platform']);
        $customer = Customer::create([
            'customer_name' => 'Test Customer',
            'activation_date' => now(),
            'platform_id' => $platform->id,
            'submitted_by' => $kamUser->id,
        ]);

        $vdc = \App\Models\Vdc::create(['customer_id' => $customer->id, 'vdc_name' => 'Existing VDC']);

        // TEST 1: Tech Allocation Source (Expect TechResourceConfirmationEmail)
        $task1 = Task::create([
            'customer_id' => $customer->id,
            'activation_date' => now(),
            'allocation_type' => 'upgrade',
            'assigned_to' => $techUser->id,
            'assigned_by' => $kamUser->id,
            'task_status_id' => 1,
        ]);

        $this->actingAs($techUser)
            ->postJson(route('my-tasks.complete', $task1), [
                'vdc_id' => $vdc->id,
                'source' => 'tech_allocation',
            ])
            ->assertOk();

        Mail::assertQueued(\App\Mail\TechResourceConfirmationEmail::class, function ($mail) use ($kamUser) {
            return $mail->hasTo($kamUser->email);
        });

        Mail::assertNotQueued(\App\Mail\TaskCompletionEmail::class);
    }

    public function test_complete_task_sends_task_completion_when_source_is_not_tech_allocation()
    {
        Mail::fake();

        // Setup Roles
        $techRole = Role::firstOrCreate(['role_name' => 'tech'], ['role_label' => 'Tech']);
        $kamRole = Role::firstOrCreate(['role_name' => 'kam'], ['role_label' => 'KAM']);
        $managementRole = Role::firstOrCreate(['role_name' => 'management'], ['role_label' => 'Management']);

        // Setup Users
        $techUser = User::factory()->create(['role_id' => $techRole->id]);
        $kamUser = User::factory()->create(['role_id' => $kamRole->id]);
        $managerUser = User::factory()->create(['role_id' => $managementRole->id]);

        // Setup Data (re-fetching or checking existence to avoid duplicates if ID based conflicts)
        // Since RefreshDatabase, we can recreate.
        Service::firstOrCreate(['service_name' => 'vCPU'], ['unit' => 'Core']);
        CustomerStatus::firstOrCreate(['name' => 'Production']);
        TaskStatus::firstOrCreate(['name' => 'Proceed from KAM']);
        TaskStatus::firstOrCreate(['name' => 'Pending']);
        TaskStatus::firstOrCreate(['name' => 'Proceed from Tech']);

        $platform = \App\Models\Platform::firstOrCreate(['platform_name' => 'Test Platform']);
        $customer = Customer::create([
            'customer_name' => 'Test Customer 2',
            'activation_date' => now(),
            'platform_id' => $platform->id,
            'submitted_by' => $kamUser->id,
        ]);
        $vdc = \App\Models\Vdc::create(['customer_id' => $customer->id, 'vdc_name' => 'Existing VDC 2']);

        // TEST 2: My Tasks Source (Expect TaskCompletionEmail)
        $task2 = Task::create([
            'customer_id' => $customer->id,
            'activation_date' => now(),
            'allocation_type' => 'upgrade',
            'assigned_to' => $techUser->id,
            'assigned_by' => $kamUser->id,
            'task_status_id' => 1,
        ]);

        $this->actingAs($techUser)
            ->postJson(route('my-tasks.complete', $task2), [
                'vdc_id' => $vdc->id,
                // No source or different source
            ])
            ->assertOk();

        Mail::assertQueued(\App\Mail\TaskCompletionEmail::class, function ($mail) use ($managerUser) {
            return $mail->hasTo($managerUser->email);
        });

        Mail::assertNotQueued(\App\Mail\TechResourceConfirmationEmail::class);
    }
}
