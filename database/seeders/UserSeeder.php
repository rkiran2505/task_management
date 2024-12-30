<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create an Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin', // Admin role
        ]);

        // Create a regular User
        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('12345678'),
            'role' => 'user', // Regular user role
        ]);

        // Optionally, create more users
        User::create([
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'password' => Hash::make('12345678'),
            'role' => 'user', // Regular user role
        ]);
    }
}
