<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@mytime.com',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        // Create a regular user for testing
        User::create([
            'name' => 'John Doe',
            'email' => 'user@mytime.com',
            'role' => 'user',
            'password' => Hash::make('user123'),
        ]);
    }
}