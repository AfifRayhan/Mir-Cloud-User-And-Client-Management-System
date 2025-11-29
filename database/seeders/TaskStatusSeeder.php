<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaskStatus;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Proceed from KAM',
            'Proceed from Pro Tech',
            'Proceed from Tech',
            'Proceed from Billing',
            
        ];

        foreach ($statuses as $status) {
            TaskStatus::firstOrCreate(
                ['name' => $status],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
