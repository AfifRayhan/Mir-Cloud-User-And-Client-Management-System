<?php

namespace Tests\Feature;

use App\Models\Platform;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformServiceFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $this->seed(\Database\Seeders\RoleSeeder::class);

        // Create an admin user
        $this->admin = User::factory()->create([
            'role_id' => 1, // Admin
        ]);

        // Mock the permissions if necessary, or just login
        $this->actingAs($this->admin);
    }

    /** @test */
    public function it_seeds_default_services_when_a_new_platform_is_created()
    {
        $response = $this->post(route('platforms.store'), [
            'platform_name' => 'New Test Platform',
        ]);

        $response->assertRedirect(route('platforms.index'));

        $platform = Platform::where('platform_name', 'New Test Platform')->first();
        $this->assertNotNull($platform);

        $services = Service::where('platform_id', $platform->id)->get();

        $this->assertCount(8, $services);

        $expectedServices = [
            'vCPU' => 'core',
            'Memory' => 'GB',
            'NVMe' => 'GB',
            'BS' => 'GB',
            'EIP' => null,
            'VPN' => null,
            'BDIX' => null,
            'BW' => 'Mbps',
        ];

        foreach ($expectedServices as $name => $unit) {
            $this->assertTrue($services->contains('service_name', $name));
            $this->assertEquals($unit, $services->where('service_name', $name)->first()->unit);
        }
    }

    /** @test */
    public function it_filters_services_by_platform_in_service_management()
    {
        $platform1 = Platform::factory()->create(['platform_name' => 'Platform 1']);
        $platform2 = Platform::factory()->create(['platform_name' => 'Platform 2']);

        Service::factory()->create([
            'platform_id' => $platform1->id,
            'service_name' => 'Service P1',
            'inserted_by' => $this->admin->id,
        ]);

        Service::factory()->create([
            'platform_id' => $platform2->id,
            'service_name' => 'Service P2',
            'inserted_by' => $this->admin->id,
        ]);

        // No filter
        $response = $this->get(route('services.index'));
        $response->assertStatus(200);
        $response->assertSee('Service P1');
        $response->assertSee('Service P2');

        // Filter by platform 1
        $response = $this->get(route('services.index', ['platform_id' => $platform1->id]));
        $response->assertStatus(200);
        $response->assertSee('Service P1');
        $response->assertDontSee('Service P2');

        // Filter by platform 2
        $response = $this->get(route('services.index', ['platform_id' => $platform2->id]));
        $response->assertStatus(200);
        $response->assertSee('Service P2');
        $response->assertDontSee('Service P1');
    }
}
