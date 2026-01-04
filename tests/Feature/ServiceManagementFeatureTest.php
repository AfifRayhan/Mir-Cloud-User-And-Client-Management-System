<?php

namespace Tests\Feature;

use App\Models\Platform;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceManagementFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed database if needed, but RefreshDatabase handles migration
    }

    public function test_admin_can_see_delete_button()
    {
        $admin = User::factory()->create(['role_id' => 1]); // Admin
        $platform = Platform::create(['platform_name' => 'Test Platform']);
        Service::create([
            'platform_id' => $platform->id, 
            'service_name' => 'Test Service', 
            'unit' => 'GB', 
            'unit_price' => 10.00,
            'inserted_by' => $admin->id
        ]);

        $response = $this->actingAs($admin)->get(route('services.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Service');
        $response->assertSee('Delete'); // Button should be visible
    }

    public function test_management_cannot_see_delete_button()
    {
        $management = User::factory()->create(['role_id' => 6]); // Management
        $platform = Platform::create(['platform_name' => 'Test Platform']);
        Service::create([
            'platform_id' => $platform->id, 
            'service_name' => 'Test Service', 
            'unit' => 'GB', 
            'unit_price' => 10.00,
            'inserted_by' => $management->id
        ]);

        $response = $this->actingAs($management)->get(route('services.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Service');
        $response->assertDontSee('Delete'); // Button should NOT be visible
    }

    public function test_pro_tech_cannot_see_delete_button()
    {
        $proTech = User::factory()->create(['role_id' => 2]); // Pro-Tech
        $platform = Platform::create(['platform_name' => 'Test Platform']);
        Service::create([
            'platform_id' => $platform->id, 
            'service_name' => 'Test Service', 
            'unit' => 'GB', 
            'unit_price' => 10.00,
            'inserted_by' => $proTech->id
        ]);

        $response = $this->actingAs($proTech)->get(route('services.index'));

        $response->assertStatus(200);
        $response->assertDontSee('Delete');
    }

    public function test_admin_can_delete_service()
    {
        $admin = User::factory()->create(['role_id' => 1]);
        $platform = Platform::create(['platform_name' => 'Test Platform']);
        $service = Service::create([
            'platform_id' => $platform->id, 
            'service_name' => 'Delete Me',
            'inserted_by' => $admin->id
        ]);

        $response = $this->actingAs($admin)->delete(route('services.destroy', $service));

        $response->assertRedirect(route('services.index'));
        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }

    public function test_management_cannot_delete_service()
    {
        $management = User::factory()->create(['role_id' => 6]);
        $platform = Platform::create(['platform_name' => 'Test Platform']);
        $service = Service::create([
            'platform_id' => $platform->id, 
            'service_name' => 'Keep Me',
            'inserted_by' => $management->id
        ]);

        $response = $this->actingAs($management)->delete(route('services.destroy', $service));

        $response->assertStatus(403);
        $this->assertDatabaseHas('services', ['id' => $service->id]);
    }
}
