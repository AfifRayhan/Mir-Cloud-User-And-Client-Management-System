<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'admin',
            'pro-kam',
            'kam',
            'pro-tech',
            'tech',
            'management',
            'bill',
        ];

        foreach ($roles as $role) {
            \App\Models\Role::firstOrCreate([
                'role_name' => $role,
            ]);
        }
    }
}
