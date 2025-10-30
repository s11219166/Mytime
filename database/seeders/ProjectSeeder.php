<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user (admin) to assign as creator
        $admin = User::first();

        if (!$admin) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }

        // Skip if projects already exist
        if (Project::count() > 0) {
            $this->command->info('Projects already exist. Skipping seeding.');
            return;
        }

        $projects = [
            [
                'name' => 'E-commerce Platform',
                'description' => 'Complete online shopping website with payment integration and inventory management',
                'status' => 'active',
                'priority' => 'high',
                'budget' => 15000.00,
                'start_date' => '2024-01-15',
                'end_date' => '2024-03-30',
                'progress' => 75,
                'tags' => ['web', 'ecommerce', 'php', 'laravel'],
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Mobile App Development',
                'description' => 'Cross-platform mobile application for iOS and Android with real-time features',
                'status' => 'inprogress',
                'priority' => 'medium',
                'budget' => 25000.00,
                'start_date' => '2024-02-01',
                'end_date' => '2024-05-15',
                'progress' => 45,
                'tags' => ['mobile', 'react-native', 'ios', 'android'],
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Analytics Dashboard',
                'description' => 'Data visualization tool with advanced reporting and real-time analytics',
                'status' => 'review_pending',
                'priority' => 'medium',
                'budget' => 8000.00,
                'start_date' => '2023-12-01',
                'end_date' => '2024-01-31',
                'progress' => 85,
                'tags' => ['analytics', 'dashboard', 'charts', 'reporting'],
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Website Redesign',
                'description' => 'Modern responsive design with improved user experience and performance',
                'status' => 'paused',
                'priority' => 'low',
                'budget' => 5000.00,
                'start_date' => '2024-03-01',
                'end_date' => '2024-06-30',
                'progress' => 25,
                'tags' => ['design', 'ui/ux', 'responsive', 'frontend'],
                'created_by' => $admin->id,
            ],
            [
                'name' => 'API Integration',
                'description' => 'Third-party service integration with payment gateways and external APIs',
                'status' => 'overdue',
                'priority' => 'urgent',
                'budget' => 3500.00,
                'start_date' => '2024-01-10',
                'end_date' => '2024-02-28',
                'progress' => 60,
                'tags' => ['api', 'integration', 'payments', 'backend'],
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Legacy System Migration',
                'description' => 'Database migration and system modernization project',
                'status' => 'cancelled',
                'priority' => 'low',
                'budget' => 12000.00,
                'start_date' => '2023-11-15',
                'end_date' => '2023-12-31',
                'progress' => 15,
                'tags' => ['migration', 'database', 'legacy', 'modernization'],
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Client Portal',
                'description' => 'Customer management system with self-service capabilities',
                'status' => 'awaiting_input',
                'priority' => 'medium',
                'budget' => 7500.00,
                'start_date' => '2024-02-15',
                'end_date' => '2024-04-30',
                'progress' => 30,
                'tags' => ['portal', 'crm', 'customer', 'management'],
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Security Audit',
                'description' => 'Comprehensive security assessment and vulnerability testing',
                'status' => 'completed',
                'priority' => 'high',
                'budget' => 4000.00,
                'start_date' => '2023-10-01',
                'end_date' => '2023-11-30',
                'progress' => 100,
                'tags' => ['security', 'audit', 'testing', 'compliance'],
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Performance Optimization',
                'description' => 'Website and application performance improvements and optimization',
                'status' => 'active',
                'priority' => 'medium',
                'budget' => 6000.00,
                'start_date' => '2024-03-15',
                'end_date' => '2024-05-01',
                'progress' => 20,
                'tags' => ['performance', 'optimization', 'speed', 'caching'],
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Content Management System',
                'description' => 'Custom CMS development with advanced content editing features',
                'status' => 'revision_needed',
                'priority' => 'medium',
                'budget' => 9500.00,
                'start_date' => '2024-01-20',
                'end_date' => '2024-04-15',
                'progress' => 55,
                'tags' => ['cms', 'content', 'management', 'editor'],
                'created_by' => $admin->id,
            ],
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }

        $this->command->info('Projects seeded successfully!');
    }
}
