<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Service::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $platforms = \App\Models\Platform::all();
        $acs = $platforms->where('platform_name', 'ACS')->first();
        $huawei = $platforms->where('platform_name', 'Huawei')->first();

        if ($acs) {
            $acsServices = [
                ['service_name' => 'vCPU', 'unit' => 'core', 'unit_price' => 500.00],
                ['service_name' => 'Memory', 'unit' => 'GB', 'unit_price' => 300.00],
                ['service_name' => 'NVMe', 'unit' => 'GB', 'unit_price' => 1000.00],
                ['service_name' => 'BS', 'unit' => 'GB', 'unit_price' => 150.00],
                ['service_name' => 'PI', 'unit' => null, 'unit_price' => 20.00],
                ['service_name' => 'SS', 'unit' => null, 'unit_price' => 40.00],
                ['service_name' => 'EIP', 'unit' => null, 'unit_price' => 1000.00],
                ['service_name' => 'VPN', 'unit' => null, 'unit_price' => 1500.00],
                ['service_name' => 'BDIX', 'unit' => null, 'unit_price' => 2000.00],
                ['service_name' => 'BW', 'unit' => 'Mbps', 'unit_price' => 2500.00],
            ];

            foreach ($acsServices as $service) {
                $service['platform_id'] = $acs->id;
                Service::create($service);
            }
        }

        if ($huawei) {
            $huaweiServices = [
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

            foreach ($huaweiServices as $service) {
                $service['platform_id'] = $huawei->id;
                Service::create($service);
            }
        }
    }
}
