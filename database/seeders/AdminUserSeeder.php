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
        // Create admin user only if it doesn't exist
        User::firstOrCreate(
            ['email' => 'admin@mytime.com'],
            [
                'name' => 'Admin User',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
            ]
        );

        // Create a regular user for testing only if it doesn't exist
        User::firstOrCreate(
            ['email' => 'user@mytime.com'],
            [
                'name' => 'John Doe',
                'role' => 'user',
                'password' => Hash::make('user123'),
            ]
        );
    }
}