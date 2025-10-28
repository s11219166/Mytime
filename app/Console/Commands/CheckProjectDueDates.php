<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckProjectDueDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:check-due-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check project due dates and send reminders';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService)
    {
        $this->info('Checking project due dates...');
        
        $result = $notificationService->checkProjectDueDates();
        
        $this->info('Project due date check completed!');
        
        return Command::SUCCESS;
    }
}
