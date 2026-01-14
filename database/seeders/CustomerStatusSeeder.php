<?php

namespace Database\Seeders;

use App\Models\CustomerStatus;
use Illuminate\Database\Seeder;

class CustomerStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['Billable', 'Test', 'Test to Billable', 'Billable to Test'];

        foreach ($statuses as $status) {
            CustomerStatus::firstOrCreate(['name' => $status]);
        }
    }
}
