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
            ['service_name' => 'RAM', 'unit' => 'GB', 'unit_price' => 300.00],
            ['service_name' => 'Storage', 'unit' => 'GB', 'unit_price' => 50.00],
            ['service_name' => 'Internet', 'unit' => 'Mbps', 'unit_price' => 800.00],
            ['service_name' => 'Real IP', 'unit' => null, 'unit_price' => 1000.00],
            ['service_name' => 'VPN', 'unit' => null, 'unit_price' => 1500.00],
            ['service_name' => 'BDIX', 'unit' => null, 'unit_price' => 2000.00],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(['service_name' => $service['service_name']], $service);
        }
    }
}
