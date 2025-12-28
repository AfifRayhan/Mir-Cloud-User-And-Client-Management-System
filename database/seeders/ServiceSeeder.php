<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['service_name' => 'vCPU', 'unit' => 'core', 'unit_price' => 500.00],
            ['service_name' => 'Memory', 'unit' => 'GB', 'unit_price' => 300.00],
            ['service_name' => 'SAS', 'unit' => 'GB', 'unit_price' => 50.00],
            ['service_name' => 'SSD', 'unit' => 'GB', 'unit_price' => 100.00],
            ['service_name' => 'BS', 'unit' => 'GB', 'unit_price' => 150.00],
            ['service_name' => 'PI', 'unit' => null, 'unit_price' => 20.00],
            ['service_name' => 'SS', 'unit' => null, 'unit_price' => 40.00],
            ['service_name' => 'EIP', 'unit' => null, 'unit_price' => 1000.00],
            ['service_name' => 'VPN', 'unit' => null, 'unit_price' => 1500.00],
            ['service_name' => 'BDIX', 'unit' => null, 'unit_price' => 2000.00],
            ['service_name' => 'BW', 'unit' => 'Mbps', 'unit_price' => 2500.00],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(['service_name' => $service['service_name']], $service);
        }
    }
}
