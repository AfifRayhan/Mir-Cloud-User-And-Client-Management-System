<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            'other'
        ];

        foreach ($roles as $role) {
            \App\Models\Role::create([
                'role_name' => $role
            ]);
        }
    }
}
