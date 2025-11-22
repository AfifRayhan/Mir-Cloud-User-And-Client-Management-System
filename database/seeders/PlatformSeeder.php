<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = [
            'ACS',
            'Huawei',
        ];

        foreach ($platforms as $name) {
            Platform::firstOrCreate(['platform_name' => $name]);
        }
    }
}

