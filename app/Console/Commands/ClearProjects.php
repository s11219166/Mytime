<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;

class ClearProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all projects from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->confirm('Are you sure you want to delete ALL projects? This cannot be undone.')) {
            $count = Project::count();
            Project::truncate();
            $this->info("Successfully deleted {$count} projects from the database.");
        } else {
            $this->info('Operation cancelled.');
        }
    }
}
