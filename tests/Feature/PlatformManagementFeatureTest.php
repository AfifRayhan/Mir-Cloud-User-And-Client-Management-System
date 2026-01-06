<?php

namespace Tests\Feature;

use App\Models\Platform;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformManagementFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed database if needed, but RefreshDatabase handles migration
    }

    public function test_admin_can_see_delete_button_and_services()
    {
        $admin = User::factory()->create(['role_id' => 1]); // Admin
        $platform = Platform::create(['platform_name' => 'Test Platform']);
        Service::create([
            'platform_id' => $platform->id,
            'service_name' => 'Test Service',
            'unit' => 'GB',
            'unit_price' => 10.00,
            'inserted_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('platforms.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Platform');
        $response->assertSee('Test Service'); // Should be in the modal HTML
        $response->assertSee('Delete'); // Button should be visible
    }

    public function test_management_cannot_see_delete_button_but_can_view_services()
    {
        $management = User::factory()->create(['role_id' => 6]); // Management
        $platform = Platform::create(['platform_name' => 'Test Platform']);
        Service::create([
            'platform_id' => $platform->id,
            'service_name' => 'Test Service',
            'unit' => 'GB',
            'unit_price' => 10.00,
            'inserted_by' => $management->id,
        ]);

        $response = $this->actingAs($management)->get(route('platforms.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Platform');
        $response->assertSee('Test Service'); // View modal content
        $response->assertDontSee('Delete'); // Button should NOT be visible
    }

    public function test_tech_cannot_see_delete_button()
    {
        $tech = User::factory()->create(['role_id' => 3]); // Tech
        $platform = Platform::create(['platform_name' => 'Test Platform']);

        $response = $this->actingAs($tech)->get(route('platforms.index'));

        $response->assertStatus(200);
        $response->assertDontSee('Delete');
    }

    public function test_admin_can_delete_platform()
    {
        $admin = User::factory()->create(['role_id' => 1]);
        $platform = Platform::create(['platform_name' => 'Delete Me']);

        $response = $this->actingAs($admin)->delete(route('platforms.destroy', $platform));

        $response->assertRedirect(route('platforms.index'));
        $this->assertDatabaseMissing('platforms', ['id' => $platform->id]);
    }

    public function test_management_cannot_delete_platform()
    {
        $management = User::factory()->create(['role_id' => 6]);
        $platform = Platform::create(['platform_name' => 'Keep Me']);

        $response = $this->actingAs($management)->delete(route('platforms.destroy', $platform));

        $response->assertStatus(403);
        $this->assertDatabaseHas('platforms', ['id' => $platform->id]);
    }
}
