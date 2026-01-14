<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::all()->pluck('id', 'role_name');

        $users = [
            [
                'name' => 'Harun Or Rashid',
                'email' => 'full1@gmail.com',
                'username' => 'full1@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['pro-kam'],
            ],
            [
                'name' => 'Md. Feroz Alam',
                'email' => 'full2@gmail.com',
                'username' => 'full2@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['kam'],
            ],
            [
                'name' => 'Mukidur Rahman',
                'email' => 'full3@gmail.com',
                'username' => 'full3@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['kam'],
            ],
            [
                'name' => 'Ashraful Alam',
                'email' => 'full4@gmail.com',
                'username' => 'full4@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['pro-tech'],
            ],
            [
                'name' => 'Rakibul Islam',
                'email' => 'full5@gmail.com',
                'username' => 'full5@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['tech'],
            ],
            [
                'name' => 'Nishat Sultana',
                'email' => 'full6@gmail.com',
                'username' => 'full6@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['tech'],
            ],
            [
                'name' => 'Monirul Islam Abir',
                'email' => 'full7@gmail.com',
                'username' => 'full7@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['tech'],
            ],
            [
                'name' => 'Tanvir Ahmed',
                'email' => 'full8@gmail.com',
                'username' => 'full8@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['tech'],
            ],
            [
                'name' => 'Khondoker Nurul Huda',
                'email' => 'full9@gmail.com',
                'username' => 'full9@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['tech'],
            ],
            [
                'name' => 'Afif Rayhan Pranto',
                'email' => 'full10@gmail.com',
                'username' => 'full10@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['management'],
            ],
            [
                'name' => 'Fake Billing Name',
                'email' => 'full@gmail.com',
                'username' => 'full@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['bill'],
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
