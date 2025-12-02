<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first
        $this->call([
            RoleSeeder::class,
            PlatformSeeder::class,
            ServiceSeeder::class,
            CustomerStatusSeeder::class,
            TaskStatusSeeder::class,
            UserDepartmentSeeder::class,
        ]);

        // Create admin user
        $adminRole = \App\Models\Role::where('role_name', 'admin')->first();
        
        if ($adminRole) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'username' => 'admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role_id' => $adminRole->id,
            ]);
        }
    }
}
