<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FinancialCategory;
use Illuminate\Support\Facades\DB;

class FinancialCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing default categories (user_id is null)
        FinancialCategory::whereNull('user_id')->delete();

        $categories = [
            // Income Categories
            [
                'name' => 'Salary',
                'type' => 'income',
                'icon' => '💼',
                'color' => '#10b981',
                'user_id' => null
            ],
            [
                'name' => 'Freelance',
                'type' => 'income',
                'icon' => '💻',
                'color' => '#059669',
                'user_id' => null
            ],
            [
                'name' => 'Investment',
                'type' => 'income',
                'icon' => '📈',
                'color' => '#34d399',
                'user_id' => null
            ],
            [
                'name' => 'Other Income',
                'type' => 'income',
                'icon' => '💰',
                'color' => '#6ee7b7',
                'user_id' => null
            ],

            // Expense Categories
            [
                'name' => 'Food',
                'type' => 'expense',
                'icon' => '🍔',
                'color' => '#ef4444',
                'user_id' => null
            ],
            [
                'name' => 'Transport',
                'type' => 'expense',
                'icon' => '🚗',
                'color' => '#dc2626',
                'user_id' => null
            ],
            [
                'name' => 'Utilities',
                'type' => 'expense',
                'icon' => '⚡',
                'color' => '#f87171',
                'user_id' => null
            ],
            [
                'name' => 'Rent',
                'type' => 'expense',
                'icon' => '🏠',
                'color' => '#b91c1c',
                'user_id' => null
            ],
            [
                'name' => 'Entertainment',
                'type' => 'expense',
                'icon' => '🎬',
                'color' => '#fca5a5',
                'user_id' => null
            ],
            [
                'name' => 'Healthcare',
                'type' => 'expense',
                'icon' => '🏥',
                'color' => '#991b1b',
                'user_id' => null
            ],
            [
                'name' => 'Shopping',
                'type' => 'expense',
                'icon' => '🛍️',
                'color' => '#fee2e2',
                'user_id' => null
            ],

            // Savings Categories
            [
                'name' => 'Emergency Fund',
                'type' => 'savings',
                'icon' => '🚨',
                'color' => '#3b82f6',
                'user_id' => null
            ],
            [
                'name' => 'Retirement',
                'type' => 'savings',
                'icon' => '👴',
                'color' => '#2563eb',
                'user_id' => null
            ],
            [
                'name' => 'Investment Fund',
                'type' => 'savings',
                'icon' => '📊',
                'color' => '#60a5fa',
                'user_id' => null
            ],
            [
                'name' => 'Goal-based Savings',
                'type' => 'savings',
                'icon' => '🎯',
                'color' => '#93c5fd',
                'user_id' => null
            ],

            // Bank Deposit Categories
            [
                'name' => 'Fixed Deposit',
                'type' => 'bank_deposit',
                'icon' => '🏦',
                'color' => '#f59e0b',
                'user_id' => null
            ],
            [
                'name' => 'Savings Account',
                'type' => 'bank_deposit',
                'icon' => '💳',
                'color' => '#d97706',
                'user_id' => null
            ],
            [
                'name' => 'Current Account',
                'type' => 'bank_deposit',
                'icon' => '💵',
                'color' => '#fbbf24',
                'user_id' => null
            ],
        ];

        foreach ($categories as $category) {
            FinancialCategory::create($category);
        }

        $this->command->info('Financial categories seeded successfully!');
    }
}
