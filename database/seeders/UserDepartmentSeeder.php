<?php

namespace Database\Seeders;

use App\Models\UserDepartment;
use Illuminate\Database\Seeder;

class UserDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = ['Marketing', 'Technical', 'Billing'];

        foreach ($departments as $department) {
            UserDepartment::firstOrCreate(['department_name' => $department]);
        }
    }
}
