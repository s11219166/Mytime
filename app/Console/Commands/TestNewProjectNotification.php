<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\User;
use Illuminate\Console\Command;

class TestNewProjectNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:new-project-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test new project notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing new project notification...');

        // Get a user
        $user = User::first();
        if (!$user) {
            $this->error('No users found!');
            return Command::FAILURE;
        }

        // Get a project
        $project = Project::first();
        if (!$project) {
            $this->error('No projects found!');
            return Command::FAILURE;
        }

        // Send new project notification
        $project->sendNewProjectNotification();

        $this->info('New project notification test completed!');

        return Command::SUCCESS;
    }
}
